<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('imagenes/logo.png') }}">
    <title>ACT Consultores - Bienvenidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0a1172;
            --primary-light: #1a2490;
            --accent: #13b8dd;
            --accent-dark: #0e9ab8;
            --dark: #1a1a2e;
            --light: #f8f9fc;
            --gray: #6b7280;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            overflow-x: hidden;
        }

        /* ========== NAVBAR ========== */
        .navbar-custom {
            background: var(--primary);
            padding: 12px 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .navbar-custom.scrolled {
            padding: 8px 0;
            background: rgba(10, 17, 114, 0.97);
            backdrop-filter: blur(10px);
        }
        .navbar-custom .navbar-brand img {
            height: 45px;
            width: auto;
            transition: height 0.3s;
        }
        .navbar-custom.scrolled .navbar-brand img {
            height: 38px;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 8px 16px !important;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .navbar-custom .nav-link:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.1);
        }
        .navbar-toggler {
            border: none;
            padding: 6px 10px;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .btn-accent {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-accent:hover {
            background: var(--accent-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(19, 184, 221, 0.4);
        }
        .btn-outline-light-custom {
            border: 2px solid rgba(255,255,255,0.7);
            color: #fff;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            background: transparent;
        }
        .btn-outline-light-custom:hover {
            background: rgba(255,255,255,0.15);
            border-color: #fff;
            color: #fff;
        }

        /* ========== HERO ========== */
        .hero {
            position: relative;
            min-height: 92vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, #2a3cba 100%);
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(19,184,221,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(19,184,221,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(19,184,221,0.15);
            border: 1px solid rgba(19,184,221,0.3);
            color: var(--accent);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 24px;
        }
        .hero h1 {
            font-size: 3.2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .hero h1 span {
            color: var(--accent);
        }
        .hero p.lead {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.75);
            line-height: 1.7;
            max-width: 520px;
            margin-bottom: 32px;
        }
        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .hero-img-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .hero-logo-float {
            width: 320px;
            max-width: 100%;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
            animation: floatLogo 4s ease-in-out infinite;
        }
        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Stats bar */
        .stats-bar {
            background: var(--white);
            border-radius: 16px;
            padding: 30px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        .stat-label {
            font-size: 0.85rem;
            color: var(--gray);
            font-weight: 500;
        }

        /* ========== SERVICES ========== */
        .services-section {
            padding: 100px 0 80px;
            background: var(--light);
        }
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-tag {
            display: inline-block;
            background: rgba(10,17,114,0.08);
            color: var(--primary);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
        }
        .section-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }
        .section-header p {
            color: var(--gray);
            font-size: 1.05rem;
            max-width: 550px;
            margin: 0 auto;
        }

        .service-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        .service-card .card-img-top {
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .service-card:hover .card-img-top {
            transform: scale(1.05);
        }
        .service-card .img-wrapper {
            overflow: hidden;
        }
        .service-card .card-body {
            padding: 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .service-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 16px;
        }
        .service-icon.blue { background: rgba(10,17,114,0.1); color: var(--primary); }
        .service-icon.cyan { background: rgba(19,184,221,0.1); color: var(--accent); }
        .service-icon.green { background: rgba(16,185,129,0.1); color: #10b981; }

        .service-card h4 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }
        .service-card p {
            color: var(--gray);
            font-size: 0.92rem;
            line-height: 1.65;
            flex: 1;
        }

        /* ========== WHY US ========== */
        .why-us {
            padding: 90px 0;
            background: var(--white);
        }
        .feature-item {
            display: flex;
            gap: 16px;
            margin-bottom: 30px;
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;
            background: rgba(19,184,221,0.1);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .feature-item h5 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }
        .feature-item p {
            color: var(--gray);
            font-size: 0.88rem;
            margin: 0;
            line-height: 1.5;
        }

        /* ========== TESTIMONIALS ========== */
        .testimonials {
            padding: 90px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }
        .testimonials .section-header h2 {
            color: #fff;
        }
        .testimonials .section-header p {
            color: rgba(255,255,255,0.65);
        }
        .testimonials .section-tag {
            background: rgba(255,255,255,0.1);
            color: var(--accent);
        }
        .testimonial-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 30px;
            height: 100%;
            backdrop-filter: blur(5px);
            transition: all 0.3s;
        }
        .testimonial-card:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-4px);
        }
        .testimonial-stars {
            color: #fbbf24;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }
        .testimonial-card .quote {
            color: rgba(255,255,255,0.85);
            font-size: 0.95rem;
            line-height: 1.7;
            font-style: italic;
            margin-bottom: 20px;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .testimonial-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .testimonial-author h6 {
            color: #fff;
            font-weight: 600;
            margin: 0;
            font-size: 0.9rem;
        }
        .testimonial-author small {
            color: rgba(255,255,255,0.5);
            font-size: 0.78rem;
        }

        /* ========== CTA ========== */
        .cta-section {
            padding: 80px 0;
            background: var(--light);
            text-align: center;
        }
        .cta-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 14px;
        }
        .cta-section p {
            color: var(--gray);
            font-size: 1.05rem;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-cta-whatsapp {
            background: #25d366;
            color: #fff;
            font-weight: 600;
            padding: 14px 36px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-cta-whatsapp:hover {
            background: #1fba59;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37,211,102,0.35);
        }

        /* ========== FOOTER ========== */
        .footer {
            background: var(--dark);
            color: rgba(255,255,255,0.6);
            padding: 50px 0 30px;
        }
        .footer-brand img {
            height: 40px;
            margin-bottom: 14px;
            filter: brightness(0) invert(1);
        }
        .footer-brand p {
            font-size: 0.88rem;
            max-width: 300px;
            line-height: 1.6;
        }
        .footer h6 {
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 16px;
        }
        .footer-links {
            list-style: none;
            padding: 0;
        }
        .footer-links li {
            margin-bottom: 8px;
        }
        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.88rem;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: var(--accent);
        }
        .footer-contact p {
            font-size: 0.88rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-contact i {
            color: var(--accent);
            width: 16px;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 35px;
            padding-top: 20px;
            text-align: center;
            font-size: 0.82rem;
        }

        /* ========== WHATSAPP FLOAT ========== */
        .whatsapp-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #25d366;
            color: #fff;
            padding: 14px 22px;
            border-radius: 50px;
            box-shadow: 0 6px 25px rgba(37,211,102,0.4);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.3s;
            animation: pulseWa 2s infinite;
        }
        .whatsapp-float:hover {
            color: #fff;
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(37,211,102,0.5);
        }
        .whatsapp-float i {
            font-size: 1.5rem;
        }
        @keyframes pulseWa {
            0%, 100% { box-shadow: 0 6px 25px rgba(37,211,102,0.4); }
            50% { box-shadow: 0 6px 35px rgba(37,211,102,0.6); }
        }

        /* ========== ANIMATIONS ========== */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 991px) {
            .hero h1 {
                font-size: 2.4rem;
            }
            .hero-logo-float {
                width: 220px;
                margin-top: 40px;
            }
            .stats-bar {
                margin-top: -30px;
                padding: 20px;
            }
            .stat-number {
                font-size: 1.5rem;
            }
        }
        @media (max-width: 767px) {
            .hero {
                min-height: auto;
                padding: 100px 0 60px;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .hero p.lead {
                font-size: 1rem;
            }
            .hero-buttons {
                justify-content: center;
            }
            .hero-content {
                text-align: center;
            }
            .hero p.lead {
                margin-left: auto;
                margin-right: auto;
            }
            .section-header h2 {
                font-size: 1.7rem;
            }
            .whatsapp-float span {
                display: none;
            }
            .whatsapp-float {
                padding: 16px;
                border-radius: 50%;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('imagenes/logo.png') }}" alt="ACT Consultores">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="#services">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="#why-us">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonios</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contacto</a></li>
            </ul>
            <div class="d-flex gap-2 mt-3 mt-lg-0">
                @auth
                    <div class="dropdown">
                        <button class="btn-accent dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Hola, {{ Auth::user()->nombre }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('home') }}">Home</a></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cerrar Sesion
                                </a>
                            </li>
                        </ul>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline-light-custom">Soy Asesor</a>
                    <a href="{{ route('cliente.login') }}" class="btn-accent">Soy Cliente</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 hero-content">
                <span class="hero-badge"><i class="fas fa-shield-alt me-2"></i>Consultoria tributaria profesional</span>
                <h1>Tus obligaciones tributarias en <span>buenas manos</span></h1>
                <p class="lead">Somos expertos en asesoria tributaria, declaraciones del SRI y facturacion electronica. Simplificamos tus procesos fiscales para que te enfoques en lo que importa: tu negocio.</p>
                <div class="hero-buttons">
                    @auth
                        <a href="{{ route('home') }}" class="btn-accent"><i class="fas fa-arrow-right me-2"></i>Ir al Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-accent"><i class="fas fa-briefcase me-2"></i>Acceso Asesores</a>
                        <a href="{{ route('cliente.login') }}" class="btn-outline-light-custom"><i class="fas fa-user me-2"></i>Acceso Clientes</a>
                    @endauth
                    <a href="https://wa.me/593999752027?text=Hola%2C%20necesito%20informaci%C3%B3n%20sobre%20sus%20servicios" target="_blank" class="btn-outline-light-custom" style="border-color: #25d366; color: #25d366;">
                        <i class="fab fa-whatsapp me-2"></i>Contactanos
                    </a>
                </div>
            </div>
            <div class="col-lg-5 hero-img-wrapper d-none d-lg-flex">
                <img src="{{ asset('imagenes/logo.png') }}" alt="ACT Consultores" class="hero-logo-float">
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<div class="container">
    <div class="stats-bar fade-up">
        <div class="row text-center">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">+500</div>
                    <div class="stat-label">Clientes atendidos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">+10</div>
                    <div class="stat-label">Anos de experiencia</div>
                </div>
            </div>
            <div class="col-6 col-md-3 mt-3 mt-md-0">
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Cumplimiento SRI</div>
                </div>
            </div>
            <div class="col-6 col-md-3 mt-3 mt-md-0">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Soporte continuo</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<section id="services" class="services-section">
    <div class="container">
        <div class="section-header fade-up">
            <span class="section-tag">Nuestros Servicios</span>
            <h2>Soluciones tributarias a tu medida</h2>
            <p>Ofrecemos servicios integrales para que tu negocio cumpla con todas sus obligaciones fiscales sin complicaciones.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 fade-up">
                <div class="service-card">
                    <div class="img-wrapper">
                        <img src="{{ asset('imagenes/imagen1.png') }}" alt="Asesoria Tributaria" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="service-icon blue"><i class="fas fa-balance-scale"></i></div>
                        <h4>Asesoria Tributaria</h4>
                        <p>Orientacion experta para optimizar tu carga fiscal. Te ayudamos a cumplir tus obligaciones de forma eficiente, evitando multas y sanciones innecesarias.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="service-card">
                    <div class="img-wrapper">
                        <img src="{{ asset('imagenes/imagen2.png') }}" alt="Declaraciones del SRI" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="service-icon cyan"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h4>Declaraciones del SRI</h4>
                        <p>Preparamos y presentamos tus declaraciones mensuales y anuales con precision. IVA, Impuesto a la Renta, Anexos y mas, siempre a tiempo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="service-card">
                    <div class="img-wrapper">
                        <img src="{{ asset('imagenes/imagen3.png') }}" alt="Facturacion Electronica" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="service-icon green"><i class="fas fa-receipt"></i></div>
                        <h4>Facturacion Electronica</h4>
                        <p>Implementamos tu sistema de facturacion electronica de manera rapida. Cumple con los requisitos del SRI y moderniza la gestion de tus comprobantes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Us Section -->
<section id="why-us" class="why-us">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0 text-center fade-up">
                <img src="{{ asset('imagenes/logo.png') }}" alt="ACT Consultores" style="max-width: 280px; opacity: 0.9;">
            </div>
            <div class="col-lg-7 fade-up">
                <span class="section-tag">Por que elegirnos</span>
                <h2 style="font-size: 2rem; font-weight: 700; color: var(--dark); margin-bottom: 30px;">Confianza, experiencia y resultados</h2>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-user-tie"></i></div>
                    <div>
                        <h5>Equipo profesional certificado</h5>
                        <p>Contadores y asesores con amplia experiencia en normativa tributaria ecuatoriana.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h5>Cumplimiento puntual</h5>
                        <p>Nos aseguramos de que todas tus obligaciones se presenten dentro de los plazos establecidos.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <div>
                        <h5>Atencion personalizada</h5>
                        <p>Cada cliente recibe un servicio adaptado a las necesidades especificas de su negocio.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-laptop"></i></div>
                    <div>
                        <h5>Plataforma digital</h5>
                        <p>Accede al estado de tus tramites y documentos desde cualquier lugar con nuestra plataforma en linea.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="testimonials">
    <div class="container">
        <div class="section-header fade-up">
            <span class="section-tag">Testimonios</span>
            <h2>Lo que dicen nuestros clientes</h2>
            <p>La satisfaccion de nuestros clientes es nuestra mejor carta de presentacion.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 fade-up">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="quote">"ACT Consultores me ayudo a regularizar mis impuestos sin estres. El proceso fue claro y profesional de principio a fin."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">ML</div>
                        <div>
                            <h6>Maria Lopez</h6>
                            <small>Emprendedora</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="quote">"Muy profesionales y atentos. Me explicaron todo el proceso de facturacion electronica y lo implementaron en tiempo record."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">CP</div>
                        <div>
                            <h6>Carlos Perez</h6>
                            <small>Comerciante</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="quote">"Recomiendo totalmente sus servicios. Son confiables, eficientes y siempre estan disponibles para resolver cualquier duda."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">AG</div>
                        <div>
                            <h6>Andrea Gomez</h6>
                            <small>Empresaria</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="contact" class="cta-section">
    <div class="container fade-up">
        <h2>Listo para simplificar tus impuestos?</h2>
        <p>Contactanos hoy y recibe una asesoria inicial sin compromiso. Estamos aqui para ayudarte.</p>
        <a href="https://wa.me/593999752027?text=Hola%2C%20necesito%20informaci%C3%B3n%20sobre%20sus%20servicios" target="_blank" class="btn-cta-whatsapp">
            <i class="fab fa-whatsapp"></i>Escribenos por WhatsApp
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-brand">
                    <img src="{{ asset('imagenes/logo.png') }}" alt="ACT Consultores">
                    <p>Soluciones tributarias integrales para personas naturales y empresas en Ecuador.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h6>Servicios</h6>
                <ul class="footer-links">
                    <li><a href="#services">Asesoria Tributaria</a></li>
                    <li><a href="#services">Declaraciones del SRI</a></li>
                    <li><a href="#services">Facturacion Electronica</a></li>
                    <li><a href="#services">Contabilidad General</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Contacto</h6>
                <div class="footer-contact">
                    <p><i class="fab fa-whatsapp"></i> +593 999 752 027</p>
                    <p><i class="fas fa-envelope"></i> isramejia23@hotmail.com</p>
                    <p><i class="fas fa-phone"></i> +593 983 774 624</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 ACT Consultores | Desarrollado por FIM-X-Suite</p>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/593999752027?text=Hola%2C%20necesito%20informaci%C3%B3n%20sobre%20sus%20servicios" target="_blank" class="whatsapp-float">
    <i class="fab fa-whatsapp"></i>
    <span>Chatea con nosotros</span>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        var navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Close mobile navbar
                var navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    new bootstrap.Collapse(navbarCollapse).hide();
                }
            }
        });
    });

    // Fade-up animation on scroll
    var fadeElements = document.querySelectorAll('.fade-up');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    fadeElements.forEach(function(el) {
        observer.observe(el);
    });
</script>

</body>
</html>