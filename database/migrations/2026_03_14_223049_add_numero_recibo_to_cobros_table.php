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
        Schema::table('cobros', function (Blueprint $table) {
            $table->string('numero_recibo')->nullable()->after('tipo_pago');
            $table->index(['numero_recibo', 'factura_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cobros', function (Blueprint $table) {
            $table->dropIndex(['numero_recibo', 'factura_id']);
            $table->dropColumn('numero_recibo');
        });
    }
};
