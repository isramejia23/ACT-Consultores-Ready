<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->unsignedBigInteger('vencimiento_id')->nullable()->after('transferida_de');

            $table->foreign('vencimiento_id')
                ->references('id')
                ->on('vencimientos')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropForeign(['vencimiento_id']);
            $table->dropColumn('vencimiento_id');
        });
    }
};