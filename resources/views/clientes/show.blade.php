@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="bi bi-person-badge text-primary me-2"></i>Detalles del Cliente
        </h3>
        <div>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('clientes.edit', $cliente->id_clientes) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Columna izquierda - Información básica -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i> Información Personal</h5 >
                </div>
                <div class="card-body py-3 px-3">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Nombre completo</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->nombre_cliente }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Cédula</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->cedula_cliente }}</div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Teléfono</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->telefono_cliente ?? 'No especificado' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Email</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->email_cliente }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label text-muted small mb-1">Dirección</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->direccion ?? 'No especificada' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Asesor</label>
                            <div class="p-2 bg-light rounded">
                                @if($cliente->usuario)
                                    <i class="bi bi-person-check me-1"></i>{{ $cliente->usuario->nombre }} {{ $cliente->usuario->apellido }}
                                @else
                                    <span class="text-muted"><i class="bi bi-person-x me-1"></i>No asignado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha - Información comercial -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i> Información Comercial</h5>
                </div>
                <div class="card-body py-3 px-3">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Régimen</label>
                            <div class="p-2 bg-light rounded">{{ optional($cliente->regimen)->nombre ?? 'Sin régimen' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Estado</label>
                            <div class="p-2 bg-light rounded">
                                <span class="badge bg-{{ $cliente->estado == 'Activo' ? 'success' : 'secondary' }}">{{ $cliente->estado }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small mb-1">Actividad económica</label>
                        <div class="p-2 bg-light rounded">{{ $cliente->actividad ?? 'No especificada' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Fecha firma</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->fecha_firma ? \Carbon\Carbon::parse($cliente->fecha_firma)->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Fecha facturación</label>
                            <div class="p-2 bg-light rounded">{{ $cliente->fecha_facturacion ? \Carbon\Carbon::parse($cliente->fecha_facturacion)->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Saldo actual</label>
                            <div class="p-2 bg-light rounded fw-bold">${{ number_format($cliente->saldo, 2) }}</div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Observaciones -->
    @if($cliente->observaciones)
    <div class="row mt-2">
        <div class="col-12">
            <div class="card shadow-sm mb-1">
                <div class="card-header bg-primary text-white py-2 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i> Observaciones</h5>
                    <a href="{{ route('clientes.edit', $cliente->id_clientes) }}" class="btn btn-sm btn-light">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
                <div class="card-body py-3 px-4">
                    <p class="mb-0" style="white-space: pre-wrap; font-size: 0.95rem;">{{ $cliente->observaciones }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Obligaciones y Trabajos del cliente -->
    <div class="row mt-4">
        <!-- Obligaciones (izquierda) -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i> Obligaciones</h5>
                    @can('crear-obligacion')
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalCrearObligacionShow">
                        <i class="bi bi-calendar-plus"></i> Nueva
                    </button>
                    @endcan
                </div>
                <div class="card-body p-0">
                    @if($obligaciones->count() > 0)
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Periodicidad</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($obligaciones as $ob)
                                @php
                                    $diasR = now()->diffInDays($ob->fecha_vencimiento, false);
                                @endphp
                                @php
                                    $estadoOb = $ob->estado ?? ($ob->completado ? 'completada' : 'pendiente');
                                @endphp
                                <tr class="{{ $estadoOb === 'anulada' ? 'table-secondary' : ($estadoOb === 'completada' ? '' : ($diasR < 0 ? 'table-danger' : ($diasR <= 5 ? 'table-warning' : ''))) }}">
                                    <td>{{ $ob->nombre_obligacion }}</td>
                                    <td>
                                        @if($ob->periodicidad)
                                            <span class="badge bg-info text-dark">{{ ucfirst($ob->periodicidad) }}</span>
                                        @elseif($ob->tipoObligacion)
                                            <span class="badge bg-info text-dark">{{ ucfirst($ob->tipoObligacion->periodicidad) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($ob->fecha_vencimiento)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($estadoOb === 'completada')
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Completada</span>
                                        @elseif($estadoOb === 'anulada')
                                            <span class="badge bg-secondary"><i class="bi bi-x-circle-fill"></i> Anulada</span>
                                        @elseif($diasR < 0)
                                            <span class="badge bg-danger">Vencido ({{ abs(round($diasR)) }}d)</span>
                                        @elseif($diasR <= 5)
                                            <span class="badge bg-warning text-dark">Por Vencer ({{ round($diasR) }}d)</span>
                                        @else
                                            <span class="badge bg-primary">Pendiente ({{ round($diasR) }}d)</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if($estadoOb === 'pendiente')
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarOb{{ $ob->id }}" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" title="Anular" onclick="anularObligacion({{ $ob->id }}, function() { location.reload(); })">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                            <form action="{{ route('obligaciones.destroy', $ob->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="confirmDelete(event, this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-calendar-x fs-3"></i>
                        <p class="mt-2 mb-0">No hay obligaciones registradas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Trabajos (derecha) -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i> Trabajos Realizados</h5>
                </div>
                <div class="card-body p-0">
                    @if($tareas->count() > 0)
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Trabajo</th>
                                    <th>Factura</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tareas as $tarea)
                                <tr>
                                    <td>{{ $tarea->nombre }}</td>
                                    <td><small class="text-muted">{{ $tarea->numero_factura }}</small></td>
                                    <td><small>{{ $tarea->fecha_facturada ? \Carbon\Carbon::parse($tarea->fecha_facturada)->format('d/m/Y') : '-' }}</small></td>
                                    <td>${{ number_format($tarea->total, 2) }}</td>
                                    <td>
                                        @if($tarea->estado === 'Cumplida')
                                            <span class="badge bg-success">Cumplida</span>
                                        @elseif($tarea->estado === 'En Proceso')
                                            <span class="badge bg-warning text-dark">En Proceso</span>
                                        @elseif($tarea->estado === 'Anulada')
                                            <span class="badge bg-secondary">Anulada</span>
                                        @else
                                            <span class="badge bg-danger">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-clipboard-x fs-3"></i>
                        <p class="mt-2 mb-0">No hay trabajos registrados</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modales Editar Obligación -->
    @foreach($obligaciones as $ob)
    <div class="modal fade" id="modalEditarOb{{ $ob->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Editar Obligación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('obligaciones.update', $ob->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Obligación</label>
                            <input type="text" class="form-control" value="{{ $ob->nombre_obligacion }}" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Periodicidad</label>
                                <select name="periodicidad" class="form-select select-periodicidad-edit" data-ob="{{ $ob->id }}" required>
                                    <option value="mensual" {{ ($ob->periodicidad ?? optional($ob->tipoObligacion)->periodicidad) == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                    <option value="semestral" {{ ($ob->periodicidad ?? optional($ob->tipoObligacion)->periodicidad) == 'semestral' ? 'selected' : '' }}>Semestral</option>
                                    <option value="anual" {{ ($ob->periodicidad ?? optional($ob->tipoObligacion)->periodicidad) == 'anual' ? 'selected' : '' }}>Anual</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Mes</label>
                                @php
                                    $perEdit = $ob->periodicidad ?? optional($ob->tipoObligacion)->periodicidad;
                                    $mesActualOb = $ob->fecha_vencimiento ? \Carbon\Carbon::parse($ob->fecha_vencimiento)->month : null;
                                @endphp
                                <select name="mes_vencimiento" class="form-select select-mes-edit" id="selectMesEdit{{ $ob->id }}" {{ in_array($perEdit, ['anual', 'semestral']) ? '' : 'disabled' }}>
                                    <option value="">N/A</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $mesActualOb == $m ? 'selected' : '' }}>{{ ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Día Venc.</label>
                                <input type="number" name="dia_vencimiento" class="form-control" min="1" max="28" value="{{ $ob->dia_vencimiento ?? \Carbon\Carbon::parse($ob->fecha_vencimiento)->day }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal Crear Obligaciones desde Show -->
    @can('crear-obligacion')
    <div class="modal fade" id="modalCrearObligacionShow" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus"></i> Agregar Obligaciones - {{ $cliente->nombre_cliente }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('clientes.obligacion.store', $cliente->id_clientes) }}" method="POST" id="formObligacionesMultiple">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <span class="me-3"><strong>Cliente:</strong> {{ $cliente->nombre_cliente }}</span>
                            <span><strong>Régimen:</strong>
                                <span class="badge bg-secondary">{{ optional($cliente->regimen)->nombre ?? 'Sin Régimen' }}</span>
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tablaObligacionesNuevas">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 280px;">Servicio del Catálogo</th>
                                        <th style="min-width: 140px;">Periodicidad</th>
                                        <th style="min-width: 140px;">Mes</th>
                                        <th style="min-width: 110px;">Día Venc.</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyObligaciones">
                                    <tr class="fila-obligacion" data-index="0">
                                        <td>
                                            <select name="obligaciones[0][catalogo_servicio_id]" class="form-select select-catalogo" required>
                                                <option value="">-- Seleccionar servicio --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="obligaciones[0][periodicidad]" class="form-select select-periodicidad" required>
                                                <option value="">-- Seleccionar --</option>
                                                <option value="mensual">Mensual</option>
                                                <option value="semestral">Semestral</option>
                                                <option value="anual">Anual</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="obligaciones[0][mes_vencimiento]" class="form-select select-mes" disabled>
                                                <option value="">N/A</option>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}">{{ ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="obligaciones[0][dia_vencimiento]" class="form-control" min="1" max="28" placeholder="1-28" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-fila" title="Quitar" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btnAgregarFila">
                            <i class="bi bi-plus-circle me-1"></i> Agregar otra obligación
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Guardar Todas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

</div>

<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .form-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function() {
    var serviciosCargados = [];
    var filaIndex = 0;

    function initSelect2(selectEl) {
        $(selectEl).select2({
            theme: 'bootstrap-5',
            placeholder: '-- Buscar servicio --',
            allowClear: true,
            dropdownParent: $('#modalCrearObligacionShow')
        });
    }

    function cargarServicios() {
        if (serviciosCargados.length > 0) return Promise.resolve();
        return $.getJSON('/api/catalogo-servicios').then(function(servicios) {
            serviciosCargados = servicios;
        });
    }

    function llenarSelect(selectEl) {
        var $sel = $(selectEl);
        $sel.html('<option value="">-- Buscar servicio --</option>');
        serviciosCargados.forEach(function(s) {
            $sel.append('<option value="' + s.id + '">' + s.codigo + ' - ' + s.nombre + '</option>');
        });
    }

    function actualizarBotonesQuitar() {
        var filas = $('#tbodyObligaciones .fila-obligacion');
        filas.find('.btn-quitar-fila').prop('disabled', filas.length <= 1);
    }

    var mesesOptions = '<option value="">N/A</option>' +
        '<option value="1">Enero</option><option value="2">Febrero</option>' +
        '<option value="3">Marzo</option><option value="4">Abril</option>' +
        '<option value="5">Mayo</option><option value="6">Junio</option>' +
        '<option value="7">Julio</option><option value="8">Agosto</option>' +
        '<option value="9">Septiembre</option><option value="10">Octubre</option>' +
        '<option value="11">Noviembre</option><option value="12">Diciembre</option>';

    function toggleMes($fila) {
        var periodicidad = $fila.find('.select-periodicidad').val();
        var $selectMes = $fila.find('.select-mes');
        if (periodicidad === 'anual' || periodicidad === 'semestral') {
            $selectMes.prop('disabled', false).prop('required', true);
        } else {
            $selectMes.val('').prop('disabled', true).prop('required', false);
        }
    }

    // Escuchar cambio de periodicidad en filas existentes y nuevas
    $('#tbodyObligaciones').on('change', '.select-periodicidad', function() {
        toggleMes($(this).closest('.fila-obligacion'));
    });

    function agregarFila() {
        filaIndex++;
        var html = '<tr class="fila-obligacion" data-index="' + filaIndex + '">' +
            '<td><select name="obligaciones[' + filaIndex + '][catalogo_servicio_id]" class="form-select select-catalogo" required>' +
                '<option value="">-- Buscar servicio --</option></select></td>' +
            '<td><select name="obligaciones[' + filaIndex + '][periodicidad]" class="form-select select-periodicidad" required>' +
                '<option value="">-- Seleccionar --</option>' +
                '<option value="mensual">Mensual</option>' +
                '<option value="semestral">Semestral</option>' +
                '<option value="anual">Anual</option></select></td>' +
            '<td><select name="obligaciones[' + filaIndex + '][mes_vencimiento]" class="form-select select-mes" disabled>' +
                mesesOptions + '</select></td>' +
            '<td><input type="number" name="obligaciones[' + filaIndex + '][dia_vencimiento]" class="form-control" min="1" max="28" placeholder="1-28" required></td>' +
            '<td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-fila" title="Quitar"><i class="bi bi-trash"></i></button></td>' +
            '</tr>';
        var $fila = $(html);
        $('#tbodyObligaciones').append($fila);
        llenarSelect($fila.find('.select-catalogo'));
        initSelect2($fila.find('.select-catalogo'));
        actualizarBotonesQuitar();
    }

    $('#modalCrearObligacionShow').on('shown.bs.modal', function() {
        cargarServicios().then(function() {
            $('#tbodyObligaciones .select-catalogo').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    llenarSelect(this);
                    initSelect2(this);
                }
            });
        });
    });

    $('#btnAgregarFila').on('click', function() {
        agregarFila();
    });

    $('#tbodyObligaciones').on('click', '.btn-quitar-fila', function() {
        var $fila = $(this).closest('.fila-obligacion');
        $fila.find('.select-catalogo').select2('destroy');
        $fila.remove();
        actualizarBotonesQuitar();
    });

    // Toggle mes en modales de edición
    $(document).on('change', '.select-periodicidad-edit', function() {
        var obId = $(this).data('ob');
        var $selectMes = $('#selectMesEdit' + obId);
        var val = $(this).val();
        if (val === 'anual' || val === 'semestral') {
            $selectMes.prop('disabled', false).prop('required', true);
        } else {
            $selectMes.val('').prop('disabled', true).prop('required', false);
        }
    });
})();
</script>
@endsection