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
        Schema::create('regimenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('periodicidad')->default('mensual'); // mensual, semestral, anual
            $table->integer('mes_vencimiento')->nullable(); // Ej: 3 para Marzo, etc.
            $table->integer('dia_fijo')->nullable(); // Ej: 28 u otro día. Null asume 9no dígito celular/ruc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regimenes');
    }
};
