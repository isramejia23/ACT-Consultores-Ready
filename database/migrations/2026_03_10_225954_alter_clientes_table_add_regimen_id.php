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
            $table->foreignId('regimen_id')->nullable()->constrained('regimenes')->onDelete('set null');
            // Drop enum o string column regimen if it exists (but make sure to save data first in a real scenario array, 
            // since this is a new deploy we will just add the column and later drop the old column).
            // For now we add regimen_id.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['regimen_id']);
            $table->dropColumn('regimen_id');
        });
    }
};
