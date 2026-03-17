<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Cobro;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-tareas-avanzado');
    }

    public function generarReporte(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $asesorId = $request->query('asesor_id');
        $estado = $request->query('estado');

        $query = Tarea::with('cliente', 'cliente.usuario');

        if ($fechaDesde) {
            $query->where('fecha_facturada', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_facturada', '<=', $fechaHasta);
        }
        if ($estado) {
            $query->where('estado', $estado);
        }
        if ($asesorId) {
            $query->where('id_usuario', $asesorId);
        }

        $tareas = $query->orderBy('fecha_facturada', 'desc')->get();

        $pdf = Pdf::loadView('tareas.reporte_pdf', compact('tareas'));

        return $pdf->stream('reporte_tareas.pdf');
    }

    public function generarReporteAgrupado(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $estado = $request->query('estado');

        $query = Tarea::with(['cliente.usuario', 'usuario']);

        if ($fechaDesde) {
            $query->where('fecha_facturada', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_facturada', '<=', $fechaHasta);
        }
        if ($estado) {
            $query->where('estado', $estado);
        }

        $tareas = $query->orderBy('fecha_facturada', 'desc')->get();

        $tareasAgrupadas = $tareas->groupBy(function ($tarea) {
            if ($tarea->usuario) {
                return $tarea->usuario->nombre . ' ' . $tarea->usuario->apellido;
            }
            return 'Sin Asignar';
        });

        $pdf = Pdf::loadView('tareas.reporte_asesores_pdf', compact('tareasAgrupadas'));

        return $pdf->stream('reporte_tareas_asesores.pdf');
    }

    public function exportarExcelConPagos(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $estado     = $request->query('estado');
        $estadoPago = $request->query('estado_pago');

        $query = Tarea::with(['cliente.usuario', 'usuario', 'factura.cobro']);

        if ($fechaDesde) {
            $query->where('fecha_facturada', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_facturada', '<=', $fechaHasta);
        }
        if ($estado) {
            $query->where('estado', $estado);
        }

        $tareas = $query->orderBy('fecha_facturada', 'desc')->get();

        if ($estadoPago && $estadoPago !== 'Todos') {
            $tareas = $tareas->filter(function ($tarea) use ($estadoPago) {
                $factura = $tarea->factura;
                $montoFacturado = $factura->total_factura ?? $tarea->total ?? 0;
                $pagado = $factura && $factura->cobro ? $factura->cobro->sum('monto') : 0;
                $saldo = $montoFacturado - $pagado;

                if ($saldo <= 0) {
                    $estadoPagoActual = "Cancelada";
                } elseif ($pagado > 0 && $saldo > 0) {
                    $estadoPagoActual = "Con Abono";
                } else {
                    $estadoPagoActual = "Pendiente";
                }

                return $estadoPagoActual === $estadoPago;
            })->values();
        }

        $tareasAgrupadas = $tareas->groupBy(function ($tarea) {
            if ($tarea->usuario) {
                return $tarea->usuario->nombre . ' ' . $tarea->usuario->apellido;
            }
            return 'Sin Asignar';
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        foreach ($tareasAgrupadas as $asesor => $grupoTareas) {
            $sheet->setCellValue("A{$row}", "Asesor: $asesor");
            $row++;

            $sheet->setCellValue("A{$row}", "Fecha Facturada");
            $sheet->setCellValue("B{$row}", "Número de Factura");
            $sheet->setCellValue("C{$row}", "Cliente");
            $sheet->setCellValue("D{$row}", "Estado Pago");
            $sheet->setCellValue("E{$row}", "Monto Facturado");
            $sheet->setCellValue("F{$row}", "Monto Pagado");
            $sheet->setCellValue("G{$row}", "Saldo Pendiente");
            $sheet->setCellValue("H{$row}", "Tareas Incluidas");
            $sheet->setCellValue("I{$row}", "Estados de Tareas");
            $row++;

            $facturasUnicas = $grupoTareas->unique('numero_factura');

            foreach ($facturasUnicas as $tarea) {
                $factura = $tarea->factura;
                $montoFacturado = $factura->total_factura ?? $tarea->total ?? 0;
                $pagado = $factura && $factura->cobro ? $factura->cobro->sum('monto') : 0;
                $saldo = $montoFacturado - $pagado;

                if ($saldo <= 0) {
                    $estadoPagoActual = "Cancelada";
                } elseif ($pagado > 0 && $saldo > 0) {
                    $estadoPagoActual = "Con Abono";
                } else {
                    $estadoPagoActual = "Pendiente";
                }

                $tareasFactura = $factura ? $factura->tarea : collect([$tarea]);
                $nombresTareas = $tareasFactura->pluck('nombre')->implode(', ');
                $estadosTareas = $tareasFactura->pluck('estado')->implode(', ');

                $sheet->setCellValue("A{$row}", $tarea->fecha_facturada);
                $sheet->setCellValue("B{$row}", $tarea->numero_factura);
                $sheet->setCellValue("C{$row}", $tarea->cliente->nombre_cliente ?? 'Sin Cliente');
                $sheet->setCellValue("D{$row}", $estadoPagoActual);
                $sheet->setCellValue("E{$row}", number_format($montoFacturado, 2));
                $sheet->setCellValue("F{$row}", number_format($pagado, 2));
                $sheet->setCellValue("G{$row}", number_format($saldo, 2));
                $sheet->setCellValue("H{$row}", $nombresTareas);
                $sheet->setCellValue("I{$row}", $estadosTareas);
                $row++;
            }

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'reporte_tareas_con_pagos.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportarExcelAgrupado(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $estado = $request->query('estado');

        $query = Tarea::with(['cliente.usuario', 'usuario']);

        if ($fechaDesde) {
            $query->where('fecha_facturada', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_facturada', '<=', $fechaHasta);
        }
        if ($estado) {
            $query->where('estado', $estado);
        }

        $tareas = $query->orderBy('fecha_facturada', 'desc')->get();

        $tareasAgrupadas = $tareas->groupBy(function ($tarea) {
            if ($tarea->usuario) {
                return $tarea->usuario->nombre . ' ' . $tarea->usuario->apellido;
            }
            return 'Sin Asignar';
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        foreach ($tareasAgrupadas as $asesor => $grupoTareas) {
            $sheet->setCellValue("A{$row}", "Asesor: $asesor");
            $row++;

            $sheet->setCellValue("A{$row}", "Fecha Facturada");
            $sheet->setCellValue("B{$row}", "Numero de factura");
            $sheet->setCellValue("C{$row}", "Trabajo");
            $sheet->setCellValue("D{$row}", "Cliente");
            $sheet->setCellValue("E{$row}", "Estado");
            $sheet->setCellValue("F{$row}", "Precio Unitario");
            $sheet->setCellValue("G{$row}", "Cantidad");
            $sheet->setCellValue("H{$row}", "total");
            $row++;

            foreach ($grupoTareas as $tarea) {
                $sheet->setCellValue("A{$row}", $tarea->fecha_facturada);
                $sheet->setCellValue("B{$row}", $tarea->numero_factura);
                $sheet->setCellValue("C{$row}", $tarea->nombre);
                $sheet->setCellValue("D{$row}", $tarea->cliente->nombre_cliente);
                $sheet->setCellValue("E{$row}", $tarea->estado);
                $sheet->setCellValue("F{$row}", $tarea->precio_unitario);
                $sheet->setCellValue("G{$row}", $tarea->cantidad);
                $sheet->setCellValue("H{$row}", $tarea->total);
                $row++;
            }

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'reporte_tareas_asesores.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportarExcelPorFechaCumplida(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $estado     = $request->query('estado');
        $estadoPago = $request->query('estado_pago');

        $query = Tarea::with(['cliente.usuario', 'usuario', 'factura.cobro'])
            ->whereNotNull('fecha_cumplida');

        if ($fechaDesde) {
            $query->where('fecha_cumplida', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_cumplida', '<=', $fechaHasta);
        }
        if ($estado) {
            $query->where('estado', $estado);
        }

        $tareas = $query->orderBy('fecha_cumplida', 'desc')->get();

        if ($estadoPago && $estadoPago !== 'Todos') {
            $tareas = $tareas->filter(function ($tarea) use ($estadoPago) {
                $factura = $tarea->factura;
                $montoFacturado = $factura->total_factura ?? $tarea->total ?? 0;
                $pagado = $factura && $factura->cobro ? $factura->cobro->sum('monto') : 0;
                $saldo = $montoFacturado - $pagado;

                if ($saldo <= 0) {
                    $estadoPagoActual = "Cancelada";
                } elseif ($pagado > 0 && $saldo > 0) {
                    $estadoPagoActual = "Con Abono";
                } else {
                    $estadoPagoActual = "Pendiente";
                }

                return $estadoPagoActual === $estadoPago;
            })->values();
        }

        $tareasAgrupadas = $tareas->groupBy(function ($tarea) {
            if ($tarea->usuario) {
                return $tarea->usuario->nombre . ' ' . $tarea->usuario->apellido;
            }
            return 'Sin Asignar';
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        foreach ($tareasAgrupadas as $asesor => $grupoTareas) {
            $sheet->setCellValue("A{$row}", "Asesor: $asesor");
            $row++;

            $sheet->setCellValue("A{$row}", "Fecha Cumplida");
            $sheet->setCellValue("B{$row}", "Fecha Facturada");
            $sheet->setCellValue("C{$row}", "Número de Factura");
            $sheet->setCellValue("D{$row}", "Cliente");
            $sheet->setCellValue("E{$row}", "Estado Pago");
            $sheet->setCellValue("F{$row}", "Monto Facturado");
            $sheet->setCellValue("G{$row}", "Monto Pagado");
            $sheet->setCellValue("H{$row}", "Saldo Pendiente");
            $sheet->setCellValue("I{$row}", "Tareas Incluidas");
            $sheet->setCellValue("J{$row}", "Estados de Tareas");
            $row++;

            $facturasUnicas = $grupoTareas->unique('numero_factura');

            foreach ($facturasUnicas as $tarea) {
                $factura = $tarea->factura;
                $montoFacturado = $factura->total_factura ?? $tarea->total ?? 0;
                $pagado = $factura && $factura->cobro ? $factura->cobro->sum('monto') : 0;
                $saldo = $montoFacturado - $pagado;

                if ($saldo <= 0) {
                    $estadoPagoActual = "Cancelada";
                } elseif ($pagado > 0 && $saldo > 0) {
                    $estadoPagoActual = "Con Abono";
                } else {
                    $estadoPagoActual = "Pendiente";
                }

                $tareasFactura = $factura ? $factura->tarea : collect([$tarea]);
                $nombresTareas = $tareasFactura->pluck('nombre')->implode(', ');
                $estadosTareas = $tareasFactura->pluck('estado')->implode(', ');

                $sheet->setCellValue("A{$row}", $tarea->fecha_cumplida);
                $sheet->setCellValue("B{$row}", $tarea->fecha_facturada);
                $sheet->setCellValue("C{$row}", $tarea->numero_factura);
                $sheet->setCellValue("D{$row}", $tarea->cliente->nombre_cliente ?? 'Sin Cliente');
                $sheet->setCellValue("E{$row}", $estadoPagoActual);
                $sheet->setCellValue("F{$row}", number_format($montoFacturado, 2));
                $sheet->setCellValue("G{$row}", number_format($pagado, 2));
                $sheet->setCellValue("H{$row}", number_format($saldo, 2));
                $sheet->setCellValue("I{$row}", $nombresTareas);
                $sheet->setCellValue("J{$row}", $estadosTareas);
                $row++;
            }

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'reporte_tareas_por_fecha_cumplida.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportarExcelPorFechaCobro(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $estado     = $request->query('estado');
        $estadoPago = $request->query('estado_pago');

        $query = Cobro::with([
            'factura.tarea.cliente.usuario',
            'factura.tarea.usuario',
        ]);

        if ($fechaDesde) {
            $query->where('fecha_pago', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_pago', '<=', $fechaHasta);
        }

        $cobros = $query->orderBy('fecha_pago', 'desc')->get();

        $cobros = $cobros->filter(function ($cobro) use ($estado, $estadoPago) {
            $factura = $cobro->factura;
            if (!$factura) return false;

            $tareas = $factura->tarea;
            if ($tareas->isEmpty()) return false;

            if ($estado) {
                $tareas = $tareas->where('estado', $estado);
                if ($tareas->isEmpty()) return false;
            }

            if ($estadoPago && $estadoPago !== 'Todos') {
                $montoFacturado = $factura->total_factura ?? 0;
                $pagado = $factura->cobro->sum('monto');
                $saldo = $montoFacturado - $pagado;

                if ($saldo <= 0) $estadoPagoActual = "Cancelada";
                elseif ($pagado > 0 && $saldo > 0) $estadoPagoActual = "Con Abono";
                else $estadoPagoActual = "Pendiente";

                return $estadoPagoActual === $estadoPago;
            }

            return true;
        })->values();

        $cobrosAgrupados = $cobros->groupBy(function ($cobro) {
            $factura = $cobro->factura;
            $tarea = $factura->tarea->first();
            if ($tarea && $tarea->usuario) {
                return $tarea->usuario->nombre . ' ' . $tarea->usuario->apellido;
            }
            return 'Sin Asignar';
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        foreach ($cobrosAgrupados as $asesor => $grupoCobros) {
            $sheet->setCellValue("A{$row}", "Asesor: $asesor");
            $row++;

            $sheet->setCellValue("A{$row}", "Fecha Cobro");
            $sheet->setCellValue("B{$row}", "Fecha Facturada");
            $sheet->setCellValue("C{$row}", "Fecha Cumplida");
            $sheet->setCellValue("D{$row}", "Número Factura");
            $sheet->setCellValue("E{$row}", "Cliente");
            $sheet->setCellValue("F{$row}", "Estado Pago");
            $sheet->setCellValue("G{$row}", "Monto Facturado");
            $sheet->setCellValue("H{$row}", "Monto Pagado");
            $sheet->setCellValue("I{$row}", "Saldo Pendiente");
            $sheet->setCellValue("J{$row}", "Tareas Incluidas");
            $sheet->setCellValue("K{$row}", "Estados de Tareas");
            $row++;

            foreach ($grupoCobros as $cobro) {
                $factura = $cobro->factura;
                if (!$factura) continue;

                $montoFacturado = $factura->total_factura ?? 0;
                $pagado = $factura->cobro->sum('monto');
                $saldo = $montoFacturado - $pagado;

                if ($saldo <= 0) $estadoPagoActual = "Cancelada";
                elseif ($pagado > 0 && $saldo > 0) $estadoPagoActual = "Con Abono";
                else $estadoPagoActual = "Pendiente";

                $tareasFactura = $factura->tarea;
                $nombresTareas = $tareasFactura->pluck('nombre')->implode(', ');
                $estadosTareas = $tareasFactura->pluck('estado')->implode(', ');
                $fechaCumplida = $tareasFactura->pluck('fecha_cumplida')->filter()->first();

                $sheet->setCellValue("A{$row}", $cobro->fecha_pago);
                $sheet->setCellValue("B{$row}", $factura->fecha_factura);
                $sheet->setCellValue("C{$row}", $fechaCumplida ?? '');
                $sheet->setCellValue("D{$row}", $factura->numero_factura);
                $sheet->setCellValue("E{$row}", $factura->cliente->nombre_cliente ?? 'Sin Cliente');
                $sheet->setCellValue("F{$row}", $estadoPagoActual);
                $sheet->setCellValue("G{$row}", number_format($montoFacturado, 2));
                $sheet->setCellValue("H{$row}", number_format($pagado, 2));
                $sheet->setCellValue("I{$row}", number_format($saldo, 2));
                $sheet->setCellValue("J{$row}", $nombresTareas);
                $sheet->setCellValue("K{$row}", $estadosTareas);
                $row++;
            }

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'reporte_tareas_por_fecha_cobro.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportarExcelCobrosPorAsesor(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');

        $query = Cobro::with([
            'factura.tarea',
            'factura.cliente',
            'usuario',
        ]);

        if ($fechaDesde) {
            $query->where('fecha_pago', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->where('fecha_pago', '<=', $fechaHasta);
        }

        $cobros = $query->orderBy('fecha_pago', 'desc')->get();

        $cobros = $cobros->filter(function ($cobro) {
            return $cobro->factura !== null;
        });

        // Agrupar por el usuario que registro el cobro
        $cobrosAgrupados = $cobros->groupBy(function ($cobro) {
            if ($cobro->usuario) {
                return $cobro->usuario->nombre . ' ' . $cobro->usuario->apellido;
            }
            return 'Sin Asignar';
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        foreach ($cobrosAgrupados as $asesor => $grupoCobros) {
            $sheet->setCellValue("A{$row}", "Asesor: $asesor");
            $row++;

            $sheet->setCellValue("A{$row}", "Fecha Cobro");
            $sheet->setCellValue("B{$row}", "N. Factura");
            $sheet->setCellValue("C{$row}", "Cliente");
            $sheet->setCellValue("D{$row}", "Actividades");
            $sheet->setCellValue("E{$row}", "Monto Cobrado");
            $sheet->setCellValue("F{$row}", "Tipo Pago");
            $row++;

            $totalAsesor = 0;

            foreach ($grupoCobros as $cobro) {
                $factura = $cobro->factura;
                $nombresTareas = $factura->tarea->pluck('nombre')->implode(', ');

                $sheet->setCellValue("A{$row}", $cobro->fecha_pago);
                $sheet->setCellValue("B{$row}", $factura->numero_factura);
                $sheet->setCellValue("C{$row}", $factura->cliente->nombre_cliente ?? 'Sin Cliente');
                $sheet->setCellValue("D{$row}", $nombresTareas);
                $sheet->setCellValue("E{$row}", number_format($cobro->monto, 2));
                $sheet->setCellValue("F{$row}", $cobro->tipo_pago);
                $row++;

                $totalAsesor += $cobro->monto;
            }

            $sheet->setCellValue("D{$row}", "Total $asesor:");
            $sheet->setCellValue("E{$row}", number_format($totalAsesor, 2));
            $sheet->getStyle("D{$row}:E{$row}")->getFont()->setBold(true);
            $row += 2;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'reporte_cobros_por_asesor.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}