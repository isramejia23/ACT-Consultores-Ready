<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente')->after('completado_en');
        });

        // Migrar datos existentes: completado=true -> 'completada', else 'pendiente'
        DB::table('obligaciones')->where('completado', true)->update(['estado' => 'completada']);
    }

    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};