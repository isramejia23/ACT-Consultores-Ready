@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-people-fill"></i> Lista de Usuarios</h2>
        @can('crear-usuario') 
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary mb-4">
                <i class="bi bi-plus-circle"></i> Crear Usuario
            </a>
        @endcan


        <div class="table-responsive">
            <table class="table table-hover  shadow-sm">
            <!-- <table class="table table-hover table-borderless shadow-sm"> tabla sin bordes y con table-borderes-->
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-person"></i> Nombre</th>
                        <th><i class="bi bi-envelope"></i> Email</th>
                        <th><i class="bi bi-upc"></i> Código</th>
                        <th><i class="bi bi-people"></i> Clientes</th> 
                        <th><i class="bi bi-info-circle"></i> Estado</th>
                        <th><i class="bi bi-shield"></i> Rol(es)</th>
                        <th><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->codigo }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $usuario->clientes_count }} <!-- Muestra el conteo -->
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                    {{ $usuario->estado == 'Activo' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $usuario->estado }}
                                </span>
                            </td>
                            
                            <td>
                                @foreach($usuario->roles as $role)
                                    <span class="badge bg-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @can('editar-usuario') 
                                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> 
                                        </a>
                                    @endcan
                                    @can('borrar-usuario')
                                    <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
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
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $usuarios->links() }}
    </div>

    <script>
        function confirmDelete(event, button) {
            event.preventDefault();
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                button.closest('form').submit();
            }
        }
    </script>
@endsection