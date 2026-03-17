<?php

namespace App\Http\Controllers;

use App\Models\Regimen;
use App\Models\TipoObligacion;
use Illuminate\Http\Request;

class RegimenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-regimen|crear-regimen|editar-regimen|borrar-regimen', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-regimen', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-regimen', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-regimen', ['only' => ['destroy']]);
    }

    public function index()
    {
        $regimenes = Regimen::withCount('clientes')
            ->with('tiposObligacion.servicio')
            ->get();

        return view('regimenes.index', compact('regimenes'));
    }

    public function create()
    {
        return view('regimenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:100|unique:regimenes,nombre',
            'periodicidad'    => 'required|in:mensual,semestral,anual',
            'mes_vencimiento' => 'nullable|integer|min:1|max:12',
            'dia_fijo'        => 'nullable|integer|min:1|max:31',
        ]);

        Regimen::create($request->only(['nombre', 'periodicidad', 'mes_vencimiento', 'dia_fijo']));

        return redirect()->route('regimenes.index')
            ->with('success', 'Régimen creado exitosamente.');
    }

    public function edit(Regimen $regimen)
    {
        return view('regimenes.edit', compact('regimen'));
    }

    public function update(Request $request, Regimen $regimen)
    {
        $request->validate([
            'nombre'          => 'required|string|max:100|unique:regimenes,nombre,' . $regimen->id,
            'periodicidad'    => 'required|in:mensual,semestral,anual',
            'mes_vencimiento' => 'nullable|integer|min:1|max:12',
            'dia_fijo'        => 'nullable|integer|min:1|max:31',
        ]);

        $regimen->update($request->only(['nombre', 'periodicidad', 'mes_vencimiento', 'dia_fijo']));

        return redirect()->route('regimenes.index')
            ->with('success', 'Régimen actualizado correctamente.');
    }

    public function destroy(Regimen $regimen)
    {
        if ($regimen->clientes()->count() > 0) {
            return redirect()->route('regimenes.index')
                ->with('error', 'No se puede eliminar el régimen porque tiene clientes asignados.');
        }

        $regimen->delete();

        return redirect()->route('regimenes.index')
            ->with('success', 'Régimen eliminado.');
    }
}
