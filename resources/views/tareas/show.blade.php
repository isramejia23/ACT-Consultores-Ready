@extends('layouts.app')

@section('content')
<h1>Detalles de la Tarea</h1>

<p><strong>Cliente:</strong> {{ $tarea->cliente->nombre_cliente }}</p>
<p><strong>Número de Factura:</strong> {{ $tarea->numero_factura }}</p>
<p><strong>Fecha Facturada:</strong> {{ $tarea->fecha_facturada }}</p>
<p><strong>Nombre:</strong> {{ $tarea->nombre }}</p>
<p><strong>Estado:</strong> {{ $tarea->estado }}</p>
<p><strong>Fecha Cumplida:</strong> {{ $tarea->fecha_cumplida ?? 'No aplica' }}</p>
<tr>
    <th>Archivo:</th>
    <td>
        @if ($tarea->archivo)
            <a href="{{ asset('storage/' . $tarea->archivo) }}" target="_blank">Ver Archivo</a>
        @else
            No hay archivo adjunto
        @endif
    </td>
</tr>

<p><strong>Cantidad:</strong> {{ $tarea->cantidad }}</p>
<p><strong>Precio Unitario:</strong> ${{ $tarea->precio_unitario }}</p>
<p><strong>Total:</strong> ${{ $tarea->total }}</p>
<p><strong>Observación:</strong> {{ $tarea->observacion ?? 'No hay observaciones' }}</p>

<a href="{{ route('tareas.index') }}" class="btn btn-secondary">Volver a la lista</a>
<a href="{{ route('tareas.edit', $tarea->id_tareas) }}" class="btn btn-warning">Editar</a>
<form action="{{ route('tareas.destroy', $tarea->id_tareas) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">Eliminar</button>
</form>
@endsection
