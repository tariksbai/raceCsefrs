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

    <!-- Inline Sidebar & Layout Styles -->
    <style>
        /* ── Reset Bulma overrides that conflict with Bootstrap ── */
        .button, .input, .select, .textarea {
            all: revert;
        }

        /* ── Sidebar ── */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background-color: #1a1a2e;
            color: #c7c7d4;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        #sidebar .sidebar-brand i {
            font-size: 1.5rem;
            color: #6c63ff;
        }

        #sidebar .nav-section {
            flex: 1;
            padding: 1rem 0;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.5rem;
            color: #a0a0b8;
            font-size: 0.9rem;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        #sidebar .nav-link.active {
            color: #ffffff;
            background-color: rgba(108, 99, 255, 0.15);
            border-left-color: #6c63ff;
        }

        #sidebar .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }

        #sidebar .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        #sidebar .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        #sidebar .sidebar-footer .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #6c63ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        #sidebar .sidebar-footer .user-name {
            font-size: 0.9rem;
            color: #ffffff;
            font-weight: 500;
        }

        #sidebar .sidebar-footer .user-role {
            font-size: 0.75rem;
            color: #a0a0b8;
        }

        #sidebar .btn-logout {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.06);
            color: #a0a0b8;
            border: none;
            padding: 0.5rem;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        #sidebar .btn-logout:hover {
            background-color: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
        }

        /* ── Main Content ── */
        #main-content {
            margin-left: 260px;
            min-height: 100vh;
            background-color: #f4f6f9;
            transition: margin-left 0.3s ease;
        }

        /* ── Top Navbar ── */
        #top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        #top-navbar .page-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        #top-navbar .navbar-search {
            max-width: 320px;
        }

        #top-navbar .navbar-search .form-control {
            border-radius: 20px;
            padding-left: 2.5rem;
            background-color: #f4f6f9;
            border: 1px solid #e9ecef;
        }

        #top-navbar .navbar-search .search-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        #top-navbar .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-dark-mode {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #718096;
            cursor: pointer;
            padding: 0.35rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-dark-mode:hover {
            background-color: #f4f6f9;
            color: #2d3748;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        /* ── Sidebar Toggle Button (mobile) ── */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: #2d3748;
            cursor: pointer;
            margin-right: 0.75rem;
        }

        /* ── Overlay for mobile sidebar ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1035;
        }

        /* ── Dark Mode ── */
        body.dark-mode {
            background-color: #0f0f1a;
        }

        body.dark-mode #main-content {
            background-color: #0f0f1a;
        }

        body.dark-mode #top-navbar {
            background-color: #1a1a2e;
            border-bottom-color: #2d2d44;
        }

        body.dark-mode #top-navbar .page-title {
            color: #e2e8f0;
        }

        body.dark-mode #top-navbar .navbar-search .form-control {
            background-color: #16213e;
            border-color: #2d2d44;
            color: #e2e8f0;
        }

        body.dark-mode .content-wrapper {
            color: #e2e8f0;
        }

        body.dark-mode .card {
            background-color: #1a1a2e;
            border-color: #2d2d44;
            color: #e2e8f0;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: inline-block;
            }

            #sidebar-overlay.show {
                display: block;
            }
        }

        /* ── Nav section label ── */
        .nav-section-label {
            padding: 0.75rem 1.5rem 0.35rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6c6c80;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay (mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-building"></i>
            <span>{{ config('app.name') }}</span>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Principal</div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-label">Administration</div>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Utilisateurs</span>
            </a>

            <a href="{{ route('admin.roles.index') }}"
               class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                <span>R&ocirc;les</span>
            </a>

            <a href="{{ route('admin.permissions.index') }}"
               class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <i class="bi bi-key"></i>
                <span>Permissions</span>
            </a>

            <a href="{{ route('admin.groups.index') }}"
               class="nav-link {{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Groupes</span>
            </a>

            <div class="nav-section-label">Formulaires</div>

            <a href="{{ route('admin.external-forms.index') }}"
               class="nav-link {{ request()->routeIs('admin.external-forms.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Formulaires Externes</span>
            </a>

            <a href="{{ route('admin.internal-forms.index') }}"
               class="nav-link {{ request()->routeIs('admin.internal-forms.*') ? 'active' : '' }}">
                <i class="bi bi-ui-checks-grid"></i>
                <span>Formulaires Internes</span>
            </a>

            <a href="{{ route('admin.responses.index', ['formId' => 'all']) }}"
               class="nav-link {{ request()->routeIs('admin.responses.*') ? 'active' : '' }}">
                <i class="bi bi-chat-square-text"></i>
                <span>R&eacute;ponses</span>
            </a>
        </div>

        <!-- Sidebar Footer: User Info & Logout -->
        <div class="sidebar-footer">
            @auth
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->roles->first()->name ?? 'Utilisateur' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-left"></i>
                    D&eacute;connexion
                </button>
            </form>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Top Navbar -->
        <header id="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="navbar-search position-relative d-none d-md-block">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="form-control" placeholder="Rechercher...">
            </div>

            <div class="navbar-actions">
                <!-- Dark Mode Toggle -->
                <button class="btn-dark-mode" onclick="toggleDarkMode()" title="Basculer le mode sombre" aria-label="Toggle dark mode">
                    <i class="bi bi-moon-stars" id="dark-mode-icon"></i>
                </button>

                <!-- User Dropdown -->
                @auth
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-left me-2"></i>D&eacute;connexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="content-wrapper">
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

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle (mobile)
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebar-overlay').classList.toggle('show');
        }

        // Dark mode toggle
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const icon = document.getElementById('dark-mode-icon');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('bi-moon-stars');
                icon.classList.add('bi-sun');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                icon.classList.remove('bi-sun');
                icon.classList.add('bi-moon-stars');
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        // Persist dark mode preference
        (function () {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
                var icon = document.getElementById('dark-mode-icon');
                if (icon) {
                    icon.classList.remove('bi-moon-stars');
                    icon.classList.add('bi-sun');
                }
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
