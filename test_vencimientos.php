<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$vencimientos = \App\Models\Vencimiento::all();

echo "Total vencimientos: " . $vencimientos->count() . "\n\n";

foreach ($vencimientos as $v) {
    echo "ID: {$v->id}\n";
    echo "Cliente: {$v->nombre_cliente}\n";
    echo "Fecha: {$v->fecha_vencimiento}\n";
    echo "Mes: " . \Carbon\Carbon::parse($v->fecha_vencimiento)->month . "\n";
    echo "Año: " . \Carbon\Carbon::parse($v->fecha_vencimiento)->year . "\n";
    echo "Notificado: " . ($v->notificado ? 'Sí' : 'No') . "\n";
    echo "Completado: " . ($v->completado ? 'Sí' : 'No') . "\n";
    echo "---\n";
}

$mesActual = now()->month;
$anioActual = now()->year;

echo "\nMes actual: {$mesActual}\n";
echo "Año actual: {$anioActual}\n\n";

$vencimientosDelMes = \App\Models\Vencimiento::whereMonth('fecha_vencimiento', $mesActual)
    ->whereYear('fecha_vencimiento', $anioActual)
    ->get();

echo "Vencimientos del mes actual: " . $vencimientosDelMes->count() . "\n";
