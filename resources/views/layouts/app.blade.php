<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel - Aplikasi Penerima')</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">
    <!-- Topbar -->
    <header class="bg-gradient-to-r from-indigo-800 to-indigo-700 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto">
            <div class="flex justify-between items-center py-4 px-6">
                <div class="flex items-center">
                    <div class="text-xl font-bold tracking-wide flex items-center">
                        <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-md mr-3">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span>K-Means Analytics</span>
                    </div>
                    @auth
                    <nav class="hidden md:flex ml-10 space-x-8">
                        <a href="{{ url('/') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->is('/') ? 'text-white font-medium border-b-2 border-white pb-1' : '' }}">
                            <i class="fas fa-home mr-2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('beneficiary.index') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->routeIs('beneficiary.*') ? 'text-white font-medium border-b-2 border-white pb-2' : '' }}">
                            <i class="fas fa-users mr-2"></i>
                            <span>Data Penerima</span>
                        </a>
                        <a href="{{ route('statistic.index') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->routeIs('statistic.*') ? 'text-white font-medium border-b-2 border-white pb-1' : '' }}">
                            <i class="fas fa-chart-bar mr-2"></i>
                            <span>Statistik</span>
                        </a>
                        <a href="{{ route('decision.index') }}" class="flex items-center text-gray-200 hover:text-white transition-all duration-200 {{ request()->routeIs('decision.*') ? 'text-white font-medium border-b-2 border-white pb-1' : '' }}">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            <span>Panel Keputusan</span>
                        </a>
                    </nav>
                    @endauth
                </div>
                <div class="flex items-center">
                    @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center text-white focus:outline-none hover:opacity-80 transition-opacity duration-200">
                            <span class="h-9 w-9 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-white shadow-inner overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </span>
                            <span class="ml-2 hidden md:block font-medium">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-2 text-xs hidden md:block"></i>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-cog mr-2"></i> Pengaturan Profil
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <button id="mobileMenuButton" class="md:hidden ml-4 text-white focus:outline-none p-2 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors duration-200">
                        <i class="fas fa-bars"></i>
                    </button>
                    @else
                    <div>
                        <a href="{{ route('login') }}" class="text-white hover:text-gray-200 mr-4">Login</a>
                        <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">Register</a>
                    </div>
                    @endauth
                </div>
            </div>
            
            <!-- Mobile Menu -->
            @auth
            <div id="mobileMenu" class="md:hidden hidden px-4 pb-4 rounded-lg bg-indigo-900 bg-opacity-95 mt-2 shadow-lg">
                <nav class="flex flex-col space-y-3">
                    <a href="{{ url('/') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->is('/') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-home mr-3 w-6"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('beneficiary.index') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->routeIs('beneficiary.*') ? 'text-white font-medium bg-indigo-700' : '' }}">
                        <i class="fas fa-users mr-3 w-6"></i>
                        <span>Data Penerima</span>
                    </a>
                    <a href="{{ route('statistic.index') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->routeIs('statistic.*') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-chart-bar mr-3 w-6"></i>
                        <span>Statistik</span>
                    </a>
                    <a href="{{ route('decision.index') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->routeIs('decision.*') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-clipboard-list mr-3 w-6"></i>
                        <span>Panel Keputusan</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'text-white font-medium bg-indigo-700 bg-opacity-50' : '' }}">
                        <i class="fas fa-user-cog mr-3 w-6"></i>
                        <span>Pengaturan Profil</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-indigo-800 mt-2 pt-2">
                        @csrf
                        <button type="submit" class="flex items-center py-3 px-2 text-gray-200 hover:text-white hover:bg-indigo-700 rounded-lg transition-all duration-200 w-full text-left">
                            <i class="fas fa-sign-out-alt mr-3 w-6"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </nav>
            </div>
            @endauth
        </div>
    </header>

    <!-- Page Header -->
    <!-- <div class="bg-white shadow-sm border-b border-gray-200">
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
    </div> -->

    <!-- Main Content -->
    <main class="flex-1 container mx-auto py-8 px-6">
        @yield('content')
    </main>

    <footer class="bg-white py-6 px-6 border-t sticky bottom-0 z-40">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600 mb-4 md:mb-0">
                &copy; {{ date('Y') }}  APRILA RIZKIANTI - Aplikasi ini dibuat untuk keperluan Skripsi di STT PRATAMA ADI
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html> 