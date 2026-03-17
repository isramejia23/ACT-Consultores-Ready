@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 d-flex align-items-center">
        <i class="bi bi-list-check me-2"></i> Reportes
    </h1>

        <form action="{{ route('tareas.filtros_avanzados') }}" method="GET" class="mb-4 p-3 bg-light rounded shadow-sm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_desde"><i class="bi bi-calendar-event"></i> Fecha Facturación Desde:</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-3">
                    <label for="fecha_hasta"><i class="bi bi-calendar-event"></i> Fecha Facturación Hasta:</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-md-3">
                    <label for="asesor_id"><i class="bi bi-person-badge"></i> Asesor:</label>
                    <select name="asesor_id" id="asesor_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($asesores as $asesor)
                            <option value="{{ $asesor->id }}" {{ request('asesor_id') == $asesor->id ? 'selected' : '' }}>
                                {{ $asesor->nombre }} {{ $asesor->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="estado"><i class="bi bi-flag"></i> Estado:</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Cumplida" {{ request('estado') == 'Cumplida' ? 'selected' : '' }}>Cumplida</option>
                        <option value="Anulada" {{ request('estado') == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                        <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                    </select>
                </div>

                <!-- Nuevo campo Estado de Pago -->
                <div class="col-md-3">
                    <label for="estado_pago"><i class="bi bi-cash-coin"></i> Estado de Pago:</label>
                    <select name="estado_pago" id="estado_pago" class="form-select">
                        <option value="">Todos</option>
                        <option value="Cancelada" {{ request('estado_pago') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="Con Abono" {{ request('estado_pago') == 'Con Abono' ? 'selected' : '' }}>Con Abono</option>
                        <option value="Pendiente" {{ request('estado_pago') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('tareas.filtros_avanzados') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Restablecer Filtros
                    </a>
                {{--
                    Reporte PDF SImple

                    <a href="{{ route('tareas.reporte', request()->query()) }}" target="_blank" class="btn btn-success">
                        <i class="bi bi-printer"></i> Imprimir Reporte
                    </a>

                    Reporte PDF Agrupado 

                    <a href="{{ route('tareas.reporte_agrupado', request()->query()) }}" target="_blank" class="btn btn-success">
                        <i class="bi bi-printer"></i> Imprimir Reporte por Asesor
                    </a>
                --}}
                    <a href="{{ route('reporte.tareas.excel', [
                        'fecha_desde' => request('fecha_desde'),
                        'fecha_hasta' => request('fecha_hasta'),
                        'estado' => request('estado')
                        ]) }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Reporte Por Asesor
                    </a>

                    <a href="{{ route('tareas.excelConPagos', [
                            'fecha_desde' => request('fecha_desde'),
                            'fecha_hasta' => request('fecha_hasta'),
                            'estado' => request('estado'),
                            'estado_pago' => request('estado_pago')
                        ]) }}" 
                    class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Reporte por Fecha Facturación
                    </a>

                    <a href="{{ route('tareas.exportarPorFechaCumplida', request()->query()) }}"
                        class="btn btn-outline-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Reporte por Fecha Cumplida
                    </a>

                    <a href="{{ route('tareas.excelPorFechaCobro', request()->query() ) }}"
                        class="btn btn-outline-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Reporte Por Fecha Pago
                    </a>

                    <a href="{{ route('reporte.cobrosPorAsesor', [
                        'fecha_desde' => request('fecha_desde'),
                        'fecha_hasta' => request('fecha_hasta'),
                        ]) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Cobros Por Asesor
                    </a>
                    
                </div>
            </div>
        </form>

    <div class="table-responsive">
        <table class="table table-hover shadow-sm">
            <thead class="table-dark text-center">
                <tr>
                    <th><i class="bi bi-person"></i> Cliente</th>
                    <th><i class="bi bi-journal-medical"></i> Descripción</th>
                    <th><i class="bi bi-calendar-event"></i> Fecha Facturada</th>
                    <th><i class="bi bi-calendar-event"></i> Fecha Cumplida</th>
                    <th><i class="bi bi-flag"></i> Estado</th>
                    <th><i class="bi bi-person-badge-fill"></i> Asesor</th>
                    <th><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tareas as $tarea)
                    <tr>
                        <td>{{ optional($tarea->cliente)->nombre_cliente ?? 'Sin cliente' }}</td>
                        <td>{{ $tarea->nombre }}</td>
                        <td>@formatoFecha($tarea->fecha_facturada)</td>
                        <td>
                            @if($tarea->fecha_cumplida)
                                @formatoFecha($tarea->fecha_cumplida)
                            @endif
                        </td>
                        <td>
                            <span class="badge 
                                {{ $tarea->estado == 'Pendiente' ? 'text-danger border border-danger' : '' }}
                                {{ $tarea->estado == 'Cumplida' ? 'text-success border border-success' : '' }}
                                {{ $tarea->estado == 'Anulada' ? 'text-secondary border border-secondary' : '' }}
                                {{ $tarea->estado == 'En Proceso' ? 'text-warning border border-warning' : '' }}">
                                {{ $tarea->estado }}
                            </span>
                        </td>
                        <td>
                            @if ($tarea->usuario)
                                {{ $tarea->usuario->nombre }} {{ $tarea->usuario->apellido }}
                                @if ($tarea->transferida_de)
                                    <small class="text-muted">(Transferido)</small>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>

                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detalleTareaModal{{ $tarea->id_tareas }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            @can('editar-tarea')
                                <a href="{{ route('tareas.edit', $tarea->id_tareas) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                            @can('borrar-tarea')
                                <form action="{{ route('tareas.destroy', $tarea->id_tareas) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>

                    <!-- Modal de Detalles -->
                    <div class="modal fade" id="detalleTareaModal{{ $tarea->id_tareas }}" tabindex="-1" aria-labelledby="detalleTareaModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Detalles de la Tarea</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Cliente:</strong> {{ optional($tarea->cliente)->nombre_cliente ?? 'Sin cliente' }}</p>
                                        <p><strong>Descripción:</strong> {{ $tarea->nombre }}</p>
                                        <p><strong>Fecha Facturada:</strong> @formatoFecha($tarea->fecha_facturada)</p>
                                                                                <p><strong>Fecha Cumplida:</strong> 
                                            @if($tarea->fecha_cumplida)
                                                <i class="bi bi-check-circle text-success"></i> @formatoFecha($tarea->fecha_cumplida)
                                            @else
                                                <i class="bi bi-clock text-muted"></i> <span class="text-muted">Pendiente</span>
                                            @endif
                                        </p>
                                        <p><strong>Observaciones:</strong> {{ $tarea->observacion ?? 'Sin observaciones' }}</p>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-paperclip"></i> Archivo:</strong>
                                            <p class="mb-0">
                                                @if ($tarea->archivo)
                                                    <a href="{{ asset('storage/' . $tarea->archivo) }}" target="_blank" class="text-primary">
                                                        <i class="bi bi-file-earmark"></i> Ver Archivo
                                                    </a>
                                                @else
                                                    <span class="text-muted">No hay archivo adjunto</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Estado:</strong> 
                                            <span class="badge px-3 py-2 border rounded-pill 
                                                {{ $tarea->estado == 'Pendiente' ? 'text-danger border-danger' : '' }}
                                                {{ $tarea->estado == 'Cumplida' ? 'text-success border-success' : '' }}
                                                {{ $tarea->estado == 'Anulada' ? 'text-secondary border-secondary' : '' }}
                                                {{ $tarea->estado == 'En Proceso' ? 'text-warning border-warning' : '' }}">
                                                {{ $tarea->estado }}
                                            </span>
                                        </p>
                                        <p><strong>Cantidad:</strong> {{ $tarea->cantidad }}</p>
                                        <p><strong>Precio Unitario:</strong> {{ $tarea->precio_unitario }}</p>
                                        <p><strong>Total:</strong> {{ $tarea->total }}</p>
                                        <p><strong>Saldo del Cliente:</strong> $ {{ optional($tarea->cliente)->saldo ?? '0.00' }}</p>
                                        <p><strong>Asesor:</strong> 
                                            @if ($tarea->usuario)
                                                {{ $tarea->usuario->nombre }} {{ $tarea->usuario->apellido }} (por transferencia)
                                            @elseif (optional($tarea->cliente)->usuario)
                                                {{ $tarea->cliente->usuario->nombre }} {{ $tarea->cliente->usuario->apellido }} (dueño del cliente)
                                            @else
                                                No asignado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No se encontraron tareas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $tareas->links() }}
    </div>
</div>
@endsection