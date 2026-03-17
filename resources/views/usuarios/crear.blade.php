@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fas fa-user-plus"></i> Crear Usuario</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Nombre:</label>
                    <input type="text" name="nombre" class="form-control" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Apellido:</label>
                    <input type="text" name="apellido" class="form-control" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" name="email" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-barcode"></i> Código:</label>
                    <input type="text" name="codigo" class="form-control" required maxlength="20">
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-lock"></i> Contraseña:</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <small class="form-text text-muted">
                        La contraseña debe tener al menos 8 caracteres y contener:
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success"></i> Al menos una letra mayúscula (A-Z)</li>
                            <li><i class="fas fa-check-circle text-success"></i> Al menos un número (0-9)</li>
                            <li><i class="fas fa-check-circle text-success"></i> Al menos un carácter especial (@$!%*?&.)</li>
                            <li><i class="fas fa-times-circle text-danger"></i> No puede contener espacios ni estos caracteres: ' " = ; ` & | < > ^ ( ) { } [ ]</li>
                        </ul>
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-toggle-on"></i> Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user-tag"></i> Roles:</label>
                    <select name="roles[]" class="form-select" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content gap-3 mt-4">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary px-4">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
            <button type="submit" class="btn btn-success" >
                <i class="bi bi-save"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection
