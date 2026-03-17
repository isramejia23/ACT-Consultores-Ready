<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACT Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: rgb(3, 16, 159);
        }
        .navbar .nav-link,
        .navbar .navbar-brand {
            color: #ffffff !important;
        }
        .navbar .nav-link:hover {
            color: #d4d4d4 !important;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: rgb(3, 16, 159);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .info-item {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
        }

        .info-item:hover {
            background-color: #e9ecef;
        }

        .info-item strong {
            color: rgb(3, 16, 159);
        }

        .btn-primary {
            background-color: rgb(3, 16, 159);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #002366;
        }

        .btn-danger {
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #dc3545;
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
    <!-- Navbar -->
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

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h2 class="mb-4">Bienvenido, {{ auth()->guard('cliente')->user()->nombre_cliente }}</h2>

        <!-- Tarjeta de Información del Cliente -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-circle me-2"></i> Información del Cliente
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong><i class="bi bi-card-text me-2"></i> Cédula:</strong> {{ auth()->guard('cliente')->user()->cedula_cliente }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-telephone me-2"></i> Teléfono:</strong> {{ auth()->guard('cliente')->user()->telefono_cliente }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-building me-2"></i> Régimen:</strong> {{ auth()->guard('cliente')->user()->regimen->nombre ?? 'Sin Régimen' }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-flag me-2"></i> Estado:</strong> {{ auth()->guard('cliente')->user()->estado }}
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong><i class="bi bi-briefcase me-2"></i> Actividad:</strong> {{ auth()->guard('cliente')->user()->actividad }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-calendar me-2"></i> Fecha Firma:</strong> {{ auth()->guard('cliente')->user()->fecha_firma }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-cash-coin me-2"></i> Saldo:</strong> {{ auth()->guard('cliente')->user()->saldo }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ auth()->guard('cliente')->user()->email_cliente }}
                        </div>
                        <div class="info-item">
                            <strong><i class="bi bi-house me-2"></i> Dirección:</strong> {{ auth()->guard('cliente')->user()->direccion }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-4">
            <a href="{{ route('clientes.tareas') }}" class="btn btn-primary">
                <i class="bi bi-list-task me-2"></i> Mis Trabajos
            </a>
            <form action="{{ route('cliente.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; 2025 FIM-X-Suite. Todos los derechos reservados.</p>
        <p>Sistema desarrollado por Israel Mejía Carrasco | Contacto: isramejia23@hotmail.com</p>
    </footer>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>