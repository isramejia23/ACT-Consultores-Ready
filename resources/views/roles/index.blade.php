{{-- resources/views/roles/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="d-flex align-items-center">
            <i class="bi bi-person-gear me-2"></i> Gestión de Roles
        </h1>

        @can('crear-rol')
            <a href="{{ route('roles.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle"></i> Crear Nuevo Rol
            </a>
        @endcan
    </div>

    <!-- @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif -->

    <div class="table-responsive">
        <table class="table table-hover table-bordered shadow-sm">
            <thead class="table-dark text-center">
                <tr>
                    <th><i class="bi bi-person-badge"></i> Nombre</th>
                    <th><i class="bi bi-tools"></i> Acciones</th>
                </tr>
            </thead>
            <tbody class="text">
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>
                            @can('editar-rol')
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="bi bi-pencil"></i> 
                                </a>
                            @endcan

                            @can('borrar-rol')
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $roles->links() }}
</div>

@endsection
