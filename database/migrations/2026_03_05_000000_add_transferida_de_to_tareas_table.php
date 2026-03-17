<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Agregar columna transferida_de
        Schema::table('tareas', function (Blueprint $table) {
            $table->unsignedBigInteger('transferida_de')->nullable()->after('id_usuario');
        });

        // 2. Poblar id_usuario nulos con el id_usuario del cliente (dueño original)
        DB::statement('
            UPDATE tareas
            INNER JOIN clientes ON tareas.id_clientes = clientes.id_clientes
            SET tareas.id_usuario = clientes.id_usuario
            WHERE tareas.id_usuario IS NULL
              AND clientes.id_usuario IS NOT NULL
        ');

        // 3. Las tareas que ya tenian id_usuario fueron transferidas,
        //    marcar transferida_de con el id_usuario del cliente (asesor original)
        DB::statement('
            UPDATE tareas
            INNER JOIN clientes ON tareas.id_clientes = clientes.id_clientes
            SET tareas.transferida_de = clientes.id_usuario
            WHERE tareas.id_usuario IS NOT NULL
              AND tareas.id_usuario != clientes.id_usuario
              AND clientes.id_usuario IS NOT NULL
        ');
    }

    public function down()
    {
        // Restaurar id_usuario a NULL donde fue transferida (revertir a lógica vieja)
        DB::statement('
            UPDATE tareas
            SET id_usuario = NULL
            WHERE transferida_de IS NOT NULL
        ');

        // Restaurar tareas no transferidas a NULL (lógica vieja: null = dueño del cliente)
        DB::statement('
            UPDATE tareas
            INNER JOIN clientes ON tareas.id_clientes = clientes.id_clientes
            SET tareas.id_usuario = NULL
            WHERE tareas.id_usuario = clientes.id_usuario
              AND tareas.transferida_de IS NULL
        ');

        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('transferida_de');
        });
    }
};