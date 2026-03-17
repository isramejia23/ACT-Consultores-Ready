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
        Schema::create('catalogo_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // 10000, 10021, etc
            $table->string('nombre');
            $table->string('categoria')->nullable();
            $table->boolean('genera_obligacion')->default(false);
            $table->string('periodicidad')->nullable(); // mensual, semestral, anual
            $table->integer('mes')->nullable(); // 1 al 12 (para asociar a un mes en especifico)
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_servicios');
    }
};
