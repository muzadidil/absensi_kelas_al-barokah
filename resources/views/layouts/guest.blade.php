<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

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

        label, .text-label, .form-label, .input-label, .text-sm {
        color: white !important;
    }

    input, select, textarea {
        color: white !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
    }

    input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .text-red-400, .input-error {
        color: #f87171 !important; /* red-400 */
    }

    .glass a {
        color: #93c5fd; /* blue-300 */
    }

    .glass a:hover {
        color: #60a5fa; /* blue-400 */
    }

    /* .logo-animation {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    } */
    </style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans text-white antialiased bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4">
     <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass rounded-3xl shadow-lg">
        <div class="flex justify-center  mt-4">
            <img src="/images/developer.png" alt="Logo" class="h-20 w-20 logo-animation" />
        </div>
        {{ $slot }}
    </div> 
</body>
</html>
