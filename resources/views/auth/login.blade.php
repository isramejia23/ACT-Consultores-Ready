<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('imagenes/logo.png') }}">
    <title>ACT Consultores - Login Asesor</title>
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, #2a3cba 100%);
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
            background: radial-gradient(circle, rgba(19,184,221,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(19,184,221,0.08) 0%, transparent 70%);
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
            color: rgba(255,255,255,0.65);
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
            color: rgba(255,255,255,0.8);
            font-size: 0.88rem;
        }
        .brand-feature i {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 8px;
            background: rgba(19,184,221,0.15);
            color: var(--accent);
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
            color: var(--primary);
        }
        .login-header {
            margin-bottom: 36px;
        }
        .login-header .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(10,17,114,0.08);
            color: var(--primary);
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
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(19,184,221,0.1);
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
            color: var(--primary);
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
            accent-color: var(--accent);
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
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
            box-shadow: 0 8px 25px rgba(10,17,114,0.3);
        }
        .btn-login:active {
            transform: translateY(0);
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

        .btn-client {
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
        .btn-client:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(19,184,221,0.03);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 991px) {
            .login-brand {
                display: none;
            }
            .login-form-panel {
                width: 100%;
            }
        }
        @media (max-width: 576px) {
            .login-form-panel {
                padding: 24px;
            }
            .login-header h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

<!-- Panel Izquierdo: Branding -->
<div class="login-brand">
    <div class="brand-content">
        <img src="{{ asset('imagenes/logo.png') }}" alt="ACT Consultores">
        <h2>ACT Consultores</h2>
        <p>Tu aliado estrategico en materia tributaria. Gestionamos tus obligaciones fiscales con profesionalismo.</p>
        <div class="brand-features">
            <div class="brand-feature">
                <i class="bi bi-shield-check"></i>
                <span>Acceso seguro y encriptado</span>
            </div>
            <div class="brand-feature">
                <i class="bi bi-speedometer2"></i>
                <span>Panel de control intuitivo</span>
            </div>
            <div class="brand-feature">
                <i class="bi bi-bell"></i>
                <span>Notificaciones en tiempo real</span>
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
            <span class="badge-role"><i class="bi bi-briefcase"></i> Portal de Asesores</span>
            <h1>Bienvenido de vuelta</h1>
            <p>Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-floating-custom">
                <label for="email">Correo Electronico</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email"
                           class="@error('email') is-invalid @enderror"
                           value="{{ old('email', Cookie::get('remembered_email')) }}"
                           required placeholder="ejemplo@correo.com" autofocus>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-floating-custom">
                <label for="password">Contrasena</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="password" name="password"
                           class="@error('password') is-invalid @enderror"
                           @if(Cookie::get('remember_me')) value="{{ Cookie::get('remembered_password') }}" @endif
                           required placeholder="Ingresa tu contrasena">
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <!-- Remember -->
            <div class="remember-row">
                <div class="remember-check">
                    <input type="checkbox" name="remember" id="remember"
                           {{ Cookie::get('remember_me') ? 'checked' : '' }}>
                    <label for="remember">Recordarme</label>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion
            </button>
        </form>

        <div class="divider"><span>o</span></div>

        <a href="{{ route('cliente.login') }}" class="btn-client">
            <i class="bi bi-person"></i> Acceder como Cliente
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