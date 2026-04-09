<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Cliente;
use App\Http\Controllers\User;
use App\Models\User as Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;






class TareaController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
       $this->middleware('permission:ver-tarea|crear-tarea|editar-tarea|borrar-tarea|actualizar-estado-tarea|ver-tareas-avanzado|notificar-cliente', ['only' => ['index']]);
       $this->middleware('permission:crear-tarea', ['only' => ['create', 'store']]);
       $this->middleware('permission:editar-tarea', ['only' => ['edit', 'update']]);
       $this->middleware('permission:borrar-tarea', ['only' => ['destroy']]);
       $this->middleware('permission:actualizar-estado-tarea', ['only' => ['updateEstado']]); 
       $this->middleware('permission:ver-tareas-avanzado', ['only' => ['indexFiltrosAvanzados']]);
       $this->middleware('permission:notificar-cliente', ['only' => ['notificarCliente']]);
       $this->middleware('permission:transferir-tarea', ['only' => ['formTransferir', 'transferir']]);
    }
    

    /**
     * Mostrar la lista de tareas.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter');
        $search = $request->query('search');
        $month = $request->query('month', date('n'));
        $year = $request->query('year', date('Y'));

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.utf8');

        $query = Tarea::select([
            'id_tareas',
            'id_factura', // agregar para poder gestionar cobros
            'nombre',
            'numero_factura',
            'estado',
            'fecha_facturada',
            'id_clientes',
            'id_usuario',
            'total',
            'cantidad',
            'precio_unitario',
            'fecha_cumplida',
            'archivo',
            'notificado',
            'observacion',
        ]);

        // Filtro por acceso
        $query->with('cliente', 'cliente.usuario', 'factura');
        if (!$user->can('ver-todas-tareas')) {
            $query->where('id_usuario', $user->id);
        }

        // Resto de los filtros (se mantienen igual)
        if ($filter) {
            $query->where('estado', $filter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('cliente', function ($subQuery) use ($search) {
                    $subQuery->where('cedula_cliente', 'like', "%{$search}%")
                            ->orWhere('nombre_cliente', 'like', "%{$search}%");
                })
                ->orWhere('nombre', 'like', "%{$search}%"); // Busca en el campo 'nombre' de la tabla tareas
            });
        }

        if ($year) {
            $query->whereYear('fecha_facturada', $year);
        }

        if ($month) {
            $query->whereMonth('fecha_facturada', $month);
        }

        $totalSuma = $month ? $query->sum('total') : 0;

       $tareas = $query
        ->orderBy('fecha_facturada', 'desc')
        ->paginate(10)
        ->appends($request->only(['filter','search','month','year']));

        $filters = [
            'filter' => $filter,
            'search' => $search,
            'month' => $month,
            'year' => $year,
        ];

        return view('tareas.index', compact('tareas', 'filters', 'totalSuma'));
    }   

    /**
     * Mostrar el formulario para crear una nueva tarea.
     */
    public function create()
    {
        $clientes = Cliente::all(); // Obtener todos los clientes
        return view('tareas.create', compact('clientes'));
    }

    /**
     * Almacenar una nueva tarea en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_clientes' => 'required|exists:clientes,id_clientes',
            'numero_factura' => 'required|string|max:255',
            'fecha_facturada' => 'required|date',
            'nombre'=>'required|string',
            'estado' => 'required|string|in:Cumplida,En Proceso,Pendiente,Anulada',
            'fecha_cumplida' => 'nullable|date',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls,csv|max:5120',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'observacion' => 'nullable|string',
        ]);

        // Manejo del archivo
        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            $file = $request->file('archivo');
        
            // Generar un nombre único para el archivo ZIP
            $uuid = Str::uuid();
            $zipFileName = $uuid . '.zip';
            $tempZipPath = storage_path('app/temp_' . $uuid . '.zip');
        
            // Crear el archivo ZIP temporal
            $zip = new ZipArchive();
            if ($zip->open($tempZipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($file->getRealPath(), $file->getClientOriginalName());
                $zip->close();
            } else {
                throw new \Exception('No se pudo crear el archivo comprimido.');
            }
        
            // Guardar el ZIP en el disco 'public'
            $archivoPath = Storage::disk('public')->putFileAs(
                'archivos_tareas',
                new \Illuminate\Http\File($tempZipPath),
                $zipFileName
            );
        
            // Eliminar el archivo temporal
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
        
        } else {
            $archivoPath = null; // O lanza una excepción si lo necesitas obligatorio
        }
        

        Tarea::create([
            'id_clientes' => $request->id_clientes,
            'numero_factura' => $request->numero_factura,
            'fecha_facturada' => $request->fecha_facturada,
            'nombre'=>$request->nombre,
            'estado' => $request->estado,
            'fecha_cumplida' => $request->fecha_cumplida,
            'archivo' => $archivoPath,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'total' => $request->total,
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('tareas.index')->with('success', 'Trabajo creado exitosamente.');
    }


    /**
     * Mostrar los detalles de una tarea específica.
     */
    public function show(Tarea $tarea)
    {
        return view('tareas.show', compact('tarea'));
    }

    /**
     * Mostrar el formulario para editar una tarea.
     */
    public function edit(Tarea $tarea)
    {
        $clientes = Cliente::all(); // Obtener todos los clientes
        return view('tareas.edit', compact('tarea', 'clientes'));
    }

    /**
     * Actualizar la información de una tarea en la base de datos.
     */
    public function update(Request $request, Tarea $tarea)
    {
        // Validación de los campos del formulario
        $request->validate([
            'numero_factura' => 'required|string|max:255',
            'fecha_facturada' => 'required|date',
            'nombre'=>'required|string',
            'estado' => 'required|string|in:Cumplida,En Proceso,Pendiente,Anulada',
            'fecha_cumplida' => 'nullable|date',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls,csv|max:5120',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'observacion' => 'nullable|string',
        ]);
    
        // Manejo del archivo (si existe uno nuevo)
        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            $file = $request->file('archivo');
        
            // Eliminar archivo anterior si existe
            if ($tarea->archivo && Storage::disk('public')->exists($tarea->archivo)) {
                Storage::disk('public')->delete($tarea->archivo);
            }
        
            // Crear nombre único para el ZIP
            $uuid = Str::uuid();
            $zipFileName = 'archivos_tareas/' . $uuid . '.zip';
            $tempZipPath = storage_path('app/temp_' . $uuid . '.zip');
        
            // Crear archivo ZIP temporal
            $zip = new ZipArchive();
            if ($zip->open($tempZipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($file->getRealPath(), $file->getClientOriginalName());
                $zip->close();
            } else {
                throw new \Exception('No se pudo comprimir el archivo.');
            }
        
            // Subir el ZIP al storage
            $archivoPath = Storage::disk('public')->putFileAs(
                'archivos_tareas',
                new \Illuminate\Http\File($tempZipPath),
                basename($zipFileName)
            );
        
            // Eliminar el archivo temporal
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
        
        } else {
            // Mantener archivo actual si no se sube uno nuevo
            $archivoPath = $tarea->archivo;
        }
                
        // Actualiza la tarea, sin modificar el cliente
        $tarea->update([
            'numero_factura' => $request->numero_factura,
            'fecha_facturada' => $request->fecha_facturada,
            'nombre'=>$request->nombre,
            'estado' => $request->estado,
            'fecha_cumplida' => $request->fecha_cumplida,
            'archivo' => $archivoPath,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'total' => $request->total,
            'observacion' => $request->observacion,
        ]);
    
        return redirect()->route('tareas.index', [
            'filter' => $request->input('filter'),
            'search' => $request->input('search'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
            'page' => $request->input('page'),
        ])->with('success', 'Trabajo actualizado exitosamente.');


        //return redirect()->route('tareas.index')->with('success', 'Trabajo actualizado exitosamente.');
    }
    

    /**
     * Eliminar una tarea de la base de datos.
     */
    public function destroy(Tarea $tarea)
    {
        $tarea->delete();
        return redirect()->route('tareas.index')->with('success', 'Trabajo eliminado exitosamente.');
    }

    // editar solo estado

    /**
 * Actualizar información específica de una tarea en la base de datos (solo estado, fecha cumplida, observación, y archivo).
 */
    public function updateEstado(Request $request, Tarea $tarea)
    {
        try {
            $validator = Validator::make($request->all(), [
                'estado' => 'required|string|in:Cumplida,En Proceso,Pendiente,Anulada',
                'fecha_cumplida' => [
                    'nullable',
                    'required_if:estado,Cumplida',
                    'date',
                    'after_or_equal:today',
                ],
                'observacion' => 'nullable|string',
                'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls,csv|max:5120',
                'saldo' => 'nullable|numeric|min:0', // <-- Nuevo campo validado
            ], [
                'fecha_cumplida.required_if' => 'La fecha de cumplimiento es obligatoria cuando el estado es "Cumplida"',
                'fecha_cumplida.after_or_equal' => 'La fecha no puede ser anterior a hoy'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('modal_id', $tarea->id_tareas);
            }

            DB::beginTransaction();

            // === ARCHIVO ===
            $archivoPath = $tarea->archivo;

            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');

                if (!$file->isValid()) {
                    throw new \Exception('El archivo subido no es válido.');
                }

                if ($tarea->archivo && Storage::disk('public')->exists($tarea->archivo)) {
                    Storage::disk('public')->delete($tarea->archivo);
                }

                $zipFileName = 'archivos_tareas/' . Str::uuid() . '.zip';
                $tempZipPath = storage_path('app/temp_' . Str::uuid() . '.zip');

                $zip = new ZipArchive();
                if ($zip->open($tempZipPath, ZipArchive::CREATE) === true) {
                    $zip->addFile($file->getRealPath(), $file->getClientOriginalName());
                    $zip->close();
                } else {
                    throw new \Exception('No se pudo comprimir el archivo.');
                }

                $archivoPath = Storage::disk('public')->putFileAs(
                    'archivos_tareas',
                    new \Illuminate\Http\File($tempZipPath),
                    basename($zipFileName)
                );

                unlink($tempZipPath);
            }

            // === ACTUALIZAR TAREA ===
            $tarea->update([
                'estado' => $request->estado,
                'fecha_cumplida' => $request->estado === 'Cumplida' ? $request->fecha_cumplida : null,
                'observacion' => $request->observacion,
                'archivo' => $archivoPath,
            ]);

            // === AUTO-COMPLETAR OBLIGACION VINCULADA ===
            if ($request->estado === 'Cumplida' && $tarea->obligacion_id) {
                $tarea->obligacion()->update([
                    'completado' => true,
                    'completado_en' => now(),
                ]);
            }

            // === ACTUALIZAR SALDO DEL CLIENTE (si se pasó) ===
            if ($tarea->cliente) {
                $saldo = is_null($request->saldo) || $request->saldo === '' ? 0 : $request->saldo;
                $tarea->cliente->update([
                    'saldo' => $saldo
                ]);
            }

            DB::commit();

            return redirect()
            ->route('tareas.index', [
                'filter' => $request->input('filter'),
                'search' => $request->input('search'),
                'month' => $request->input('month'),
                'year' => $request->input('year'),
                'page' => $request->input('page'),
            ])
            ->with('success', 'Tarea y cliente actualizados correctamente.');



        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_id', $tarea->id_tareas);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($archivoPath) && $archivoPath !== $tarea->archivo) {
                Storage::disk('public')->delete($archivoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->with('modal_id', $tarea->id_tareas);
        }
    }


    
    public function indexFiltrosAvanzados(Request $request)
    {
        // Obtener los filtros desde la URL
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $asesorId   = $request->query('asesor_id');
        $estado     = $request->query('estado');
        $estadoPago = $request->query('estado_pago'); // ✅ Nuevo filtro

        // Inicializar la consulta con relaciones necesarias
        $query = Tarea::with(['cliente.usuario', 'factura.cobro']);

        // Aplicar filtros si existen
        if ($fechaDesde) {
            $query->where('fecha_facturada', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->where('fecha_facturada', '<=', $fechaHasta);
        }

        if ($asesorId) {
            $query->where('id_usuario', $asesorId);
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        // Obtener las tareas base
        $todasLasTareas = $query->orderBy('fecha_facturada', 'desc')->get();

        // ✅ Filtrar por estado de pago si se seleccionó
        if ($estadoPago) {
            $todasLasTareas = $todasLasTareas->filter(function ($tarea) use ($estadoPago) {
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
            })->values(); // Re-index after filter
        }

        // ✅ Siempre convertir a paginador manual para que la vista reciba un LengthAwarePaginator
        $currentPage = max(1, (int) $request->input('page', 1));
        $perPage = 15;
        $tareas = new \Illuminate\Pagination\LengthAwarePaginator(
            $todasLasTareas->forPage($currentPage, $perPage),
            $todasLasTareas->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Obtener la lista de asesores
        $asesores = \App\Models\User::all();

        return view('tareas.filtros_avanzados', compact('tareas', 'asesores'));
    }


    
    // notificacion de cliente

    public function notificarCliente(Tarea $tarea, Request $request)
    {
        $cliente = Cliente::find($tarea->id_clientes);
        if (! $cliente || ! $cliente->telefono_cliente) {
            return back()->with('error', 'El cliente no tiene un número de WhatsApp registrado.');
        }
    
        $numero = preg_replace('/\D+/', '', $cliente->telefono_cliente);
        if (str_starts_with($numero, '0')) {
            $numero = '593' . substr($numero, 1); // Ecuador
        }
    
        $baseUrl = rtrim(config('services.whatsapp.url'), '/');
        $mensaje  = $request->input('mensaje');
        $tieneArchivo = $tarea->archivo && Storage::disk('public')->exists($tarea->archivo);

        if ($tieneArchivo) {
            $url     = $baseUrl . '/send-document';
            $base64  = base64_encode(Storage::disk('public')->get($tarea->archivo));
            $filename = basename($tarea->archivo);
            $payload = [
                'numero'   => $numero,
                'base64'   => $base64,
                'filename' => $filename,
                'caption'  => $mensaje,
            ];
        } else {
            $url     = $baseUrl . '/send-message';
            $payload = [
                'numero'  => $numero,
                'mensaje' => $mensaje,
            ];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'    => config('services.whatsapp.token'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post($url, $payload);

            if ($response->successful()) {
                $tarea->update(['notificado' => true]);
                if ($request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Mensaje enviado exitosamente.']);
                }
                return back()->with('success', 'Cliente notificado exitosamente.');
            }

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al enviar: ' . $response->body()]);
            }
            return back()->with('error', 'Error al enviar: ' . $response->body());

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    
    // VISTA DE CLINETES
    
    public function clientestareas(Request $request)
    {
        // Obtener el cliente autenticado usando el guard 'cliente'
        $cliente = auth()->guard('cliente')->user();
    
        // Verificar si el cliente está autenticado
        if (!$cliente) {
            return redirect()->route('cliente.login')->with('error', 'Debes iniciar sesión para ver tus tareas.');
        }
    
        // Inicializar la consulta base para las tareas del cliente
        $query = Tarea::where('id_clientes', $cliente->id_clientes);
    
        // Aplicar filtros
        $filter = $request->query('filter'); // Estado de la tarea
        $search = $request->query('search'); // Búsqueda por descripción o título
        $month = $request->query('month'); // Mes (puede estar vacío)
        $year = $request->query('year', date('Y')); // Año actual por defecto
    
        // Filtro por estado de la tarea
        if ($filter) {
            $query->where('estado', $filter);
        }
    
        // Filtro por búsqueda (descripción o título)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%");
            });
        }
    
        // Filtrar por año
        if ($year) {
            $query->whereYear('fecha_facturada', $year);
        }
    
        // Filtrar por mes solo si se selecciona un mes específico
        if ($month) {
            $query->whereMonth('fecha_facturada', $month);
        }
    
        // Obtener las tareas paginadas y ordenadas por fecha de facturación descendente
        $tareas = $query->orderBy('fecha_facturada', 'desc')->paginate(10);
    
        // Pasar los filtros actuales a la vista para mantenerlos en los formularios
        $filters = [
            'filter' => $filter,
            'search' => $search,
            'month' => $month,
            'year' => $year,
        ];
    
        // Retornar la vista con las tareas y los filtros
        return view('clientes.tareas', compact('tareas', 'filters'));
    }





    // transferir tarea
    public function formTransferir($id)
    {
        $tarea = Tarea::findOrFail($id);
        $usuarios = Usuario::all(); // o filtra si quieres solo 'Asesores' y 'Administradores'

        return view('tareas.transferir', compact('tarea', 'usuarios'));
    }

    public function transferir(Request $request, $id)
    {
        $request->validate([
            'id_usuario' => 'required|exists:users,id'
        ]);

        $tarea = Tarea::findOrFail($id);

        // Guardar el asesor original solo en la primera transferencia
        if ($tarea->transferida_de === null) {
            $tarea->transferida_de = $tarea->id_usuario;
        }

        $tarea->id_usuario = $request->id_usuario;
        $tarea->save();

        return redirect()->route('tareas.index')->with('success', 'Tarea transferida correctamente.');
    }



}

