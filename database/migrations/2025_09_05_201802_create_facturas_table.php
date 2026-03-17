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
        Schema::create('facturas', function (Blueprint $table) {
            $table->bigIncrements('id_facturas'); // ID autoincremental
            $table->string('numero_factura', 50)->unique();
            $table->date('fecha_factura')->nullable();
            $table->unsignedBigInteger('cliente_id'); // Relación con clientes
            $table->decimal('total_factura', 10, 2)->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0); // Para pagos parciales
            $table->enum('estado_pago', ['Pendiente', 'Parcial', 'Pagado'])->default('Pendiente');
            $table->timestamps();

            // Clave foránea
            $table->foreign('cliente_id')
                  ->references('id_clientes')->on('clientes')
                  ->onDelete('restrict');
        });

        // Agregar columna a tareas para enlazar facturas
        Schema::table('tareas', function (Blueprint $table) {
            if (!Schema::hasColumn('tareas', 'id_factura')) {
                $table->unsignedBigInteger('id_factura')->nullable()->after('id_clientes');
                $table->foreign('id_factura')
                      ->references('id')->on('facturas')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Quitar relación en tareas
        Schema::table('tareas', function (Blueprint $table) {
            if (Schema::hasColumn('tareas', 'id_factura')) {
                $table->dropForeign(['id_factura']);
                $table->dropColumn('id_factura');
            }
        });

        Schema::dropIfExists('facturas');
    }
};
