@extends('Documentation.layout')

@section('title', 'Middleware - Dokumentasi')
@section('header', 'Middleware')
@section('breadcrumb')
    <nav class="mb-4 text-sm text-blue-700 font-medium flex items-center space-x-2">
        <a href="{{ route('documentation.index') }}" class="hover:underline">Dokumentasi</a>
        <span>/</span>
        <span class="text-blue-900">Middleware</span>
    </nav>
@endsection
@section('content')
    Halaman dokumentasi Middleware.
@endsection 