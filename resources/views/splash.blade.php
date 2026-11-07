<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SILAPANDU - Splash Screen</title>

    <!-- Tailwind & FontAwesome via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center overflow-hidden">

    <div class="text-center">
        <!-- Logo / Icon -->
        <div class="mb-8">
            <i class="fa-solid fa-kaaba text-8xl text-emerald-500 animate-pulse"></i>
        </div>

        <!-- Teks Utama -->
        <h1 class="text-5xl font-black tracking-[0.2em] mb-2 uppercase">
            SILAPANDU
        </h1>

        <!-- Sub-teks -->
        <p class="text-slate-400 font-medium tracking-wide mb-12">
            Sistem Layanan Pencatatan Pernikahan Terpadu
        </p>

        <!-- Loading Area -->
        <div class="flex flex-col items-center gap-4">
            <!-- Loading Bar -->
            <div class="w-48 h-1 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full animate-[loading_2.5s_ease-in-out_infinite]" style="width: 30%;"></div>
            </div>
            
            <!-- Teks Loading -->
            <p class="text-xs font-semibold text-slate-500 tracking-widest uppercase flex items-center gap-1">
                Memuat Aplikasi
                <span class="flex gap-0.5">
                    <span class="animate-bounce">.</span>
                    <span class="animate-bounce [animation-delay:0.2s]">.</span>
                    <span class="animate-bounce [animation-delay:0.4s]">.</span>
                </span>
            </p>
        </div>
    </div>

    <style>
        @keyframes loading {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(0%); }
            100% { transform: translateX(100%); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                // Redirect ke dashboard karena modul login mungkin belum dibuat
                window.location.href = "{{ route('dashboard') }}";
            }, 2500);
        });
    </script>
</body>
</html>
