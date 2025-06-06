<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel - Aplikasi Penerima')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">
    <!-- Topbar -->
    <header class="bg-gradient-to-r from-indigo-800 to-indigo-700 text-white shadow-lg">
        <div class="container mx-auto">
            <div class="flex justify-between items-center py-4 px-6">
                <div class="flex items-center">
                    <div class="text-xl font-bold tracking-wide flex items-center">
                        <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-md mr-3">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span>K-Means Analytics</span>
                    </div>
                    <nav class="hidden md:flex ml-10 space-x-8">
                        <a href="{{ url('/') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->is('/') ? 'text-white font-medium border-b-2 border-white pb-1' : '' }}">
                            <i class="fas fa-home mr-2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('penerima.index') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->routeIs('penerima.*') ? 'text-white font-medium border-b-2 border-white pb-1' : '' }}">
                            <i class="fas fa-users mr-2"></i>
                            <span>Data Penerima</span>
                        </a>
                        <a href="{{ route('statistic.index') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->routeIs('statistic.*') ? 'text-white font-medium border-b-2 border-white pb-1' : '' }}">
                            <i class="fas fa-chart-bar mr-2"></i>
                            <span>Statistik</span>
                        </a>
                    </nav>
                </div>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center text-white focus:outline-none hover:opacity-80 transition-opacity duration-200">
                            <span class="h-9 w-9 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-white shadow-inner">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="ml-2 hidden md:block font-medium">Admin</span>
                            <i class="fas fa-chevron-down ml-2 text-xs hidden md:block"></i>
                        </button>
                    </div>
                    <button id="mobileMenuButton" class="md:hidden ml-4 text-white focus:outline-none p-2 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors duration-200">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="md:hidden hidden px-4 pb-4 rounded-lg bg-indigo-900 bg-opacity-95 mt-2 shadow-lg">
                <nav class="flex flex-col space-y-3">
                    <a href="{{ url('/') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->is('/') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-home mr-3 w-6"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('penerima.index') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->routeIs('penerima.*') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-users mr-3 w-6"></i>
                        <span>Data Penerima</span>
                    </a>
                    <a href="{{ route('statistic.index') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->routeIs('statistic.*') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-chart-bar mr-3 w-6"></i>
                        <span>Statistik</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto py-5 px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                    <p class="text-sm text-gray-500 mt-1">@yield('subheader', 'Selamat datang di panel administrasi')</p>
                </div>
                <div>
                    @yield('header_actions')
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto py-8 px-6">
        @yield('content')
    </main>

    <footer class="bg-white py-6 px-6 border-t">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600 mb-4 md:mb-0">
                    &copy; {{ date('Y') }} Aplikasi Penerima Bantuan. Dibuat dengan <span class="text-red-500">&#10084;</span>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
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