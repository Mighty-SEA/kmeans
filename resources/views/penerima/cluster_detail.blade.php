@extends('layouts.app')

@section('title', 'Detail Cluster ' . ($clusterIndex + 1) . ' - Admin Panel')
@section('header', 'Detail Cluster ' . ($clusterIndex + 1))

@section('content')
<div class="bg-white rounded-lg shadow-md p-8 w-full">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h3 class="text-xl font-medium text-gray-700">Daftar Anggota Cluster {{ $clusterIndex + 1 }}</h3>
            <p class="text-sm text-gray-500 mt-1">Menampilkan semua data penerima bantuan dalam cluster ini</p>
        </div>
        <a href="{{ route('statistic.index') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition flex items-center">
            <i class="fas fa-chart-bar mr-2"></i>
            <span>Kembali ke Statistik</span>
        </a>
    </div>
    
    @php
        $clusterColors = ['red', 'blue', 'green'];
        $bgClass = 'bg-' . $clusterColors[$clusterIndex] . '-100';
        $borderClass = 'border-' . $clusterColors[$clusterIndex] . '-300';
        $textClass = 'text-' . $clusterColors[$clusterIndex] . '-800';
    @endphp
    
    <div class="mb-8 p-6 rounded-lg {{ $bgClass }} {{ $borderClass }} border">
        <div class="flex items-center">
            <div class="mr-5 p-4 rounded-full bg-white {{ $textClass }}">
                <i class="fas fa-users-cog text-2xl"></i>
            </div>
            <div>
                <h4 class="font-medium {{ $textClass }} text-lg">Informasi Cluster</h4>
                <p class="text-sm {{ $textClass }}">Total {{ $total }} data penerima dalam cluster ini</p>
            </div>
        </div>
    </div>
    
    @if(isset($clusterStats) && !empty($clusterStats))
    <div class="mb-8">
        <h4 class="font-medium text-gray-700 text-lg mb-4">Statistik Cluster (Menggunakan Rubix ML)</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Usia Stats -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h5 class="font-medium text-gray-700 mb-2">Usia</h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Minimum:</span>
                        <span class="font-medium">{{ number_format($clusterStats['usia']['min'], 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Maksimum:</span>
                        <span class="font-medium">{{ number_format($clusterStats['usia']['max'], 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Rata-rata:</span>
                        <span class="font-medium">{{ number_format($clusterStats['usia']['mean'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Median:</span>
                        <span class="font-medium">{{ number_format($clusterStats['usia']['median'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Std. Deviasi:</span>
                        <span class="font-medium">{{ number_format($clusterStats['usia']['std'], 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Jumlah Anak Stats -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h5 class="font-medium text-gray-700 mb-2">Jumlah Anak</h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Minimum:</span>
                        <span class="font-medium">{{ number_format($clusterStats['jumlah_anak']['min'], 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Maksimum:</span>
                        <span class="font-medium">{{ number_format($clusterStats['jumlah_anak']['max'], 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Rata-rata:</span>
                        <span class="font-medium">{{ number_format($clusterStats['jumlah_anak']['mean'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Median:</span>
                        <span class="font-medium">{{ number_format($clusterStats['jumlah_anak']['median'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Std. Deviasi:</span>
                        <span class="font-medium">{{ number_format($clusterStats['jumlah_anak']['std'], 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Kelayakan Rumah Stats -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h5 class="font-medium text-gray-700 mb-2">Kelayakan Rumah</h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Minimum:</span>
                        <span class="font-medium">{{ number_format($clusterStats['kelayakan_rumah']['min'], 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Maksimum:</span>
                        <span class="font-medium">{{ number_format($clusterStats['kelayakan_rumah']['max'], 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Rata-rata:</span>
                        <span class="font-medium">{{ number_format($clusterStats['kelayakan_rumah']['mean'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Median:</span>
                        <span class="font-medium">{{ number_format($clusterStats['kelayakan_rumah']['median'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Std. Deviasi:</span>
                        <span class="font-medium">{{ number_format($clusterStats['kelayakan_rumah']['std'], 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Pendapatan Stats -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h5 class="font-medium text-gray-700 mb-2">Pendapatan</h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Minimum:</span>
                        <span class="font-medium">Rp {{ number_format($clusterStats['pendapatan']['min'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Maksimum:</span>
                        <span class="font-medium">Rp {{ number_format($clusterStats['pendapatan']['max'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Rata-rata:</span>
                        <span class="font-medium">Rp {{ number_format($clusterStats['pendapatan']['mean'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Median:</span>
                        <span class="font-medium">Rp {{ number_format($clusterStats['pendapatan']['median'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Std. Deviasi:</span>
                        <span class="font-medium">Rp {{ number_format($clusterStats['pendapatan']['std'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-xs text-gray-500 italic mb-6">
            * Statistik dihitung menggunakan library Rubix ML (PHP Machine Learning)
        </div>
    </div>
    @endif
    
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Anak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelayakan Rumah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($cluster as $i => $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $i+1 }}</td>
                        <td class="px-6 py-4 font-medium text-indigo-600">{{ $row->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $row->usia }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $row->jumlah_anak }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $row->kelayakan_rumah }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Rp {{ number_format($row->pendapatan_perbulan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 