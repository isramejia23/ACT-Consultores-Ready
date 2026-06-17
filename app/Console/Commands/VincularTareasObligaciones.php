<?php

namespace App\Console\Commands;

use App\Models\CatalogoServicio;
use App\Models\Obligacion;
use App\Models\Tarea;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VincularTareasObligaciones extends Command
{
    protected $signature   = 'tareas:vincular-obligaciones
                                {mes  : Mes de facturación (1-12)}
                                {anio : Año de facturación}';

    protected $description = 'Vincula tareas del período a sus obligaciones existentes (no crea obligaciones)';

    public function handle(): int
    {
        $mes  = (int) $this->argument('mes');
        $anio = (int) $this->argument('anio');

        $this->info("Vinculando tareas del mes {$mes}/{$anio}...");

        // Fuentes reales de qué servicios tienen obligaciones:
        // 1. tipos_obligacion (régimen) y 2. obligaciones manuales por cliente
        $idsDeTipos = DB::table('tipos_obligacion')
            ->whereNotNull('catalogo_servicio_id')
            ->whereNull('deleted_at')
            ->pluck('catalogo_servicio_id')
            ->unique();

        $idsDeObligaciones = DB::table('obligaciones')
            ->whereNotNull('catalogo_servicio_id')
            ->pluck('catalogo_servicio_id')
            ->unique();

        $todosIds = $idsDeTipos->merge($idsDeObligaciones)->unique()->values();

        $codigosConObligacion = CatalogoServicio::whereIn('id', $todosIds)
            ->pluck('codigo')
            ->toArray();

        if (empty($codigosConObligacion)) {
            $this->warn('No hay servicios con tipos_obligacion ni obligaciones manuales en BD.');
            return self::SUCCESS;
        }

        $this->info('Servicios con obligaciones en BD: ' . count($codigosConObligacion));

        $tareas = Tarea::whereYear('fecha_facturada', $anio)
            ->whereMonth('fecha_facturada', $mes)
            ->whereNull('obligacion_id')
            ->whereNotNull('codigo_servicio')
            ->whereIn('codigo_servicio', $codigosConObligacion)
            ->with(['cliente'])
            ->get();

        if ($tareas->isEmpty()) {
            $this->warn('No hay tareas pendientes de vincular para ese período.');
            return self::SUCCESS;
        }

        $this->info("Tareas encontradas sin obligación: {$tareas->count()}");

        $catalogo = CatalogoServicio::whereIn('codigo', $codigosConObligacion)
            ->pluck('id', 'codigo');

        // Mes de cobro esperado por catalogo_servicio_id (desde tipos_obligacion.mes_vencimiento)
        $mesCobro = DB::table('tipos_obligacion')
            ->whereNotNull('catalogo_servicio_id')
            ->whereNotNull('mes_vencimiento')
            ->whereNull('deleted_at')
            ->pluck('mes_vencimiento', 'catalogo_servicio_id')
            ->toArray();

        $vinculadas  = 0;
        $sinObligacion = 0;
        $sinCliente  = 0;

        foreach ($tareas as $tarea) {
            $cliente = $tarea->cliente;

            if (!$cliente) {
                $sinCliente++;
                continue;
            }

            $catalogoId = $catalogo[$tarea->codigo_servicio] ?? null;
            if (!$catalogoId) continue;

            $fechaTarea = Carbon::parse($tarea->fecha_facturada);
            $periodoMes = $mesCobro[$catalogoId] ?? $mes;
            // Si el mes de vencimiento ya pasó en el año de la factura, la obligación es del año siguiente
            $taskYear = $fechaTarea->year;
            if (isset($mesCobro[$catalogoId]) && $periodoMes < $fechaTarea->month) {
                $taskYear++;
            }
            $periodo = sprintf('%04d-%02d', $taskYear, $periodoMes);

            $obligacion = Obligacion::where('cliente_id', $cliente->id_clientes)
                ->where('periodo', $periodo)
                ->where(function($q) use ($catalogoId) {
                    $q->where('catalogo_servicio_id', $catalogoId)
                      ->orWhereHas('tipoObligacion', fn($q2) => $q2->where('catalogo_servicio_id', $catalogoId))
                      ->orWhereHas('clienteServicio', fn($q2) => $q2->where('catalogo_servicio_id', $catalogoId));
                })
                ->orderBy('fecha_vencimiento', 'asc')
                ->first();

            if (!$obligacion) {
                $sinObligacion++;
                $this->warn("  Sin obligacion: tarea #{$tarea->id_tareas} | cliente {$cliente->id_clientes} ({$cliente->nombre_cliente}) | codigo {$tarea->codigo_servicio} | periodo {$periodo}");
                continue;
            }

            $tarea->update(['obligacion_id' => $obligacion->id]);

            if ($tarea->estado === 'Cumplida' && $obligacion->estado === 'pendiente') {
                $obligacion->update([
                    'completado'    => true,
                    'completado_en' => $tarea->fecha_cumplida ?? now(),
                    'estado'        => 'completada',
                ]);
            }

            $vinculadas++;
        }

        $this->info("Tareas vinculadas    : {$vinculadas}");

        if ($sinObligacion > 0) {
            $this->warn("Sin obligación existente: {$sinObligacion} (correr vencimientos:generar primero)");
        }
        if ($sinCliente > 0) {
            $this->warn("Tareas sin cliente válido: {$sinCliente}");
        }

        return self::SUCCESS;
    }
}
