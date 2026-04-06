<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Tarea;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Obligacion;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    /**
     * Muestra la página de inicio (dashboard).
     */
    public function index(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/');
        }
    
        $user = Auth::user();
        $currentMonth = $request->query('month', Carbon::now()->month);
        $currentYear = $request->query('year', Carbon::now()->year);
    
        // Obtener estadísticas básicas
        $stats = $this->getDashboardStats($user, $currentYear, $currentMonth);
    
        // Obtener clientes por vencer y vencidos (solo si es Admin o Asesor)
        $clientesPorVencer = collect();
        $clientesVencidos = collect();
        $clientesPorVencer = $this->getClientesPorVencer($user);
        $clientesVencidos = $this->getClientesVencidos($user);
        $facturacionPorVencer = $this->getFacturacionPorVencer($user);
        $facturacionVencidos = $this->getFacturacionVencidos($user);

        $usuariosActivos = $user->can('ver-todos-clientes') ? $this->getUsuariosActivos() : collect();

    
        // Obtener obligaciones para el calendario
        $obligaciones = $this->getObligacionesCalendario($user, $currentYear, $currentMonth);

        // Alerta de obligaciones vencidas/por vencer + tiempo promedio
        $alertaObligaciones = $this->getAlertaObligaciones($user);

        return view('home', array_merge($stats, [
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'clientesPorVencer' => $clientesPorVencer,
            'clientesVencidos' => $clientesVencidos,
            'facturacionPorVencer' => $facturacionPorVencer,
            'facturacionVencidos' => $facturacionVencidos,
            'usuariosActivos' => $usuariosActivos,
            'obligaciones' => $obligaciones,
            'alertaObligaciones' => $alertaObligaciones,
        ]));
    }

    /**
     * Obtiene las estadísticas del dashboard
     */
    protected function getDashboardStats($user, $year, $month)
    {
        // Consulta base para clientes
        $queryClientes = Cliente::query();
        if (!$user->can('ver-todos-clientes')) {
            $queryClientes->where('id_usuario', $user->id);
        }
    
        return [
            'totalClientes' => $queryClientes->count(),
            'tareasPendientes' => $this->getTareasCount($user, $year, $month, 'Pendiente'),
            'tareasCumplidas' => $this->getTareasCount($user, $year, $month, 'Cumplida', 'fecha_cumplida'),
            'tareasEnProceso' => $this->getTareasCount($user, $year, $month, 'En Proceso')
        ];
    }

    /**
     * Obtiene el conteo de tareas filtradas
     */
    protected function getTareasCount($user, $year, $month, $estado, $fechaCampo = 'fecha_facturada')
    {
        return Tarea::query()
            ->when(!$user->can('ver-todas-tareas'), function ($q) use ($user) {
                $q->whereHas('cliente', function ($sub) use ($user) {
                    $sub->where('id_usuario', $user->id);
                });
            })
            ->where('estado', $estado)
            ->whereYear($fechaCampo, $year)
            ->whereMonth($fechaCampo, $month)
            ->count();
    }

    /**
     * Obtiene clientes con contratos próximos a vencer
     */
    protected function getClientesPorVencer($user)
    {
        $diasAlerta = 15; // Mostrar clientes que vencen en los próximos 30 días
        $hoy = Carbon::now();
        $fechaLimite = $hoy->copy()->addDays($diasAlerta);

        return Cliente::whereNotNull('fecha_firma')
            ->where('fecha_firma', '>=', $hoy)
            ->where('fecha_firma', '<=', $fechaLimite)
            ->when(!$user->can('ver-todos-clientes'), function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            })
            ->orderBy('fecha_firma', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene clientes con contratos vencidos
     */
    protected function getClientesVencidos($user)
    {
        $hoy = Carbon::now();

        return Cliente::whereNotNull('fecha_firma')
            ->where('fecha_firma', '<', $hoy)
            ->when(!$user->can('ver-todos-clientes'), function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            })
            ->orderBy('fecha_firma', 'desc') // Ordenar por los más recientemente vencidos primero
            ->limit(10)
            ->get();
    }

    protected function getFacturacionPorVencer($user)
    {
        $diasAlerta = 15; // Mostrar clientes que vencen en los próximos 15 días
        $hoy = Carbon::now();
        $fechaLimite = $hoy->copy()->addDays($diasAlerta);

        return Cliente::whereNotNull('fecha_facturacion')
            ->where('fecha_facturacion', '>=', $hoy)
            ->where('fecha_facturacion', '<=', $fechaLimite)
            ->when(!$user->can('ver-todos-clientes'), function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            })
            ->orderBy('fecha_facturacion', 'asc')
            ->limit(10)
            ->get();
    }
    
    protected function getFacturacionVencidos($user)
    {
        $hoy = Carbon::now();

        return Cliente::whereNotNull('fecha_facturacion')
            ->where('fecha_facturacion', '<', $hoy)
            ->when(!$user->can('ver-todos-clientes'), function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            })
            ->orderBy('fecha_facturacion', 'desc') // Ordenar por los más recientemente vencidos primero
            ->limit(10)
            ->get();
    }


    public function obligacionesPorMes(Request $request)
    {
        $user = Auth::user();
        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);
        return response()->json($this->getObligacionesCalendario($user, $year, $month));
    }

    protected function getObligacionesCalendario($user, $year, $month)
    {
        return Obligacion::with(['cliente', 'tipoObligacion', 'catalogoServicio'])
            ->where('estado', '!=', 'anulada')
            ->whereYear('fecha_vencimiento', $year)
            ->whereMonth('fecha_vencimiento', $month)
            ->when(!$user->can('ver-todas-obligaciones'), function ($q) use ($user) {
                $q->whereHas('cliente', function ($sub) use ($user) {
                    $sub->where('id_usuario', $user->id);
                });
            })
            ->get()
            ->map(function ($ob) {
                $nombre = $ob->tipoObligacion->nombre ?? ($ob->catalogoServicio->nombre ?? 'Obligación');
                return [
                    'id' => $ob->id,
                    'titulo' => $nombre . ' - ' . ($ob->cliente->nombre_cliente ?? ''),
                    'fecha_vencimiento' => $ob->fecha_vencimiento->format('Y-m-d'),
                    'completado' => $ob->completado,
                    'estado' => $ob->estado,
                    'cliente_nombre' => $ob->cliente->nombre_cliente ?? 'N/A',
                    'cliente_telefono' => $ob->cliente->telefono_cliente ?? '',
                    'tipo_nombre' => $nombre,
                ];
            });
    }

    protected function getUsuariosActivos()
    {
        // Sesiones activas con user_id no null
        $sessionUsers = DB::table('sessions')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique();

        // Obtener usuarios únicos con esas sesiones
        return User::whereIn('id', $sessionUsers)->get();
    }

    /**
     * Obtiene datos para la alerta de obligaciones vencidas/por vencer
     * y el tiempo promedio de cumplimiento por usuario.
     */
    protected function getAlertaObligaciones($user)
    {
        $hoy = Carbon::now();
        $limite = $hoy->copy()->addDays(7);
        $puedeVerTodas = $user->can('ver-todas-tareas');

        // Obligaciones no completadas que ya vencieron o vencen en los proximos 7 dias
        $queryObligaciones = Obligacion::where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<=', $limite);

        if (!$puedeVerTodas) {
            $queryObligaciones->whereHas('cliente', function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            });
        }

        $obligacionesCriticas = $queryObligaciones->with(['cliente.usuario'])->get();

        $totalCriticas = $obligacionesCriticas->count();
        $vencidas = $obligacionesCriticas->filter(fn($ob) => $ob->fecha_vencimiento < $hoy)->count();
        $porVencer = $totalCriticas - $vencidas;

        // Tiempo promedio de cumplimiento (dias entre fecha_facturada y fecha_cumplida)
        $tiempoPromedioUsuarios = collect();

        if ($puedeVerTodas) {
            // Ver el promedio de todos los usuarios
            $tiempoPromedioUsuarios = User::select('id', 'nombre', 'apellido')
                ->get()
                ->map(function ($usuario) {
                    $promedio = Tarea::where('estado', 'Cumplida')
                        ->whereNotNull('fecha_cumplida')
                        ->whereNotNull('fecha_facturada')
                        ->whereHas('cliente', function ($q) use ($usuario) {
                            $q->where('id_usuario', $usuario->id);
                        })
                        ->selectRaw('AVG(DATEDIFF(fecha_cumplida, fecha_facturada)) as dias_promedio')
                        ->value('dias_promedio');

                    $usuario->dias_promedio = $promedio !== null ? round($promedio, 1) : null;
                    return $usuario;
                })
                ->filter(fn($u) => $u->dias_promedio !== null)
                ->sortBy('dias_promedio')
                ->values();
        } else {
            // Solo el promedio del usuario actual
            $promedio = Tarea::where('estado', 'Cumplida')
                ->whereNotNull('fecha_cumplida')
                ->whereNotNull('fecha_facturada')
                ->whereHas('cliente', function ($q) use ($user) {
                    $q->where('id_usuario', $user->id);
                })
                ->selectRaw('AVG(DATEDIFF(fecha_cumplida, fecha_facturada)) as dias_promedio')
                ->value('dias_promedio');

            if ($promedio !== null) {
                $obj = new \stdClass();
                $obj->nombre = $user->nombre;
                $obj->apellido = $user->apellido;
                $obj->dias_promedio = round($promedio, 1);
                $tiempoPromedioUsuarios->push($obj);
            }
        }

        return [
            'totalCriticas' => $totalCriticas,
            'vencidas' => $vencidas,
            'porVencer' => $porVencer,
            'tiempoPromedioUsuarios' => $tiempoPromedioUsuarios,
            'mostrar' => $totalCriticas >= 10,
        ];
    }
}