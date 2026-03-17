@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Crear Nueva Tarea</h1>

    </div>

    <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data" id="tareaForm">
        @csrf

        <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" id="clienteSearch" class="form-control" placeholder=" ">
                            <label for="clienteSearch">Buscar cliente</label>
                            <div class="form-text">Busque por nombre o cédula del cliente</div>
                        </div>

                        <div class="form-floating mb-3">
                            <select name="id_clientes" id="id_clientes" class="form-select" required>
                                <option value="" disabled selected></option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id_clientes }}" 
                                        data-cedula="{{ $cliente->cedula_cliente }}"
                                        data-nombre="{{ $cliente->nombre_cliente }}">
                                        {{ $cliente->nombre_cliente }} ({{ $cliente->cedula_cliente }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="id_clientes">Cliente seleccionado</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i> Detalles de la Tarea</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" class="form-control" id="nombre" placeholder=" " required>
                            <label for="nombre">Nombre de la tarea</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="numero_factura" class="form-control" id="numero_factura" placeholder=" " required>
                                    <label for="numero_factura">N° de Factura</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fecha_facturada" class="form-control" id="fecha_facturada" required>
                                    <label for="fecha_facturada">Fecha Facturada</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="estado" class="form-select" id="estado" required>
                                        <option value="Cumplida">Cumplida</option>
                                        <option value="En Proceso">En Proceso</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Anulada">Anulada</option>
                                    </select>
                                    <label for="estado">Estado</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="fechaCumplidaContainer">
                                <div class="form-floating">
                                    <input type="date" name="fecha_cumplida" class="form-control" id="fecha_cumplida">
                                    <label for="fecha_cumplida">Fecha Cumplida</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Información Financiera</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="cantidad" id="cantidad" class="form-control" placeholder=" " required min="1" step="1">
                                    <label for="cantidad">Cantidad</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="precio_unitario" id="precio_unitario" class="form-control" placeholder=" " required min="0" step="0.01">
                                    <label for="precio_unitario">P. Unitario</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" name="total" id="total" class="form-control" placeholder=" " required min="0" step="0.01" readonly>
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
                        </div>

                        <div class="form-floating">
                            <textarea name="observacion" class="form-control" placeholder=" " id="observacion" style="height: 100px"></textarea>
                            <label for="observacion">Observaciones</label>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('tareas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Salir
                            </a>
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-eraser me-1"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Guardar Tarea
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscador de clientes
    const clienteSearch = document.getElementById('clienteSearch');
    const selectCliente = document.getElementById('id_clientes');
    
    clienteSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = selectCliente.options;
        
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const cedula = option.getAttribute('data-cedula') || '';
            const nombre = option.getAttribute('data-nombre') || '';
            
            if (cedula.toLowerCase().includes(searchTerm) || nombre.toLowerCase().includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    });

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

    // Mostrar/ocultar fecha de cumplimiento según estado
    const estadoSelect = document.getElementById('estado');
    const fechaCumplidaContainer = document.getElementById('fechaCumplidaContainer');
    
    function toggleFechaCumplida() {
        fechaCumplidaContainer.style.display = estadoSelect.value === 'Cumplida' ? 'block' : 'none';
    }
    
    estadoSelect.addEventListener('change', toggleFechaCumplida);
    toggleFechaCumplida();

    // Validación del formulario
    document.getElementById('tareaForm').addEventListener('submit', function(e) {
        if (estadoSelect.value === 'Cumplida' && !document.getElementById('fecha_cumplida').value) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Fecha requerida',
                text: 'Debe ingresar la fecha de cumplimiento para tareas cumplidas',
                confirmButtonColor: '#0d6efd'
            });
        }
    });
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
    
    #fechaCumplidaContainer {
        display: none;
    }
    
    select option[style*="display: none"] {
        color: #dee2e6;
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
</style>
@endsection