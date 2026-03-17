<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Regimen;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each client map string regimen to regimen_id
        $clientes = DB::table('clientes')->get();

        foreach ($clientes as $cliente) {
            // Check if string regimen is present
            if (!empty($cliente->regimen)) {
                $regimen = Regimen::where('nombre', $cliente->regimen)->first();
                if ($regimen) {
                    DB::table('clientes')
                        ->where('id_clientes', $cliente->id_clientes)
                        ->update(['regimen_id' => $regimen->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // There is no easy reverse unless we map back. We handle this as a one way data migration.
    }
};
