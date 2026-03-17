<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class FacturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-factura|crear-factura|editar-factura|borrar-factura', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-factura', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-factura', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-factura', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Factura::with(['cliente', 'tarea', 'cobro']);
        
        // Filtrado por acceso
        $query->with('cliente', 'cliente.usuario');
        if (!$user->can('ver-todas-facturas')) {
            $query->whereHas('cliente', function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            });
        }

        $usuarios = $user->can('ver-todas-facturas')
            ? User::where('estado', 'Activo')->orderBy('nombre')->get()
            : collect();

        // Defaults: mes y año actual cuando no hay filtros aplicados
        if (!$request->has('mes') && !$request->has('anio') && !$request->has('search') && !$request->has('estado')) {
            $request->merge([
                'mes' => date('n'),
                'anio' => date('Y'),
            ]);
        }

        // Filtros para listado
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_factura', 'like', "%{$search}%")
                ->orWhereHas('cliente', function ($q) use ($search) {
                    $q->where('nombre_cliente', 'like', "%{$search}%")
                        ->orWhere('cedula_cliente', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado_pago', $request->estado);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_factura', $request->mes);
        }
        if ($request->filled('anio')) {
            $query->whereYear('fecha_factura', $request->anio);
        }

        // ✅ Totales: clon sin filtro de estado
        $totalesQuery = Factura::with(['cliente', 'tarea', 'cobro']);
        if (!$user->can('ver-todas-facturas')) {
            $totalesQuery->whereHas('cliente', function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            });
        }

        if ($request->filled('search')) {
            $totalesQuery->where(function ($q) use ($search) {
                $q->where('numero_factura', 'like', "%{$search}%")
                ->orWhereHas('cliente', function ($q) use ($search) {
                    $q->where('nombre_cliente', 'like', "%{$search}%")
                        ->orWhere('cedula_cliente', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('mes')) {
            $totalesQuery->whereMonth('fecha_factura', $request->mes);
        }
        if ($request->filled('anio')) {
            $totalesQuery->whereYear('fecha_factura', $request->anio);
        }

        // ✅ Total facturado
        $totalFacturado = $totalesQuery->sum('total_factura');

        // ✅ Total recaudado (independiente del estado)
        $totalRecaudado = $totalesQuery->sum(DB::raw('total_factura - saldo_pendiente'));

        $facturas = $query->orderBy('fecha_factura', 'desc')->paginate(10);

        $aniosDisponibles = Factura::selectRaw('YEAR(fecha_factura) as anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('facturas.index', compact('facturas', 'usuarios', 'totalFacturado', 'totalRecaudado', 'aniosDisponibles'));
    }
   /**
     * Mostrar formulario para crear factura
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre_cliente')->get();
        return view('facturas.create', compact('clientes'));
    }

    /**
     * Crear una nueva factura
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_factura' => 'required|string|unique:facturas,numero_factura',
            'fecha_factura' => 'required|date',
            'cliente_id' => 'required|exists:clientes,id_clientes',
            'total_factura' => 'required|numeric|min:0',
        ]);

        $factura = Factura::create([
            'numero_factura' => $request->numero_factura,
            'fecha_factura' => $request->fecha_factura,
            'cliente_id' => $request->cliente_id,
            'total_factura' => $request->total_factura,
            'saldo_pendiente' => $request->total_factura,
            'estado_pago' => 'Pendiente'
        ]);

        return redirect()->route('facturas.index')
            ->with('success', 'Factura creada correctamente');
    }

    /**
     * Mostrar una factura específica con sus relaciones
     */
    public function show($id)
    {
        $factura = Factura::with(['cliente', 'tarea', 'cobro'])->findOrFail($id);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Mostrar formulario para editar factura
     */
    public function edit($id)
    {
        $factura = Factura::findOrFail($id);
        $clientes = Cliente::orderBy('nombre_cliente')->get();
        return view('facturas.edit', compact('factura', 'clientes'));
    }

    /**
     * Actualizar una factura
     */
    public function update(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);

        $request->validate([
            'numero_factura' => 'required|string|unique:facturas,numero_factura,' . $id . ',id_facturas',
            'fecha_factura' => 'required|date',
            'cliente_id' => 'required|exists:clientes,id_clientes',
            'total_factura' => 'required|numeric|min:0',
            'estado_pago' => 'required|in:Pendiente,Parcial,Pagado',
        ]);

        $factura->update($request->all());

        return redirect()->route('facturas.index')
            ->with('success', 'Factura actualizada correctamente');
    }

    /**
     * Eliminar una factura
     */
    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);
        $factura->delete();
        
        return redirect()->route('facturas.index')
            ->with('success', 'Factura eliminada correctamente');
    }
}