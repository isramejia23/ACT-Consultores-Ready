<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('imagenes/logo.png') }}">
    <title>ACT Consultores - Login Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0a1172;
            --primary-light: #1a2490;
            --accent: #13b8dd;
            --accent-dark: #0e9ab8;
            --dark: #1a1a2e;
            --gray: #6b7280;
            --light: #f8f9fc;
            --teal: #0d9488;
            --teal-light: #14b8a6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* ========== PANEL IZQUIERDO (branding) ========== */
        .login-brand {
            width: 45%;
            background: linear-gradient(135deg, var(--teal) 0%, var(--accent-dark) 50%, var(--accent) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -30%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }
        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .brand-content img {
            width: 180px;
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.25));
            margin-bottom: 35px;
            animation: floatLogo 4s ease-in-out infinite;
        }
        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .brand-content h2 {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .brand-content p {
            color: rgba(255,255,255,0.7);
            font-size: 0.95rem;
            line-height: 1.6;
            max-width: 320px;
        }
        .brand-features {
            margin-top: 40px;
            text-align: left;
        }
        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            color: rgba(255,255,255,0.85);
            font-size: 0.88rem;
        }
        .brand-feature i {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 8px;
            background: rgba(255,255,255,0.15);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        /* ========== PANEL DERECHO (formulario) ========== */
        .login-form-panel {
            width: 55%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #fff;
        }
        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .login-form-wrapper .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--gray);
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 40px;
            transition: color 0.2s;
        }
        .login-form-wrapper .back-link:hover {
            color: var(--teal);
        }
        .login-header {
            margin-bottom: 36px;
        }
        .login-header .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(13,148,136,0.08);
            color: var(--teal);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .login-header h1 {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 6px;
        }
        .login-header p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Form styles */
        .form-floating-custom {
            position: relative;
            margin-bottom: 20px;
        }
        .form-floating-custom label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            display: block;
        }
        .form-floating-custom .input-wrapper {
            position: relative;
        }
        .form-floating-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
            z-index: 2;
        }
        .form-floating-custom input {
            width: 100%;
            padding: 13px 14px 13px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.92rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            background: #fafbfc;
        }
        .form-floating-custom input:focus {
            outline: none;
            border-color: var(--teal);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        }
        .form-floating-custom input.is-invalid {
            border-color: #ef4444;
        }
        .form-floating-custom .invalid-feedback {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 4px;
        }
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            font-size: 1.1rem;
            z-index: 2;
            transition: color 0.2s;
        }
        .toggle-password:hover {
            color: var(--teal);
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--teal);
            cursor: pointer;
        }
        .remember-check label {
            font-size: 0.85rem;
            color: #374151;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--teal) 0%, var(--accent-dark) 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(13,148,136,0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        .alert-errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .alert-errors ul {
            margin: 0;
            padding: 0 0 0 18px;
            font-size: 0.85rem;
            color: #dc2626;
        }
        .alert-errors li {
            margin-bottom: 2px;
        }

        .divider {
            text-align: center;
            margin: 28px 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e5e7eb;
        }
        .divider span {
            background: #fff;
            padding: 0 16px;
            position: relative;
            color: var(--gray);
            font-size: 0.82rem;
        }

        .btn-advisor {
            width: 100%;
            padding: 13px;
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-advisor:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(10,17,114,0.02);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 991px) {
            .login-brand { display: none; }
            .login-form-panel { width: 100%; }
        }
        @media (max-width: 576px) {
            .login-form-panel { padding: 24px; }
            .login-header h1 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

<!-- Panel Izquierdo: Branding -->
<div class="login-brand">
    <div class="brand-content">
        <img src="{{ asset('imagenes/logo.png') }}" alt="ACT Consultores">
        <h2>Portal de Clientes</h2>
        <p>Consulta el estado de tus tramites, revisa documentos y mantente al dia con tus obligaciones tributarias.</p>
        <div class="brand-features">
            <div class="brand-feature">
                <i class="bi bi-folder2-open"></i>
                <span>Consulta tus documentos</span>
            </div>
            <div class="brand-feature">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Seguimiento de tramites</span>
            </div>
            <div class="brand-feature">
                <i class="bi bi-chat-dots"></i>
                <span>Comunicacion directa</span>
            </div>
        </div>
    </div>
</div>

<!-- Panel Derecho: Formulario -->
<div class="login-form-panel">
    <div class="login-form-wrapper">
        <a href="/" class="back-link">
            <i class="bi bi-arrow-left"></i> Volver al inicio
        </a>

        <div class="login-header">
            <span class="badge-role"><i class="bi bi-person-circle"></i> Portal de Clientes</span>
            <h1>Accede a tu cuenta</h1>
            <p>Ingresa tus credenciales de cliente para continuar</p>
        </div>

        @if ($errors->any())
            <div class="alert-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('cliente.login') }}">
            @csrf

            <!-- Email -->
            <div class="form-floating-custom">
                <label for="email_cliente">Correo Electronico</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email_cliente" name="email_cliente"
                           value="{{ old('email_cliente') }}"
                           required placeholder="ejemplo@correo.com" autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="form-floating-custom">
                <label for="password">Contrasena</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="password" name="password"
                           required placeholder="Ingresa tu contrasena">
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <!-- Remember -->
            <div class="remember-row">
                <div class="remember-check">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Recordarme</label>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion
            </button>
        </form>

        <div class="divider"><span>o</span></div>

        <a href="{{ route('login') }}" class="btn-advisor">
            <i class="bi bi-briefcase"></i> Acceder como Asesor
        </a>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var passwordField = document.getElementById('password');
        var icon = this.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordField.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    });
</script>

</body>
</html>