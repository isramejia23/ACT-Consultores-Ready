@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-collection"></i> Regímenes Tributarios</h2>
        @can('crear-regimen')
        <a href="{{ route('regimenes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Régimen
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle"></i> {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-3">
        @forelse($regimenes as $regimen)
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <strong><i class="bi bi-shield-check"></i> {{ $regimen->nombre }}</strong>
                    <span class="badge bg-light text-dark">{{ ucfirst($regimen->periodicidad) }}</span>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-2 small">
                        <li><i class="bi bi-calendar2"></i> <strong>Día fijo de vencimiento:</strong>
                            {{ $regimen->dia_fijo ? 'Día ' . $regimen->dia_fijo : 'Según 9no dígito cédula' }}
                        </li>
                        @if($regimen->mes_vencimiento)
                        <li><i class="bi bi-calendar-month"></i> <strong>Mes específico:</strong>
                            {{ \Carbon\Carbon::create()->month($regimen->mes_vencimiento)->translatedFormat('F') }}
                        </li>
                        @endif
                        <li class="mt-1"><i class="bi bi-people"></i> <strong>Clientes asignados:</strong> {{ $regimen->clientes_count }}</li>
                    </ul>

                    <hr class="my-2">
                    <p class="small mb-1 text-muted"><i class="bi bi-list-check"></i> <strong>Tipos de obligación ({{ $regimen->tiposObligacion->count() }}):</strong></p>
                    @if($regimen->tiposObligacion->count())
                    <ul class="list-unstyled small ps-2">
                        @foreach($regimen->tiposObligacion->take(5) as $tipo)
                        <li><span class="badge bg-secondary me-1">{{ ucfirst($tipo->periodicidad) }}</span> {{ $tipo->nombre }}</li>
                        @endforeach
                        @if($regimen->tiposObligacion->count() > 5)
                        <li class="text-muted">... y {{ $regimen->tiposObligacion->count() - 5 }} más</li>
                        @endif
                    </ul>
                    @else
                    <p class="small text-muted">Sin tipos de obligación aún. <a href="{{ route('tipos-obligacion.create') }}">Crear uno</a></p>
                    @endif
                </div>
                <div class="card-footer bg-transparent d-flex gap-2">
                    @can('editar-regimen')
                    <a href="{{ route('regimenes.edit', $regimen) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    @endcan
                    @can('borrar-regimen')
                    <form action="{{ route('regimenes.destroy', $regimen) }}" method="POST" onsubmit="return confirm('¿Eliminar este régimen?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Eliminar</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> No hay regímenes registrados aún. <a href="{{ route('regimenes.create') }}">Crear el primero</a>.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
