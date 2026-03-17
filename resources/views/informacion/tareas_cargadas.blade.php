@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tareas Cargadas</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" action="{{ route('tareas.cargadas') }}" class="row mb-4">
        <div class="col-md-4">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por tarea o cliente..." value="{{ request('busqueda') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
        <div class="col-md-1">
            <a href="{{ route('tareas.cargadas') }}" class="btn btn-secondary w-100">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>


    <table class="table">
        <thead>
            <tr>
                <th><i class="bi bi-receipt"></i> Número Factura</th>
                <th><i class="bi bi-calendar"></i> Fecha</th>
                <th><i class="bi bi-person"></i> Nombre Cliente</th>
                <th><i class="bi bi-journal-medical"></i> Trabajo</th>
                <th><i class="bi bi-gear"></i> Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tareasCargadas as $tarea)
                <tr>
                    <td>{{ $tarea->numfac }}</td>
                    <td>@formatoFecha($tarea->fecha) </td>
                    <td>{{ $tarea->nombre_cliente }}</td>
                    <td>{{ $tarea->nombre }}</td>
                    <td>
                        <div class="d-flex gap-2"> <!-- Espaciado uniforme entre botones -->
                            <!-- Botón para crear cliente -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                                data-bs-target="#createClientModal{{ $tarea->id }}" 
                                data-nombre-cliente="{{ $tarea->nombre_cliente }}">
                                <i class="bi bi-person-add"></i> 
                            </button>

                            <!-- Botón para eliminar -->
                             @can('borrar-tarea-cargada')
                                <form action="{{ route('tareas.cargadas.eliminar', $tarea->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                        <!-- Modal para Crear Cliente -->
                        <div class="modal fade" id="createClientModal{{ $tarea->id }}" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg"> <!-- Aumentamos el tamaño del modal -->
                                <form action="{{ route('clientes.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="createClientModalLabel">
                                                <i class="bi bi-person-plus"></i> Crear Cliente
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Nombre Cliente -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-person"></i> Nombre Cliente</label>
                                                    <input type="text" class="form-control" name="nombre_cliente" value="{{ $tarea->nombre_cliente }}" required readonly>
                                                </div>
                                                <!-- Cédula Cliente -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-credit-card"></i> Cédula Cliente</label>
                                                        <input 
                                                            type="text" 
                                                            name="cedula_cliente" 
                                                            class="form-control" 
                                                            id="cedula_cliente" 
                                                            required 
                                                            minlength="10" 
                                                            maxlength="13" 
                                                            pattern="[0-9]{10,13}" 
                                                            title="La cédula debe tener entre 10 y 13 dígitos numéricos"
                                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                                            value="{{ $tarea->cedula }}" 
                                                            required readonly>
                                                </div>
                                                <!-- Régimen -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-list-check"></i> Régimen</label>
                                                    <select class="form-select" name="regimen_id" required>
                                                        @foreach(\App\Models\Regimen::all() as $regimenOption)
                                                            <option value="{{ $regimenOption->id }}">{{ $regimenOption->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Estado -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-toggle-on"></i> Estado</label>
                                                    <select class="form-select" name="estado" required>
                                                        <option value="Activo">Activo</option>
                                                        <option value="Inactivo">Inactivo</option>
                                                    </select>
                                                </div>
                                                <!-- Correo Cliente -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-envelope"></i> Correo Cliente</label>
                                                    <input type="email" class="form-control" name="email_cliente" value="{{ old('email_cliente') }}">
                                                </div>
                                                <!-- Actividad -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-briefcase"></i> Actividad</label>
                                                    <input type="text" class="form-control" name="actividad">
                                                </div>
                                                <!-- Clave SRI-->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-briefcase"></i> Clave SRI</label>
                                                    <input type="text" class="form-control" name="claves">
                                                </div>

                                                <!-- Fecha Firma -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-calendar"></i> Fecha de Firma</label>
                                                    <input type="date" class="form-control" name="fecha_firma">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-calendar"></i> Fecha de Plan de Facturacion</label>
                                                    <input type="date" class="form-control" name="fecha_facturacion">
                                                </div>
                                                <!-- Saldo -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-cash"></i> Saldo</label>
                                                    <input type="number" class="form-control" name="saldo" step="0.01" placeholder="0.00">
                                                </div>
                                                <!-- Teléfono Cliente -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-telephone"></i> Teléfono Cliente</label>
                                                    <input 
                                                    type="tel" 
                                                    name="telefono_cliente" 
                                                    class="form-control" 
                                                    id="telefono_cliente" 
                                                    value="{{ old('telefono_cliente', $cliente->telefono_cliente ?? '') }}" 
                                                    required 
                                                    minlength="10" 
                                                    maxlength="10" 
                                                    pattern="[0-9]{10}" 
                                                    title="El teléfono debe tener 10 dígitos numéricos (sin espacios ni guiones)"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" >
                                                </div>
                                                <!-- Contraseña -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-key"></i> Contraseña</label>
                                                    <input type="password" class="form-control" name="password" value="{{ $tarea->cedula }}" required>
                                                </div>
                                                <!-- Dirección -->
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"><i class="bi bi-geo-alt"></i> Dirección</label>
                                                    <input type="text" class="form-control" name="direccion" value="{{ $tarea->direccion }}"required readonly>
                                                </div>
                                                <!-- Usuario -->
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label"><i class="bi bi-person-badge"></i> Usuario</label>
                                                        <select class="form-select" name="id_usuario" required @cannot('ver-todas-tareas') disabled @endcannot>
                                                            @cannot('ver-todas-tareas')
                                                                <option value="{{ auth()->user()->id }}" selected>
                                                                    {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                                                                </option>
                                                            @else
                                                                @foreach($usuarios as $usuario)
                                                                    <option value="{{ $usuario->id }}">
                                                                        {{ $usuario->nombre }} {{ $usuario->apellido }}
                                                                    </option>
                                                                @endforeach
                                                            @endcannot
                                                        </select>
                                                        @cannot('ver-todas-tareas')
                                                            <input type="hidden" name="id_usuario" value="{{ auth()->user()->id }}">
                                                        @endcannot
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle"></i> Cerrar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save"></i> Guardar Cliente
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>            
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $tareasCargadas->links() }}
    </div>
</div>
@endsection
