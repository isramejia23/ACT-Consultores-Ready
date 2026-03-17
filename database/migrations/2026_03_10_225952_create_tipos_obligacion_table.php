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
        Schema::create('tipos_obligacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('regimen_id')->constrained('regimenes')->onDelete('cascade');
            $table->string('periodicidad'); // mensual, semestral, anual
            
            // Relacion opcional con un catalogo de servicios especifico para auto completarlo
            $table->foreignId('catalogo_servicio_id')->nullable()->constrained('catalogo_servicios')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_obligacion');
    }
};
