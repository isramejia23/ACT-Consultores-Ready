<?php

namespace App\Http\Controllers;

use App\Models\TipoObligacion;
use App\Models\Regimen;
use App\Models\CatalogoServicio;
use Illuminate\Http\Request;

class TipoObligacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $regimenes = Regimen::with(['tiposObligacion.servicio'])
            ->withCount('tiposObligacion')
            ->get();
        $servicios = CatalogoServicio::orderBy('codigo')->get();

        return view('tipos_obligacion.index', compact('regimenes', 'servicios'));
    }

    public function create()
    {
        $regimenes  = Regimen::orderBy('nombre')->get();
        $servicios  = CatalogoServicio::orderBy('codigo')->get();

        return view('tipos_obligacion.create', compact('regimenes', 'servicios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'               => 'required|string|max:150',
            'regimen_ids'          => 'required|array|min:1',
            'regimen_ids.*'        => 'exists:regimenes,id',
            'periodicidad'         => 'required|in:mensual,semestral,anual',
            'mes_vencimiento'      => 'nullable|integer|between:1,12',
            'dia_vencimiento'      => 'nullable|integer|between:1,28',
            'catalogo_servicio_id' => 'nullable|exists:catalogo_servicios,id',
        ]);

        $creados = 0;
        $duplicados = 0;

        foreach ($request->regimen_ids as $regimenId) {
            // Evitar duplicados (mismo régimen + mismo servicio + misma periodicidad + mismo mes)
            $query = TipoObligacion::where('regimen_id', $regimenId)
                ->where('catalogo_servicio_id', $request->catalogo_servicio_id)
                ->where('periodicidad', $request->periodicidad);

            if ($request->mes_vencimiento) {
                $query->where('mes_vencimiento', $request->mes_vencimiento);
            } else {
                $query->whereNull('mes_vencimiento');
            }

            if ($query->exists()) {
                $duplicados++;
                continue;
            }

            TipoObligacion::create([
                'nombre'               => $request->nombre,
                'regimen_id'           => $regimenId,
                'periodicidad'         => $request->periodicidad,
                'mes_vencimiento'      => $request->mes_vencimiento ?: null,
                'dia_vencimiento'      => $request->dia_vencimiento ?: null,
                'catalogo_servicio_id' => $request->catalogo_servicio_id ?: null,
            ]);
            $creados++;
        }

        $mensaje = "Se crearon $creados tipo(s) de obligación.";
        if ($duplicados > 0) {
            $mensaje .= " Se omitieron $duplicados por duplicado.";
        }

        return redirect()->route('tipos-obligacion.index')
            ->with($creados > 0 ? 'success' : 'error', $mensaje);
    }

    public function update(Request $request, TipoObligacion $tiposObligacion)
    {
        $request->validate([
            'nombre'               => 'required|string|max:150',
            'regimen_id'           => 'required|exists:regimenes,id',
            'periodicidad'         => 'required|in:mensual,semestral,anual',
            'mes_vencimiento'      => 'nullable|integer|between:1,12',
            'dia_vencimiento'      => 'nullable|integer|between:1,28',
            'catalogo_servicio_id' => 'nullable|exists:catalogo_servicios,id',
        ]);

        $tiposObligacion->update([
            'nombre'               => $request->nombre,
            'regimen_id'           => $request->regimen_id,
            'periodicidad'         => $request->periodicidad,
            'mes_vencimiento'      => $request->mes_vencimiento ?: null,
            'dia_vencimiento'      => $request->dia_vencimiento ?: null,
            'catalogo_servicio_id' => $request->catalogo_servicio_id ?: null,
        ]);

        return redirect()->route('tipos-obligacion.index')
            ->with('success', 'Tipo de obligación actualizado exitosamente.');
    }

    public function destroy(TipoObligacion $tiposObligacion)
    {
        $tiposObligacion->delete();

        return redirect()->route('tipos-obligacion.index')
            ->with('success', 'Tipo de obligación eliminado.');
    }
}
