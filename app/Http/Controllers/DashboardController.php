<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tarea;
use App\Models\Factura;
use App\Models\Cobro;
use App\Models\Cliente;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-dashboard', ['only' => ['index']]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $usuario_id = $request->query('usuario_id');
        $year = $request->query('year', date('Y'));
        $month = $request->query('month');

        // Función para aplicar filtro de usuario a consultas de Tarea
        $applyUserFilter = function ($query) use ($usuario_id) {
            if ($usuario_id) {
                $query->whereHas('cliente', function ($q) use ($usuario_id) {
                    $q->where('id_usuario', $usuario_id);
                });
            }
            return $query;
        };

        // Función para aplicar filtro de usuario a consultas de Factura
        $applyUserFilterFactura = function ($query) use ($usuario_id) {
            if ($usuario_id) {
                $query->whereHas('cliente', function ($q) use ($usuario_id) {
                    $q->where('id_usuario', $usuario_id);
                });
            }
            return $query;
        };

        // =============================================
        // DATOS EXISTENTES (tareas y gráfico de ingresos)
        // =============================================
        $query = Tarea::whereYear('fecha_facturada', $year);
        $query = $applyUserFilter($query);

        $datos = $query->clone()
            ->selectRaw('MONTH(fecha_facturada) as mes, SUM(total) as ingresos')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $tiempoPromedio = $query->clone()
            ->whereNotNull('fecha_cumplida')
            ->selectRaw('AVG(DATEDIFF(fecha_cumplida, fecha_facturada)) as dias_promedio')
            ->value('dias_promedio');

        $getTasksQuery = function ($estado) use ($year, $month, $applyUserFilter) {
            $query = Tarea::where('estado', $estado);
            if ($month) {
                if ($estado === 'Cumplida') {
                    $query->whereYear('fecha_cumplida', $year)->whereMonth('fecha_cumplida', $month);
                } else {
                    $query->whereYear('fecha_facturada', $year)->whereMonth('fecha_facturada', $month);
                }
            } else {
                if ($estado === 'Cumplida') {
                    $query->whereYear('fecha_cumplida', $year);
                } else {
                    $query->whereYear('fecha_facturada', $year);
                }
            }
            return $applyUserFilter($query);
        };

        $tareasPendientes = $getTasksQuery('Pendiente')->count();
        $tareasCumplidas = $getTasksQuery('Cumplida')->count();
        $tareasEnProceso = $getTasksQuery('En Proceso')->count();

        // =============================================
        // KPIs - Tarjetas principales
        // =============================================

        // Total facturado del periodo
        $queryFacturas = Factura::whereYear('fecha_factura', $year);
        if ($month) {
            $queryFacturas->whereMonth('fecha_factura', $month);
        }
        $queryFacturas = $applyUserFilterFactura($queryFacturas);
        $totalFacturado = $queryFacturas->sum('total_factura');

        // Total cobrado del periodo
        $queryCobros = Cobro::whereYear('fecha_pago', $year);
        if ($month) {
            $queryCobros->whereMonth('fecha_pago', $month);
        }
        if ($usuario_id) {
            $queryCobros->whereHas('factura.cliente', function ($q) use ($usuario_id) {
                $q->where('id_usuario', $usuario_id);
            });
        }
        $totalCobrado = $queryCobros->sum('monto');

        // Saldo pendiente global (facturas del periodo con saldo > 0)
        $saldoPendiente = (clone $queryFacturas)->where('saldo_pendiente', '>', 0)->sum('saldo_pendiente');

        // Clientes activos
        $queryClientes = Cliente::where('estado', 'Activo');
        if ($usuario_id) {
            $queryClientes->where('id_usuario', $usuario_id);
        }
        $clientesActivos = $queryClientes->count();

        // =============================================
        // Ingresos vs Cobros por mes (gráfico agrupado)
        // =============================================

        // Facturado por mes
        $facturadoPorMes = Factura::whereYear('fecha_factura', $year);
        $facturadoPorMes = $applyUserFilterFactura($facturadoPorMes);
        $facturadoPorMes = $facturadoPorMes
            ->selectRaw('MONTH(fecha_factura) as mes, SUM(total_factura) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Cobrado por mes
        $cobradoPorMes = Cobro::whereYear('fecha_pago', $year);
        if ($usuario_id) {
            $cobradoPorMes->whereHas('factura.cliente', function ($q) use ($usuario_id) {
                $q->where('id_usuario', $usuario_id);
            });
        }
        $cobradoPorMes = $cobradoPorMes
            ->selectRaw('MONTH(fecha_pago) as mes, SUM(monto) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Construir arrays de 12 meses para ambos datasets
        $facturadoMensual = [];
        $cobradoMensual = [];
        for ($i = 1; $i <= 12; $i++) {
            $facturadoMensual[] = round($facturadoPorMes->get($i, 0), 2);
            $cobradoMensual[] = round($cobradoPorMes->get($i, 0), 2);
        }

        // =============================================
        // Rendimiento por asesor/usuario
        // =============================================
        $rendimientoAsesores = User::select('users.id', 'users.nombre', 'users.apellido')
            ->withCount(['clientes as clientes_count' => function ($q) {
                $q->where('estado', 'Activo');
            }])
            ->get()
            ->map(function ($usuario) use ($year, $month) {
                // Tareas cumplidas del asesor
                $tareasCumplidasQuery = Tarea::where('estado', 'Cumplida')
                    ->whereHas('cliente', function ($q) use ($usuario) {
                        $q->where('id_usuario', $usuario->id);
                    })
                    ->whereYear('fecha_cumplida', $year);
                if ($month) {
                    $tareasCumplidasQuery->whereMonth('fecha_cumplida', $month);
                }
                $usuario->tareas_cumplidas = $tareasCumplidasQuery->count();

                // Ingresos generados (total facturado de sus clientes)
                $ingresosQuery = Factura::whereYear('fecha_factura', $year)
                    ->whereHas('cliente', function ($q) use ($usuario) {
                        $q->where('id_usuario', $usuario->id);
                    });
                if ($month) {
                    $ingresosQuery->whereMonth('fecha_factura', $month);
                }
                $usuario->ingresos_generados = round($ingresosQuery->sum('total_factura'), 2);

                return $usuario;
            })
            ->filter(function ($usuario) {
                return $usuario->tareas_cumplidas > 0 || $usuario->ingresos_generados > 0 || $usuario->clientes_count > 0;
            })
            ->sortByDesc('ingresos_generados')
            ->values();

        // Traducción de meses al español
        $mesesEspañol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $usuarios = User::all();

        return view('dashboard', compact(
            'datos', 'tiempoPromedio', 'usuarios', 'year', 'usuario_id',
            'tareasPendientes', 'tareasCumplidas', 'tareasEnProceso', 'month', 'mesesEspañol',
            'totalFacturado', 'totalCobrado', 'saldoPendiente', 'clientesActivos',
            'facturadoMensual', 'cobradoMensual', 'rendimientoAsesores'
        ));
    }
}
