<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Autentikasi - K-Means Analytics')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">
    <main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="py-4 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} K-Means Analytics. All rights reserved.
    </footer>
</body>
</html> 