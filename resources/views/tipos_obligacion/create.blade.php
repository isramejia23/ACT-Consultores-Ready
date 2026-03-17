@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tipos-obligacion.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
        <h2 class="mb-0"><i class="bi bi-plus-circle"></i> Nuevo Tipo de Obligación</h2>
    </div>

    @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-x-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0" style="max-width:600px">
        <div class="card-body">
            <form action="{{ route('tipos-obligacion.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre del tipo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" placeholder="Ej: Declaración IVA Mensual, Anexo Accionistas..." required>
                    <div class="form-text">Describe la obligación específica de este régimen.</div>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Régimen <span class="text-danger">*</span></label>
                    <select name="regimen_id" id="regimen_id" class="form-select @error('regimen_id') is-invalid @enderror" required>
                        <option value="">-- Seleccionar Régimen --</option>
                        @foreach($regimenes as $regimen)
                        <option value="{{ $regimen->id }}" {{ old('regimen_id', request('regimen_id')) == $regimen->id ? 'selected' : '' }}>
                            {{ $regimen->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('regimen_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                    <select name="periodicidad" id="periodicidad" class="form-select @error('periodicidad') is-invalid @enderror" required>
                        <option value="mensual"   {{ old('periodicidad') == 'mensual'   ? 'selected' : '' }}>Mensual</option>
                        <option value="semestral" {{ old('periodicidad') == 'semestral' ? 'selected' : '' }}>Semestral</option>
                        <option value="anual"     {{ old('periodicidad') == 'anual'     ? 'selected' : '' }}>Anual</option>
                    </select>
                    @error('periodicidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row mb-3" id="campos_vencimiento" style="display:none;">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mes de vencimiento</label>
                        <select name="mes_vencimiento" id="mes_vencimiento" class="form-select @error('mes_vencimiento') is-invalid @enderror">
                            <option value="">-- Sin definir --</option>
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $nombreMes)
                                <option value="{{ $i + 1 }}" {{ old('mes_vencimiento') == ($i + 1) ? 'selected' : '' }}>{{ $nombreMes }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Mes en que se genera esta obligación.</div>
                        @error('mes_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Día de vencimiento</label>
                        <input type="number" name="dia_vencimiento" class="form-control @error('dia_vencimiento') is-invalid @enderror"
                               value="{{ old('dia_vencimiento') }}" min="1" max="28" placeholder="Ej: 15">
                        <div class="form-text">Si se deja vacío, usa el día del régimen o cédula.</div>
                        @error('dia_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Servicio del Catálogo (opcional)</label>
                    <select name="catalogo_servicio_id" class="form-select @error('catalogo_servicio_id') is-invalid @enderror"
                            id="catalogo_select">
                        <option value="">-- Sin vincular a servicio específico --</option>
                        @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id }}" {{ old('catalogo_servicio_id') == $servicio->id ? 'selected' : '' }}>
                            [{{ $servicio->codigo }}] {{ $servicio->nombre }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-text">Al vincularlo con el catálogo, el sistema podrá hacer match automático cuando se importe el Excel.</div>
                    @error('catalogo_servicio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                    <a href="{{ route('tipos-obligacion.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mostrar/ocultar campos de mes y día según periodicidad
const periodicidadSelect = document.getElementById('periodicidad');
const camposVencimiento = document.getElementById('campos_vencimiento');

function toggleCamposVencimiento() {
    const valor = periodicidadSelect.value;
    camposVencimiento.style.display = (valor === 'anual' || valor === 'semestral') ? '' : 'none';
}

periodicidadSelect.addEventListener('change', toggleCamposVencimiento);
toggleCamposVencimiento(); // estado inicial

// Filtrar el catalogo select usando Select2 o búsqueda simple
document.getElementById('catalogo_select').addEventListener('change', function() {
    // Auto-fill nombre si se selecciona un servicio y el campo nombre está vacío
    const opt = this.options[this.selectedIndex];
    const nombreInput = document.querySelector('input[name="nombre"]');
    if (opt.value && !nombreInput.value.trim()) {
        nombreInput.value = opt.text.replace(/^\[.*?\]\s+/, '');
    }
});
</script>
@endsection
