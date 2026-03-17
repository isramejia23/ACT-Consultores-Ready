<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerarVencimientosMensuales::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('vencimientos:generar')
        ->monthly(); // Ejecuta el comando el primer día del mes a las 00:00
    }
    


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}