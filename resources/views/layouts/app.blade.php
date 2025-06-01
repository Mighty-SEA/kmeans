<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel - Aplikasi Penerima')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Topbar -->
    <header class="bg-indigo-800 text-white shadow-md">
        <div class="container mx-auto">
            <div class="flex justify-between items-center py-4 px-6">
                <div class="flex items-center">
                    <div class="text-xl font-bold tracking-wide flex items-center">
                        <i class="fas fa-chart-pie mr-3"></i>
                        <span>K-Means Admin</span>
                    </div>
                    <nav class="hidden md:flex ml-10 space-x-8">
                        <a href="{{ url('/') }}" class="flex items-center text-gray-200 hover:text-white transition {{ request()->is('/') ? 'text-white font-medium' : '' }}">
                            <i class="fas fa-home mr-2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('penerima.index') }}" class="flex items-center text-gray-200 hover:text-white transition {{ request()->routeIs('penerima.*') ? 'text-white font-medium' : '' }}">
                            <i class="fas fa-users mr-2"></i>
                            <span>Data Penerima</span>
                        </a>
                        <a href="{{ route('statistic.index') }}" class="flex items-center text-gray-200 hover:text-white transition {{ request()->routeIs('statistic.*') ? 'text-white font-medium' : '' }}">
                            <i class="fas fa-chart-bar mr-2"></i>
                            <span>Statistik</span>
                        </a>
                    </nav>
                </div>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center text-white focus:outline-none">
                            <span class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="ml-2 hidden md:block">Admin</span>
                        </button>
                    </div>
                    <button id="mobileMenuButton" class="md:hidden ml-4 text-white focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="md:hidden hidden px-4 pb-4">
                <nav class="flex flex-col space-y-3">
                    <a href="{{ url('/') }}" class="flex items-center py-2 text-gray-200 hover:text-white transition {{ request()->is('/') ? 'text-white font-medium' : '' }}">
                        <i class="fas fa-home mr-2 w-6"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('penerima.index') }}" class="flex items-center py-2 text-gray-200 hover:text-white transition {{ request()->routeIs('penerima.*') ? 'text-white font-medium' : '' }}">
                        <i class="fas fa-users mr-2 w-6"></i>
                        <span>Data Penerima</span>
                    </a>
                    <a href="{{ route('statistic.index') }}" class="flex items-center py-2 text-gray-200 hover:text-white transition {{ request()->routeIs('statistic.*') ? 'text-white font-medium' : '' }}">
                        <i class="fas fa-chart-bar mr-2 w-6"></i>
                        <span>Statistik</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto py-4 px-6">
            <h2 class="text-xl font-medium text-gray-800">@yield('header', 'Dashboard')</h2>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto py-6 px-6">
        @yield('content')
    </main>

    <footer class="bg-white py-4 px-6 border-t text-center text-xs text-gray-600">
        <div class="container mx-auto">
            &copy; {{ date('Y') }} Aplikasi Penerima Bantuan. Dibuat dengan <span class="text-red-500">&#10084;</span>
        </div>
    </footer>

    <script>
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html> 