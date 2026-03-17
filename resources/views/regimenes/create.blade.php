@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('regimenes.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
        <h2 class="mb-0"><i class="bi bi-plus-circle"></i> Crear Régimen</h2>
    </div>

    <div class="card shadow-sm border-0" style="max-width:550px">
        <div class="card-body">
            <form action="{{ route('regimenes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" placeholder="Ej: RIMPE, Régimen General..." required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                    <select name="periodicidad" class="form-select @error('periodicidad') is-invalid @enderror" required>
                        <option value="mensual"   {{ old('periodicidad') == 'mensual'   ? 'selected' : '' }}>Mensual</option>
                        <option value="semestral" {{ old('periodicidad') == 'semestral' ? 'selected' : '' }}>Semestral</option>
                        <option value="anual"     {{ old('periodicidad') == 'anual'     ? 'selected' : '' }}>Anual</option>
                    </select>
                    @error('periodicidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Día fijo de vencimiento</label>
                    <input type="number" name="dia_fijo" class="form-control @error('dia_fijo') is-invalid @enderror"
                           value="{{ old('dia_fijo') }}" min="1" max="31" placeholder="Ej: 28 (vacío = según 9no dígito cédula)">
                    <div class="form-text">Si se deja vacío, el vencimiento se calculará usando el 9no dígito de la cédula del cliente.</div>
                    @error('dia_fijo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Mes de vencimiento (para regímenes anuales/semestrales)</label>
                    <select name="mes_vencimiento" class="form-select @error('mes_vencimiento') is-invalid @enderror">
                        <option value="">-- No aplica / Todos los meses --</option>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('mes_vencimiento') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                    @error('mes_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                    <a href="{{ route('regimenes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
