@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-cash-coin"></i> Lista de Cobros</h2>
    
       <!--
            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('cobros.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Registrar Nuevo Cobro
                </a>
            </div>
        -->
    <!-- Filtros -->
    <form action="{{ route('cobros.index') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">
            <!-- Campo de búsqueda -->
            <div class="col-md-6">
                <label for="search" class="form-label">Buscar por factura o cliente:</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="N° factura, nombre o cédula" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            
            <!-- Filtro por fecha -->
            <div class="col-md-4">
                <label for="fecha" class="form-label">Filtrar por fecha:</label>
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            
            <!-- Botón de limpiar -->
            <div class="col-md-2">
                <a href="{{ route('cobros.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-hash"></i> Factura</th>
                    <th><i class="bi bi-person"></i> Cliente</th>
                    <th><i class="bi bi-calendar"></i> Fecha Pago</th>
                    <th><i class="bi bi-currency-dollar"></i> Monto</th>
                    <th><i class="bi bi-credit-card"></i> Tipo Pago</th>
                    <th><i class="bi bi-person"></i> Registrado por</th>
                    <th><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cobros as $cobro)
                    <tr>
                        <td>#{{ optional($cobro->factura)->numero_factura ?? 'N/A' }}</td>
                        <td>{{ optional(optional($cobro->factura)->cliente)->nombre_cliente ?? 'N/A' }}</td>
                        <td>@formatoFecha($cobro->fecha_pago) </td>
                        <td class="text-success fw-bold">${{ number_format($cobro->monto, 2) }}</td>
                        <td>
                            <span class="badge 
                                @if($cobro->tipo_pago == 'Efectivo') bg-success
                                @elseif($cobro->tipo_pago == 'Transferencia') bg-primary
                                @else bg-info
                                @endif">
                                {{ $cobro->tipo_pago }}
                            </span>
                        </td>
                        <td>{{ $cobro->usuario->nombre ?? 'N/A' }} {{ $cobro->usuario->apellido ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('cobros.show', $cobro->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> 
                                </a>
                                
                                <!-- Botón para abrir modal de edición -->
                                 @can('crear-cobro')
                                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#editarCobroModal{{ $cobro->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endcan

                                <!-- Botón para abrir modal de eliminación -->
                                 @can('borrar-cobro')
                                <form action="{{ route('cobros.destroy', $cobro->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete(event, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox"></i> No se encontraron cobros
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $cobros->appends(request()->query())->links() }}
        </div>

        @foreach($cobros as $cobro)
        <!-- Modal de edición -->
            <div class="modal fade" id="editarCobroModal{{ $cobro->id }}" tabindex="-1" aria-labelledby="editarCobroModalLabel{{ $cobro->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold" id="editarCobroModalLabel{{ $cobro->id }}">
                            Editar Cobro #{{ $cobro->id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <form action="{{ route('cobros.update', $cobro->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Monto</label>
                            <input type="number" name="monto" step="0.01" min="0.01" class="form-control" value="{{ old('monto', $cobro->monto) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fecha de Pago</label>
                            <input type="date" name="fecha_pago" class="form-control" value="{{ old('fecha_pago', $cobro->fecha_pago) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de Pago</label>
                            <select name="tipo_pago" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Efectivo" {{ old('tipo_pago', $cobro->tipo_pago) == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="Transferencia" {{ old('tipo_pago', $cobro->tipo_pago) == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="Tarjeta" {{ old('tipo_pago', $cobro->tipo_pago) == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            </select>
                        </div>

                        <div class="alert alert-info small">
                             Saldo actual de factura: <strong>${{ number_format(optional($cobro->factura)->saldo_pendiente ?? 0, 2) }}</strong>
                        </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning text-white fw-semibold">
                            <i class="bi bi-save"></i> Guardar cambios
                        </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Validación de monto en los modales de edición
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($cobros as $cobro)
        @can('editar-cobro')
        const montoEdit{{ $cobro->id }} = document.getElementById('monto_edit{{ $cobro->id }}');
        const maxMontoEdit{{ $cobro->id }} = {{ (optional($cobro->factura)->saldo_pendiente ?? 0) + $cobro->monto }};
        
        montoEdit{{ $cobro->id }}.addEventListener('change', function() {
            if (parseFloat(this.value) > maxMontoEdit{{ $cobro->id }}) {
                alert('El monto no puede ser mayor al saldo disponible: $' + maxMontoEdit{{ $cobro->id }}.toFixed(2));
                this.value = maxMontoEdit{{ $cobro->id }}.toFixed(2);
            }
        });
        @endcan
        @endforeach
    });
</script>
@endsection