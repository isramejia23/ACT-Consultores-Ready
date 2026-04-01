<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Regimen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\TareaImportService;
use App\Services\GeneradorVencimientos;


class ClienteController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
       $this->middleware('permission:ver-cliente|crear-cliente|editar-cliente|borrar-cliente', ['only' => ['index']]);
       $this->middleware('permission:crear-cliente', ['only' => ['create', 'store']]);
       $this->middleware('permission:editar-cliente', ['only' => ['edit', 'update']]);
       $this->middleware('permission:borrar-cliente', ['only' => ['destroy']]);
       $this->middleware('permission:mensajes-cliente', ['only' => ['notificarCliente']]);
    }


        public function index(Request $request)
    {
        $user = Auth::user();
        $asesorId = $request->query('asesor_id');
        
        // Inicializa la consulta de clientes
        $query = Cliente::query();

        // Filtro por acceso: si no tiene permiso global, solo ve sus clientes
        if (!$user->can('ver-todos-clientes')) {
            $query->where('id_usuario', $user->id);
        }

        // Filtro por asesor (solo si tiene acceso global)
        if ($user->can('ver-todos-clientes') && $asesorId) {
            $query->where('id_usuario', $asesorId);
        }

        // Filtro por búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cedula_cliente', 'like', "%{$search}%")
                ->orWhere('nombre_cliente', 'like', "%{$search}%");
            });
        }

        // Filtro por régimen
        if ($request->has('regimen_id') && $request->regimen_id != '') {
            $query->where('regimen_id', $request->regimen_id);
        }

        // Obtener clientes con paginación
        $clientes = $query->select([
            'id_clientes',
            'nombre_cliente', 
            'cedula_cliente', 
            'email_cliente', 
            'telefono_cliente', 
            'regimen_id',
            'saldo',
            'id_usuario',
        ])->paginate(10);

        // Mantener parámetros en los links de paginación
        $clientes->appends($request->query());

        // Lista de regímenes disponibles
        $regimenes = Regimen::all();

        // Obtener lista de asesores para el filtro (solo para administradores)
        $asesores = $user->can('ver-todos-clientes') ? User::all() : collect();

        return view('clientes.index', compact('clientes', 'regimenes', 'asesores'));
    }


    public function show($id)
    {
        $cliente = Cliente::with(['regimen', 'usuario', 'obligaciones.tipoObligacion', 'obligaciones.catalogoServicio'])->find($id);
        if ($cliente) {
            $obligaciones = $cliente->obligaciones()->with(['tipoObligacion', 'catalogoServicio'])
                ->orderBy('fecha_vencimiento', 'desc')
                ->get();

            $tareas = \App\Models\Tarea::where('id_clientes', $cliente->id_clientes)
                ->with('factura')
                ->orderBy('fecha_facturada', 'desc')
                ->get();

            return view('clientes.show', compact('cliente', 'obligaciones', 'tareas'));
        }
        return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
    }

    public function create()
    {
        $regimenes = Regimen::all();
    
        // Obtener el usuario autenticado
        $usuarioAuth = auth()->user();
    
        // Si tiene acceso global puede asignar a cualquier usuario, si no, solo a sí mismo
        if ($usuarioAuth->can('ver-todos-clientes')) {
            $usuarios = \App\Models\User::all();
        } else {
            $usuarios = [$usuarioAuth];
        }
    
        return view('clientes.create', compact('usuarios', 'regimenes'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_cliente' => 'required|string|max:255',
            'cedula_cliente' => 'required|string|digits_between:10,13|unique:clientes,cedula_cliente',
            'telefono_cliente' => 'required|string|digits:10',
            'regimen_id' => 'required|exists:regimenes,id',
            'estado' => 'required|in:Activo,Inactivo',
            'actividad' => 'nullable|string',
            'claves' => 'nullable|string',
            'fecha_facturacion'=> 'nullable|date',
            'fecha_firma' => 'nullable|date',
            'saldo' => 'nullable|numeric|min:0',
            'email_cliente' => 'required|string|email|unique:clientes',
            'password' => 'required|string|min:6',
            'direccion' => 'nullable|string',
            'id_usuario' => 'required|exists:users,id',
        ],[
            'cedula_cliente.unique' => 'Esta cédula ya está registrada en nuestro sistema',
            'cedula_cliente.digits_between' => 'La cédula debe tener entre 10 y 13 dígitos',
            'email_cliente.unique'=>'Correo ya registrado en nuestro sistema'
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('clientes.create')
                            ->withErrors($validator)
                            ->withInput();
        }
        
        try {
            DB::beginTransaction();

            // Crear el cliente en la base de datos
            $cliente = Cliente::create([
                'nombre_cliente' => $request->nombre_cliente,
                'cedula_cliente' => $request->cedula_cliente,
                'telefono_cliente' => $request->telefono_cliente,
                'regimen_id' => $request->regimen_id,
                'estado' => $request->estado,
                'actividad' => $request->actividad,
                'claves' => $request->claves,
                'fecha_firma' => $request->fecha_firma,
                'fecha_facturacion' => $request->fecha_facturacion,
                'saldo' => $request->saldo,
                'email_cliente' => $request->email_cliente,
                'password' => Hash::make($request->password),
                'direccion' => $request->direccion,
                'id_usuario' => $request->id_usuario,
            ]);

            // Procesar tareas cargadas: crear facturas, tareas y limpiar tareas_cargadas
            $importService = app(TareaImportService::class);
            $importService->procesarTareasCargadas($cliente);

            // Generar obligaciones automáticas del régimen para el mes actual
            $generador = app(GeneradorVencimientos::class);
            $generador->generarParaCliente($cliente);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('clientes.create')
                ->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }

        return redirect()->route('clientes.show', $cliente->id_clientes)->with('success', 'Cliente creado con éxito. Puede agregar obligaciones específicas desde aquí.');
    }
    

    public function edit($id)
    {
        $cliente = Cliente::find($id);
        $usuarios = \App\Models\User::all();

        if (!$cliente) {
            return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
        }

        $regimenes = Regimen::all();

        return view('clientes.edit', compact('cliente', 'regimenes', 'usuarios'));
    }

    
    public function update(Request $request, $id_clientes)
    {
        $cliente = Cliente::find($id_clientes);
        
        if (!$cliente) {
            return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
        }
    
        $validator = Validator::make($request->all(), [
            'nombre_cliente' => 'required|string|max:255',
            'cedula_cliente' => 'required|string|digits_between:10,13|unique:clientes,cedula_cliente,' . $id_clientes . ',id_clientes',
            'telefono_cliente' => 'required|string|digits:10',
            'regimen_id' => 'required|exists:regimenes,id',
            'estado' => 'required|in:Activo,Inactivo',
            'actividad' => 'nullable|string',
            'claves' => 'nullable|string',
            'fecha_firma' => 'nullable|date',
            'fecha_facturacion'=> 'nullable|date',
            'saldo' => 'nullable|numeric|min:0',
            'email_cliente' => 'required|string|email|unique:clientes,email_cliente,' . $id_clientes . ',id_clientes',
            'password' => 'nullable|string|min:6',
            'direccion' => 'nullable|string',
        ],[
            'cedula_cliente.unique' => 'Esta cédula ya está registrada en nuestro sistema',
            'cedula_cliente.digits_between' => 'La cédula debe tener entre 10 y 13 dígitos',
            'email_cliente.unique'=>'Correo ya registrado en nuestro sistema'
        ]);
    
        // Solo validar el campo id_usuario si tiene acceso global
        if (auth()->user()->can('ver-todos-clientes')) {
            $validator->addRules([
                'id_usuario' => 'required|exists:users,id',
            ]);
        }
    
        if ($validator->fails()) {
            return redirect()->route('clientes.edit', $id_clientes)
                            ->withErrors($validator)
                            ->withInput();
        }
    
        // Construcción de los datos a actualizar
        $datosActualizados = [
            'nombre_cliente' => $request->nombre_cliente,
            'cedula_cliente' => $request->cedula_cliente, // El mutator calculará automáticamente el dígito
            'telefono_cliente' => $request->telefono_cliente,
            'regimen_id' => $request->regimen_id,
            'estado' => $request->estado,
            'actividad' => $request->actividad,
            'claves' => $request->claves,
            'fecha_firma' => $request->fecha_firma,
            'fecha_facturacion' => $request->fecha_facturacion,
            'saldo' => $request->saldo,
            'email_cliente' => $request->email_cliente,
            'direccion' => $request->direccion,
        ];
    
        // Solo actualizar password si se proporcionó uno nuevo
        if ($request->password) {
            $datosActualizados['password'] = Hash::make($request->password);
        }
    
        // Si tiene acceso global, permitir actualizar el id_usuario
        if (auth()->user()->can('ver-todos-clientes')) {
            $datosActualizados['id_usuario'] = $request->id_usuario;
        }
    
        // Detectar cambios antes de actualizar
        $regimenCambio = $cliente->regimen_id != $request->regimen_id;
        $pasaAInactivo = $cliente->estado === 'Activo' && $request->estado === 'Inactivo';

        // Actualizar el cliente - el mutator setCedulaClienteAttribute se ejecutará automáticamente
        $cliente->update($datosActualizados);

        // Si el cliente pasó a Inactivo, anular sus obligaciones pendientes
        if ($pasaAInactivo) {
            $cliente->obligaciones()
                ->where('estado', 'pendiente')
                ->update(['estado' => 'anulada']);
        }

        // Si cambió el régimen, eliminar obligaciones de régimen pendientes y generar las nuevas
        if ($regimenCambio) {
            // Eliminar solo las obligaciones ligadas al régimen (tipo_obligacion_id) que no estén completadas
            $cliente->obligaciones()
                ->whereNotNull('tipo_obligacion_id')
                ->where('completado', false)
                ->delete();

            // Generar las obligaciones del nuevo régimen
            $generador = app(GeneradorVencimientos::class);
            $generador->generarParaCliente($cliente->fresh());
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado con éxito');
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente');
    }


    //     Notificar cliente 
    public function notificarCliente(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id_clientes',
            'mensaje'    => 'required|string',
        ]);

        $cliente = Cliente::find($request->input('id_cliente'));
        if (! $cliente || ! $cliente->telefono_cliente) {
            return back()->with('error', 'El cliente no tiene un número de WhatsApp registrado.');
        }

        $numero = preg_replace('/\D+/', '', $cliente->telefono_cliente);
        if (str_starts_with($numero, '0')) {
            $numero = '593' . substr($numero, 1); // Ecuador
        }

        $urlText = rtrim(env('WHATSAPP_API_URL'), '/') . '/send-message';
        $mensaje = $request->input('mensaje');

        $payload = [
            'numero'  => $numero,
            'mensaje' => $mensaje,
        ];

        try {
            $response = Http::withHeaders([
                'x-api-key'    => env('WHATSAPP_API_TOKEN'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($urlText, $payload);

            if ($response->successful()) {
                return back()->with('success', 'Mensaje enviado exitosamente.');
            }

            return back()->with('error', 'Error al enviar el mensaje: ' . $response->body());

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }
}