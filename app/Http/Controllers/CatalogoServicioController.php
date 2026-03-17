<?php

namespace App\Http\Controllers;

use App\Models\CatalogoServicio;
use Illuminate\Http\Request;

class CatalogoServicioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = CatalogoServicio::withTrashed();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('nombre', 'like', "%{$s}%")
                  ->orWhere('categoria', 'like', "%{$s}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('genera_obligacion')) {
            $query->where('genera_obligacion', $request->genera_obligacion === '1');
        }

        if ($request->filled('activo')) {
            if ($request->activo === '1') {
                $query->whereNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        $catalogos = $query->orderBy('codigo')->paginate(20)->appends($request->query());
        $categorias = CatalogoServicio::withTrashed()->select('categoria')->distinct()->pluck('categoria')->filter();

        return view('catalogo.index', compact('catalogos', 'categorias'));
    }

    /**
     * Activa o desactiva el flag genera_obligacion de un servicio (toggle AJAX)
     */
    public function toggleObligacion(CatalogoServicio $catalogoServicio)
    {
        $catalogoServicio->update([
            'genera_obligacion' => !$catalogoServicio->genera_obligacion,
        ]);

        return response()->json([
            'genera_obligacion' => $catalogoServicio->genera_obligacion,
            'message' => $catalogoServicio->genera_obligacion
                ? 'Ahora genera obligación'
                : 'Ya no genera obligación',
        ]);
    }

    /**
     * Activa o desactiva el servicio (softdelete vs restore)
     */
    public function toggleActivo($id)
    {
        $servicio = CatalogoServicio::withTrashed()->findOrFail($id);

        if ($servicio->trashed()) {
            $servicio->restore();
            $msg = 'Servicio reactivado.';
        } else {
            $servicio->delete();
            $msg = 'Servicio desactivado.';
        }

        return response()->json(['message' => $msg, 'activo' => !$servicio->trashed()]);
    }
}
