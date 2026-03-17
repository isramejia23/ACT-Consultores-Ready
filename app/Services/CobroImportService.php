<?php

namespace App\Services;

use App\Models\Cobro;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CobroImportService
{
    const TIPO_PAGO_MAP = [
        'CA' => 'Efectivo',
        'AB' => 'Transferencia',
    ];

    public function importarDesdeExcel(string $rutaArchivo, int $usuarioId): array
    {
        $reader = IOFactory::createReader('Xls');
        $spreadsheet = @$reader->load($rutaArchivo);
        $sheet = $spreadsheet->getActiveSheet();

        $resultados = [
            'cobros_creados'       => 0,
            'duplicados_omitidos'  => 0,
            'facturas_no_encontradas' => [],
            'montos_excedidos'     => [],
            'filas_vacias'         => 0,
        ];

        // Precargar recibos existentes para detección de duplicados
        $recibosExistentes = Cobro::whereNotNull('numero_recibo')
            ->select('numero_recibo', 'factura_id')
            ->get()
            ->groupBy('numero_recibo')
            ->map(fn($group) => $group->pluck('factura_id')->toArray());

        $cobrosParaCrear = [];

        for ($row = 10; $row <= $sheet->getHighestRow(); $row++) {
            $numeroDocumento = trim($sheet->getCell('M' . $row)->getValue() ?? '');
            $numeroRecibo    = trim($sheet->getCell('Q' . $row)->getValue() ?? '');
            $tipoPagoRaw     = strtoupper(trim($sheet->getCell('J' . $row)->getValue() ?? ''));
            $monto           = (float) ($sheet->getCell('Y' . $row)->getValue() ?? 0);

            // Fecha emisión (col T) - es serial de Excel
            $fechaRaw = $sheet->getCell('T' . $row)->getValue();

            if (empty($numeroDocumento) || empty($numeroRecibo) || $monto <= 0) {
                $resultados['filas_vacias']++;
                continue;
            }

            // Convertir fecha
            $fechaPago = null;
            if (is_numeric($fechaRaw) && $fechaRaw > 40000) {
                $fechaPago = ExcelDate::excelToDateTimeObject($fechaRaw)->format('Y-m-d');
            }

            if (!$fechaPago) {
                $fechaPago = now()->format('Y-m-d');
            }

            // Verificar duplicado: mismo recibo + mismo documento de factura
            if (isset($recibosExistentes[$numeroRecibo])) {
                $facturaDup = Factura::where('numero_factura', $numeroDocumento)->first();
                if ($facturaDup && in_array($facturaDup->id_facturas, $recibosExistentes[$numeroRecibo])) {
                    $resultados['duplicados_omitidos']++;
                    continue;
                }
            }

            // Mapear tipo de pago
            $tipoPago = self::TIPO_PAGO_MAP[$tipoPagoRaw] ?? 'Otro';

            $cobrosParaCrear[] = [
                'numero_documento' => $numeroDocumento,
                'numero_recibo'    => $numeroRecibo,
                'monto'            => $monto,
                'fecha_pago'       => $fechaPago,
                'tipo_pago'        => $tipoPago,
                'row'              => $row,
            ];
        }

        // Procesar cobros dentro de una transacción
        DB::transaction(function () use ($cobrosParaCrear, $usuarioId, &$resultados) {
            foreach ($cobrosParaCrear as $data) {
                $factura = Factura::where('numero_factura', $data['numero_documento'])->first();

                if (!$factura) {
                    $resultados['facturas_no_encontradas'][] = $data['numero_documento'];
                    continue;
                }

                // Verificar que el monto no exceda el saldo
                if ($data['monto'] > $factura->saldo_pendiente) {
                    $resultados['montos_excedidos'][] = [
                        'factura'  => $data['numero_documento'],
                        'recibo'   => $data['numero_recibo'],
                        'monto'    => $data['monto'],
                        'saldo'    => $factura->saldo_pendiente,
                    ];
                    continue;
                }

                // Crear cobro
                Cobro::create([
                    'factura_id'     => $factura->id_facturas,
                    'monto'          => $data['monto'],
                    'fecha_pago'     => $data['fecha_pago'],
                    'tipo_pago'      => $data['tipo_pago'],
                    'numero_recibo'  => $data['numero_recibo'],
                    'usuario_id'     => $usuarioId,
                ]);

                // Actualizar saldo de la factura
                $factura->saldo_pendiente -= $data['monto'];

                if ($factura->saldo_pendiente <= 0) {
                    $factura->saldo_pendiente = 0;
                    $factura->estado_pago = 'Pagado';
                } elseif ($factura->saldo_pendiente < $factura->total_factura) {
                    $factura->estado_pago = 'Parcial';
                }

                $factura->save();
                $resultados['cobros_creados']++;
            }
        });

        return $resultados;
    }
}