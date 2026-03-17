@extends('layouts.app')

@section('content')
    <div class="container">
        <h2><i class="bi bi-list-task"></i> Lista de Trabajos</h2>

        <a href="{{ route('tareas.create') }}" class="btn btn-primary mb-4">
            <i class="bi bi-file-earmark-plus"></i> Crear Nueva Tarea
        </a>
        <div class="alert alert-info">
            <strong>Total acumulado:</strong> ${{ number_format($totalSuma, 2) }}
        </div>
        

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form action="{{ route('tareas.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Búsqueda por cédula -->
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Búsqueda por Cédula o Nombre Cliente o Nombre Tarea" value="{{ $filters['search'] ?? '' }}">
                        </div>

                        <!-- Filtro por estado -->
                        <div class="col-md-3">
                            <select name="filter" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="Pendiente" {{ ($filters['filter'] ?? '') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="En Proceso" {{ ($filters['filter'] ?? '') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Cumplida" {{ ($filters['filter'] ?? '') == 'Cumplida' ? 'selected' : '' }}>Cumplida</option>
                                <option value="Anulada" {{ ($filters['filter'] ?? '') == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                            </select>
                        </div>

                        <!-- Filtro por mes -->

                        <div class="col-md-2">
                            <select name="month" class="form-select">
                                <option value="">Todos los meses </option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ ($filters['month'] ?? '') == $i ? 'selected' : '' }}>
                                        {{ ucfirst(\Carbon\Carbon::create(null, $i)->translatedFormat('F')) }}
                                    </option>
                                @endfor
                            </select>
                        </div>


                        <!-- Filtro por año -->
                        <div class="col-md-2">
                            <select name="year" class="form-select">
                                <option value="">Todos los años</option>
                                @for ($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ ($filters['year'] ?? '') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Botón de filtrar -->
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                        </div>

                        <!-- Botón de reiniciar -->
                        <div class="col-md-1">
                            <a href="{{ route('tareas.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-card-text"></i> Cédula / RUC</th>
                        <th><i class="bi bi-person"></i> Cliente</th>
                        <th><i class="bi bi-journal-medical"></i> Trabajo </th>
                        <th><i class="bi bi-receipt"></i> Número Factura</th>
                        <th><i class="bi bi-flag"></i> Estado</th>
                        <th><i class="bi bi-cash-coin"></i> Fecha facturada</th>
                        <th><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tareas as $tarea)
                    <tr class="tarea-row" data-estado="{{ $tarea->estado }}">
                        <td>{{ optional($tarea->cliente)->cedula_cliente ?? 'N/A' }}</td>
                        <td>{{ optional($tarea->cliente)->nombre_cliente ?? 'N/A' }}</td>
                        <td>{{ $tarea->nombre }}</td>
                        <td>{{ $tarea->numero_factura }}</td>

                        <td>
                            <span class="badge px-3 py-2 border rounded-pill 
                                {{ $tarea->estado == 'Pendiente' ? 'text-danger border-danger' : '' }}
                                {{ $tarea->estado == 'Cumplida' ? 'text-success border-success' : '' }}
                                {{ $tarea->estado == 'Anulada' ? 'text-secondary border-secondary' : '' }}
                                {{ $tarea->estado == 'En Proceso' ? 'text-warning border-warning' : '' }}">
                                {{ $tarea->estado }}
                            </span>
                        </td>
                        <!-- <td>${{ number_format($tarea->total, 2) }}</td> -->
                        <td>@formatoFecha($tarea->fecha_facturada)</td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detalleTareaModal{{ $tarea->id_tareas }}">
                                <i class="bi bi-eye"></i> <!-- Ícono de ojo -->
                            </button>
                            @can('actualizar-estado-tarea')  
                                @if(!in_array($tarea->estado, ['Cumplida', 'Anulada']))
                                    <button class="btn btn-outline-info  btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $tarea->id_tareas }}">
                                        <i class="bi bi-pencil-square"></i> 
                                    </button>
                                @endif
                            @endcan
                            
                            @can('editar-tarea')  
                                <!-- Envia tambien los filtros -->
                                <a href="{{ route('tareas.edit', [
                                    'tarea' => $tarea->id_tareas,
                                    'filter' => request('filter'),
                                    'search' => request('search'),
                                    'month' => request('month'),
                                    'year' => request('year'),
                                    'page' => request('page')
                                ]) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan


                            @can('borrar-tarea')  
                                <form action="{{ route('tareas.destroy', $tarea->id_tareas) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete(event, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                            @can('notificar-cliente')
                                @if($tarea->estado == 'Cumplida')
                                    @if($tarea->cliente && $tarea->cliente->telefono_cliente)
                                        <!-- Botón para notificar (solo si el cliente tiene teléfono) -->
                                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#notificarModal{{ $tarea->id_tareas }}">
                                            <i class="bi bi-whatsapp"></i>
                                            @if($tarea->notificado)
                                                <span class="ms-1" style="font-size: 0.75rem;"></span>
                                            @endif
                                        </button>
                                        
                                        <!-- Badge que muestra si ya fue notificado -->
                                        @if($tarea->notificado)
                                            <span class="text-success ms-1" style="font-size: 0.75rem;">
                                                <i class="bi bi-check-circle"></i> Enviado
                                            </span>
                                        @endif
                                        
                                        <!-- Incluir el partial del modal -->
                                        @include('partials.notificarTarea', ['tarea' => $tarea])
                                    @else
                                        <!-- Mensaje si no tiene teléfono -->
                                        <span class="text-danger" style="font-size: 0.75rem;" title="El cliente no tiene número registrado">
                                            <i class="bi bi-exclamation-triangle"></i> Sin teléfono
                                        </span>
                                    @endif
                                @endif
                            @endcan
                            @can('transferir-tarea')
                            <a href="{{ route('tareas.formTransferir', $tarea->id_tareas) }}" 
                                class="btn btn-outline-primary btn-sm" 
                                title="Transferir Tarea">
                                <i class="bi bi-arrow-left-right"></i>
                            </a>
                            @endcan

                            <!-- Botón para abrir modal 
                            @can('crear-cobro')
                                @if($tarea->factura && $tarea->factura->saldo_pendiente > 0)
                                
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#crearCobroModalTarea{{ $tarea->id_tareas }}">
                                        <i class="bi bi-cash"></i> 
                                    </button>
                                @else
                                    <span class="text-muted small">
                                        <i class="bi bi-info-circle"></i>
                                        {{ !$tarea->factura ? 'Sin factura' : 'Cancelada' }}
                                    </span>
                                @endif
                            @endcan        
                            -->            
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
                                                    {{ $tarea->usuario->nombre }} {{ $tarea->usuario->apellido }}
                                                    @if ($tarea->transferida_de)
                                                        <small class="text-muted">(transferida de {{ $tarea->transferidaDe->nombre ?? '' }} {{ $tarea->transferidaDe->apellido ?? '' }})</small>
                                                    @endif
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

                    <!-- Modal para editar el estado -->
                    <div class="modal fade" id="editModal{{ $tarea->id_tareas }}" tabindex="-1" aria-labelledby="editModalLabel{{ $tarea->id_tareas }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg"><!-- 👈 más ancho -->
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="editModalLabel{{ $tarea->id_tareas }}">
                                        <i class="bi bi-pencil-square"></i> {{ $tarea->nombre }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="formEditar{{ $tarea->id_tareas }}" action="{{ route('tareas.updateEstado', $tarea->id_tareas) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <div class="row g-3"><!-- 👈 usamos grid -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-person-circle"></i> Cliente:</label>
                                                <span class="form-control-static d-block">{{ optional($tarea->cliente)->nombre_cliente ?? 'N/A' }}</span>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-card-text"></i> Nombre de la Tarea:</label>
                                                <span class="form-control-static d-block">{{ old('nombre', $tarea->nombre) }}</span>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-receipt"></i> Número de Factura:</label>
                                                <span class="form-control-static d-block">{{ old('numero_factura', $tarea->numero_factura) }}</span>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-cash-coin"></i> Total:</label>
                                                <span class="form-control-static d-block">${{ old('total', $tarea->total) }}</span>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-info-circle"></i> Estado:</label>
                                                <select name="estado" id="estado{{ $tarea->id_tareas }}" class="form-select estado" required>
                                                    <option value="Cumplida" {{ $tarea->estado == 'Cumplida' ? 'selected' : '' }}>Cumplida</option>
                                                    <option value="En Proceso" {{ $tarea->estado == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                                    <option value="Pendiente" {{ $tarea->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="Anulada" {{ $tarea->estado == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-wallet2"></i> Saldo del Cliente:</label>
                                                <input type="number" name="saldo" class="form-control" step="0.01" value="{{ old('saldo', optional($tarea->cliente)->saldo ?? 0) }}">
                                            </div>

                                            

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="bi bi-calendar-check"></i> Fecha Cumplida:</label>
                                                <input type="date" name="fecha_cumplida" id="fecha_cumplida{{ $tarea->id_tareas }}" class="form-control" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="archivo" class="form-label"><i class="bi bi-paperclip"></i> Archivo</label>
                                                <input type="file" class="form-control form-control-sm" name="archivo" id="archivo">
                                                <div class="form-text">Formatos aceptados: PDF, Word, JPG, PNG, XLS (Max 5MB)</div>

                                                @if ($tarea->archivo)
                                                    <div class="mt-2">
                                                        <label>Archivo Actual:</label>
                                                        <a href="{{ asset('storage/' . $tarea->archivo) }}" target="_blank" class="d-block text-primary">
                                                            <i class="bi bi-file-earmark"></i> Ver Archivo
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label for="observacion" class="form-label"><strong>Observación</strong></label>
                                                <textarea name="observacion" class="form-control">{{ old('observacion', $tarea->observacion) }}</textarea>
                                            </div>
                                            
                                            <!-- Necesario para mantener en la misma pagina y con los filtros -->
                                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                            <input type="hidden" name="month" value="{{ request('month') }}">
                                            <input type="hidden" name="year" value="{{ request('year') }}">
                                            <input type="hidden" name="page" value="{{ request('page') }}">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="bi bi-x-circle"></i> Cerrar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Actualizar Estado
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal cobros -->    
                    <!-- Modal de cobro INTEGRADO DIRECTAMENTE -->
                    @if($tarea->factura && $tarea->factura->saldo_pendiente > 0)
                    <div class="modal fade" id="crearCobroModalTarea{{ $tarea->id_tareas }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Registrar Cobro - Tarea: {{ $tarea->nombre }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('tareas.store-cobro') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="factura_id" value="{{ $tarea->factura->id_facturas }}">
                                        <input type="hidden" name="tarea_id" value="{{ $tarea->id_tareas }}">
                                        
                                        <div class="alert alert-info">
                                            <strong>Factura:</strong> #{{ $tarea->factura->numero_factura }}<br>
                                            <strong>Cliente:</strong> {{ optional(optional($tarea->factura)->cliente)->nombre_cliente ?? 'N/A' }}<br>
                                            <strong>Saldo pendiente:</strong> ${{ number_format($tarea->factura->saldo_pendiente, 2) }}
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Monto *</label>
                                            <input type="number" step="0.01" min="0.01" max="{{ $tarea->factura->saldo_pendiente }}" 
                                                name="monto" class="form-control" value="{{ $tarea->factura->saldo_pendiente }}" required>
                                            <div class="form-text">Monto máximo: ${{ number_format($tarea->factura->saldo_pendiente, 2) }}</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Pago *</label>
                                            <input type="date" name="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Pago *</label>
                                            <select name="tipo_pago" class="form-select" required>
                                                <option value="">-- Seleccionar Tipo --</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Transferencia">Transferencia</option>
                                                <option value="Tarjeta">Tarjeta</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Registrado por *</label>
                                            @can('ver-todas-tareas')
                                                <select name="usuario_id" class="form-select" required>
                                                    <option value="">-- Seleccionar Usuario --</option>
                                                    @foreach(\App\Models\User::select('id', 'nombre', 'apellido')->orderBy('nombre')->get() as $usuario)
                                                        <option value="{{ $usuario->id }}" {{ auth()->id() == $usuario->id ? 'selected' : '' }}>
                                                            {{ $usuario->nombre }} {{ $usuario->apellido }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="usuario_id" value="{{ auth()->id() }}">
                                                <input type="text" class="form-control" value="{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}" readonly>
                                            @endcan
                                        </div>
                                    </div>
                                        <!-- Necesario para mantener en la misma pagina y con los filtros -->
                                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                            <input type="hidden" name="month" value="{{ request('month') }}">
                                            <input type="hidden" name="year" value="{{ request('year') }}">
                                            <input type="hidden" name="page" value="{{ request('page') }}">
                                        <!-- No Borrar -->    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Registrar Cobro</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <script>


                        document.addEventListener("DOMContentLoaded", function () {
                                const buttons = document.querySelectorAll(".filter-btn");
                                const rows = document.querySelectorAll(".tarea-row");

                                buttons.forEach(button => {
                                    button.addEventListener("click", function () {
                                        const filter = this.getAttribute("data-filter");

                                        rows.forEach(row => {
                                            const estado = row.getAttribute("data-estado");
                                            if (filter === "all" || estado === filter) {
                                                row.style.display = "";
                                            } else {
                                                row.style.display = "none";
                                            }
                                        });
                                    });
                                });
                            });
                    </script>

                    @endforeach
                </tbody>
            </table>
        <div class="table-responsive">
        <div class="d-flex justify-content-center">
            {{ $tareas->appends(['filter' => request('filter')])->links() }}
        </div>
    </div>
@endsection
