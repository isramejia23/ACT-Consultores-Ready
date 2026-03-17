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
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('catalogo_servicio_id')->nullable()->after('tipo_obligacion_id');
            $table->foreign('catalogo_servicio_id')->references('id')->on('catalogo_servicios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->dropForeign(['catalogo_servicio_id']);
            $table->dropColumn('catalogo_servicio_id');
        });
    }
};
