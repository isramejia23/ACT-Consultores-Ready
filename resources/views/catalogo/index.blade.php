@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-grid"></i> Catálogo de Servicios</h2>
        <span class="text-muted small">Total: {{ $catalogos->total() }} servicios</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('catalogo.index') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por código, nombre o categoría..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="genera_obligacion" class="form-select">
                    <option value="">Genera obligación (todos)</option>
                    <option value="1" {{ request('genera_obligacion') == '1' ? 'selected' : '' }}>Sí genera</option>
                    <option value="0" {{ request('genera_obligacion') == '0' ? 'selected' : '' }}>No genera</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="activo" class="form-select">
                    <option value="">Estado (todos)</option>
                    <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
                <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x"></i></a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover shadow-sm align-middle small">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Periodicidad</th>
                    <th class="text-center">Mes</th>
                    <th class="text-center">Genera<br>Obligación</th>
                    <th class="text-center">Activo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($catalogos as $servicio)
                <tr class="{{ $servicio->trashed() ? 'table-secondary text-muted' : '' }}">
                    <td><code>{{ $servicio->codigo }}</code></td>
                    <td>{{ $servicio->nombre }}</td>
                    <td>
                        @if($servicio->categoria)
                        <span class="badge bg-info text-dark">{{ $servicio->categoria }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($servicio->periodicidad ?? '-') }}</td>
                    <td class="text-center">
                        @if($servicio->mes)
                        {{ \Carbon\Carbon::create()->month($servicio->mes)->translatedFormat('M') }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-center">
                        <button
                            class="btn btn-sm {{ $servicio->genera_obligacion ? 'btn-success' : 'btn-outline-secondary' }} toggle-obligacion"
                            data-id="{{ $servicio->id }}"
                            data-url="{{ route('catalogo.toggleObligacion', $servicio) }}"
                            title="{{ $servicio->genera_obligacion ? 'Haz clic para desactivar' : 'Haz clic para activar' }}"
                            @if($servicio->trashed()) disabled @endif>
                            <i class="bi bi-{{ $servicio->genera_obligacion ? 'bell-fill' : 'bell-slash' }}"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        <button
                            class="btn btn-sm {{ $servicio->trashed() ? 'btn-outline-danger' : 'btn-success' }} toggle-activo"
                            data-id="{{ $servicio->id }}"
                            data-url="{{ route('catalogo.toggleActivo', $servicio->id) }}"
                            title="{{ $servicio->trashed() ? 'Reactivar' : 'Desactivar' }}">
                            <i class="bi bi-{{ $servicio->trashed() ? 'toggle-off' : 'toggle-on' }}"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox"></i> No se encontraron servicios</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $catalogos->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Toggle genera_obligacion
    document.querySelectorAll('.toggle-obligacion').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.dataset.url;
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                this.classList.toggle('btn-success', data.genera_obligacion);
                this.classList.toggle('btn-outline-secondary', !data.genera_obligacion);
                this.querySelector('i').className = `bi bi-${data.genera_obligacion ? 'bell-fill' : 'bell-slash'}`;
            });
        });
    });

    // Toggle activo/inactivo
    document.querySelectorAll('.toggle-activo').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.dataset.url;
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const row = this.closest('tr');
                row.classList.toggle('table-secondary', !data.activo);
                row.classList.toggle('text-muted', !data.activo);
                this.classList.toggle('btn-success', data.activo);
                this.classList.toggle('btn-outline-danger', !data.activo);
                this.querySelector('i').className = `bi bi-toggle-${data.activo ? 'on' : 'off'}`;
                this.title = data.activo ? 'Desactivar' : 'Reactivar';
            });
        });
    });
});
</script>
@endsection
