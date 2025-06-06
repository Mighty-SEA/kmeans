@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header', 'Pengaturan Profil')
@section('subheader', 'Kelola informasi akun dan ubah password Anda')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Sidebar -->
    <div class="md:col-span-1">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex flex-col items-center text-center">
                <div class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl mb-4">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="font-semibold text-xl">{{ $user->name }}</h4>
                <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="md:col-span-2 space-y-6">
        <!-- Status Message -->
        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Profile Information Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Profil</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-700 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Update Password Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h2>
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input id="password" name="password" type="password" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-700 transition">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 