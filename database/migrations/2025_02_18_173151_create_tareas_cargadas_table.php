<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tareas_cargadas', function (Blueprint $table) {
            $table->id();
            $table->string('org');
            $table->string('numfac');
            $table->date('fecha');
            $table->string('bo')->nullable();
            $table->string('seccion')->nullable();
            $table->string('codigo');
            $table->string('nombre');
            $table->integer('cant');
            $table->decimal('p_u', 10, 2);
            $table->decimal('dscto', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->string('codcli');
            $table->string('cedula');
            $table->string('nombre_cliente'); // Cambio para evitar conflicto con 'nombre'
            $table->string('direccion')->nullable();
            $table->string('estado');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tareas_cargadas');
    }
};
