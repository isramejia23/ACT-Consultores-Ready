<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tareas - ACT Clientes</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .navbar {
            background-color: rgb(3, 16, 159);
        }
        .navbar .nav-link, .navbar .navbar-brand {
            color: #ffffff !important;
        }
        .navbar .nav-link:hover {
            color: #d4d4d4 !important;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.75em;
        }
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .card-text {
            color: #6c757d;
        }
        .btn-archivo {
            width: 100%;
            margin-top: 1rem;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .footer {
            background-color: rgb(255, 255, 255);
            color: rgb(110, 110, 110);
            padding: 15px 0;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- 🟢 Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a href="{{ route('clientes.dashboard') }}">
            <img src="{{ asset('imagenes/logo.png') }}" alt="Logo ACT Consultores" style="height: 50px; width: auto; margin-right: 10px;">
        </a>
        @auth
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ auth()->guard('cliente')->user()->nombre_cliente }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Cerrar sesión
                        </a>
                    </li>
                </ul>
            </li>
            <form id="logout-form" action="{{ route('cliente.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endauth
    </div>
</nav>

<!-- 📌 Contenido Principal -->
<div class="container mt-4">
    <h2 class="text-primary mb-4"><i class="bi bi-clipboard-check"></i> Mis Tareas</h2>

    <!-- 🔎 Formulario de Filtros -->
    <form method="GET" action="{{ route('clientes.tareas') }}" class="mb-4">
        <div class="row g-2 row-cols-md-auto align-items-center">
            <div class="col">
                <select name="filter" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="Cumplida" {{ request('filter') == 'Cumplida' ? 'selected' : '' }}>Cumplida</option>
                    <option value="En Proceso" {{ request('filter') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="Pendiente" {{ request('filter') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Anulada" {{ request('filter') == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>
            <div class="col">
                <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
            </div>
            <div class="col">
                <input type="number" name="year" class="form-control" placeholder="Año" value="{{ request('year', date('Y')) }}">
            </div>
            <div class="col">
                <input type="number" name="month" class="form-control" placeholder="Mes" value="{{ request('month') }}">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('clientes.tareas') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Limpiar</a>
            </div>
        </div>
    </form>

    <!-- 📌 Lista de Tareas en Tarjetas -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse ($tareas as $tarea)
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">{{ $tarea->nombre }}</h5>
                        <small class="text-muted">Fecha Realizada: 
                            @if($tarea->fecha_cumplida)
                                @formatoFecha($tarea->fecha_cumplida)
                            @endif
                        </small>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $tarea->descripcion }}</p>
                        <span class="badge 
                            {{ $tarea->estado == 'Pendiente' ? 'text-danger border border-danger' : '' }}
                            {{ $tarea->estado == 'Cumplida' ? 'text-success border border-success' : '' }}
                            {{ $tarea->estado == 'Anulada' ? 'text-secondary border border-secondary' : '' }}
                            {{ $tarea->estado == 'En Proceso' ? 'text-warning border border-warning' : '' }}">
                            {{ $tarea->estado }}
                        </span>
                        @if ($tarea->archivo)
                            <a href="{{ asset('storage/' . $tarea->archivo) }}" target="_blank" class="btn btn-outline-primary btn-archivo">
                                <i class="bi bi-file-earmark"></i> Ver Archivo
                            </a>
                        @else
                            <p class="text-muted mt-2">No hay archivo adjunto</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-clipboard-x"></i>
                    <h4>No hay tareas registradas</h4>
                    <p>No se encontraron tareas con los filtros seleccionados.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- 📌 Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $tareas->appends(request()->query())->links() }}
    </div>

    <!-- 🔙 Botón Volver -->
    <div class="mt-4 text-center">
        <a href="{{ route('clientes.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
        </a>
    </div>
</div>
<footer class="footer">
    <p>&copy; 2025 FIM-X-Suite. Todos los derechos reservados.</p>
    <p>Sistema desarrollado por Israel Mejía Carrasco | Contacto: isramejia23@hotmail.com</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>