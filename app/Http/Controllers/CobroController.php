<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\User;
use App\Services\CobroImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CobroController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-cobro|crear-cobro|editar-cobro|borrar-cobro', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-cobro', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-cobro', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-cobro', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Cobro::with(['factura.cliente', 'usuario']);
        
        // Filtrado por acceso
        $query->with('factura.cliente.usuario');
        if (!$user->can('ver-todos-cobros')) {
            $query->whereHas('factura.cliente', function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            });
        }
        
        // Filtros de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('factura', function($q) use ($search) {
                    $q->where('numero_factura', 'like', "%{$search}%")
                      ->orWhereHas('cliente', function($q) use ($search) {
                          $q->where('nombre_cliente', 'like', "%{$search}%")
                            ->orWhere('cedula_cliente', 'like', "%{$search}%");
                      });
                });
            });
        }
        
        // Filtro por fecha
        if ($request->has('fecha') && !empty($request->fecha)) {
            $query->whereDate('fecha_pago', $request->fecha);
        }

        $cobros = $query->orderBy('fecha_pago', 'desc')->paginate(10);
        
        return view('cobros.index', compact('cobros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $factura_id = $request->input('factura_id');
        $factura = null;
        $facturas = collect();
        $usuarios = collect();

        // Obtener lista de usuarios para administradores
        if ($user->can('ver-todos-cobros')) {
            $usuarios = User::where('activo', true)
                ->orderBy('name')
                ->get();
        }

        if ($factura_id) {
            $factura = Factura::with('cliente')->find($factura_id);

            // Validar que el usuario tenga acceso a esta factura
            if ($factura && !$user->can('ver-todos-cobros')) {
                if ($factura->cliente->id_usuario != $user->id) {
                    abort(403, 'No tienes permiso para acceder a esta factura');
                }
            }
        } else {
            // Obtener facturas disponibles según acceso
            $facturasQuery = Factura::where('saldo_pendiente', '>', 0)->with('cliente');
            if (!$user->can('ver-todos-cobros')) {
                $facturasQuery->whereHas('cliente', function ($q) use ($user) {
                    $q->where('id_usuario', $user->id);
                });
            }
            $facturas = $facturasQuery->get();
        }

        return view('cobros.create', compact('factura', 'facturas', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'factura_id' => 'required|exists:facturas,id_facturas',
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'tipo_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
            'usuario_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $factura = Factura::findOrFail($request->factura_id);
            
            // Validar que el monto no exceda el saldo pendiente
            if ($request->monto > $factura->saldo_pendiente) {
                return back()->withErrors([
                    'monto' => 'El monto no puede ser mayor al saldo pendiente ($' . number_format($factura->saldo_pendiente, 2) . ')'
                ])->withInput();
            }

            // Crear el cobro
            $cobro = Cobro::create([
                'factura_id' => $request->factura_id,
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'tipo_pago' => $request->tipo_pago,
                'usuario_id' => $request->usuario_id, // Usar el usuario del request
            ]);

            // Actualizar el saldo pendiente de la factura
            $factura->saldo_pendiente -= $request->monto;
            
            // Actualizar el estado de pago
            if ($factura->saldo_pendiente == 0) {
                $factura->estado_pago = 'Pagado';
            } else if ($factura->saldo_pendiente < $factura->total_factura) {
                $factura->estado_pago = 'Parcial';
            }
            
            $factura->save();

            DB::commit();

            return redirect()->route('facturas.index')
                ->with('success', 'Cobro registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el cobro: ' . $e->getMessage()])->withInput();
        }
    }
    public function storeCobroDesdeTarea(Request $request)
    {
        $request->validate([
            'factura_id' => 'required|exists:facturas,id_facturas',
            'tarea_id' => 'required|exists:tareas,id_tareas',
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'tipo_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
            'usuario_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $factura = Factura::findOrFail($request->factura_id);
            $tarea = \App\Models\Tarea::with('cliente', 'factura')->findOrFail($request->tarea_id);

            if (!$tarea->factura || $tarea->factura->id_facturas != $request->factura_id) {
                return back()->withErrors(['error' => 'La tarea no tiene una factura válida asociada'])->withInput();
            }

            if ($request->monto > $factura->saldo_pendiente) {
                return back()->withErrors([
                    'monto' => 'El monto no puede ser mayor al saldo pendiente ($' . number_format($factura->saldo_pendiente, 2) . ')'
                ])->withInput();
            }

            $cobro = Cobro::create([
                'factura_id' => $request->factura_id,
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'tipo_pago' => $request->tipo_pago,
                'usuario_id' => $request->usuario_id,
            ]);

            $factura->saldo_pendiente -= $request->monto;

            if ($factura->saldo_pendiente == 0) {
                $factura->estado_pago = 'Pagado';
            } else if ($factura->saldo_pendiente < $factura->total_factura) {
                $factura->estado_pago = 'Parcial';
            }

            $factura->save();

            DB::commit();

            return redirect()->route('tareas.index', [
                    'filter' => $request->input('filter'),
                    'search' => $request->input('search'),
                    'month' => $request->input('month'),
                    'year' => $request->input('year'),
                    'page' => $request->input('page'),
                ])
                ->with('success', 'Cobro registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el cobro: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cobro = Cobro::with(['factura.cliente', 'usuario'])->findOrFail($id);
        
        // Validar permisos de acceso
        $user = Auth::user();
        if (!$user->can('ver-todos-cobros') && $cobro->factura->cliente->id_usuario != $user->id) {
            abort(403, 'No tienes permiso para ver este cobro');
        }

        return view('cobros.show', compact('cobro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cobro = Cobro::with(['factura.cliente'])->findOrFail($id);
        return view('cobros.edit', compact('cobro'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'tipo_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
        ]);

        try {
            DB::beginTransaction();

            $cobro = Cobro::with('factura')->findOrFail($id);
            $factura = $cobro->factura;

            // Diferencia entre nuevo monto y el anterior
            $diferencia = $request->monto - $cobro->monto;

            // Validación del saldo
            $saldoDisponible = $factura->saldo_pendiente + $cobro->monto;
            if ($request->monto > $saldoDisponible) {
                return back()->withErrors([
                    'monto' => 'El monto no puede exceder el saldo disponible ($' . number_format($saldoDisponible, 2) . ')'
                ])->withInput();
            }

            // Actualización del cobro
            $cobro->update([
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'tipo_pago' => $request->tipo_pago,
            ]);

            // Actualizar saldo y estado de pago
            $factura->saldo_pendiente -= $diferencia;

            if ($factura->saldo_pendiente <= 0) {
                $factura->saldo_pendiente = 0;
                $factura->estado_pago = 'Pagado';
            } elseif ($factura->saldo_pendiente < $factura->total_factura) {
                $factura->estado_pago = 'Parcial';
            } else {
                $factura->estado_pago = 'Pendiente';
            }

            $factura->save();

            DB::commit();

            return redirect()->route('cobros.index')
                ->with('success', 'Cobro actualizado correctamente y saldo de la factura ajustado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Error al actualizar el cobro: ' . $e->getMessage()
            ])->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $cobro = Cobro::with('factura')->findOrFail($id);
            $factura = $cobro->factura;
            
            // Validar permisos de acceso
            $user = Auth::user();
            if (!$user->can('ver-todos-cobros') && $factura->cliente->id_usuario != $user->id) {
                abort(403, 'No tienes permiso para eliminar este cobro');
            }

            // Restaurar el saldo pendiente de la factura
            $factura->saldo_pendiente += $cobro->monto;
            
            // Actualizar el estado de pago
            if ($factura->saldo_pendiente == $factura->total_factura) {
                $factura->estado_pago = 'Pendiente';
            } else if ($factura->saldo_pendiente > 0) {
                $factura->estado_pago = 'Parcial';
            }
            
            $factura->save();

            // Eliminar el cobro
            $cobro->delete();

            DB::commit();

            return redirect()->route('cobros.index')
                ->with('success', 'Cobro eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el cobro: ' . $e->getMessage()]);
        }
    }

    public function importarExcel(Request $request)
    {
        $request->validate([
            'archivo_cobros' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $archivo = $request->file('archivo_cobros');
            $importService = app(CobroImportService::class);
            $resultados = $importService->importarDesdeExcel(
                $archivo->getPathname(),
                Auth::id()
            );

            $mensaje = "Importación completada: {$resultados['cobros_creados']} cobros registrados.";

            if ($resultados['duplicados_omitidos'] > 0) {
                $mensaje .= " {$resultados['duplicados_omitidos']} duplicados omitidos.";
            }

            if (count($resultados['facturas_no_encontradas']) > 0) {
                $uniqueFacturas = array_unique($resultados['facturas_no_encontradas']);
                $mensaje .= " " . count($uniqueFacturas) . " facturas no encontradas en el sistema: " . implode(', ', array_slice($uniqueFacturas, 0, 10));
                if (count($uniqueFacturas) > 10) {
                    $mensaje .= '...';
                }
            }

            if (count($resultados['montos_excedidos']) > 0) {
                $mensaje .= " " . count($resultados['montos_excedidos']) . " cobros con monto mayor al saldo pendiente fueron omitidos.";
            }

            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar cobros: ' . $e->getMessage());
        }
    }
}