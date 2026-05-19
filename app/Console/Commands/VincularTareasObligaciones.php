<?php

namespace App\Console\Commands;

use App\Models\CatalogoServicio;
use App\Models\Obligacion;
use App\Models\Tarea;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VincularTareasObligaciones extends Command
{
    protected $signature   = 'tareas:vincular-obligaciones
                                {mes  : Mes de facturación (1-12)}
                                {anio : Año de facturación}';

    protected $description = 'Vincula tareas del período a sus obligaciones existentes (no crea obligaciones)';

    private const CODIGOS_CON_OBLIGACION = [
        '10001','10002','10003','10004','10005','10006',
        '10007','10008','10009','10010','10011','10012',
        '10021','10022','10023','10024','10025','10026',
        '10027','10028','10029','10030','10031','10032',
        '10017','10033','10034','10036','10054',
        '50002','10056','10057','10055','10060',
    ];

    public function handle(): int
    {
        $mes  = (int) $this->argument('mes');
        $anio = (int) $this->argument('anio');

        $this->info("Vinculando tareas del mes {$mes}/{$anio}...");

        $tareas = Tarea::whereYear('fecha_facturada', $anio)
            ->whereMonth('fecha_facturada', $mes)
            ->whereNull('obligacion_id')
            ->whereNotNull('codigo_servicio')
            ->whereIn('codigo_servicio', self::CODIGOS_CON_OBLIGACION)
            ->with(['cliente'])
            ->get();

        if ($tareas->isEmpty()) {
            $this->warn('No hay tareas pendientes de vincular para ese período.');
            return self::SUCCESS;
        }

        $this->info("Tareas encontradas sin obligación: {$tareas->count()}");

        $catalogo = CatalogoServicio::whereIn('codigo', self::CODIGOS_CON_OBLIGACION)
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

            $taskYear   = Carbon::parse($tarea->fecha_facturada)->year;
            $periodoMes = $mesCobro[$catalogoId] ?? $mes;
            $periodo    = sprintf('%04d-%02d', $taskYear, $periodoMes);

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
