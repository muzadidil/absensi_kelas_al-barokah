<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dasbor Guru') · Sistem Absensi Kelas Al-Barokah</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @stack('styles')

    <style>
        :root {
            --fp-primary: #4f46e5;
            --fp-primary-soft: rgba(79, 70, 229, 0.1);
            --fp-canvas: #f4f5f9;
            --fp-ink: #1f2430;
            --fp-ink-muted: #6b7280;
            --fp-border: #e7e8ee;
            --fp-radius: 0.9rem;
            --fp-shadow: 0 1px 2px rgba(16,24,40,.05), 0 1px 3px rgba(16,24,40,.06);
            --fp-sidebar-w: 260px;
        }
        * { font-family: "Inter", "Segoe UI", system-ui, sans-serif; }
        body {
            background: var(--fp-canvas);
            color: var(--fp-ink);
            margin: 0;
        }

        .fp-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--fp-sidebar-w);
            height: 100vh;
            background: #fff;
            border-right: 1px solid var(--fp-border);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .25s ease;
        }
        .fp-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 1.25rem 1.25rem 1rem;
        }
        .fp-brand img { width: 36px; height: 36px; border-radius: 0.6rem; }
        .fp-brand-text { font-weight: 700; font-size: 0.95rem; line-height: 1.15; }
        .fp-brand-text small { display: block; font-weight: 500; color: var(--fp-ink-muted); font-size: 0.72rem; }

        .fp-nav { padding: 0.5rem 0.85rem; flex: 1; overflow-y: auto; }
        .fp-nav .nav-section-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--fp-ink-muted);
            padding: 0.9rem 0.6rem 0.35rem;
        }
        .fp-nav a {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: 0.6rem;
            color: var(--fp-ink-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.88rem;
            margin-bottom: 0.15rem;
            transition: background .15s ease, color .15s ease;
        }
        .fp-nav a i { font-size: 1.05rem; width: 20px; text-align: center; }
        .fp-nav a:hover { background: #f3f4f8; color: var(--fp-ink); }
        .fp-nav a.active { background: var(--fp-primary-soft); color: var(--fp-primary); font-weight: 600; }

        .fp-sidebar-footer {
            padding: 0.85rem;
            border-top: 1px solid var(--fp-border);
        }
        .fp-user-card {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 0.6rem;
            border-radius: 0.7rem;
        }
        .fp-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--fp-primary-soft);
            color: var(--fp-primary);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
            flex-shrink: 0;
        }
        .fp-user-card .name { font-weight: 600; font-size: 0.85rem; color: var(--fp-ink); line-height: 1.2; }
        .fp-user-card .role { font-size: 0.72rem; color: var(--fp-ink-muted); }

        .fp-main { margin-left: var(--fp-sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .fp-topbar {
            background: #fff;
            border-bottom: 1px solid var(--fp-border);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .fp-topbar h1 { font-size: 1.05rem; font-weight: 700; margin: 0; }
        .fp-content { padding: 1.5rem; flex: 1; }

        .fp-sidebar-toggle { display: none; }

        @media (max-width: 991px) {
            .fp-sidebar { transform: translateX(-100%); }
            .fp-sidebar.open { transform: translateX(0); }
            .fp-main { margin-left: 0; }
            .fp-sidebar-toggle { display: inline-flex; }
        }

        .card { border: none; border-radius: var(--fp-radius); box-shadow: var(--fp-shadow); }
    </style>
</head>
<body>

    <nav class="fp-sidebar" id="fpSidebar">
        <div class="fp-brand">
            <img src="{{ asset('images/developer.png') }}" alt="Logo">
            <div class="fp-brand-text">
                Absensi Al-Barokah
                <small>Panel Guru</small>
            </div>
        </div>

        <div class="fp-nav">
            <div class="nav-section-label">Menu</div>
            <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dasbor
            </a>
            <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check-fill"></i> Isi Absensi
            </a>

            <div class="nav-section-label">Akun</div>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Profil Saya
            </a>
        </div>

        <div class="fp-sidebar-footer">
            <div class="fp-user-card">
                <div class="fp-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="flex-grow-1">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role">Guru</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Logout">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="fp-main">
        <div class="fp-topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary fp-sidebar-toggle" onclick="document.getElementById('fpSidebar').classList.toggle('open')">
                    <i class="bi bi-list"></i>
                </button>
                <h1>@yield('title', 'Dasbor Guru')</h1>
            </div>
        </div>

        <div class="fp-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') === 'profile-updated' ? 'Profil berhasil diperbarui.' : (session('status') === 'password-updated' ? 'Kata sandi berhasil diperbarui.' : session('status')) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
