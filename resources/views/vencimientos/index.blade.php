@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header con navegación de meses -->
    <div class="d-flex justify-content-between align-items-center my-3">
        <a href="{{ route('vencimientos.index', ['month' => $currentMonth - 1, 'year' => $currentYear]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-chevron-left"></i> Mes Anterior
        </a>
        <h3 class="mb-0">
            <i class="bi bi-calendar-check text-primary me-2"></i>Obligaciones de {{ ucfirst($mesActual) }} {{ $anioActual }}
        </h3>
        <div>
            <a href="{{ route('vencimientos.index', ['month' => $currentMonth + 1, 'year' => $currentYear]) }}" class="btn btn-outline-secondary me-2">
                Mes Siguiente <i class="bi bi-chevron-right"></i>
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalGenerarObligaciones">
                <i class="bi bi-lightning-charge"></i> Generar
            </button>
        </div>
    </div>

    {{-- Modal para generar obligaciones manualmente --}}
    <div class="modal fade" id="modalGenerarObligaciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-lightning-charge"></i> Generar Obligaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('vencimientos.generar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mes</label>
                            <select name="mes" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ano</label>
                            <input type="number" name="anio" class="form-control" value="{{ $currentYear }}" min="2020" max="2100" required>
                        </div>
                        <div class="alert alert-warning small py-2 mb-0">
                            <i class="bi bi-info-circle"></i> Esto generara o actualizara obligaciones para todos los clientes activos del mes seleccionado.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-play-circle"></i> Generar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('vencimientos.index') }}">
                <input type="hidden" name="month" value="{{ $currentMonth }}">
                <input type="hidden" name="year" value="{{ $currentYear }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text"
                               name="cedula"
                               class="form-control"
                               placeholder="Buscar por cedula"
                               value="{{ $filtros['cedula'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <select name="regimen_id" class="form-select">
                            <option value="todos">Todos los regimenes</option>
                            @foreach($regimenes as $regimen_option)
                                <option value="{{ $regimen_option->id }}" {{ ($filtros['regimen_id'] ?? '') == $regimen_option->id ? 'selected' : '' }}>
                                    {{ $regimen_option->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-2"></i>Filtrar
                            </button>
                            <a href="{{ route('vencimientos.index', ['month' => $currentMonth, 'year' => $currentYear]) }}" class="btn btn-secondary">
                                <i class="bi bi-eraser me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendario de Obligaciones -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-calendar3 me-2"></i> Calendario de Obligaciones
        </div>
        <div class="card-body">
            <div id="calendarioVenc"></div>
        </div>
    </div>

    <!-- Obligaciones Completadas del Mes -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-check-circle me-2"></i> Completadas este mes</span>
            <span class="badge bg-light text-success" id="contadorCompletadasVenc">
                {{ collect($obligacionesCalendario)->where('completado', true)->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Cliente</th>
                            <th>Obligacion</th>
                            <th>Fecha Vencimiento</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCompletadasVenc">
                        @foreach(collect($obligacionesCalendario)->where('completado', true) as $ob)
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
            @if(collect($obligacionesCalendario)->where('completado', true)->isEmpty())
            <div class="text-center py-3 text-muted" id="sinCompletadasVenc">
                <i class="bi bi-calendar-x fs-3"></i>
                <p class="mt-2 mb-0">No hay obligaciones completadas este mes</p>
            </div>
            @else
            <div class="text-center py-3 text-muted d-none" id="sinCompletadasVenc">
                <i class="bi bi-calendar-x fs-3"></i>
                <p class="mt-2 mb-0">No hay obligaciones completadas este mes</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Obligaciones del Dia (calendario) -->
<div class="modal fade" id="modalObligacionesDiaVenc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Obligaciones del <span id="modalObligacionesFechaVenc"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Obligacion</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaObligacionesDiaVenc">
                        </tbody>
                    </table>
                </div>
                <div id="sinObligacionesVenc" class="p-4 text-center text-muted d-none">
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    <p class="mt-2 mb-0">No hay obligaciones para este dia</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form oculto para marcar completado desde el calendario -->
<form id="formCompletarCalendarioVenc" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<style>
    #calendarioVenc .fc-toolbar-title {
        font-size: 1.1rem !important;
    }
    #calendarioVenc .fc-event {
        font-size: 0.78rem;
        border: none;
        padding: 2px 6px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        text-align: center;
    }
    #calendarioVenc .fc-daygrid-day-number {
        font-size: 0.85rem;
    }
    #calendarioVenc .fc-daygrid-event {
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
    var calendarEl = document.getElementById('calendarioVenc');
    if (!calendarEl) return;

    var obligaciones = @json($obligacionesCalendario ?? []);
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
                eventos.push({
                    title: grupo.completadas + ' completada(s)',
                    start: fecha, color: '#198754', display: 'block',
                    extendedProps: { fecha: fecha, items: grupo.items }
                });
            } else if (noPendientes > 0) {
                var color = grupo.vencidas > 0 ? '#dc3545' : '#0d6efd';
                eventos.push({
                    title: noPendientes + ' pendiente(s)',
                    start: fecha, color: color, display: 'block',
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
        var tbody = document.getElementById('tbodyCompletadasVenc');
        var sinMsg = document.getElementById('sinCompletadasVenc');
        var contador = document.getElementById('contadorCompletadasVenc');

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

    window.marcarCompletadoVenc = function(id) {
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
                    obligaciones.forEach(function(ob) {
                        if (ob.id === id) { ob.completado = true; ob.estado = 'completada'; }
                    });

                    refrescarCalendario();
                    actualizarTablaCompletadas();

                    var modalEl = document.getElementById('modalObligacionesDiaVenc');
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

    window.anularObligacionVenc = function(id) {
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
            abrirModalObligacionesVenc(info.event.extendedProps.fecha);
        },
        noEventsContent: 'No hay obligaciones para este periodo'
    });

    calendar.render();

    function abrirModalObligacionesVenc(fecha) {
        var itemsActualizados = obligaciones.filter(function(ob) { return ob.fecha_vencimiento === fecha; });

        var partes = fecha.split('-');
        document.getElementById('modalObligacionesFechaVenc').textContent = partes[2] + '/' + partes[1] + '/' + partes[0];

        var tbody = document.getElementById('tablaObligacionesDiaVenc');
        var sinOblig = document.getElementById('sinObligacionesVenc');

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
                    acciones = '<button class="btn btn-outline-primary btn-sm me-1" onclick="marcarCompletadoVenc(' + ob.id + ')" title="Marcar completado"><i class="bi bi-check2-square"></i></button>';
                    acciones += '<button class="btn btn-outline-secondary btn-sm me-1" onclick="anularObligacionVenc(' + ob.id + ')" title="Anular"><i class="bi bi-x-circle"></i></button>';
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

        var modal = new bootstrap.Modal(document.getElementById('modalObligacionesDiaVenc'));
        modal.show();
    }
});
</script>
@endsection