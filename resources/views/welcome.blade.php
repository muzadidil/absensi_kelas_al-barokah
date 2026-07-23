<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Absensi Kelas Al-Barokah</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS via Vite (Breeze-style) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(to right, #1a202c, #2d3748);
            font-family: 'Instrument Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.5rem; /* 24px */
        }

        .logo-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-white antialiased">
    <div class="max-w-2xl text-center px-6 py-12 glass rounded-3xl shadow-lg">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/developer.png') }}" alt="Logo" class="h-20 w-20 logo-animation">
            <!-- <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel Logo" class="h-20 w-20 logo-animation"> -->
        </div>
        <h1 class="text-3xl font-semibold mb-2">Selamat Datang</h1>
        <p class="text-lg mb-6">Sistem Absensi Kelas Al-Barokah</p>

        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
            <a href="{{ route('login') }}" style="background-color: #2563eb; color: white;" class="px-5 py-2 rounded-full font-medium transition-all duration-200">Login</a>
            {{-- <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full font-medium transition-all duration-200">Login</a> --}}
            <!-- <a href="{{ route('register.form') }}" class="border border-blue-800 hover:bg-white hover:text-white px-5 py-2 rounded-full font-medium transition-all duration-200">Register</a> -->
        </div>
    </div>
</body>
</html>
