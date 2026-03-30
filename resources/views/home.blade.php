@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Navegación de Meses -->
    <div class="row mt-2">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <a href="{{ route('home', ['month' => $currentMonth - 1, 'year' => $currentYear]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Mes Anterior
            </a>
            <h4 class="mb-0">
                {{ ucfirst(\Carbon\Carbon::create($currentYear, $currentMonth)->translatedFormat('F Y')) }}
            </h4>
            <a href="{{ route('home', ['month' => $currentMonth + 1, 'year' => $currentYear]) }}" class="btn btn-outline-secondary">
                Mes Siguiente <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mt-2 g-2">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-success bg-opacity-10 me-3">
                        <i class="bi bi-people text-success" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Total Clientes</div>
                        <div class="fw-bold fs-5">{{ $totalClientes }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-secondary bg-opacity-10 me-3">
                        <i class="bi bi-clock text-secondary" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Tareas Pendientes</div>
                        <div class="fw-bold fs-5">{{ $tareasPendientes }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-info bg-opacity-10 me-3">
                        <i class="bi bi-check-circle text-info" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Tareas Cumplidas</div>
                        <div class="fw-bold fs-5">{{ $tareasCumplidas }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-primary bg-opacity-10 me-3">
                        <i class="bi bi-gear text-primary" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Tareas en Proceso</div>
                        <div class="fw-bold fs-5">{{ $tareasEnProceso }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Alertas -->
    <div class="row mt-2 g-2">
        <!-- Firmas por Vencer -->
        <div class="col-6 col-md-3">
            <div class="card card-alerta shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#modalFirmasPorVencer" role="button">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-primary bg-opacity-10 me-3">
                        <i class="bi bi-pen text-primary" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Firmas por Vencer</div>
                        <div class="fw-bold fs-5">{{ $clientesPorVencer->count() }}</div>
                    </div>
                    @if($clientesPorVencer->count() > 0)
                        <span class="badge bg-danger rounded-pill">!</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Firmas Vencidas -->
        <div class="col-6 col-md-3">
            <div class="card card-alerta shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#modalFirmasVencidas" role="button">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-danger bg-opacity-10 me-3">
                        <i class="bi bi-pen-fill text-danger" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Firmas Vencidas</div>
                        <div class="fw-bold fs-5">{{ $clientesVencidos->count() }}</div>
                    </div>
                    @if($clientesVencidos->count() > 0)
                        <span class="badge bg-danger rounded-pill">!</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Facturación por Vencer -->
        <div class="col-6 col-md-3">
            <div class="card card-alerta shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#modalFacturacionPorVencer" role="button">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-info bg-opacity-10 me-3">
                        <i class="bi bi-receipt-cutoff text-info" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Facturación por Vencer</div>
                        <div class="fw-bold fs-5">{{ $facturacionPorVencer->count() }}</div>
                    </div>
                    @if($facturacionPorVencer->count() > 0)
                        <span class="badge bg-danger rounded-pill">!</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Facturación Vencida -->
        <div class="col-6 col-md-3">
            <div class="card card-alerta shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#modalFacturacionVencida" role="button">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-circle bg-danger bg-opacity-10 me-3">
                        <i class="bi bi-receipt text-danger" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted">Facturación Vencida</div>
                        <div class="fw-bold fs-5">{{ $facturacionVencidos->count() }}</div>
                    </div>
                    @if($facturacionVencidos->count() > 0)
                        <span class="badge bg-danger rounded-pill">!</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario de Obligaciones -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-calendar3 me-2"></i> Calendario de Obligaciones
                </div>
                <div class="card-body">
                    <div id="calendario"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Obligaciones Completadas del Mes -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-check-circle me-2"></i> Completadas este mes</span>
                    <span class="badge bg-light text-success" id="contadorCompletadas">
                        {{ collect($obligaciones)->where('completado', true)->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Obligación</th>
                                    <th>Fecha Vencimiento</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCompletadas">
                                @foreach(collect($obligaciones)->where('completado', true) as $ob)
                                <tr>
                                    <td>{{ $ob['cliente_nombre'] }}</td>
                                    <td>{{ $ob['tipo_nombre'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ob['fecha_vencimiento'])->format('d/m/Y') }}</td>
                                    <td class="text-center"><span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Completada</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(collect($obligaciones)->where('completado', true)->isEmpty())
                    <div class="text-center py-3 text-muted" id="sinCompletadas">
                        <i class="bi bi-calendar-x fs-3"></i>
                        <p class="mt-2 mb-0">No hay obligaciones completadas este mes</p>
                    </div>
                    @else
                    <div class="text-center py-3 text-muted d-none" id="sinCompletadas">
                        <i class="bi bi-calendar-x fs-3"></i>
                        <p class="mt-2 mb-0">No hay obligaciones completadas este mes</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Obligaciones del Día -->
<div class="modal fade" id="modalObligacionesDia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Obligaciones del <span id="modalObligacionesFecha"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Obligación</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaObligacionesDia">
                        </tbody>
                    </table>
                </div>
                <div id="sinObligaciones" class="p-4 text-center text-muted d-none">
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    <p class="mt-2 mb-0">No hay obligaciones para este día</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form oculto para marcar completado desde el calendario -->
<form id="formCompletarCalendario" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

{{-- ==================== MODALES ==================== --}}

<!-- Modal Firmas por Vencer -->
<div class="modal fade" id="modalFirmasPorVencer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pen me-2"></i>Firmas por Vencer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($clientesPorVencer->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($clientesPorVencer as $cliente)
                            @php
                                $diasRestantes = now()->diffInDays($cliente->fecha_firma);
                                $bgClass = $diasRestantes <= 7 ? 'list-group-item-danger' : '';
                            @endphp
                            <div class="list-group-item {{ $bgClass }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cliente->nombre_cliente }}</strong>
                                        <br><small class="text-muted">Vence: @formatoFecha($cliente->fecha_firma)</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-dark rounded-pill">{{ round($diasRestantes) }}d</span>
                                        <button class="btn btn-sm btn-outline-primary ms-2"
                                                data-bs-toggle="modal" data-bs-target="#modalNotificacion{{ $cliente->id_clientes }}">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        <p class="mt-2 mb-0">No hay firmas proximas a vencer</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Firmas Vencidas -->
<div class="modal fade" id="modalFirmasVencidas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-pen-fill me-2"></i>Firmas Vencidas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($clientesVencidos->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($clientesVencidos as $cliente)
                            @php $diasVencidos = now()->diffInDays($cliente->fecha_firma); @endphp
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cliente->nombre_cliente }}</strong>
                                        <br><small class="text-muted">Vencio: @formatoFecha($cliente->fecha_firma)</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger rounded-pill">{{ round($diasVencidos) }}d</span>
                                        <button class="btn btn-sm btn-outline-primary ms-2"
                                                data-bs-toggle="modal" data-bs-target="#modalNotificacionVencida{{ $cliente->id_clientes }}">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        <p class="mt-2 mb-0">No hay firmas vencidas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Facturacion por Vencer -->
<div class="modal fade" id="modalFacturacionPorVencer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-receipt-cutoff me-2"></i>Facturacion por Vencer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($facturacionPorVencer->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($facturacionPorVencer as $cliente)
                            @php $diasRestantes = now()->diffInDays($cliente->fecha_facturacion); @endphp
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cliente->nombre_cliente }}</strong>
                                        <br><small class="text-muted">Vence: @formatoFecha($cliente->fecha_facturacion)</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-dark rounded-pill">{{ round($diasRestantes) }}d</span>
                                        <button class="btn btn-sm btn-outline-primary ms-2"
                                                data-bs-toggle="modal" data-bs-target="#modalNotificacionFacturacion{{ $cliente->id_clientes }}">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        <p class="mt-2 mb-0">No hay facturaciones proximas a vencer</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Facturacion Vencida -->
<div class="modal fade" id="modalFacturacionVencida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Facturacion Vencida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($facturacionVencidos->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($facturacionVencidos as $cliente)
                            @php $diasVencidos = now()->diffInDays($cliente->fecha_facturacion); @endphp
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cliente->nombre_cliente }}</strong>
                                        <br><small class="text-muted">Vencio: @formatoFecha($cliente->fecha_facturacion)</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger rounded-pill">{{ round($diasVencidos) }}d</span>
                                        <button class="btn btn-sm btn-outline-primary ms-2"
                                                data-bs-toggle="modal" data-bs-target="#modalNotificacionFacturacion{{ $cliente->id_clientes }}">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        <p class="mt-2 mb-0">No hay facturaciones vencidas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- Contenedor oculto: solo se necesitan los modales de estos partials --}}
<div style="display:none;">
    @foreach($clientesPorVencer as $cliente)
        @include('partials.clienteFirma', ['cliente' => $cliente])
    @endforeach
    @foreach($clientesVencidos as $cliente)
        @include('partials.clienteFirmaVencida', ['cliente' => $cliente])
    @endforeach
    @foreach($facturacionPorVencer as $cliente)
        @include('partials.clienteFacturacion', ['cliente' => $cliente])
    @endforeach
    @foreach($facturacionVencidos as $cliente)
        @include('partials.facturacionVencida', ['cliente' => $cliente])
    @endforeach
</div>

<style>
    .card-alerta {
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-left: 4px solid transparent;
    }
    .card-alerta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .icon-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* FullCalendar overrides */
    #calendario .fc-toolbar-title {
        font-size: 1.1rem !important;
    }
    #calendario .fc-event {
        font-size: 0.78rem;
        border: none;
        padding: 2px 6px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        text-align: center;
    }
    #calendario .fc-daygrid-day-number {
        font-size: 0.85rem;
    }
    #calendario .fc-daygrid-event {
        margin: 1px 2px;
    }
</style>
@endsection

@section('scripts')
<!-- FullCalendar CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/es.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario');
    if (!calendarEl) return;

    var obligaciones = @json($obligaciones ?? []);
    var calendar;
    var csrfToken = '{{ csrf_token() }}';

    function construirEventos() {
        var porFecha = {};
        obligaciones.forEach(function(ob) {
            var fecha = ob.fecha_vencimiento;
            if (!porFecha[fecha]) {
                porFecha[fecha] = { pendientes: 0, completadas: 0, vencidas: 0, items: [] };
            }
            if (ob.completado) {
                porFecha[fecha].completadas++;
            } else if (new Date(fecha + 'T23:59:59') < new Date()) {
                porFecha[fecha].vencidas++;
            } else {
                porFecha[fecha].pendientes++;
            }
            porFecha[fecha].items.push(ob);
        });

        var eventos = [];
        Object.keys(porFecha).forEach(function(fecha) {
            var grupo = porFecha[fecha];
            var noPendientes = grupo.pendientes + grupo.vencidas;
            if (noPendientes === 0 && grupo.completadas > 0) {
                // Todas completadas: solo mostrar badge verde
                eventos.push({
                    title: grupo.completadas + ' completada(s)',
                    start: fecha,
                    color: '#198754',
                    display: 'block',
                    extendedProps: { fecha: fecha, items: grupo.items }
                });
            } else if (noPendientes > 0) {
                // Pendientes/vencidas
                var color = grupo.vencidas > 0 ? '#dc3545' : '#0d6efd';
                eventos.push({
                    title: noPendientes + ' pendiente(s)',
                    start: fecha,
                    color: color,
                    display: 'block',
                    extendedProps: { fecha: fecha, items: grupo.items }
                });
            }
        });
        return eventos;
    }

    function refrescarCalendario() {
        calendar.removeAllEvents();
        construirEventos().forEach(function(ev) { calendar.addEvent(ev); });
    }

    function actualizarTablaCompletadas() {
        var completadas = obligaciones.filter(function(ob) { return ob.completado; });
        var tbody = document.getElementById('tbodyCompletadas');
        var sinMsg = document.getElementById('sinCompletadas');
        var contador = document.getElementById('contadorCompletadas');

        contador.textContent = completadas.length;

        if (completadas.length === 0) {
            tbody.innerHTML = '';
            sinMsg.classList.remove('d-none');
        } else {
            sinMsg.classList.add('d-none');
            var html = '';
            completadas.forEach(function(ob) {
                var partes = ob.fecha_vencimiento.split('-');
                html += '<tr>' +
                    '<td>' + (ob.cliente_nombre || '-') + '</td>' +
                    '<td>' + (ob.tipo_nombre || '-') + '</td>' +
                    '<td>' + partes[2] + '/' + partes[1] + '/' + partes[0] + '</td>' +
                    '<td class="text-center"><span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Completada</span></td>' +
                    '</tr>';
            });
            tbody.innerHTML = html;
        }
    }

    window.marcarCompletadoCal = function(id) {
        Swal.fire({
            title: '¿Marcar como completado?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, completar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            fetch('/vencimientos/' + id + '/completar', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    // Actualizar en el array local
                    obligaciones.forEach(function(ob) {
                        if (ob.id === id) { ob.completado = true; ob.estado = 'completada'; }
                    });

                    // Refrescar calendario y tabla
                    refrescarCalendario();
                    actualizarTablaCompletadas();

                    // Cerrar modal y mostrar éxito
                    var modalEl = document.getElementById('modalObligacionesDia');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    Swal.fire({ icon: 'success', title: 'Completada', showConfirmButton: false, timer: 1200 });
                }
            })
            .catch(function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la obligación' });
            });
        });
    };

    window.anularObligacionCal = function(id) {
        anularObligacion(id, function(anulId) {
            obligaciones = obligaciones.filter(function(ob) { return ob.id !== anulId; });
            refrescarCalendario();
            actualizarTablaCompletadas();
        });
    };

    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        initialDate: '{{ $currentYear }}-{{ str_pad($currentMonth, 2, "0", STR_PAD_LEFT) }}-01',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        buttonText: { today: 'Hoy', month: 'Mes', list: 'Lista' },
        height: 'auto',
        events: construirEventos(),
        eventClick: function(info) {
            abrirModalObligaciones(info.event.extendedProps.fecha, info.event.extendedProps.items);
        },
        noEventsContent: 'No hay obligaciones para este periodo'
    });

    calendar.render();

    function abrirModalObligaciones(fecha, items) {
        // Releer items actualizados del array principal
        var itemsActualizados = obligaciones.filter(function(ob) { return ob.fecha_vencimiento === fecha; });

        var partes = fecha.split('-');
        document.getElementById('modalObligacionesFecha').textContent = partes[2] + '/' + partes[1] + '/' + partes[0];

        var tbody = document.getElementById('tablaObligacionesDia');
        var sinOblig = document.getElementById('sinObligaciones');

        if (itemsActualizados.length === 0) {
            tbody.innerHTML = '';
            sinOblig.classList.remove('d-none');
            tbody.closest('.table-responsive').classList.add('d-none');
        } else {
            sinOblig.classList.add('d-none');
            tbody.closest('.table-responsive').classList.remove('d-none');

            var html = '';
            itemsActualizados.forEach(function(ob) {
                var estadoBadge = '';
                var acciones = '';
                var estado = ob.estado || (ob.completado ? 'completada' : 'pendiente');

                if (estado === 'completada') {
                    estadoBadge = '<span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Completada</span>';
                    acciones = '<span class="badge bg-success"><i class="bi bi-check-circle-fill"></i></span>';
                } else if (estado === 'anulada') {
                    estadoBadge = '<span class="badge bg-secondary"><i class="bi bi-x-circle-fill"></i> Anulada</span>';
                    acciones = '<span class="badge bg-secondary"><i class="bi bi-x-circle-fill"></i></span>';
                } else if (new Date(ob.fecha_vencimiento + 'T23:59:59') < new Date()) {
                    estadoBadge = '<span class="badge bg-danger">Vencida</span>';
                } else {
                    estadoBadge = '<span class="badge bg-primary">Pendiente</span>';
                }

                if (estado === 'pendiente') {
                    acciones = '<button class="btn btn-outline-primary btn-sm me-1" onclick="marcarCompletadoCal(' + ob.id + ')" title="Marcar completado"><i class="bi bi-check2-square"></i></button>';
                    acciones += '<button class="btn btn-outline-secondary btn-sm me-1" onclick="anularObligacionCal(' + ob.id + ')" title="Anular"><i class="bi bi-x-circle"></i></button>';
                    if (ob.cliente_telefono) {
                        var nombre = (ob.cliente_nombre || '').replace(/'/g, "\\'");
                        acciones += '<button class="btn btn-success btn-sm" onclick="abrirWhatsApp(\'' + nombre + '\', null, ' + ob.id + ')" title="WhatsApp"><i class="bi bi-whatsapp"></i></button>';
                    }
                }

                html += '<tr>' +
                    '<td>' + (ob.cliente_nombre || '-') + '</td>' +
                    '<td>' + (ob.tipo_nombre || '-') + '</td>' +
                    '<td class="text-center">' + estadoBadge + '</td>' +
                    '<td class="text-center">' + acciones + '</td>' +
                    '</tr>';
            });
            tbody.innerHTML = html;
        }

        var modal = new bootstrap.Modal(document.getElementById('modalObligacionesDia'));
        modal.show();
    }
});

// ============================
// Alerta de Obligaciones Criticas
// ============================
@if($alertaObligaciones['mostrar'])
document.addEventListener('DOMContentLoaded', function() {
    var datos = @json($alertaObligaciones);
    var vencidas = datos.vencidas;
    var porVencer = datos.porVencer;
    var total = datos.totalCriticas;
    var usuarios = datos.tiempoPromedioUsuarios;

    // Construir tabla de tiempo promedio
    var tablaPromedio = '';
    if (usuarios.length > 0) {
        tablaPromedio = '<div style="margin-top:15px;text-align:left;">' +
            '<p style="font-weight:600;font-size:0.95rem;margin-bottom:8px;color:#2c3e50;">' +
            '<i class="fas fa-clock" style="color:#f39c12;"></i> Tiempo promedio de cumplimiento:</p>' +
            '<table style="width:100%;border-collapse:collapse;font-size:0.85rem;">' +
            '<thead><tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">' +
            '<th style="padding:6px 10px;text-align:left;">Asesor</th>' +
            '<th style="padding:6px 10px;text-align:center;">Dias promedio</th>' +
            '<th style="padding:6px 10px;text-align:center;">Estado</th>' +
            '</tr></thead><tbody>';

        usuarios.forEach(function(u) {
            var color = u.dias_promedio <= 5 ? '#27ae60' : (u.dias_promedio <= 15 ? '#f39c12' : '#e74c3c');
            var icono = u.dias_promedio <= 5 ? 'fa-check-circle' : (u.dias_promedio <= 15 ? 'fa-exclamation-circle' : 'fa-times-circle');
            var estado = u.dias_promedio <= 5 ? 'Rapido' : (u.dias_promedio <= 15 ? 'Regular' : 'Lento');
            tablaPromedio += '<tr style="border-bottom:1px solid #eee;">' +
                '<td style="padding:5px 10px;">' + u.nombre + ' ' + u.apellido + '</td>' +
                '<td style="padding:5px 10px;text-align:center;font-weight:600;color:' + color + ';">' + u.dias_promedio + ' dias</td>' +
                '<td style="padding:5px 10px;text-align:center;"><i class="fas ' + icono + '" style="color:' + color + ';"></i> <span style="color:' + color + ';">' + estado + '</span></td>' +
                '</tr>';
        });

        tablaPromedio += '</tbody></table></div>';
    }

    Swal.fire({
        icon: 'warning',
        title: '<span style="font-size:1.1rem;">Obligaciones que requieren atencion</span>',
        html:
            '<div style="text-align:left;font-size:0.9rem;">' +
                '<div style="display:flex;justify-content:space-around;margin:15px 0;">' +
                    '<div style="text-align:center;padding:10px 15px;background:#fff3cd;border-radius:8px;flex:1;margin-right:8px;">' +
                        '<div style="font-size:1.8rem;font-weight:700;color:#856404;">' + total + '</div>' +
                        '<div style="font-size:0.8rem;color:#856404;">Total criticas</div>' +
                    '</div>' +
                    '<div style="text-align:center;padding:10px 15px;background:#f8d7da;border-radius:8px;flex:1;margin-right:8px;">' +
                        '<div style="font-size:1.8rem;font-weight:700;color:#721c24;">' + vencidas + '</div>' +
                        '<div style="font-size:0.8rem;color:#721c24;">Vencidas</div>' +
                    '</div>' +
                    '<div style="text-align:center;padding:10px 15px;background:#cce5ff;border-radius:8px;flex:1;">' +
                        '<div style="font-size:1.8rem;font-weight:700;color:#004085;">' + porVencer + '</div>' +
                        '<div style="font-size:0.8rem;color:#004085;">Por vencer (7 dias)</div>' +
                    '</div>' +
                '</div>' +
                '<p style="margin-top:10px;color:#6c757d;font-size:0.85rem;">' +
                    '<i class="fas fa-info-circle"></i> Revisa el <strong>calendario de obligaciones</strong> y ponte al dia con las tareas pendientes.' +
                '</p>' +
                tablaPromedio +
            '</div>',
        width: 600,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-calendar-alt"></i> Ir al calendario',
        confirmButtonColor: '#0d6efd',
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        cancelButtonColor: '#6c757d',
    }).then(function(result) {
        if (result.isConfirmed) {
            var calendario = document.getElementById('calendario');
            if (calendario) {
                calendario.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});
@endif
</script>
@endsection
