<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Bulma CSS (lower priority, loaded after Bootstrap) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" media="all" data-priority="low">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        /* Reset Bulma overrides that conflict with Bootstrap */
        .button, .input, .select, .textarea {
            all: revert;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 2rem 1rem;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 450px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background-color: #6c63ff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .auth-brand-icon i {
            font-size: 2rem;
            color: #ffffff;
        }

        .auth-brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }

        .auth-brand-tagline {
            color: #a0a0b8;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .auth-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 2rem;
        }

        .auth-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-card .form-label {
            font-weight: 500;
            color: #4a5568;
            font-size: 0.875rem;
        }

        .auth-card .form-control {
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .auth-card .form-control:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
        }

        .auth-card .btn-primary {
            background-color: #6c63ff;
            border-color: #6c63ff;
            padding: 0.65rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.2s ease;
        }

        .auth-card .btn-primary:hover {
            background-color: #5a52d5;
            border-color: #5a52d5;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #a0a0b8;
            font-size: 0.875rem;
        }

        .auth-footer a {
            color: #6c63ff;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
            color: #8b83ff;
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="auth-wrapper">
        <!-- Brand / App Name -->
        <div class="auth-brand">
            <div class="auth-brand-icon">
                <i class="bi bi-building"></i>
            </div>
            <h1 class="auth-brand-name">{{ config('app.name') }}</h1>
            <p class="auth-brand-tagline">Plateforme de Formulaires Citoyens</p>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer Links -->
        <div class="auth-footer">
            <a href="{{ route('home') }}">
                <i class="bi bi-arrow-left me-1"></i>Retour &agrave; l'accueil
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
