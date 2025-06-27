@extends('layouts.app')

@section('title', 'Panel Keputusan - Admin Panel')
@section('header', 'Panel Keputusan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8 w-full">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h3 class="text-xl font-medium text-gray-700">Panel Keputusan Penerima Bantuan</h3>
            <p class="text-sm text-gray-500 mt-1">Sistem pendukung keputusan berdasarkan hasil clustering K-Means</p>
        </div>
    </div>
    
    <div class="flex items-center justify-center p-10">
        <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-700">Halaman dalam pengembangan</h3>
            <p class="mt-2 text-gray-500">Panel keputusan sedang dalam proses pengembangan.</p>
        </div>
    </div>
</div>
@endsection 