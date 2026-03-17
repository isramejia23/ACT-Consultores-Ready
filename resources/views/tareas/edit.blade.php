@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="bi bi-pencil-square text-primary me-2"></i> Editar Tarea</h1>

    </div>

    <form action="{{ route('tareas.update', $tarea->id_tareas) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Primera columna -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="cliente" value="{{ $tarea->cliente->nombre_cliente }}" disabled>
                            <label for="cliente">Cliente</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i> Detalles de la Tarea</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" class="form-control" id="nombre" value="{{ $tarea->nombre }}" required>
                            <label for="nombre">Nombre de la tarea</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="numero_factura" class="form-control" id="numero_factura" value="{{ $tarea->numero_factura }}" required>
                                    <label for="numero_factura">N° de Factura</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fecha_facturada" class="form-control" id="fecha_facturada" value="{{ $tarea->fecha_facturada }}" required>
                                    <label for="fecha_facturada">Fecha Facturada</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="estado" class="form-select" id="estado" required>
                                        <option value="Cumplida" {{ $tarea->estado == 'Cumplida' ? 'selected' : '' }}>Cumplida</option>
                                        <option value="En Proceso" {{ $tarea->estado == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="Pendiente" {{ $tarea->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="Anulada" {{ $tarea->estado == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                                    </select>
                                    <label for="estado">Estado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fecha_cumplida" class="form-control" id="fecha_cumplida" value="{{ $tarea->fecha_cumplida }}">
                                    <label for="fecha_cumplida">Fecha Cumplida</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Información Financiera</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="cantidad" class="form-control" id="cantidad" value="{{ $tarea->cantidad }}" required min="1" step="1">
                                    <label for="cantidad">Cantidad</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="precio_unitario" class="form-control" id="precio_unitario" value="{{ $tarea->precio_unitario }}" required min="0" step="0.01">
                                    <label for="precio_unitario">P. Unitario</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="total" class="form-control" id="total" value="{{ $tarea->total }}" required min="0" step="0.01" readonly>
                                    <label for="total">Total</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i> Documentación</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Archivo adjunto</label>
                            <input type="file" name="archivo" class="form-control" id="archivo" accept=".pdf,.doc,.docx,.jpg,.png">
                            <div class="form-text">Formatos aceptados: PDF, Word, JPG, PNG,XLS (Max 5MB)</div>
                            
                            @if ($tarea->archivo)
                                <div class="mt-3">
                                    <label class="form-label">Archivo actual:</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text fs-4 me-2 text-primary"></i>
                                        <a href="{{ asset('storage/' . $tarea->archivo) }}" target="_blank" class="text-decoration-none">
                                            {{ basename($tarea->archivo) }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-floating">
                            <textarea name="observacion" class="form-control" placeholder=" " id="observacion" style="height: 100px">{{ $tarea->observacion }}</textarea>
                            <label for="observacion">Observaciones</label>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('tareas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Regresar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Actualizar Tarea
                            </button>
                    </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Necesario para mantener en la misma pagina y con los filtros -->
        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="month" value="{{ request('month') }}">
        <input type="hidden" name="year" value="{{ request('year') }}">
        <input type="hidden" name="page" value="{{ request('page') }}">


    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cálculo automático del total
    const cantidadInput = document.getElementById('cantidad');
    const precioUnitarioInput = document.getElementById('precio_unitario');
    const totalInput = document.getElementById('total');
    
    function calcularTotal() {
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioUnitarioInput.value) || 0;
        const total = cantidad * precio;
        
        totalInput.value = total.toFixed(2);
    }
    
    cantidadInput.addEventListener('input', calcularTotal);
    precioUnitarioInput.addEventListener('input', calcularTotal);
});
</script>

<style>
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .card-header {
        border-radius: 0 !important;
    }
    
    .form-floating label {
        color: #6c757d;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label {
        color: #0d6efd;
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    a.text-decoration-none:hover {
        text-decoration: underline !important;
    }
</style>
@endsection