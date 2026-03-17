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
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('regimen');
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->dropForeign(['vencimiento_id']);
            $table->dropColumn('vencimiento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('regimen')->nullable();
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->foreignId('vencimiento_id')->nullable()->constrained('vencimientos')->onDelete('set null');
        });
    }
};
