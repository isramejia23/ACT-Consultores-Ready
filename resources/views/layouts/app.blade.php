<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('imagenes/logo.png') }}" >
    <title>ACT Consultores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-color: rgb(3, 16, 159);
            --secondary-color: #f9f9f9;
            --text-color: rgb(74, 69, 69);
            --btn-color: rgb(19, 184, 221);
            --sidebar-width: 250px;
            --sidebar-collapsed: 70px;
        }

        body {
            overflow-x: hidden;
        }

        .table th {
            background-color: var(--primary-color);
            color: #fff;
        }

        /* ========== SIDEBAR ========== */
        #sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: var(--sidebar-collapsed);
            background-color: var(--primary-color);
            transition: width 0.3s ease;
            z-index: 1050;
            overflow-x: hidden;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        #sidebar.expanded {
            width: var(--sidebar-width);
        }

        /* Header del sidebar */
        #sidebar .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            min-height: 60px;
        }
        #sidebar.expanded .sidebar-header {
            justify-content: space-between;
            padding: 12px 15px;
        }
        #sidebar .sidebar-logo {
            display: none;
        }
        #sidebar.expanded .sidebar-logo {
            display: flex;
            align-items: center;
        }
        #sidebar .sidebar-header img {
            height: 40px;
            width: auto;
        }

        /* Boton toggle dentro del header */
        #sidebar .sidebar-toggle {
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 4px 6px;
            display: flex;
            align-items: center;
        }
        #sidebar .sidebar-toggle:hover {
            color: #fff;
        }
        #sidebar .sidebar-toggle i {
            font-size: 1.1rem;
        }

        /* Navegacion */
        #sidebar .sidebar-nav {
            flex: 1;
            padding-top: 5px;
        }
        #sidebar .sidebar-nav a {
            color: rgba(255,255,255,0.85);
            padding: 11px 0 11px 22px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
            font-size: 0.9rem;
        }
        #sidebar .sidebar-nav a:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        #sidebar .sidebar-nav a i {
            min-width: 26px;
            font-size: 1.15rem;
            text-align: center;
            flex-shrink: 0;
        }
        #sidebar .sidebar-nav a .link-text {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
            margin-left: 10px;
        }
        #sidebar.expanded .sidebar-nav a .link-text {
            opacity: 1;
            visibility: visible;
        }

        /* Submenu */
        #sidebar .submenu {
            display: none;
            padding-left: 0;
        }
        #sidebar.expanded .submenu {
            padding-left: 15px;
        }
        #sidebar .submenu a {
            font-size: 0.82rem;
            padding: 8px 0 8px 30px;
        }

        /* Footer sidebar */
        #sidebar .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.15);
            padding: 10px;
            text-align: center;
        }
        #sidebar .sidebar-footer .user-name {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            padding: 4px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            opacity: 0;
            visibility: hidden;
            height: 0;
            transition: opacity 0.2s, height 0.2s;
        }
        #sidebar.expanded .sidebar-footer .user-name {
            opacity: 1;
            visibility: visible;
            height: auto;
        }
        #sidebar .sidebar-footer a {
            color: rgba(255,255,255,0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 0.85rem;
            padding: 6px 0;
            transition: color 0.2s;
        }
        #sidebar.expanded .sidebar-footer a {
            justify-content: flex-start;
            padding-left: 12px;
        }
        #sidebar .sidebar-footer a:hover {
            color: #fff;
        }
        #sidebar .sidebar-footer a .link-text {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s;
            margin-left: 8px;
        }
        #sidebar.expanded .sidebar-footer a .link-text {
            opacity: 1;
            visibility: visible;
        }

        /* Chevron en submenus: oculto cuando colapsado */
        #sidebar .submenu-chevron {
            opacity: 0;
            transition: opacity 0.2s;
        }
        #sidebar.expanded .submenu-chevron {
            opacity: 1;
        }

        /* ========== OVERLAY (solo mobile) ========== */
        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1040;
        }
        #sidebar-overlay.show {
            display: block;
        }

        /* ========== CONTENIDO PRINCIPAL ========== */
        .main-wrapper {
            margin-left: var(--sidebar-collapsed);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        body.sidebar-expanded .main-wrapper {
            margin-left: var(--sidebar-width);
        }

        /* ========== MOBILE ========== */
        @media (max-width: 991.98px) {
            #sidebar {
                left: calc(-1 * var(--sidebar-width));
                width: var(--sidebar-width);
            }
            #sidebar.mobile-show {
                left: 0;
            }
            /* En mobile siempre mostrar textos */
            #sidebar .sidebar-nav a .link-text,
            #sidebar .sidebar-footer a .link-text,
            #sidebar .sidebar-footer .user-name,
            #sidebar .submenu-chevron {
                opacity: 1;
                visibility: visible;
                height: auto;
            }
            #sidebar .submenu {
                padding-left: 15px;
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            /* Boton hamburguesa mobile */
            #mobileToggle {
                display: block !important;
            }
            /* Ocultar toggle desktop en mobile */
            #sidebar .sidebar-toggle {
                display: none;
            }
        }
        @media (min-width: 992px) {
            #mobileToggle {
                display: none !important;
            }
        }

        /* Boton hamburguesa mobile */
        #mobileToggle {
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 1100;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 1.3rem;
            cursor: pointer;
        }

        /* ========== OTROS ========== */
        .btn-orange {
            background-color: var(--btn-color);
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }
        .btn-orange:hover {
            background-color: rgb(19, 144, 181);
        }

        .footer {
            background-color: rgb(255, 255, 255);
            color: rgb(110, 110, 110);
            padding: 15px 0;
            text-align: center;
            width: 100%;
            margin-top: auto;
        }

        #loading-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Spinner -->
    <div id="loading-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Cargando...</p>
    </div>

    <!-- Boton hamburguesa MOBILE -->
    @if(Auth::guard('web')->check())
    <button id="mobileToggle">
        <i class="bi bi-list"></i>
    </button>
    @endif

    <!-- Overlay mobile -->
    <div id="sidebar-overlay"></div>

    <!-- Sidebar -->
    @if(Auth::guard('web')->check())
    <div id="sidebar">
        <!-- Toggle + Logo -->
        <div class="sidebar-header">
            <button class="sidebar-toggle" id="desktopToggle" title="Expandir/Contraer">
                <i class="bi bi-chevron-right" id="toggleIcon"></i>
            </button>
            <a href="{{ route('home') }}" class="sidebar-logo">
                <img src="{{ asset('imagenes/logo.png') }}" alt="Logo ACT">
            </a>
        </div>

        <!-- Navegacion -->
        <div class="sidebar-nav">
            <a href="{{ route('home') }}"><i class="bi bi-house-door"></i><span class="link-text">Home</span></a>

            @can('ver-rol')
                <a href="{{ route('roles.index') }}"><i class="bi bi-person-badge"></i><span class="link-text">Roles</span></a>
            @endcan

            @can('ver-cliente')
                <a href="{{ route('clientes.index') }}"><i class="bi bi-person"></i><span class="link-text">Clientes</span></a>
            @endcan

            <a href="{{ route('tareas.cargadas') }}"><i class="bi bi-folder-check"></i><span class="link-text">Tareas Cargadas</span></a>

            @can('ver-tarea')
                <a href="#" class="toggle-submenu">
                    <i class="bi bi-briefcase"></i>
                    <span class="link-text">Trabajos</span>
                    <i class="bi bi-chevron-down submenu-chevron ms-auto me-3" style="font-size:0.7rem;"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('tareas.index') }}"><i class="bi bi-list-task"></i><span class="link-text">Todos</span></a>
                    <a href="{{ route('tareas.index', ['filter' => 'Pendiente']) }}"><i class="bi bi-hourglass-split text-light"></i><span class="link-text">Pendientes</span></a>
                    <a href="{{ route('tareas.index', ['filter' => 'En Proceso']) }}"><i class="bi bi-gear-wide-connected text-info"></i><span class="link-text">En Proceso</span></a>
                    <a href="{{ route('tareas.index', ['filter' => 'Cumplida']) }}"><i class="bi bi-check-circle text-success"></i><span class="link-text">Cumplidas</span></a>
                    <a href="{{ route('tareas.index', ['filter' => 'Anulada']) }}"><i class="bi bi-x-circle text-danger"></i><span class="link-text">Anuladas</span></a>
                </div>
            @endcan

            @can('ver-tareas-avanzado')
                <a href="{{ route('tareas.filtros_avanzados') }}"><i class="fas fa-filter"></i><span class="link-text">Reportes</span></a>
            @endcan

            @can('ver-dashboard')
                <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i><span class="link-text">Dashboard</span></a>
            @endcan

            @can('importar-exel')
                <a href="{{ route('importar.excel.form') }}"><i class="bi bi-file-earmark-spreadsheet"></i><span class="link-text">Importar Excel</span></a>
            @endcan

            @can('ver-factura')
                <a href="{{ route('facturas.index') }}"><i class="bi bi-file-text"></i><span class="link-text">Facturas</span></a>
            @endcan

            @can('ver-cobro')
                <a href="{{ route('cobros.index') }}"><i class="bi bi-cash-stack"></i><span class="link-text">Cobros</span></a>
            @endcan

            <a href="{{ route('vencimientos.index') }}"><i class="bi bi-bank"></i><span class="link-text">Obligaciones </span></a>

            @if(auth()->user()->can('ver-usuario') || auth()->user()->can('ver-regimen') || auth()->user()->can('ver-catalogo-servicio') || auth()->user()->can('ver-tipo-obligacion'))
                <a href="#" class="toggle-submenu-admin">
                    <i class="bi bi-gear"></i>
                    <span class="link-text">Configuracion</span>
                    <i class="bi bi-chevron-down submenu-chevron ms-auto me-3" style="font-size:0.7rem;"></i>
                </a>
                <div class="submenu" id="submenu-admin">
                    @can('ver-usuario')
                        <a href="{{ route('usuarios.index') }}"><i class="bi bi-people"></i><span class="link-text">Usuarios</span></a>
                    @endcan
                    @can('ver-regimen')
                        <a href="{{ route('regimenes.index') }}"><i class="bi bi-shield-check"></i><span class="link-text">Regimenes</span></a>
                    @endcan
                    @can('ver-catalogo-servicio')
                        <a href="{{ route('catalogo.index') }}"><i class="bi bi-grid"></i><span class="link-text">Catalogo</span></a>
                    @endcan
                    @can('ver-tipo-obligacion')
                        <a href="{{ route('tipos-obligacion.index') }}"><i class="bi bi-list-check"></i><span class="link-text">Obligaciones</span></a>
                    @endcan
                </div>
            @endif
        </div>

        <!-- Footer: usuario y logout -->
        @auth
        <div class="sidebar-footer">
            <div class="user-name"><i class="bi bi-person-circle"></i> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-left"></i><span class="link-text">Cerrar sesion</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        @else
        <div class="sidebar-footer">
            <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i><span class="link-text">Iniciar Sesion</span></a>
            <a href="{{ route('cliente.login') }}"><i class="bi bi-person"></i><span class="link-text">Clientes</span></a>
        </div>
        @endauth
    </div>
    @endif

    <!-- Contenido principal -->
    <div class="main-wrapper">
        <div class="container-fluid mt-3 flex-grow-1 px-4">
            @yield('content')
        </div>

        <footer class="footer">
            <p>&copy; 2025 FIM-X-Suite. Todos los derechos reservados.</p>
            <p>Sistema desarrollado por Israel Mejia Carrasco | Contacto: isramejia23@hotmail.com</p>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Spinner
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('loading-overlay').style.display = 'flex';
            window.addEventListener('load', function() {
                document.getElementById('loading-overlay').style.display = 'none';
            });
        });
        document.addEventListener('submit', function(e) {
            document.getElementById('loading-overlay').style.display = 'flex';
        });

        // === SIDEBAR LOGIC ===
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebar-overlay');
        var desktopToggle = document.getElementById('desktopToggle');
        var mobileToggle = document.getElementById('mobileToggle');
        var toggleIcon = document.getElementById('toggleIcon');

        // Desktop: expandir/contraer
        if (desktopToggle && sidebar) {
            desktopToggle.addEventListener('click', function() {
                sidebar.classList.toggle('expanded');
                document.body.classList.toggle('sidebar-expanded');
                if (toggleIcon) {
                    toggleIcon.classList.toggle('bi-chevron-right');
                    toggleIcon.classList.toggle('bi-chevron-left');
                }
            });
        }

        // Mobile: abrir/cerrar
        if (mobileToggle && sidebar) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-show');
                if (overlay) overlay.classList.toggle('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', function() {
                if (sidebar) sidebar.classList.remove('mobile-show');
                overlay.classList.remove('show');
            });
        }

        // Toggle submenu Trabajos
        var trabajosToggle = document.querySelector('.toggle-submenu');
        if (trabajosToggle) {
            trabajosToggle.addEventListener('click', function(e) {
                e.preventDefault();
                var submenu = this.nextElementSibling;
                if (submenu) submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';
            });
        }

        // Toggle submenu Configuracion
        var adminToggle = document.querySelector('.toggle-submenu-admin');
        if (adminToggle) {
            adminToggle.addEventListener('click', function(e) {
                e.preventDefault();
                var submenu = document.getElementById('submenu-admin');
                if (submenu) submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';
            });
        }

        // Alertas
        function showSuccessAlert(message) {
            Swal.fire({ position: "center", icon: "success", title: message, showConfirmButton: false, timer: 1500 });
        }

        function anularObligacion(id, callback) {
            Swal.fire({
                title: '¿Anular esta obligación?',
                text: 'La obligación dejará de mostrarse como pendiente',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/obligaciones/' + id + '/anular', {
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
                        Swal.fire({ icon: 'success', title: 'Anulada', showConfirmButton: false, timer: 1200 });
                        if (typeof callback === 'function') callback(id);
                    }
                })
                .catch(function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo anular la obligación' });
                });
            });
        }

        function confirmDelete(event, button) {
            event.preventDefault();
            Swal.fire({
                title: '¿Estas seguro?',
                text: "No podras revertir esto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = button.closest('form');
                    if (form) {
                        document.getElementById('loading-overlay').style.display = 'flex';
                        form.submit();
                    }
                }
            });
        }
    </script>

    @if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            showSuccessAlert("{{ session('success') }}");
        });
    </script>
    @elseif(session('error') || $errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if($errors->any())
                var errorMessage = "{!! $errors->first() !!}";
            @elseif(session('error'))
                var errorMessage = "{{ session('error') }}";
            @else
                var errorMessage = "Por favor corrige los errores en el formulario";
            @endif

            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: errorMessage,
            }).then(() => {
                @if(session('modal_id'))
                    var modal = new bootstrap.Modal(document.getElementById('editModal{{ session('modal_id') }}'));
                    modal.show();
                @endif
            });
        });
    </script>
    @endif
    @yield('scripts')
</body>
</html>
