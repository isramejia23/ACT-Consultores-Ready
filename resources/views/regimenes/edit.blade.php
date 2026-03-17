@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('regimenes.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
        <h2 class="mb-0"><i class="bi bi-pencil"></i> Editar Régimen: {{ $regimen->nombre }}</h2>
    </div>

    <div class="card shadow-sm border-0" style="max-width:550px">
        <div class="card-body">
            <form action="{{ route('regimenes.update', $regimen) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $regimen->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                    <select name="periodicidad" class="form-select" required>
                        <option value="mensual"   {{ old('periodicidad', $regimen->periodicidad) == 'mensual'   ? 'selected' : '' }}>Mensual</option>
                        <option value="semestral" {{ old('periodicidad', $regimen->periodicidad) == 'semestral' ? 'selected' : '' }}>Semestral</option>
                        <option value="anual"     {{ old('periodicidad', $regimen->periodicidad) == 'anual'     ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Día fijo de vencimiento</label>
                    <input type="number" name="dia_fijo" class="form-control"
                           value="{{ old('dia_fijo', $regimen->dia_fijo) }}" min="1" max="31">
                    <div class="form-text">Vacío = según 9no dígito de la cédula.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Mes de vencimiento</label>
                    <select name="mes_vencimiento" class="form-select">
                        <option value="">-- No aplica --</option>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('mes_vencimiento', $regimen->mes_vencimiento) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                    <a href="{{ route('regimenes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
