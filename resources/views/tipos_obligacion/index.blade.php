@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-list-check"></i> Obligación por Régimen</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle"></i> {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        {{-- Sidebar de regímenes --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-2">
                    <i class="bi bi-shield-check"></i> <strong>Regímenes</strong>
                </div>
                <div class="list-group list-group-flush" id="regimen-list">
                    @forelse($regimenes as $i => $regimen)
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center regimen-item {{ $i === 0 ? 'active' : '' }}"
                           data-regimen="{{ $regimen->id }}">
                            <div>
                                <div class="fw-semibold">{{ $regimen->nombre }}</div>
                                <small class="text-{{ $i === 0 ? 'white-50' : 'muted' }}">
                                    {{ ucfirst($regimen->periodicidad) }}
                                    &middot;
                                    @if($regimen->dia_fijo)
                                        Día fijo: {{ $regimen->dia_fijo }}
                                    @else
                                        9no dígito cédula
                                    @endif
                                </small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $regimen->tipos_obligacion_count }}</span>
                        </a>
                    @empty
                        <div class="list-group-item text-muted text-center small">
                            No hay regímenes. <a href="{{ route('regimenes.create') }}">Crear uno</a>.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Panel derecho: obligaciones del régimen seleccionado --}}
        <div class="col-md-9">
            @foreach($regimenes as $i => $regimen)
            <div class="regimen-panel" id="panel-{{ $regimen->id }}" style="{{ $i !== 0 ? 'display:none;' : '' }}">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-shield-check"></i> <strong>{{ $regimen->nombre }}</strong>
                            <span class="badge bg-light text-dark ms-2">{{ ucfirst($regimen->periodicidad) }}</span>
                            @if($regimen->dia_fijo)
                                <span class="badge bg-light text-dark ms-1">Día fijo: {{ $regimen->dia_fijo }}</span>
                            @else
                                <span class="badge bg-light text-dark ms-1">Día: 9no dígito cédula</span>
                            @endif
                        </span>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalCrear"
                                onclick="preSeleccionarRegimen('{{ $regimen->id }}')">
                            <i class="bi bi-plus-circle"></i> Agregar tipo
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if($regimen->tiposObligacion->count())
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-normal">Nombre</th>
                                    <th class="fw-normal">Periodicidad</th>
                                    <th class="fw-normal">Día de vencimiento</th>
                                    <th class="fw-normal">Servicio del Catálogo</th>
                                    <th class="fw-normal">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regimen->tiposObligacion as $tipo)
                                <tr>
                                    <td>{{ $tipo->nombre }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($tipo->periodicidad) }}</span>
                                        @if($tipo->periodicidad === 'semestral')
                                            <span class="badge bg-info text-dark">Ene / Jul</span>
                                        @elseif($tipo->mes_vencimiento)
                                            <span class="badge bg-info text-dark">
                                                {{ ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][$tipo->mes_vencimiento - 1] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tipo->dia_vencimiento)
                                            <span class="badge bg-warning text-dark">Día {{ $tipo->dia_vencimiento }}</span>
                                        @elseif($regimen->dia_fijo)
                                            <span class="text-muted small">Día {{ $regimen->dia_fijo }} <i>(del régimen)</i></span>
                                        @else
                                            <span class="text-muted small">Según 9no dígito cédula</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tipo->servicio)
                                            <code>{{ $tipo->servicio->codigo }}</code> — {{ $tipo->servicio->nombre }}
                                        @else
                                            <span class="text-muted small">Sin servicio vinculado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                onclick="llenarModalEditar({{ json_encode([
                                                    'id' => $tipo->id,
                                                    'nombre' => $tipo->nombre,
                                                    'regimen_id' => $tipo->regimen_id,
                                                    'periodicidad' => $tipo->periodicidad,
                                                    'mes_vencimiento' => $tipo->mes_vencimiento,
                                                    'dia_vencimiento' => $tipo->dia_vencimiento,
                                                    'catalogo_servicio_id' => $tipo->catalogo_servicio_id,
                                                ]) }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('tipos-obligacion.destroy', $tipo) }}" method="POST"
                                                  onsubmit="return confirm('¿Eliminar este tipo de obligación?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="bi bi-inbox"></i> Sin tipos de obligación en este régimen.
                            <br>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalCrear"
                               onclick="preSeleccionarRegimen('{{ $regimen->id }}')">
                                Agregar uno
                            </a>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Modal Crear Tipo de Obligación --}}
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tipos-obligacion.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel"><i class="bi bi-plus-circle"></i> Nuevo Tipo de Obligación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Columna izquierda --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre del tipo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" id="modal_nombre"
                                       value="{{ old('nombre') }}" placeholder="Ej: Declaración IVA Mensual..." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                                <select name="periodicidad" id="modal_periodicidad" class="form-select" required>
                                    <option value="mensual"   {{ old('periodicidad') == 'mensual'   ? 'selected' : '' }}>Mensual</option>
                                    <option value="semestral" {{ old('periodicidad') == 'semestral' ? 'selected' : '' }}>Semestral (Ene/Jul)</option>
                                    <option value="anual"     {{ old('periodicidad') == 'anual'     ? 'selected' : '' }}>Anual</option>
                                </select>
                            </div>

                            <div class="row mb-3" id="modal_campos_vencimiento" style="display:none;">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Mes</label>
                                    <select name="mes_vencimiento" class="form-select">
                                        <option value="">-- Sin definir --</option>
                                        @foreach(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] as $i => $nombreMes)
                                            <option value="{{ $i + 1 }}" {{ old('mes_vencimiento') == ($i + 1) ? 'selected' : '' }}>{{ $nombreMes }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Día</label>
                                    <input type="number" name="dia_vencimiento" class="form-control"
                                           value="{{ old('dia_vencimiento') }}" min="1" max="28" placeholder="Ej: 15">
                                    <div class="form-text">Vacío = día del régimen o cédula.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Servicio del Catálogo (opcional)</label>
                                <input type="text" id="modal_catalogo_buscar" class="form-control mb-1" placeholder="Buscar por código o nombre...">
                                <select name="catalogo_servicio_id" class="form-select" id="modal_catalogo_select" size="5">
                                    <option value="">-- Sin vincular --</option>
                                    @foreach($servicios as $servicio)
                                    <option value="{{ $servicio->id }}" {{ old('catalogo_servicio_id') == $servicio->id ? 'selected' : '' }}>
                                        [{{ $servicio->codigo }}] {{ $servicio->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Columna derecha: Regímenes con checkboxes --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Regímenes <span class="text-danger">*</span></label>
                            <div class="form-text mb-2">Selecciona uno o varios regímenes donde aplicará esta obligación.</div>
                            <div class="border rounded p-2" style="max-height: 320px; overflow-y: auto;">
                                @foreach($regimenes as $regimen)
                                <div class="form-check mb-1">
                                    <input class="form-check-input regimen-check" type="checkbox" name="regimen_ids[]"
                                           value="{{ $regimen->id }}" id="crear_regimen_{{ $regimen->id }}"
                                           {{ is_array(old('regimen_ids')) && in_array($regimen->id, old('regimen_ids')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="crear_regimen_{{ $regimen->id }}">
                                        <strong>{{ $regimen->nombre }}</strong>
                                        <small class="text-muted d-block">{{ ucfirst($regimen->periodicidad) }}
                                            &middot;
                                            @if($regimen->dia_fijo)
                                                Día fijo: {{ $regimen->dia_fijo }}
                                            @else
                                                9no dígito cédula
                                            @endif
                                        </small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSeleccionarTodos">Seleccionar todos</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeseleccionarTodos">Ninguno</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Modal Editar Tipo de Obligación --}}
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditar" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel"><i class="bi bi-pencil"></i> Editar Tipo de Obligación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del tipo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" id="edit_nombre" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Régimen <span class="text-danger">*</span></label>
                        <select name="regimen_id" id="edit_regimen_id" class="form-select" required>
                            <option value="">-- Seleccionar Régimen --</option>
                            @foreach($regimenes as $regimen)
                            <option value="{{ $regimen->id }}">{{ $regimen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                        <select name="periodicidad" id="edit_periodicidad" class="form-select" required>
                            <option value="mensual">Mensual</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>

                    <div class="row mb-3" id="edit_campos_vencimiento" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mes de vencimiento</label>
                            <select name="mes_vencimiento" id="edit_mes_vencimiento" class="form-select">
                                <option value="">-- Sin definir --</option>
                                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $nombreMes)
                                    <option value="{{ $i + 1 }}">{{ $nombreMes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Día de vencimiento</label>
                            <input type="number" name="dia_vencimiento" id="edit_dia_vencimiento" class="form-control" min="1" max="28">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Servicio del Catálogo (opcional)</label>
                        <input type="text" id="edit_catalogo_buscar" class="form-control mb-1" placeholder="Buscar servicio por código o nombre...">
                        <select name="catalogo_servicio_id" class="form-select" id="edit_catalogo_select" size="5">
                            <option value="">-- Sin vincular a servicio específico --</option>
                            @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}">
                                [{{ $servicio->codigo }}] {{ $servicio->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Navegación sidebar: mostrar panel del régimen seleccionado
document.querySelectorAll('.regimen-item').forEach(function(item) {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const regimenId = this.dataset.regimen;

        // Actualizar active en sidebar
        document.querySelectorAll('.regimen-item').forEach(el => {
            el.classList.remove('active');
            el.querySelector('small').classList.remove('text-white-50');
            el.querySelector('small').classList.add('text-muted');
        });
        this.classList.add('active');
        this.querySelector('small').classList.remove('text-muted');
        this.querySelector('small').classList.add('text-white-50');

        // Mostrar panel correspondiente
        document.querySelectorAll('.regimen-panel').forEach(p => p.style.display = 'none');
        document.getElementById('panel-' + regimenId).style.display = '';
    });
});

// Mostrar/ocultar campos de mes y día según periodicidad en modal
const modalPeriodicidad = document.getElementById('modal_periodicidad');
const modalCampos = document.getElementById('modal_campos_vencimiento');

function toggleModalCampos() {
    const valor = modalPeriodicidad.value;
    modalCampos.style.display = (valor === 'anual' || valor === 'semestral') ? '' : 'none';
}

modalPeriodicidad.addEventListener('change', toggleModalCampos);
toggleModalCampos();

// Filtro buscable para servicios del catálogo
const catalogoSelect = document.getElementById('modal_catalogo_select');
const catalogoBuscar = document.getElementById('modal_catalogo_buscar');
const catalogoOpciones = Array.from(catalogoSelect.options).map(opt => ({
    value: opt.value,
    text: opt.textContent,
    selected: opt.selected
}));

catalogoBuscar.addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    catalogoSelect.innerHTML = '';
    catalogoOpciones.forEach(function(opt) {
        if (!opt.value || opt.text.toLowerCase().includes(filtro)) {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            catalogoSelect.appendChild(option);
        }
    });
});

// Auto-fill nombre al seleccionar servicio
catalogoSelect.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const nombreInput = document.getElementById('modal_nombre');
    if (opt.value && !nombreInput.value.trim()) {
        nombreInput.value = opt.text.replace(/^\[.*?\]\s+/, '');
    }
});

// === Modal Editar ===
const editCatalogoSelect = document.getElementById('edit_catalogo_select');
const editCatalogoBuscar = document.getElementById('edit_catalogo_buscar');
const editCatalogoOpciones = Array.from(editCatalogoSelect.options).map(opt => ({
    value: opt.value,
    text: opt.textContent
}));

editCatalogoBuscar.addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const valorActual = editCatalogoSelect.value;
    editCatalogoSelect.innerHTML = '';
    editCatalogoOpciones.forEach(function(opt) {
        if (!opt.value || opt.text.toLowerCase().includes(filtro)) {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            if (opt.value === valorActual) option.selected = true;
            editCatalogoSelect.appendChild(option);
        }
    });
});

const editPeriodicidad = document.getElementById('edit_periodicidad');
const editCampos = document.getElementById('edit_campos_vencimiento');

function toggleEditCampos() {
    const valor = editPeriodicidad.value;
    editCampos.style.display = (valor === 'anual' || valor === 'semestral') ? '' : 'none';
}
editPeriodicidad.addEventListener('change', toggleEditCampos);

function llenarModalEditar(data) {
    document.getElementById('formEditar').action = '/tipos-obligacion/' + data.id;
    document.getElementById('edit_nombre').value = data.nombre;
    document.getElementById('edit_regimen_id').value = data.regimen_id;
    document.getElementById('edit_periodicidad').value = data.periodicidad;
    document.getElementById('edit_mes_vencimiento').value = data.mes_vencimiento || '';
    document.getElementById('edit_dia_vencimiento').value = data.dia_vencimiento || '';
    editCatalogoSelect.value = data.catalogo_servicio_id || '';
    editCatalogoBuscar.value = '';
    // Restaurar todas las opciones
    editCatalogoSelect.innerHTML = '';
    editCatalogoOpciones.forEach(function(opt) {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        if (opt.value == (data.catalogo_servicio_id || '')) option.selected = true;
        editCatalogoSelect.appendChild(option);
    });
    toggleEditCampos();
}

// === Checkboxes de regímenes en modal Crear ===
function preSeleccionarRegimen(regimenId) {
    // Desmarcar todos primero
    document.querySelectorAll('.regimen-check').forEach(cb => cb.checked = false);
    // Marcar solo el del panel actual
    var cb = document.getElementById('crear_regimen_' + regimenId);
    if (cb) cb.checked = true;
}

document.getElementById('btnSeleccionarTodos').addEventListener('click', function() {
    document.querySelectorAll('.regimen-check').forEach(cb => cb.checked = true);
});
document.getElementById('btnDeseleccionarTodos').addEventListener('click', function() {
    document.querySelectorAll('.regimen-check').forEach(cb => cb.checked = false);
});

// Abrir modal automáticamente si hay errores de validación
@if($errors->any())
    new bootstrap.Modal(document.getElementById('modalCrear')).show();
@endif
</script>
@endsection