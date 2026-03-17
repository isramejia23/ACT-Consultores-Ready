@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-receipt"></i> Lista de Facturas</h2>
    
    <div class="d-flex justify-content-between mb-4">
        @can('crear-factura')
        <a href="{{ route('facturas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Crear Nueva Factura
        </a>
        @endcan
    </div>
        <!-- Resumen de Totales -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-primary">
                <strong><i class="bi bi-receipt"></i> Total Facturado:</strong>
                ${{ number_format($totalFacturado, 2) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-success">
                <strong><i class="bi bi-cash-coin"></i> Total Recaudado:</strong>
                ${{ number_format($totalRecaudado, 2) }}
            </div>
        </div>
    </div>

    <!-- Filtros (tu código actual) -->
    <form action="{{ route('facturas.index') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">
            <!-- Campo de búsqueda -->
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar factura o cliente:</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                        placeholder="N° factura, nombre o cédula" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Filtro por estado -->
            <div class="col-md-2">
                <label for="estado" class="form-label">Estado:</label>
                <select name="estado" id="estado" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Parcial" {{ request('estado') == 'Parcial' ? 'selected' : '' }}>Parcial</option>
                    <option value="Pagado" {{ request('estado') == 'Pagado' ? 'selected' : '' }}>Pagado</option>
                </select>
            </div>

            <!-- ✅ Filtro por mes -->
            <div class="col-md-2">
                <label for="mes" class="form-label">Mes:</label>
                <select name="mes" id="mes" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mes', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- ✅ Filtro por año -->
            <div class="col-md-2">
                <label for="anio" class="form-label">Año:</label>
                <select name="anio" id="anio" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach($aniosDisponibles as $anio)
                        <option value="{{ $anio }}" {{ request('anio', date('Y')) == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botón de limpiar -->
            <div class="col-md-2">
                <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>


    <div class="table-responsive">
        <table class="table table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-hash"></i> N° Factura</th>
                    <th><i class="bi bi-calendar"></i> Fecha</th>
                    <th><i class="bi bi-person"></i> Cliente</th>
                    <th><i class="bi bi-card-text"></i> Cédula</th>
                    <th><i class="bi bi-currency-dollar"></i> Total</th>
                    <th><i class="bi bi-cash-coin"></i> Saldo</th>
                    <th><i class="bi bi-circle-fill"></i> Estado</th>
                    <th><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facturas as $factura)
                    <tr>
                        <td>{{ $factura->numero_factura }}</td>
                        <td>@formatoFecha($factura->fecha_factura)</td>
                        <td>{{ optional($factura->cliente)->nombre_cliente ?? 'N/A' }}</td>
                        <td>{{ optional($factura->cliente)->cedula_cliente ?? 'N/A' }}</td>
                        <td>${{ number_format($factura->total_factura, 2) }}</td>
                        <td>${{ number_format($factura->saldo_pendiente, 2) }}</td>
                        <td>
                            <span class="badge px-3 py-2 border rounded-pill 
                                @if($factura->estado_pago == 'Pagado') text-success border-success
                                @elseif($factura->estado_pago == 'Parcial') text-warning border-warning
                                @else text-danger border-danger
                                @endif">
                                {{ $factura->estado_pago }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                @can('ver-factura')
                                <!-- Botón para abrir el modal de detalles -->
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detalleFacturaModal{{ $factura->id_facturas }}">
                                    <i class="bi bi-eye"></i> 
                                </button>
                                @endcan
                                
                                @can('editar-factura')
                                <a href="{{ route('facturas.edit', $factura->id_facturas) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-pencil"></i> 
                                </a>
                                @endcan
                                
                                @can('borrar-factura')
                                <form action="{{ route('facturas.destroy', $factura->id_facturas) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete(event, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                <!--
                                    @can('crear-cobro')
                                            @if($factura->saldo_pendiente <= 0)
                                                <span class="text-success ms-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-check-circle"></i> Cancelado
                                                </span>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#crearCobroModal{{ $factura->id_facturas }}">
                                                    <i class="bi bi-cash"></i> 
                                                </button>
                                            @endif
                                        @endcan
                                -->            
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $facturas->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal para Detalles de Factura -->
@foreach($facturas as $factura)
@can('ver-factura')
<div class="modal fade" id="detalleFacturaModal{{ $factura->id_facturas }}" tabindex="-1" aria-labelledby="detalleFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalles de Factura #{{ $factura->numero_factura }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> {{ optional($factura->cliente)->nombre_cliente ?? 'N/A' }}</p>
                        <p><strong>Cédula:</strong> {{ optional($factura->cliente)->cedula_cliente ?? 'N/A' }}</p>
                        <p><strong>Fecha Factura:</strong> @formatoFecha($factura->fecha_factura) </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> 
                            <span class="badge 
                                @if($factura->estado_pago == 'Pagado') bg-success
                                @elseif($factura->estado_pago == 'Parcial') bg-warning
                                @else bg-danger
                                @endif">
                                {{ $factura->estado_pago }}
                            </span>
                        </p>
                        <p><strong>Total Factura:</strong> ${{ number_format($factura->total_factura, 2) }}</p>
                        <p><strong>Saldo Pendiente:</strong> ${{ number_format($factura->saldo_pendiente, 2) }}</p>
                    </div>
                </div>
                
                <h6 class="border-bottom pb-2">Tareas de la Factura</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unitario</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($factura->tarea as $tarea)
                                <tr>
                                    <td>{{ $tarea->nombre }}</td>
                                    <td class="text-center">{{ $tarea->cantidad }}</td>
                                    <td class="text-end">${{ number_format($tarea->precio_unitario, 2) }}</td>
                                    <td class="text-end">${{ number_format($tarea->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay tareas asociadas</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr>
                                <th colspan="3" class="text-end">Total Factura:</th>
                                <th class="text-end">${{ number_format($factura->total_factura, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Sección de Cobros Realizados -->
                <h6 class="border-bottom pb-2">Cobros Realizados</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Registrado por</th>
                                <th>Fecha de Pago</th>
                                <th>Tipo de Pago</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Ordenar cobros por fecha de pago (más reciente primero)
                                $cobrosOrdenados = $factura->cobro->sortByDesc('fecha_pago');
                            @endphp
                            
                            @forelse($cobrosOrdenados as $cobro)
                                <tr>
                                    <td>{{ $cobro->usuario->nombre ?? 'N/A' }} {{ $cobro->usuario->apellido ?? '' }}</td>
                                    <td>@formatoFecha($cobro->fecha_pago)</td>
                                    <td>
                                        <span class="badge 
                                            @if($cobro->tipo_pago == 'Efectivo') bg-success
                                            @elseif($cobro->tipo_pago == 'Transferencia') bg-primary
                                            @else bg-info
                                            @endif">
                                            {{ $cobro->tipo_pago }}
                                        </span>
                                    </td>
                                    <td class="text-end">${{ number_format($cobro->monto, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No se han realizado cobros</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($factura->cobro->count() > 0)
                        <tfoot class="table-group-divider">
                            <tr>
                                <th colspan="3" class="text-end"><strong>Total Cobrado:</strong></th>
                                <th class="text-end"><strong>${{ number_format($factura->cobro->sum('monto'), 2) }}</strong></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end"><strong>Saldo Pendiente:</strong></th>
                                <th class="text-end"><strong>${{ number_format($factura->saldo_pendiente, 2) }}</strong></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endcan
@endforeach
<!-- Modal para Crear Cobro -->
@foreach($facturas as $factura)
@if($factura->saldo_pendiente > 0)
@can('crear-cobro')
<div class="modal fade" id="crearCobroModal{{ $factura->id_facturas }}" tabindex="-1" aria-labelledby="crearCobroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar Cobro - Factura #{{ $factura->numero_factura }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('cobros.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="factura_id" value="{{ $factura->id_facturas }}">
                    
                    <div class="alert alert-info">
                        <strong>Cliente:</strong> {{ optional($factura->cliente)->nombre_cliente ?? 'N/A' }}<br>
                        <strong>Saldo pendiente:</strong> ${{ number_format($factura->saldo_pendiente, 2) }}
                    </div>

                    <div class="mb-3">
                        <label for="monto{{ $factura->id_facturas }}" class="form-label">Monto *</label>
                        <input type="number" step="0.01" min="0.01" max="{{ $factura->saldo_pendiente }}" 
                               name="monto" id="monto{{ $factura->id_facturas }}" 
                               class="form-control" value="{{ $factura->saldo_pendiente }}" required>
                        <div class="form-text">Monto máximo: ${{ number_format($factura->saldo_pendiente, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_pago{{ $factura->id_facturas }}" class="form-label">Fecha de Pago *</label>
                        <input type="date" name="fecha_pago" id="fecha_pago{{ $factura->id_facturas }}" 
                               class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_pago{{ $factura->id_facturas }}" class="form-label">Tipo de Pago *</label>
                        <select name="tipo_pago" id="tipo_pago{{ $factura->id_facturas }}" class="form-select" required>
                            <option value="">-- Seleccionar Tipo --</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                        </select>
                    </div>

                    <!-- Campo para seleccionar usuario -->
                    <div class="mb-3">
                        <label for="usuario_id{{ $factura->id_facturas }}" class="form-label">Registrado por *</label>
                        @can('ver-todas-facturas')
                            <select name="usuario_id" id="usuario_id{{ $factura->id_facturas }}" class="form-select" required>
                                <option value="">-- Seleccionar Usuario --</option>
                                @foreach($usuarios as $usuario)
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Cobro</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endif
@endforeach
@endsection

@section('scripts')
<script>
    function confirmDelete(event, button) {
        event.preventDefault();
        if (confirm('¿Estás seguro de que deseas eliminar esta factura?')) {
            button.closest('form').submit();
        }
    }

    // Validación de monto en los modales de cobro
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($facturas as $factura)
        @if($factura->saldo_pendiente > 0)
        const montoInput{{ $factura->id_facturas }} = document.getElementById('monto{{ $factura->id_facturas }}');
        const maxMonto{{ $factura->id_facturas }} = {{ $factura->saldo_pendiente }};
        
        montoInput{{ $factura->id_facturas }}.addEventListener('change', function() {
            if (parseFloat(this.value) > maxMonto{{ $factura->id_facturas }}) {
                alert('El monto no puede ser mayor al saldo pendiente: $' + maxMonto{{ $factura->id_facturas }}.toFixed(2));
                this.value = maxMonto{{ $factura->id_facturas }}.toFixed(2);
            }
        });
        @endif
        @endforeach
    });
</script>
@endsection