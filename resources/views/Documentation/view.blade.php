@extends('Documentation.layout')

@section('title', 'View - Dokumentasi')
@section('header', 'View')
@section('breadcrumb')
    <nav class="mb-4 text-sm text-blue-700 font-medium flex items-center space-x-2">
        <a href="{{ route('documentation.index') }}" class="hover:underline">Dokumentasi</a>
        <span>/</span>
        <span class="text-blue-900">View</span>
    </nav>
@endsection
@section('content')
    Halaman dokumentasi View.
@endsection 