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
            $table->string('periodicidad')->nullable()->after('catalogo_servicio_id');
            $table->unsignedTinyInteger('dia_vencimiento')->nullable()->after('periodicidad');
        });
    }

    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->dropColumn(['periodicidad', 'dia_vencimiento']);
        });
    }
};
