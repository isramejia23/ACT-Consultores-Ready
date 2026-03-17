<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Regimen;
use App\Models\CatalogoServicio;
use App\Models\TipoObligacion;

class DatabaseUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Regimenes
        $regimenesData = [
            ['nombre' => 'Mensual', 'periodicidad' => 'Mensual', 'mes_vencimiento' => null, 'dia_fijo' => null],
            ['nombre' => 'Semestral', 'periodicidad' => 'Semestral', 'mes_vencimiento' => null, 'dia_fijo' => null],
            ['nombre' => 'Negocio Popular', 'periodicidad' => 'Anual', 'mes_vencimiento' => 3, 'dia_fijo' => null],
            ['nombre' => 'Rimpe emprendedor', 'periodicidad' => 'Anual', 'mes_vencimiento' => 3, 'dia_fijo' => null],
            ['nombre' => 'Rimpe', 'periodicidad' => 'Anual', 'mes_vencimiento' => 3, 'dia_fijo' => null],
            ['nombre' => 'Contable', 'periodicidad' => 'Anual', 'mes_vencimiento' => 4, 'dia_fijo' => null],
            ['nombre' => 'Agrícola', 'periodicidad' => 'Anual', 'mes_vencimiento' => 5, 'dia_fijo' => null],
            ['nombre' => 'Sin Regimen', 'periodicidad' => 'Ninguno', 'mes_vencimiento' => null, 'dia_fijo' => null],
        ];

        foreach ($regimenesData as $data) {
            Regimen::updateOrCreate(['nombre' => $data['nombre']], $data);
        }

        // 2. Seed Catalogo Servicios (Example of the 145 items from Excel)
        // Note: For brevity in the seeder, we will just seed the most common ones that link to obligations.
        // It's expected that the user might import the rest from the actual Excel or add them via a UI.
        $serviciosData = [
            ['codigo' => 'ASESORIA', 'nombre' => 'ASESORIA CONTABLE/TRIBUTARIA', 'categoria' => 'Asesoria', 'genera_obligacion' => true, 'periodicidad' => 'Mensual', 'activo' => true],
            ['codigo' => 'DEC_MENSUAL', 'nombre' => 'DECLARACIÓN MENSUAL IVA/RET', 'categoria' => 'Declaracion', 'genera_obligacion' => true, 'periodicidad' => 'Mensual', 'activo' => true],
            ['codigo' => 'DEC_SEMESTRAL', 'nombre' => 'DECLARACIÓN SEMESTRAL IVA/RET', 'categoria' => 'Declaracion', 'genera_obligacion' => true, 'periodicidad' => 'Semestral', 'activo' => true],
            ['codigo' => 'DEC_RTA_NAT', 'nombre' => 'DECLARACIÓN IMPUESTO A LA RENTA - P. NATURALES', 'categoria' => 'Declaracion', 'genera_obligacion' => true, 'periodicidad' => 'Anual', 'mes' => 3, 'activo' => true],
            ['codigo' => 'DEC_RTA_SOC', 'nombre' => 'DECLARACIÓN IMPUESTO A LA RENTA - SOCIEDADES', 'categoria' => 'Declaracion', 'genera_obligacion' => true, 'periodicidad' => 'Anual', 'mes' => 4, 'activo' => true],
            ['codigo' => 'ANEXO_ATS', 'nombre' => 'ANEXO TRANSACCIONAL SIMPLIFICADO (ATS)', 'categoria' => 'Anexos', 'genera_obligacion' => true, 'periodicidad' => 'Mensual', 'activo' => true],
        ];

        foreach ($serviciosData as $data) {
            CatalogoServicio::updateOrCreate(['codigo' => $data['codigo']], $data);
        }

        // 3. Tipos de Obligación por Default
        // We link regimens to standard obligations
        $regMensual = Regimen::where('nombre', 'Mensual')->first();
        $servMensual = CatalogoServicio::where('codigo', 'DEC_MENSUAL')->first();
        if ($regMensual && $servMensual) {
            TipoObligacion::updateOrCreate(
                ['regimen_id' => $regMensual->id, 'catalogo_servicio_id' => $servMensual->id],
                ['nombre' => 'Impuestos Mensuales', 'periodicidad' => 'Mensual']
            );
        }

        $regSemestral = Regimen::where('nombre', 'Semestral')->first();
        $servSemestral = CatalogoServicio::where('codigo', 'DEC_SEMESTRAL')->first();
        if ($regSemestral && $servSemestral) {
            TipoObligacion::updateOrCreate(
                ['regimen_id' => $regSemestral->id, 'catalogo_servicio_id' => $servSemestral->id],
                ['nombre' => 'Impuestos Semestrales', 'periodicidad' => 'Semestral']
            );
        }

        // Tipos anuales
        $regNegocioPopular = Regimen::where('nombre', 'Negocio Popular')->first();
        $servRentaNat = CatalogoServicio::where('codigo', 'DEC_RTA_NAT')->first();
        if ($regNegocioPopular && $servRentaNat) {
            TipoObligacion::updateOrCreate(
                ['regimen_id' => $regNegocioPopular->id, 'catalogo_servicio_id' => $servRentaNat->id],
                ['nombre' => 'Impuesto a la Renta', 'periodicidad' => 'Anual']
            );
        }
    }
}
