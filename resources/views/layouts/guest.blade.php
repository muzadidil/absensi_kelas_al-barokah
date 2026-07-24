<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ \App\Models\Setting::siteName() }}</title>

    <link rel="icon" href="{{ \App\Models\Setting::faviconUrl() }}" />

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        :root {
            /* Warna aksen tunggal — dipakai untuk tombol, tautan, dan fokus input,
               supaya semua elemen "isian" konsisten satu warna (biru). */
            --auth-accent: #2563eb;
            --auth-accent-strong: #1d4ed8;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Figtree', 'Instrument Sans', system-ui, sans-serif;
            background:
                radial-gradient(900px 500px at 12% 18%, rgba(37, 99, 235, 0.28), transparent 60%),
                radial-gradient(800px 500px at 88% 82%, rgba(59, 130, 246, 0.20), transparent 55%),
                linear-gradient(135deg, #0b1120 0%, #111a2e 50%, #0b1120 100%);
            color: #fff;
        }

        /* ===== Kartu auth split (branding + form) ===== */
        .auth-shell {
            width: 100%;
            max-width: 940px;
            margin: auto;
        }
        .auth-card {
            display: flex;
            flex-wrap: wrap;
            border-radius: 1.5rem;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 30px 60px -20px rgba(2, 6, 23, 0.7);
            backdrop-filter: blur(10px);
        }

        /* --- Panel kiri: branding --- */
        .auth-brand {
            flex: 1 1 360px;
            padding: 2.5rem 2.25rem;
            display: flex;
            flex-direction: column;
            background:
                linear-gradient(160deg, rgba(37, 99, 235, 0.35), rgba(37, 99, 235, 0.05));
            border-right: 1px solid rgba(255, 255, 255, 0.10);
        }
        .auth-logo {
            width: 88px;
            height: 88px;
            object-fit: contain;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.10);
            padding: 0.6rem;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .auth-brand-name {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.25;
            margin: 1.25rem 0 0.35rem;
        }
        .auth-brand-tagline {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        .auth-address {
            margin-top: auto;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.85rem;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.82);
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 0.85rem;
            padding: 0.85rem 1rem;
        }
        .auth-address svg { flex-shrink: 0; margin-top: 2px; }

        /* --- Panel kanan: form --- */
        .auth-form {
            flex: 1 1 380px;
            padding: 2.5rem 2.25rem;
        }

        /* ===== Konsistensi warna "isian" (input/select/textarea) ===== */
        label, .text-label, .form-label, .input-label, .text-sm {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        input, select, textarea {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.22) !important;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--auth-accent) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.35) !important;
            background-color: rgba(255, 255, 255, 0.12) !important;
        }
        select { color-scheme: dark; }
        select option { background-color: #111a2e !important; color: #fff !important; }
        input::placeholder { color: rgba(255, 255, 255, 0.55); }

        .text-red-400, .input-error { color: #f87171 !important; }

        .auth-form a { color: #93c5fd; }
        .auth-form a:hover { color: #bfdbfe; }

        @media (max-width: 767.98px) {
            .auth-brand { flex-basis: 100%; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.10); padding: 2rem 1.75rem; }
            .auth-form  { flex-basis: 100%; padding: 2rem 1.75rem; }
            .auth-address { margin-top: 1.25rem; }
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="auth-shell">
            <div class="auth-card">
                <!-- Branding -->
                <div class="auth-brand">
                    <img src="{{ \App\Models\Setting::loginLogoUrl() }}" alt="Logo" class="auth-logo" />
                    <div class="auth-brand-name">{{ \App\Models\Setting::siteName() }}</div>
                    <div class="auth-brand-tagline">Selamat datang. Silakan masuk untuk melanjutkan.</div>

                    @if(\App\Models\Setting::address())
                        <div class="auth-address">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                            </svg>
                            <span>{!! nl2br(e(\App\Models\Setting::address())) !!}</span>
                        </div>
                    @endif
                </div>

                <!-- Form -->
                <div class="auth-form">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
