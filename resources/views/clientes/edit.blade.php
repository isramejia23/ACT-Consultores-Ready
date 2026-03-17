@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i> Editar Cliente</h2>

    </div>

    <form action="{{ route('clientes.update', $cliente->id_clientes) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Primera columna -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i> Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre_cliente" class="form-control" id="nombre_cliente" 
                                   value="{{ $cliente->nombre_cliente }}" required maxlength="50">
                            <label for="nombre_cliente">Nombre del Cliente</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input 
                                           value="{{ $cliente->cedula_cliente }}" 
                                           type="text" 
                                            name="cedula_cliente" 
                                            class="form-control" 
                                            id="cedula_cliente" 
                                            required 
                                            minlength="10" 
                                            maxlength="13" 
                                            pattern="[0-9]{10,13}" 
                                            title="La cédula debe tener entre 10 y 13 dígitos numéricos"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" >
                                    <label for="cedula_cliente">Cédula</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
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
                                    <label for="telefono_cliente">Teléfono</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="email" name="email_cliente" class="form-control" id="email_cliente" 
                                   value="{{ $cliente->email_cliente }}" required maxlength="100">
                            <label for="email_cliente">Correo Electrónico</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" name="password" class="form-control" id="password" 
                                   minlength="6" placeholder=" ">
                            <label for="password">Nueva Contraseña</label>
                            <div class="form-text">Dejar vacío para mantener la contraseña actual</div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i> Asesor</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <select name="id_usuario" class="form-select" id="id_usuario" required
                                @cannot('ver-todos-clientes') disabled @endcannot>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ $cliente->id_usuario == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->nombre }} {{ $usuario->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="id_usuario">Asesor Asignado</label>
                            @cannot('ver-todos-clientes')
                                <input type="hidden" name="id_usuario" value="{{ $cliente->id_usuario }}">
                            @endcannot
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i> Información Comercial</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="regimen_id" class="form-select" id="regimen_id" required>
                                        @foreach($regimenes as $regimen)
                                            <option value="{{ $regimen->id }}" {{ $cliente->regimen_id == $regimen->id ? 'selected' : '' }}>
                                                {{ $regimen->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="regimen_id">Régimen</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="estado" class="form-select" id="estado" required>
                                        <option value="Activo" {{ $cliente->estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Inactivo" {{ $cliente->estado == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    <label for="estado">Estado</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="text" name="actividad" class="form-control" id="actividad" 
                                   value="{{ $cliente->actividad }}" maxlength="200">
                            <label for="actividad">Actividad Económica</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" name="direccion" class="form-control" id="direccion" 
                                   value="{{ $cliente->direccion }}" maxlength="200">
                            <label for="direccion">Dirección</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="text" name="claves" class="form-control" id="claves" 
                                   value="{{$cliente->claves }}" maxlength="200">
                            <label for="claves">Clave SRI</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fecha_firma" class="form-control" id="fecha_firma" 
                                           value="{{ $cliente->fecha_firma }}">
                                    <label for="fecha_firma">Fecha de Firma</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fecha_facturacion" class="form-control" id="fecha_facturacion" 
                                           value="{{  $cliente->fecha_facturacion}}">
                                    <label for="fecha_facturacion">Fecha de Plan de Facturación</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" name="saldo" class="form-control" id="saldo" 
                                           min="0" step="0.01" value="{{ $cliente->saldo }}">
                                    <label for="saldo">Saldo ($)</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Regresar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Actualizar Cliente
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </form>
</div>

<style>
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .card-header {
        border-radius: 0 !important;
    }
    
    .form-floating label {
        color: #6c757d;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label {
        color: #0d6efd;
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
</style>
@endsection