<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id('id_tareas');
            $table->unsignedBigInteger('id_clientes');
            $table->string('numero_factura');
            $table->date('fecha_facturada');
            $table->string('nombre');
            $table->enum('estado', ['Cumplida', 'En Proceso', 'Pendiente','Anulada']);
            $table->date('fecha_cumplida')->nullable();
            $table->string('archivo')->nullable();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('total', 10, 2);
            $table->text('observacion')->nullable();
            $table->boolean('notificado')->default(false);
            $table->timestamps();

            $table->foreign('id_clientes')->references('id_clientes')->on('clientes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tareas');
    }
};
