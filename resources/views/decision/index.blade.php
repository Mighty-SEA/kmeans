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
        <a href="{{ route('decision.create') }}" class="flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-md">
            <i class="fas fa-plus mr-2"></i>
            <span>Buat Keputusan Baru</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 flex items-center">
            <i class="fas fa-check-circle mr-2 text-xl text-green-600"></i>
            <span class="text-base">{{ session('success') }}</span>
        </div>
    @endif

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
    
    <!-- Daftar Keputusan -->
    <div>
        <h4 class="text-lg font-medium text-gray-700 mb-4">Daftar Keputusan</h4>
        
        @if($decisionResults->isEmpty())
            <div class="border rounded-lg p-8 bg-gray-50 text-center">
                <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-600">Belum ada keputusan yang dibuat</p>
                <p class="text-gray-500 text-sm mt-2">Klik tombol 'Buat Keputusan Baru' untuk mulai membuat keputusan</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($decisionResults as $result)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-indigo-600">{{ $result->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($result->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = ['red', 'blue', 'green'];
                                    $color = $colors[$result->cluster] ?? 'gray';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    Cluster {{ $result->cluster + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $result->count }} orang</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $result->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('decision.show', $result->id) }}" class="px-3 py-1.5 rounded bg-indigo-500 text-white text-xs font-medium hover:bg-indigo-600 transition flex items-center">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <form action="{{ route('decision.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus keputusan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded bg-red-500 text-white text-xs font-medium hover:bg-red-600 transition flex items-center">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection 