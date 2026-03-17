@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Editar Usuario</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-fill"></i> Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $usuario->nombre }}" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-fill"></i> Apellido:</label>
                    <input type="text" name="apellido" class="form-control" value="{{ $usuario->apellido }}" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope-fill"></i> Email:</label>
                    <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-upc-scan"></i> Código:</label>
                    <input type="text" name="codigo" class="form-control" value="{{ $usuario->codigo }}" required maxlength="20">
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-toggle-on"></i> Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="Activo" {{ $usuario->estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ $usuario->estado == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-badge-fill"></i> Roles:</label>
                    <select name="roles[]" class="form-select" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ in_array($role, $userRole) ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Campo de Contraseña -->
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-lock-fill"></i> Nueva Contraseña:</label>
                    <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                    <small class="form-text text-muted">
                        En caso de cambiar la contraseña debe tener al menos 8 caracteres y contener:
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success"></i> Al menos una letra mayúscula (A-Z)</li>
                            <li><i class="fas fa-check-circle text-success"></i> Al menos un número (0-9)</li>
                            <li><i class="fas fa-check-circle text-success"></i> Al menos un carácter especial (@$!%*?&.)</li>
                            <li><i class="fas fa-times-circle text-danger"></i> No puede contener espacios ni estos caracteres: ' " = ; ` & | < > ^ ( ) { } [ ]</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content gap-3 mt-4">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary px-4">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
            <button type="submit" class="btn btn-primary px-4 ">
                <i class="bi bi-save"></i> Actualizar
            </button>
        </div>
    </form>
</div>
@endsection