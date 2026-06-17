<?php

namespace App\Console\Commands;

use App\Models\CatalogoServicio;
use App\Models\Tarea;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillCodigosServicio extends Command
{
    protected $signature = 'tareas:backfill-codigos
                                {--dry-run : Muestra qué se actualizaría sin escribir en BD}';

    protected $description = 'Rellena codigo_servicio en tareas históricas donde es NULL, buscando por coincidencia de nombre con catalogo_servicios';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Modo DRY-RUN: no se escribirá nada en la base de datos.');
        }

        // Catálogo: clave = nombre normalizado (sin espacios dobles, minúsculas)
        $catalogo = CatalogoServicio::all()
            ->mapWithKeys(fn($s) => [self::normalizar($s->nombre) => $s->codigo]);

        $tareasSinCodigo = Tarea::whereNull('codigo_servicio')->get();

        if ($tareasSinCodigo->isEmpty()) {
            $this->info('No hay tareas con codigo_servicio vacío. Nada que hacer.');
            return self::SUCCESS;
        }

        $this->info("Tareas sin codigo_servicio: {$tareasSinCodigo->count()}");

        $actualizadas  = 0;
        $sinCoincidencia = 0;
        $pendientes    = [];

        foreach ($tareasSinCodigo as $tarea) {
            $nombreNorm = self::normalizar($tarea->nombre);
            $codigo     = $catalogo[$nombreNorm] ?? null;

            if (!$codigo) {
                $sinCoincidencia++;
                $pendientes[] = ['id' => $tarea->id_tareas, 'nombre' => $tarea->nombre];
                continue;
            }

            if (!$dryRun) {
                $tarea->update(['codigo_servicio' => $codigo]);
            } else {
                $this->line("  [dry] tarea #{$tarea->id_tareas} '{$tarea->nombre}' → {$codigo}");
            }

            $actualizadas++;
        }

        $this->info("Actualizadas  : {$actualizadas}");
        $this->info("Sin coincidencia: {$sinCoincidencia}");

        if (!empty($pendientes)) {
            $this->warn('Las siguientes tareas no se pudieron emparejar y requieren revisión manual:');
            $this->table(['id_tarea', 'nombre'], $pendientes);
        }

        if (!$dryRun && $actualizadas > 0) {
            $this->info('Ahora corre: php artisan tareas:vincular-obligaciones {mes} {anio} para los meses afectados.');
        }

        return self::SUCCESS;
    }

    private static function normalizar(string $nombre): string
    {
        // Minúsculas, sin espacios múltiples, sin espacios al inicio/fin
        return strtolower(preg_replace('/\s+/', ' ', trim($nombre)));
    }
}