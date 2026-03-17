<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_obligacion', function (Blueprint $table) {
            // Mes en que se genera la obligación (1-12). Requerido para anual, opcional para semestral.
            $table->unsignedTinyInteger('mes_vencimiento')->nullable()->after('periodicidad');
            // Día fijo de vencimiento del tipo. Si es null, usa regimen.dia_fijo o 9no dígito cédula.
            $table->unsignedTinyInteger('dia_vencimiento')->nullable()->after('mes_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::table('tipos_obligacion', function (Blueprint $table) {
            $table->dropColumn(['mes_vencimiento', 'dia_vencimiento']);
        });
    }
};
