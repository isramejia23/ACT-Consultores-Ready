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
        Schema::create('vencimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')
                ->constrained('clientes', 'id_clientes')
                ->onDelete('cascade');; // Especifica la columna primaria
            $table->string('cedula_cliente', 13);
            $table->string('nombre_cliente');
            $table->date('fecha_vencimiento');
            $table->string('regimen');
            $table->tinyInteger('digito');
            $table->boolean('completado')->default(false);
            $table->dateTime('generado_en')->useCurrent();
            $table->dateTime('completado_en')->nullable();
            $table->timestamps();
            
            $table->index('fecha_vencimiento');
            $table->index('completado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vencimientos');
    }
};
