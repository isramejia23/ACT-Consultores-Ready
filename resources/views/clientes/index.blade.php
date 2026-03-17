@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-people-fill"></i> Lista de Clientes</h2>
    @can('crear-cliente') 
        <a href="{{ route('clientes.create') }}" class="btn btn-primary mb-4">
            <i class="bi bi-person-add"></i> Crear Nuevo Cliente
        </a>
    @endcan

    

    <form action="{{ route('clientes.index') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">

            <!-- Filtro por asesor solo para administrador -->

            @can('ver-todos-clientes')
            <div class="col-md-4">
                <label class="form-label" for="asesor_id">Filtrar por asesor:</label>
                <div class="input-group">
                    <select name="asesor_id" id="asesor_id" class="form-select">
                        <option value="">Todos los asesores</option>
                        @foreach($asesores as $asesor)
                            <option value="{{ $asesor->id }}" {{ request('asesor_id') == $asesor->id ? 'selected' : '' }}>
                                {{ $asesor->nombre }} {{ $asesor->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endcan

            <!-- Campo de búsqueda -->
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar cliente:</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cédula o nombre" value="{{ request('search') }}">
                </div>
            </div>
            
            <!-- Filtro por régimen -->
            <div class="col-md-4">
                <label for="regimen" class="form-label">Filtrar por régimen:</label>
                <select name="regimen_id" id="regimen" class="form-select">
                    <option value="">Todos los regímenes</option>
                    @foreach($regimenes as $regimenOption)
                        <option value="{{ $regimenOption->id }}" {{ request('regimen_id') == $regimenOption->id ? 'selected' : '' }}>
                            {{ $regimenOption->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Botón de aplicar filtros -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Aplicar
                </button>
            </div>
            
            <!-- Botón de limpiar -->
            <div class="col-md-2">
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-person"></i> Nombre</th>
                    <th><i class="bi bi-card-text"></i> Cédula</th>
                    <th><i class="bi bi-envelope"></i> Email</th>
                    <th><i class="bi bi-telephone"></i> Teléfono</th>
                    <th><i class="bi bi-building"></i> Régimen</th>
                    <th><i class="bi bi-building"></i> Saldo</th>
                    <th><i class="bi bi-gear"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre_cliente }}</td>
                        <td>{{ $cliente->cedula_cliente }}</td>
                        <td>{{ $cliente->email_cliente }}</td>
                        <td>{{ $cliente->telefono_cliente }}</td>
                        <td>{{ $cliente->regimen ? $cliente->regimen->nombre : 'Sin Régimen' }}</td>
                        <td>
                            @if(empty($cliente->saldo) || $cliente->saldo == 0)
                                <span class="text-success">Al día</span>
                            @else
                                ${{ number_format($cliente->saldo, 2) }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('clientes.show', $cliente->id_clientes) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> 
                                </a>
                                @can('editar-cliente')
                                    <a href="{{ route('clientes.edit', $cliente->id_clientes) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-pencil"></i> 
                                    </a>
                                @endcan
                                @can('borrar-cliente')
                                <form action="{{ route('clientes.destroy', $cliente->id_clientes) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn--outline-danger btn-sm" onclick="confirmDelete(event, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('mensajes-cliente')
                                <!-- Botón para abrir el modal -->
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEnviarMensaje{{ $cliente->id_clientes }}">
                                    <i class="bi bi-whatsapp"></i>
                                </button>
                                @endcan
                                @can('crear-obligacion')
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearObligacion{{ $cliente->id_clientes }}" title="Crear obligación">
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    <!-- Modal para cada cliente -->
                    <div class="modal fade" id="modalEnviarMensaje{{ $cliente->id_clientes }}" tabindex="-1" aria-labelledby="modalEnviarMensajeLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Encabezado del modal -->
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-whatsapp"></i> Enviar Mensaje a {{ $cliente->nombre_cliente ?? 'Cliente' }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Cuerpo del modal -->
                                <div class="modal-body">
                                    <form id="formEnviarMensaje{{ $cliente->id_clientes }}" action="{{ route('notificar.cliente') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id_cliente" value="{{ $cliente->id_clientes }}">

                                        <!-- Información del cliente -->
                                        <div class="mb-3">
                                            <p><strong>Cliente:</strong> {{ $cliente->nombre_cliente ?? 'Sin cliente' }}</p>
                                        </div>

                                        <!-- Campo de mensaje -->
                                        <textarea class="form-control" name="mensaje" id="mensaje" rows="4" required>
Estimado/a {{ $cliente->nombre_cliente ?? 'Cliente' }}, 

{{ old('mensaje', '¡Gracias por su confianza!') }}
                                        </textarea>

                                        <!-- Campo para adjuntar archivo -->
                                        <!-- <div class="mb-3">
                                            <label for="archivo" class="form-label"><i class="bi bi-paperclip"></i> Adjuntar archivo (opcional):</label>
                                            <input type="file" class="form-control" name="archivo" id="archivo">
                                        </div> -->

                                        <!-- Botones de acción -->
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Enviar Mensaje</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Crear Obligación -->
                    @can('crear-obligacion')
                    <div class="modal fade" id="modalCrearObligacion{{ $cliente->id_clientes }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-calendar-plus"></i> Nueva Obligación - {{ $cliente->nombre_cliente }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('clientes.obligacion.store', $cliente->id_clientes) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Cliente:</strong> {{ $cliente->nombre_cliente }}</p>
                                            <p class="mb-0"><strong>Régimen:</strong>
                                                <span class="badge bg-secondary">{{ $cliente->regimen ? $cliente->regimen->nombre : 'Sin Régimen' }}</span>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Servicio del Catálogo <span class="text-danger">*</span></label>
                                            <select name="catalogo_servicio_id" class="form-select select-catalogo-servicio"
                                                    style="width:100%" required>
                                                <option value="">-- Cargando servicios... --</option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Periodicidad <span class="text-danger">*</span></label>
                                                <select name="periodicidad" class="form-select" required>
                                                    <option value="">-- Seleccionar --</option>
                                                    <option value="mensual">Mensual</option>
                                                    <option value="anual">Anual</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Día de Vencimiento <span class="text-danger">*</span></label>
                                                <input type="number" name="dia_vencimiento" class="form-control" min="1" max="28" placeholder="1-28" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Crear Obligación</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endcan
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $clientes->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var catalogoCargado = false;
var catalogoOpciones = '';

$('.select-catalogo-servicio').each(function() {
    var $select = $(this);
    var $modal = $select.closest('.modal');

    $modal.on('shown.bs.modal', function() {
        if ($select.hasClass('select2-hidden-accessible')) return;

        if (catalogoCargado) {
            $select.html(catalogoOpciones);
            $select.select2({
                theme: 'bootstrap-5',
                placeholder: '-- Buscar servicio --',
                allowClear: true,
                dropdownParent: $modal
            });
            return;
        }

        $.getJSON('/api/catalogo-servicios', function(servicios) {
            catalogoOpciones = '<option value="">-- Buscar servicio --</option>';
            servicios.forEach(function(s) {
                catalogoOpciones += '<option value="' + s.id + '">' + s.codigo + ' - ' + s.nombre + '</option>';
            });
            catalogoCargado = true;
            $select.html(catalogoOpciones);
            $select.select2({
                theme: 'bootstrap-5',
                placeholder: '-- Buscar servicio --',
                allowClear: true,
                dropdownParent: $modal
            });
        }).fail(function() {
            $select.html('<option value="">-- Error al cargar servicios --</option>');
        });
    });
});
</script>
@endsection