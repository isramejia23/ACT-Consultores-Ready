<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obligaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes', 'id_clientes')->onDelete('cascade');
            
            // Obligacion por regimen (declaracion mensual, dir, etc)
            $table->foreignId('tipo_obligacion_id')->nullable()->constrained('tipos_obligacion')->onDelete('cascade');
            
            // Obligacion por servicio adicional recurrente
            $table->foreignId('cliente_servicio_id')->nullable()->constrained('cliente_servicio')->onDelete('cascade');
            
            $table->date('fecha_vencimiento')->nullable();
            $table->string('periodo'); // 2026-01, 2026-S1, 2026
            $table->boolean('completado')->default(false);
            $table->timestamp('completado_en')->nullable();
            $table->timestamp('generado_en')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligaciones');
    }
};
