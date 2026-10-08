<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AgroSystem') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ====== Estilos base ====== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #F7F8F5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* ====== Contenedor principal ====== */
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            min-height: 600px;
        }

        /* ====== Panel izquierdo (imagen + overlay) ====== */
        .login-hero {
            position: relative;
            background: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=800&h=600&fit=crop&crop=center') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            color: white;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(17, 52, 32, 0.55); /* #113420 con opacidad */
            z-index: 1;
        }

        .login-hero > * {
            position: relative;
            z-index: 2;
        }

        .login-hero .brand {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .login-hero .brand span {
            color: #6FA34F; /* verde claro para contraste */
        }

        .login-hero .tagline {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 2.5rem;
            max-width: 80%;
        }

        .login-hero .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .login-hero .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 400;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .login-hero .feature-list li::before {
            content: "✓";
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background: rgba(111, 163, 79, 0.3);
            border-radius: 50%;
            font-weight: 700;
            color: #6FA34F;
            font-size: 0.9rem;
        }

        /* ====== Panel derecho (formulario) ====== */
        .login-form {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        /* Logo SVG minimalista */
        .logo-svg {
            width: 48px;
            height: 48px;
            margin-bottom: 1.75rem;
        }

        .login-form h2 {
            font-size: 1.75rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #1F4D3D;
            margin-bottom: 0.3rem;
        }

        .login-form .subtitle {
            color: #5F6368;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        /* ====== Inputs ====== */
        .input-group {
            margin-bottom: 1.25rem;
        }

        .input-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1F4D3D;
            margin-bottom: 0.3rem;
        }

        .input-group .input-wrapper {
            position: relative;
        }

        .input-group .input-wrapper .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #9CA3AF;
            pointer-events: none;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            border: 1.5px solid #DADADA;
            border-radius: 16px;
            font-size: 0.95rem;
            background: #FFFFFF;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            outline: none;
            color: #1F4D3D;
        }

        .input-group input:focus {
            border-color: #2E7D32;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.08);
        }

        .input-group input::placeholder {
            color: #B0B0B0;
        }

        /* ====== Opciones (recordar + olvidé) ====== */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1.5rem 0 1.8rem;
        }

        .form-options .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #5F6368;
            cursor: pointer;
        }

        .form-options .remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #2E7D32;
            border-radius: 4px;
            border: 1.5px solid #DADADA;
            cursor: pointer;
        }

        .form-options .forgot-link {
            font-size: 0.9rem;
            color: #2E7D32;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .form-options .forgot-link:hover {
            color: #1F4D3D;
            text-decoration: underline;
        }

        /* ====== Botón ====== */
        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: #2E7D32;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.01em;
        }

        .btn-login:hover {
            background: #1F4D3D;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        /* ====== Mensajes de error / estado ====== */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
        }

        .alert-success {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #166534;
        }

        /* ====== Responsive ====== */
        @media (max-width: 900px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                border-radius: 24px;
            }

            .login-hero {
                display: none; /* Ocultamos la imagen en móvil */
            }

            .login-form {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .login-form {
                padding: 1.5rem 1rem;
            }
            .login-form h2 {
                font-size: 1.5rem;
            }
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
        }

        /* ====== Animaciones suaves ====== */
        .fade-slide {
            opacity: 0;
            transform: translateY(12px);
            animation: fadeSlideUp 0.6s ease forwards;
        }

        @keyframes fadeSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.15s; }
        .delay-3 { animation-delay: 0.25s; }
        .delay-4 { animation-delay: 0.35s; }
    </style>
</head>
<body>

    <div class="login-wrapper fade-slide">

        <!-- ====== Panel Izquierdo (Hero) ====== -->
        <div class="login-hero">
            <div class="brand">AGRO<span>PALMA</span></div>
            <p class="tagline">Tecnología para una agricultura más rentable.</p>
            <ul class="feature-list">
                <li>Producción</li>
                <li>Costos</li>
                <li>Inventarios</li>
                <li>Trazabilidad</li>
                <li>Agricultura de precisión</li>
            </ul>
        </div>

        <!-- ====== Panel Derecho (Formulario) ====== -->
        <div class="login-form">

            <!-- Logo SVG (hoja minimalista) -->
            <svg class="logo-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="24" fill="#1F4D3D" />
                <path d="M24 14C24 14 18 20 18 26C18 30 20 34 24 34C28 34 30 30 30 26C30 20 24 14 24 14Z" fill="#6FA34F" />
                <path d="M24 14C24 14 20 18 20 24C20 28 22 30 24 30C26 30 28 28 28 24C28 18 24 14 24 14Z" fill="#2E7D32" />
                <path d="M24 14C24 14 22 16 22 20C22 23 23 24 24 24C25 24 26 23 26 20C26 16 24 14 24 14Z" fill="#F7F8F5" />
            </svg>

            <h2>Bienvenido nuevamente</h2>
            <p class="subtitle">Inicie sesión para administrar sus cultivos y operaciones agrícolas.</p>

            <!-- Mensajes de estado / error -->
            @if (session('status'))
                <div class="alert alert-success fade-slide delay-1">
                    <span>✅</span> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error fade-slide delay-1">
                    <span>⚠️</span> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="fade-slide delay-1">
                @csrf

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrapper">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nombre@ejemplo.com">
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                </div>

                <!-- Opciones -->
                <div class="form-options">
                    <label class="remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidó su contraseña?</a>
                    @endif
                </div>

                <!-- Botón -->
                <button type="submit" class="btn-login">Iniciar sesión</button>
            </form>
        </div>
    </div>

</body>
</html>