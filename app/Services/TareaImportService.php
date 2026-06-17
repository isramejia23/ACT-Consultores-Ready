<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Tarea;
use App\Models\TareaCargada;
use App\Models\Obligacion;
use Illuminate\Support\Facades\DB;

class TareaImportService
{
    /**
     * Procesa las tareas cargadas de un cliente: crea facturas, tareas y limpia tareas_cargadas.
     *
     * @param Cliente $cliente
     * @return array ['facturas_creadas' => int, 'tareas_creadas' => int]
     */
    public function procesarTareasCargadas(Cliente $cliente): array
    {
        $tareasPendientes = TareaCargada::where('cedula', $cliente->cedula_cliente)->get();

        if ($tareasPendientes->isEmpty()) {
            return ['facturas_creadas' => 0, 'tareas_creadas' => 0];
        }

        $facturasCreadas = 0;
        $tareasCreadas = 0;

        // Agrupar por numero de factura para calcular totales
        $tareasPorFactura = $tareasPendientes->groupBy('numfac');

        DB::transaction(function () use ($cliente, $tareasPendientes, $tareasPorFactura, &$facturasCreadas, &$tareasCreadas) {
            $facturaIds = []; // numfac => id_facturas

            foreach ($tareasPendientes as $tareaCargada) {
                // Crear factura si no existe aun para este numfac
                if (!isset($facturaIds[$tareaCargada->numfac])) {
                    $totalFactura = $tareasPorFactura[$tareaCargada->numfac]->sum('total');

                    $factura = Factura::firstOrCreate(
                        ['numero_factura' => $tareaCargada->numfac],
                        [
                            'cliente_id'      => $cliente->id_clientes,
                            'fecha_factura'   => $tareaCargada->fecha,
                            'total_factura'   => $totalFactura,
                            'saldo_pendiente' => $totalFactura,
                            'estado_pago'     => 'Pendiente',
                        ]
                    );

                    $facturaIds[$tareaCargada->numfac] = $factura->id_facturas;

                    if ($factura->wasRecentlyCreated) {
                        $facturasCreadas++;
                    }
                }

                // Crear tarea si no existe duplicada
                $tareaExistente = Tarea::where('numero_factura', $tareaCargada->numfac)
                    ->whereRaw('TRIM(nombre) = ?', [trim($tareaCargada->nombre)])
                    ->exists();

                if (!$tareaExistente) {
                    $obligacionId = $this->buscarObligacionParaTarea(
                        $cliente, $tareaCargada->codigo, $tareaCargada->fecha
                    );

                    Tarea::create([
                        'id_clientes'    => $cliente->id_clientes,
                        'id_usuario'     => $cliente->id_usuario,
                        'id_factura'     => $facturaIds[$tareaCargada->numfac],
                        'numero_factura' => $tareaCargada->numfac,
                        'fecha_facturada'=> $tareaCargada->fecha,
                        'estado'         => 'Pendiente',
                        'nombre'         => $tareaCargada->nombre,
                        'codigo_servicio'=> $tareaCargada->codigo,
                        'fecha_cumplida' => null,
                        'archivo'        => null,
                        'cantidad'       => $tareaCargada->cant,
                        'precio_unitario'=> $tareaCargada->p_u,
                        'total'          => $tareaCargada->total,
                        'observacion'    => null,
                        'obligacion_id'  => $obligacionId,
                    ]);
                    $tareasCreadas++;
                }
            }

            // Eliminar solo las tareas cargadas de este cliente que fueron procesadas
            TareaCargada::where('cedula', $cliente->cedula_cliente)->delete();
        });

        return ['facturas_creadas' => $facturasCreadas, 'tareas_creadas' => $tareasCreadas];
    }

    /**
     * Procesa tareas cargadas para todos los clientes que tengan coincidencias.
     * Se usa al importar Excel (los clientes que ya existen se procesan de inmediato).
     *
     * @param array $datosProcesados Los datos ya parseados del Excel
     * @return array ['facturas_creadas' => int, 'tareas_creadas' => int]
     */
    public function procesarDesdeDatosImportados(array $datosProcesados): array
    {
        $cedulasUnicas = collect($datosProcesados)->pluck('cedula')->unique();

        $clientes = Cliente::whereIn('cedula_cliente', $cedulasUnicas)->get();

        $totalFacturas = 0;
        $totalTareas = 0;

        foreach ($clientes as $cliente) {
            $resultado = $this->procesarTareasCargadas($cliente);
            $totalFacturas += $resultado['facturas_creadas'];
            $totalTareas += $resultado['tareas_creadas'];
        }

        return ['facturas_creadas' => $totalFacturas, 'tareas_creadas' => $totalTareas];
    }

    /**
     * Busca una obligación que coincida con el servicio facturado usando el catálogo.
     *
     * El período se determina desde tipos_obligacion.mes_vencimiento (configurado en BD),
     * no desde la fecha_facturada. Esto garantiza que facturas tardías encuentren
     * la obligación del mes correcto sin saltar a una futura.
     */
    protected function buscarObligacionParaTarea(Cliente $cliente, string $codigoServicio, ?string $fechaTarea): ?int
    {
        $servicio = \App\Models\CatalogoServicio::where('codigo', trim($codigoServicio))->first();

        if (!$servicio) {
            return null;
        }

        $periodo = $this->calcularPeriodoPorServicio($servicio->id, $fechaTarea);

        $obligacion = Obligacion::where('cliente_id', $cliente->id_clientes)
            ->where('periodo', $periodo)
            ->where(function($query) use ($servicio) {
                $query->where('catalogo_servicio_id', $servicio->id)
                    ->orWhereHas('tipoObligacion', fn($q) => $q->where('catalogo_servicio_id', $servicio->id))
                    ->orWhereHas('clienteServicio', fn($q) => $q->where('catalogo_servicio_id', $servicio->id));
            })
            ->orderBy('fecha_vencimiento', 'asc')
            ->first();

        return $obligacion?->id;
    }

    /**
     * Determina el período de obligación para un catalogo_servicio_id dado.
     * Consulta tipos_obligacion.mes_vencimiento desde la BD.
     * Fallback: mes de la fecha_facturada.
     */
    private function calcularPeriodoPorServicio(int $catalogoId, ?string $fechaTarea): string
    {
        $baseDate = $fechaTarea ? \Carbon\Carbon::parse($fechaTarea) : now();

        $mesCobro = \Illuminate\Support\Facades\DB::table('tipos_obligacion')
            ->where('catalogo_servicio_id', $catalogoId)
            ->whereNotNull('mes_vencimiento')
            ->whereNull('deleted_at')
            ->value('mes_vencimiento');

        if ($mesCobro === null) {
            return $baseDate->format('Y-m');
        }

        // Si el mes de vencimiento ya pasó en el año de la factura, la obligación es del año siguiente
        $anio = $baseDate->year;
        if ($mesCobro < $baseDate->month) {
            $anio++;
        }

        return sprintf('%04d-%02d', $anio, $mesCobro);
    }
}
