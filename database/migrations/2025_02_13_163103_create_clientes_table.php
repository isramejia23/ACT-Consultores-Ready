<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('id_clientes');

            $table->string('nombre_cliente');
            $table->string('cedula_cliente')->unique();
            $table->string('telefono_cliente')->nullable();
            $table->string('regimen');
            $table->string('email_cliente')->unique();
            $table->integer('digito');
            
            $table->enum('estado', ['Activo', 'Inactivo']);
            $table->string('actividad')->nullable();
            
            $table->string('password');
            $table->string('direccion')->nullable();
            $table->string('claves')->nullable();
            
            $table->date('fecha_firma')->nullable();
            $table->date('fecha_facturacion')->nullable();
            $table->decimal('saldo', 10, 2)->nullable();

            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
            $table->rememberToken();

        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
