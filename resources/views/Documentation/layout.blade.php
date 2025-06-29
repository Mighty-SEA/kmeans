<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dokumentasi')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans min-h-screen">
    <div class="fixed top-0 left-0 h-screen w-[220px] bg-blue-100 flex flex-col z-20">
        <div class="py-6 px-4">
            <h2 class="text-lg font-bold tracking-wide text-blue-900">Dokumentasi</h2>
        </div>
        <ul class="flex-1 space-y-1 px-0">
            <li><a href="{{ route('documentation.model') }}" class="block px-6 py-2 rounded transition-colors duration-200 hover:bg-blue-200 {{ request()->routeIs('documentation.model') ? 'bg-blue-200' : '' }} text-blue-900 font-bold">Model</a></li>
            <li><a href="{{ route('documentation.view') }}" class="block px-6 py-2 rounded transition-colors duration-200 hover:bg-blue-200 {{ request()->routeIs('documentation.view') ? 'bg-blue-200' : '' }} text-blue-900 font-bold">View</a></li>
            <li><a href="{{ route('documentation.controller') }}" class="block px-6 py-2 rounded transition-colors duration-200 hover:bg-blue-200 {{ request()->routeIs('documentation.controller') ? 'bg-blue-200' : '' }} text-blue-900 font-bold">Controller</a></li>
            <li><a href="{{ route('documentation.route') }}" class="block px-6 py-2 rounded transition-colors duration-200 hover:bg-blue-200 {{ request()->routeIs('documentation.route') ? 'bg-blue-200' : '' }} text-blue-900 font-bold">Route</a></li>
            <li><a href="{{ route('documentation.middleware') }}" class="block px-6 py-2 rounded transition-colors duration-200 hover:bg-blue-200 {{ request()->routeIs('documentation.middleware') ? 'bg-blue-200' : '' }} text-blue-900 font-bold">Middleware</a></li>
            <li><a href="{{ route('documentation.migration') }}" class="block px-6 py-2 rounded transition-colors duration-200 hover:bg-blue-200 {{ request()->routeIs('documentation.migration') ? 'bg-blue-200' : '' }} text-blue-900 font-bold">Migration</a></li>
        </ul>
    </div>
    <div class="bg-white min-h-screen p-8 ml-[220px]">
        @yield('breadcrumb')
        <h1 class="text-2xl font-bold mb-4 text-blue-800">@yield('header', 'Dokumentasi')</h1>
        <div class="text-gray-700 text-base">
            @yield('content')
        </div>
    </div>
</body>
</html> 