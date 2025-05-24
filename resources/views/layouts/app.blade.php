<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aplikasi Penerima')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#FDFDFC] min-h-screen flex flex-col">
    <nav class="bg-white shadow-md py-4 px-6 flex items-center justify-between">
        <div class="text-xl font-bold text-[#f53003] tracking-wide">
            <a href="{{ url('/') }}">Penerima Bantuan</a>
        </div>
        <div>
            <a href="{{ route('penerima.index') }}" class="text-[#1b1b18] font-medium hover:text-[#f53003] transition">Data Penerima</a>
        </div>
    </nav>
    <main class="flex-1">
        @yield('content')
    </main>
    <footer class="bg-white text-center py-4 text-xs text-[#706f6c] border-t mt-8">
        &copy; {{ date('Y') }} Aplikasi Penerima Bantuan. Dibuat dengan <span class="text-[#f53003]">&#10084;</span>.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html> 