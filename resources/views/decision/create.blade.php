@extends('layouts.app')

@section('title', 'Buat Keputusan Baru - Admin Panel')
@section('header', 'Buat Keputusan Baru')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8 w-full">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h3 class="text-xl font-medium text-gray-700">Buat Keputusan Berdasarkan Cluster</h3>
            <p class="text-sm text-gray-500 mt-1">Pilih kluster dan jumlah penerima bantuan</p>
        </div>
        <a href="{{ route('decision.index') }}" class="flex items-center px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Kembali</span>
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200 flex items-center">
            <i class="fas fa-exclamation-circle mr-2 text-xl text-red-600"></i>
            <span class="text-base">{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Ringkasan Cluster -->
    <div class="mb-8">
        <h4 class="text-lg font-medium text-gray-700 mb-4">Ringkasan Cluster</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($clusterCounts as $cluster => $count)
            <div class="border rounded-lg shadow-sm p-6 bg-white flex flex-col">
                <div class="flex items-center mb-4">
                    @php
                        $colors = ['red', 'blue', 'green'];
                        $color = $colors[$cluster] ?? 'gray';
                    @endphp
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-800">Cluster {{ $cluster + 1 }}</h4>
                        <p class="text-sm text-gray-500">{{ $count }} penerima</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Form Keputusan -->
    <div class="bg-gray-50 p-6 rounded-lg border mb-8">
        <h4 class="text-lg font-medium text-gray-700 mb-6">Form Pembuatan Keputusan</h4>
        <form action="{{ route('decision.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="title">Judul Keputusan<span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="cluster">Pilih Prioritas<span class="text-red-500">*</span></label>
                    <select id="cluster" name="cluster" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('cluster') border-red-500 @enderror" required>
                        <option value="">Pilih Prioritas</option>
                        <option value="all" {{ old('cluster') == 'all' ? 'selected' : '' }}>Sesuai Prioritas</option>
                        @foreach($clusterCounts as $cluster => $count)
                            @php
                                $prioritas = $rankMap[$cluster] ?? '-';
                            @endphp
                            <option value="{{ $cluster }}" {{ old('cluster') == $cluster ? 'selected' : '' }}>
                                Prioritas {{ $prioritas }} ({{ $count }} penerima)
                            </option>
                        @endforeach
                    </select>
                    @error('cluster')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="count">Jumlah Penerima<span class="text-red-500">*</span></label>
                    <input type="number" id="count" name="count" value="{{ old('count') }}" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('count') border-red-500 @enderror" required>
                    <p class="text-xs text-gray-500 mt-1">Jumlah penerima yang akan dipilih dari cluster terpilih</p>
                    @error('count')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="notes">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <a href="{{ route('decision.index') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition mr-4">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <span>Simpan Keputusan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 