@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Transferir Tarea: {{ $tarea->titulo }}</h4>

    {{-- Mostrar a quién pertenece actualmente la tarea --}}
    <div class="alert alert-info">
        <strong>Pertenece a:</strong>
        @if ($tarea->usuario)
            {{ $tarea->usuario->nombre }} {{ $tarea->usuario->apellido }} (por transferencia)
        @elseif ($tarea->cliente && $tarea->cliente->usuario)
            {{ $tarea->cliente->usuario->nombre }} {{ $tarea->cliente->usuario->apellido }} (dueño del cliente)
        @else
            No asignado
        @endif
    </div>

    <form action="{{ route('tareas.transferir', $tarea->id_tareas) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="id_usuario" class="form-label">Selecciona un usuario</label>
            <select name="id_usuario" class="form-select" required>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Transferir</button>
        <a href="{{ route('tareas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
