<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Obligacion;
use App\Models\Regimen;
use App\Models\TipoObligacion;
use App\Models\CatalogoServicio;
use App\Services\GeneradorVencimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ObligacionesController extends Controller // <- renombrado de VencimientosController
{
    protected $calculador;

    public function __construct(GeneradorVencimientos $calculador)
    {
        $this->calculador = $calculador;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $hoy = now();
        $currentMonth = $request->query('month', $hoy->month);
        $currentYear = $request->query('year', $hoy->year);


        $query = Obligacion::with(['cliente' => function($q) {
            $q->with(['usuario', 'regimen']);
        }, 'tipoObligacion', 'catalogoServicio', 'clienteServicio'])
        ->where('estado', '!=', 'anulada')
        ->whereMonth('fecha_vencimiento', $currentMonth)
        ->whereYear('fecha_vencimiento', $currentYear);

        // Filtro por acceso
        if (!$user->can('ver-todas-obligaciones')) {
            $query->whereHas('cliente', function($q) use ($user) {
                $q->where('id_usuario', $user->id);
            });
        }

        // Filtros adicionales
        if ($request->filled('cedula')) {
            $query->where('cedula_cliente', 'LIKE', '%'.$request->cedula.'%');
        }

        if ($request->filled('regimen_id') && $request->regimen_id != 'todos') {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('regimen_id', $request->regimen_id);
            });
        }

        // Ordenamiento
        $obligaciones = $query->orderByRaw('ABS(DATEDIFF(fecha_vencimiento, ?))', [$hoy])
            ->orderBy('fecha_vencimiento')
            ->get();

        $regimenes = Regimen::all();

        // Usar las mismas obligaciones filtradas para el calendario
        $obligacionesCalendario = $obligaciones->map(function ($ob) {
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

        return view('vencimientos.index', [
            'hoy' => $hoy,
            'currentMonth' => (int) $currentMonth,
            'currentYear' => (int) $currentYear,
            'mesActual' => Carbon::create($currentYear, $currentMonth)->translatedFormat('F'),
            'anioActual' => $currentYear,
            'regimenes' => $regimenes,
            'filtros' => $request->only(['cedula', 'regimen_id']),
            'obligacionesCalendario' => $obligacionesCalendario,
        ]);
    }

    public function notificarVencimiento(Request $request)
    {
        $request->validate([
            'vencimiento_id' => 'required|exists:obligaciones,id',
            'mensaje' => 'required|string',
        ]);

        $obligacion = Obligacion::with(['cliente', 'tipoObligacion'])->find($request->input('vencimiento_id'));

        if (!$obligacion || !$obligacion->cliente) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'No se encontró el cliente.']);
            return back()->with('error', 'No se encontró el vencimiento o el cliente asociado.');
        }

        $cliente = $obligacion->cliente;

        if (!$cliente->telefono_cliente) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'El cliente no tiene WhatsApp registrado.']);
            return back()->with('error', 'El cliente no tiene un número de WhatsApp registrado.');
        }

        // Formatear número de teléfono para Ecuador
        $numero = preg_replace('/\D+/', '', $cliente->telefono_cliente);
        if (str_starts_with($numero, '0')) {
            $numero = '593' . substr($numero, 1);
        }

        $urlText = rtrim(config('services.whatsapp.url'), '/') . '/send-message';
        $mensaje = $request->input('mensaje');

        $payload = [
            'numero'  => $numero,
            'mensaje' => $mensaje,
        ];

        try {
            $response = Http::withHeaders([
                'x-api-key'    => config('services.whatsapp.token'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($urlText, $payload);

            if ($response->successful()) {
                if ($request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Mensaje enviado a ' . $cliente->nombre_cliente]);
                }
                return back()->with('success', 'Mensaje enviado exitosamente a ' . $cliente->nombre_cliente);
            }

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al enviar: ' . $response->body()]);
            }
            return back()->with('error', 'Error al enviar el mensaje: ' . $response->body());

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    public function marcarCompletado(Request $request, $id)
    {
        $obligacion = Obligacion::findOrFail($id);

        $obligacion->update([
            'completado' => true,
            'completado_en' => now(),
            'estado' => 'completada',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Obligación completada']);
        }

        return back()->with('success', 'Obligación marcada como completada para ' . $obligacion->cliente->nombre_cliente);
    }

    public function anularObligacion(Request $request, $id)
    {
        $obligacion = Obligacion::findOrFail($id);

        $obligacion->update([
            'estado' => 'anulada',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Obligación anulada']);
        }

        return back()->with('success', 'Obligación anulada exitosamente.');
    }

    /**
     * Genera obligaciones manualmente para el mes/año que elija el usuario.
     */
    public function generarManual(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'mes'  => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2020|max:2100',
        ]);

        $generadas = $this->calculador->generarParaMes(
            (int) $request->mes,
            (int) $request->anio
        );

        return redirect()->route('vencimientos.index')
            ->with('success', "Se generaron {$generadas} obligaciones para " . \Carbon\Carbon::createFromDate($request->anio, $request->mes, 1)->translatedFormat('F Y') . '.');
    }

    /**
     * Devuelve los tipos de obligación de un régimen (JSON para AJAX).
     */
    public function tiposPorRegimen(Regimen $regimen)
    {
        $tipos = TipoObligacion::where('regimen_id', $regimen->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'periodicidad', 'mes_vencimiento', 'dia_vencimiento']);

        return response()->json($tipos);
    }

    /**
     * Crea una obligación específica para un cliente (manual, desde catálogo).
     */
    public function crearParaCliente(Request $request, Cliente $cliente)
    {
        $request->validate([
            'obligaciones'                        => 'required|array|min:1',
            'obligaciones.*.catalogo_servicio_id' => 'required|exists:catalogo_servicios,id',
            'obligaciones.*.periodicidad'         => 'required|in:mensual,semestral,anual',
            'obligaciones.*.dia_vencimiento'      => 'required|integer|min:1|max:28',
            'obligaciones.*.mes_vencimiento'      => 'nullable|integer|min:1|max:12',
        ]);

        $hoy = now();
        $creadas = 0;
        $duplicadas = 0;

        foreach ($request->obligaciones as $obData) {
            $dia = (int) $obData['dia_vencimiento'];
            $mesElegido = !empty($obData['mes_vencimiento']) ? (int) $obData['mes_vencimiento'] : null;

            if ($obData['periodicidad'] === 'mensual') {
                $fecha = Carbon::create($hoy->year, $hoy->month, $dia);
                if ($fecha->lt($hoy)) {
                    $fecha->addMonth();
                }
            } elseif ($obData['periodicidad'] === 'semestral') {
                if ($mesElegido) {
                    $fecha = Carbon::create($hoy->year, $mesElegido, $dia);
                    if ($fecha->lt($hoy)) {
                        $fecha->addMonths(6);
                    }
                } else {
                    $mesSemestral = $hoy->month <= 6 ? 6 : 12;
                    $fecha = Carbon::create($hoy->year, $mesSemestral, $dia);
                    if ($fecha->lt($hoy)) {
                        $fecha->addMonths(6);
                    }
                }
            } else { // anual
                $mesAnual = $mesElegido ?? 12;
                $fecha = Carbon::create($hoy->year, $mesAnual, $dia);
                if ($fecha->lt($hoy)) {
                    $fecha->addYear();
                }
            }

            $periodo = $fecha->format('Y-m');

            $existe = Obligacion::where('cliente_id', $cliente->id_clientes)
                ->where('catalogo_servicio_id', $obData['catalogo_servicio_id'])
                ->where('periodo', $periodo)
                ->exists();

            if ($existe) {
                $duplicadas++;
                continue;
            }

            Obligacion::create([
                'cliente_id'           => $cliente->id_clientes,
                'catalogo_servicio_id' => $obData['catalogo_servicio_id'],
                'periodicidad'         => $obData['periodicidad'],
                'dia_vencimiento'      => $dia,
                'fecha_vencimiento'    => $fecha,
                'periodo'              => $periodo,
                'completado'           => false,
                'generado_en'          => now(),
            ]);
            $creadas++;
        }

        $msg = $creadas . ' obligación(es) creada(s) exitosamente.';
        if ($duplicadas > 0) {
            $msg .= ' ' . $duplicadas . ' omitida(s) por duplicado.';
        }

        return back()->with('success', $msg);
    }

    public function actualizarObligacion(Request $request, Obligacion $obligacion)
    {
        $request->validate([
            'periodicidad'     => 'required|in:mensual,semestral,anual',
            'dia_vencimiento'  => 'required|integer|min:1|max:28',
            'mes_vencimiento'  => 'nullable|integer|min:1|max:12',
        ]);

        $dia = (int) $request->dia_vencimiento;
        $mesElegido = $request->mes_vencimiento ? (int) $request->mes_vencimiento : null;
        $hoy = now();

        if ($request->periodicidad === 'mensual') {
            $fecha = Carbon::create($hoy->year, $hoy->month, $dia);
            if ($fecha->lt($hoy)) {
                $fecha->addMonth();
            }
        } elseif ($request->periodicidad === 'semestral') {
            if ($mesElegido) {
                $fecha = Carbon::create($hoy->year, $mesElegido, $dia);
                if ($fecha->lt($hoy)) {
                    $fecha->addMonths(6);
                }
            } else {
                $mesSemestral = $hoy->month <= 6 ? 6 : 12;
                $fecha = Carbon::create($hoy->year, $mesSemestral, $dia);
                if ($fecha->lt($hoy)) {
                    $fecha->addMonths(6);
                }
            }
        } else {
            $mesAnual = $mesElegido ?? 12;
            $fecha = Carbon::create($hoy->year, $mesAnual, $dia);
            if ($fecha->lt($hoy)) {
                $fecha->addYear();
            }
        }

        $obligacion->update([
            'periodicidad'      => $request->periodicidad,
            'dia_vencimiento'   => $dia,
            'fecha_vencimiento' => $fecha,
            'periodo'           => $fecha->format('Y-m'),
        ]);

        return back()->with('success', 'Obligación actualizada exitosamente.');
    }

    public function eliminarObligacion(Obligacion $obligacion)
    {
        $obligacion->delete();
        return back()->with('success', 'Obligación eliminada exitosamente.');
    }

    /**
     * Devuelve todos los servicios del catálogo (JSON para AJAX).
     */
    public function catalogoServicios()
    {
        $servicios = CatalogoServicio::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'categoria']);

        return response()->json($servicios);
    }
}