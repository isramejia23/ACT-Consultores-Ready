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
        Schema::create('cobros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('factura_id'); // Relación con facturas
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->enum('tipo_pago', ['Efectivo', 'Transferencia', 'Cheque', 'Otro'])->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable(); // Quién registró el cobro
            $table->timestamps();

            // Relaciones
            $table->foreign('factura_id')->references('id_facturas')->on('facturas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};
