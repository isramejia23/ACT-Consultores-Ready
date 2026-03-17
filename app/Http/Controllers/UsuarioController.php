<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
       $this->middleware('permission:ver-usuario|crear-usuario|editar-usuario|borrar-usuario', ['only' => ['index']]);
       $this->middleware('permission:crear-usuario', ['only' => ['create', 'store']]);
       $this->middleware('permission:editar-usuario', ['only' => ['edit', 'update']]);
       $this->middleware('permission:borrar-usuario', ['only' => ['destroy']]);
    }

    public function index()
    {
        $usuarios = User::with(['roles', 'clientes'])
            ->withCount('clientes') // Esto agregará un campo clientes_count
            ->paginate(10);
    
        return view('usuarios.index', compact('usuarios'));
    }
    

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('usuarios.crear', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'codigo' => 'required|string|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]+$/', // Mayúscula, número y carácter especial
                'regex:/^[^\s\'"=;`&|<>\^(){}\[\]]+$/', // Evita caracteres no permitidos
            ],
            'estado' => 'required|in:Activo,Inactivo',
            'roles' => 'required|array',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial (@$!%*?&.), y no puede contener espacios ni caracteres no permitidos.',
            'roles.required' => 'Debe seleccionar al menos un rol.',
        ]);
    
        // Si la validación falla, redirige con errores y datos ingresados
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Envía los errores de validación
                ->withInput(); // Mantiene los datos del formulario
        }
    
        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'codigo'=> $request->codigo,
            'password' => Hash::make($request->password),
            'estado' => $request->estado,
        ]);
    
        $user->assignRole($request->roles);
    
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        return view('usuarios.show', compact('user'));
    }

    public function edit(User $usuario)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $usuario->roles->pluck('name')->toArray();
        return view('usuarios.editar', compact('usuario', 'roles', 'userRole'));
    }
    
    public function update(Request $request, $id)
    {
        // Busca el usuario por ID
        $user = User::findOrFail($id);
    
        // Validación de los campos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $id,
            'codigo' => 'required|string|unique:users,codigo,' . $id,
            'estado' => 'required|in:Activo,Inactivo',
            'roles' => 'required|array',
            'password' => [
                'nullable', // La contraseña es opcional en la actualización
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]+$/', // Mayúscula, número y carácter especial
                'regex:/^[^\s\'"=;`&|<>\^(){}\[\]]+$/',
            ],
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial (@$!%*?&.), y no puede contener espacios ni caracteres no permitidos.',
            'roles.required' => 'Debe seleccionar al menos un rol.', // Mensaje personalizado para roles
        ]);
    
        // Si la validación falla, redirige con errores y datos ingresados
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Envía los errores de validación
                ->withInput(); // Mantiene los datos del formulario
        }
    
        // Obtiene todos los datos del formulario
        $input = $request->all();
    
        // Si se envía una nueva contraseña, se actualiza
        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
        } else {
            // Si no se envía, se excluye del array de entrada
            $input = Arr::except($input, ['password']);
        }
    
        // Actualiza el usuario
        $user->update($input);
    
        // Actualiza los roles del usuario
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        $user->assignRole($request->roles);
    
        // Redirecciona con un mensaje de éxito
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }
    

    public function destroy($id)
    {
        try {
            // Busca el usuario por ID
            $user = User::findOrFail($id);
    
            // Depuración: Verifica el usuario encontrado
            // dd('Usuario encontrado:', $user);
            if ($user->clientes()->exists()) {
                return redirect()->route('usuarios.index')->with('error', 'No se puede eliminar el usuario porque tiene clientes asociados.');
            }
            // Elimina los roles asociados al usuario
            DB::table('model_has_roles')->where('model_id', $user->id)->delete();
    
            // Elimina el usuario
            $user->delete();
    
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
        }
    }
}
