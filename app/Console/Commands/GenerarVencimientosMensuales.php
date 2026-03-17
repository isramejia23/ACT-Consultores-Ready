<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeneradorVencimientos;

class GenerarVencimientosMensuales extends Command
{
    protected $signature = 'vencimientos:generar';
    protected $description = 'Genera los vencimientos para el mes actual';

    public function handle(GeneradorVencimientos $generador)
    {
        $generador->generarVencimientosDelMes();
        $this->info('Vencimientos generados exitosamente para este mes.');
    }
}