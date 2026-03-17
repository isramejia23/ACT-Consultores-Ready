@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Financiero</h2>

    <!-- Filtros -->
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Periodo:</label>
                <select name="year" class="form-select">
                    @for ($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Mes:</label>
                <select name="month" class="form-select">
                    <option value="">Todo el anio</option>
                    @foreach ($mesesEspañol as $num => $nombre)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Usuario:</label>
                <select name="usuario_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ $usuario_id == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->nombre }} {{ $usuario->apellido }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    <!-- ============================== -->
    <!-- KPIs - Tarjetas principales    -->
    <!-- ============================== -->
    <div class="row g-3 mb-4">
        <!-- Total Facturado -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;background-color:rgba(52,152,219,0.15);">
                        <i class="fas fa-file-invoice-dollar text-primary" style="font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">Total Facturado</p>
                        <h4 class="mb-0 fw-bold text-primary">{{ number_format($totalFacturado, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cobrado -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;background-color:rgba(46,204,113,0.15);">
                        <i class="fas fa-money-bill-wave text-success" style="font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">Total Cobrado</p>
                        <h4 class="mb-0 fw-bold text-success">{{ number_format($totalCobrado, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Pendiente -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;background-color:rgba(231,76,60,0.15);">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">Saldo Pendiente</p>
                        <h4 class="mb-0 fw-bold text-danger">{{ number_format($saldoPendiente, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiempo Promedio + Clientes Activos -->
        <div class="col-xl-3 col-md-6">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-2">
                            <i class="fas fa-clock text-warning mb-1" style="font-size:1.2rem;"></i>
                            <p class="text-muted mb-0" style="font-size:0.7rem;">Tiempo Promedio</p>
                            <h5 class="mb-0 fw-bold text-warning">{{ $tiempoPromedio ? number_format($tiempoPromedio, 0) . 'd' : 'N/A' }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-2">
                            <i class="fas fa-users text-info mb-1" style="font-size:1.2rem;"></i>
                            <p class="text-muted mb-0" style="font-size:0.7rem;">Clientes Activos</p>
                            <h5 class="mb-0 fw-bold text-info">{{ $clientesActivos }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de progreso: Facturado vs Cobrado -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            @php
                $porcentajeCobrado = $totalFacturado > 0 ? min(($totalCobrado / $totalFacturado) * 100, 100) : 0;
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted fw-bold">Tasa de cobro del periodo</small>
                <small class="fw-bold {{ $porcentajeCobrado >= 70 ? 'text-success' : ($porcentajeCobrado >= 40 ? 'text-warning' : 'text-danger') }}">
                    {{ number_format($porcentajeCobrado, 1) }}%
                </small>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar {{ $porcentajeCobrado >= 70 ? 'bg-success' : ($porcentajeCobrado >= 40 ? 'bg-warning' : 'bg-danger') }}"
                     role="progressbar" style="width: {{ $porcentajeCobrado }}%"></div>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- Graficos principales           -->
    <!-- ============================== -->
    <div class="row g-3 mb-4">
        <!-- Tareas por Estado (existente) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-bold">
                    <i class="fas fa-tasks"></i> Tareas por Estado
                </div>
                <div class="card-body">
                    <canvas id="tareasChart"></canvas>
                    <div class="row text-center mt-3">
                        <div class="col-4">
                            <span class="badge bg-warning text-dark d-block py-2">{{ $tareasPendientes }}</span>
                            <small class="text-muted">Pendientes</small>
                        </div>
                        <div class="col-4">
                            <span class="badge bg-primary d-block py-2">{{ $tareasEnProceso }}</span>
                            <small class="text-muted">En Proceso</small>
                        </div>
                        <div class="col-4">
                            <span class="badge bg-success d-block py-2">{{ $tareasCumplidas }}</span>
                            <small class="text-muted">Cumplidas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos por Mes (existente, mejorado) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-bold">
                    <i class="fas fa-chart-line"></i> Ingresos por Mes (Tareas)
                </div>
                <div class="card-body">
                    <canvas id="ingresosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- Ingresos vs Cobros             -->
    <!-- ============================== -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 fw-bold">
                    <i class="fas fa-balance-scale"></i> Facturado vs Cobrado por Mes
                </div>
                <div class="card-body">
                    <canvas id="ingresosVsCobrosChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- Rendimiento por Asesor         -->
    <!-- ============================== -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-bold">
                    <i class="fas fa-user-tie"></i> Rendimiento por Asesor - Ingresos Generados
                </div>
                <div class="card-body">
                    <canvas id="rendimientoIngresosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-bold">
                    <i class="fas fa-clipboard-check"></i> Rendimiento por Asesor - Detalle
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-white" style="font-size:0.8rem;">Asesor</th>
                                    <th class="text-center text-white" style="font-size:0.8rem;">Cumplidas</th>
                                    <th class="text-end text-white" style="font-size:0.8rem;">Facturado</th>
                                    <th class="text-center text-white" style="font-size:0.8rem;">Clientes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rendimientoAsesores as $asesor)
                                <tr>
                                    <td style="font-size:0.85rem;">{{ $asesor->nombre }} {{ $asesor->apellido }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $asesor->tareas_cumplidas }}</span>
                                    </td>
                                    <td class="text-end" style="font-size:0.85rem;">{{ number_format($asesor->ingresos_generados, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $asesor->clientes_count }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Sin datos para el periodo</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Colores reutilizables
    var colores = {
        primary: '#3498db',
        success: '#2ecc71',
        warning: '#f39c12',
        danger: '#e74c3c',
        purple: '#8e44ad',
        info: '#17a2b8'
    };

    // ============================
    // 1. Tareas por Estado (Doughnut)
    // ============================
    var ctxTareas = document.getElementById('tareasChart').getContext('2d');
    new Chart(ctxTareas, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'En Proceso', 'Cumplidas'],
            datasets: [{
                data: [{{ $tareasPendientes }}, {{ $tareasEnProceso }}, {{ $tareasCumplidas }}],
                backgroundColor: [colores.warning, colores.primary, colores.success],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } }
            }
        }
    });

    // ============================
    // 2. Ingresos por Mes (Area)
    // ============================
    var ctxIngresos = document.getElementById('ingresosChart').getContext('2d');
    new Chart(ctxIngresos, {
        type: 'line',
        data: {
            labels: @json($datos->pluck('mes')->map(fn($m) => $mesesEspañol[$m])),
            datasets: [{
                label: 'Ingresos por Mes',
                data: @json($datos->pluck('ingresos')),
                borderColor: colores.purple,
                backgroundColor: 'rgba(142, 68, 173, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: colores.purple,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // ============================
    // 3. Facturado vs Cobrado
    // ============================
    var meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    var facturadoData = @json($facturadoMensual);
    var cobradoData = @json($cobradoMensual);

    var ctxVs = document.getElementById('ingresosVsCobrosChart').getContext('2d');
    new Chart(ctxVs, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Facturado',
                    data: facturadoData,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: colores.primary,
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Cobrado',
                    data: cobradoData,
                    backgroundColor: 'rgba(46, 204, 113, 0.7)',
                    borderColor: colores.success,
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + Number(context.raw).toLocaleString('es-CR', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // ============================
    // 4. Rendimiento por Asesor (Horizontal Bar)
    // ============================
    var asesoresData = @json($rendimientoAsesores);
    var nombresAsesores = asesoresData.map(function(a) { return a.nombre + ' ' + a.apellido; });
    var ingresosAsesores = asesoresData.map(function(a) { return a.ingresos_generados; });
    var tareasAsesores = asesoresData.map(function(a) { return a.tareas_cumplidas; });

    var ctxRendimiento = document.getElementById('rendimientoIngresosChart').getContext('2d');
    new Chart(ctxRendimiento, {
        type: 'bar',
        data: {
            labels: nombresAsesores,
            datasets: [
                {
                    label: 'Ingresos Generados',
                    data: ingresosAsesores,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: colores.primary,
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Tareas Cumplidas',
                    data: tareasAsesores,
                    backgroundColor: 'rgba(46, 204, 113, 0.7)',
                    borderColor: colores.success,
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Ingresos Generados') {
                                return context.dataset.label + ': ' + Number(context.raw).toLocaleString('es-CR', {minimumFractionDigits: 2});
                            }
                            return context.dataset.label + ': ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endsection