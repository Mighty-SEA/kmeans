@extends('layouts.auth')

@section('title', 'Login - K-Means Analytics')

@section('content')
<div class="w-full max-w-md">
    <div class="text-center mb-10">
        <div class="inline-block p-4 bg-indigo-600 rounded-full mb-5 shadow-lg">
            <i class="fas fa-chart-pie text-white text-3xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">K-Means Analytics</h1>
        <p class="text-gray-600 mt-2">Masuk ke akun Anda untuk melanjutkan</p>
    </div>
    
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login</h2>
            
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" type="email" class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" type="password" class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror" name="password" required autocomplete="current-password">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat Saya</label>
                    </div>
                </div>

                <div class="mb-6">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600">
                Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection 