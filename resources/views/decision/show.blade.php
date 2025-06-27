@extends('layouts.app')

@section('title', 'Detail Keputusan - Admin Panel')
@section('header', 'Detail Keputusan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8 w-full">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h3 class="text-xl font-medium text-gray-700">{{ $decisionResult->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">Detail keputusan dan daftar penerima bantuan</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('decision.index') }}" class="flex items-center px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Kembali</span>
            </a>
            <form action="{{ route('decision.destroy', $decisionResult->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus keputusan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center px-5 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition">
                    <i class="fas fa-trash-alt mr-2"></i>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 flex items-center">
            <i class="fas fa-check-circle mr-2 text-xl text-green-600"></i>
            <span class="text-base">{{ session('success') }}</span>
        </div>
    @endif
    
    <!-- Detail Keputusan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Informasi Dasar -->
        <div class="col-span-2 bg-gray-50 p-6 rounded-lg border">
            <h4 class="text-lg font-medium text-gray-700 mb-4 border-b pb-2">Informasi Keputusan</h4>
            
            <div class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <h5 class="text-sm font-medium text-gray-500">Dibuat Pada</h5>
                        <p class="text-base">{{ $decisionResult->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <h5 class="text-sm font-medium text-gray-500">Cluster</h5>
                        <p class="flex items-center">
                            @php
                                $colors = ['red', 'blue', 'green'];
                                $color = $colors[$decisionResult->cluster] ?? 'gray';
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                Cluster {{ $decisionResult->cluster + 1 }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h5 class="text-sm font-medium text-gray-500">Jumlah Penerima</h5>
                        <p class="text-base">{{ $decisionResult->count }} orang</p>
                    </div>
                </div>
                
                @if($decisionResult->description)
                <div>
                    <h5 class="text-sm font-medium text-gray-500">Deskripsi</h5>
                    <p class="text-base">{{ $decisionResult->description }}</p>
                </div>
                @endif
                
                @if($decisionResult->notes)
                <div>
                    <h5 class="text-sm font-medium text-gray-500">Catatan</h5>
                    <p class="text-base">{{ $decisionResult->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Statistik Ringkas -->
        <div class="col-span-1 bg-gray-50 p-6 rounded-lg border">
            <h4 class="text-lg font-medium text-gray-700 mb-4 border-b pb-2">Ringkasan Statistik</h4>
            
            @php
                $beneficiaries = $decisionResult->beneficiaries;
                $totalBeneficiaries = $beneficiaries->count();
                
                if ($totalBeneficiaries > 0) {
                    $avgAge = $beneficiaries->avg('usia');
                    $avgIncome = $beneficiaries->avg('pendapatan_perbulan');
                    $avgChildren = $beneficiaries->avg('jumlah_anak');
                } else {
                    $avgAge = 0;
                    $avgIncome = 0; 
                    $avgChildren = 0;
                }
            @endphp
            
            <div class="space-y-4">
                <div>
                    <h5 class="text-xs text-gray-500 mb-1">Rata-rata usia</h5>
                    <p class="text-xl font-semibold">{{ number_format($avgAge, 1) }} tahun</p>
                </div>
                
                <div>
                    <h5 class="text-xs text-gray-500 mb-1">Rata-rata pendapatan</h5>
                    <p class="text-xl font-semibold">Rp {{ number_format($avgIncome, 0, ',', '.') }}</p>
                </div>
                
                <div>
                    <h5 class="text-xs text-gray-500 mb-1">Rata-rata jumlah anak</h5>
                    <p class="text-xl font-semibold">{{ number_format($avgChildren, 1) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Penerima -->
    <div>
        <h4 class="text-lg font-medium text-gray-700 mb-4">Daftar Penerima</h4>
        
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Anak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelayakan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($decisionResult->beneficiaries as $i => $beneficiary)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i+1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-indigo-600">{{ $beneficiary->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $beneficiary->nik }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $beneficiary->alamat }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $beneficiary->usia }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $beneficiary->jumlah_anak }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $beneficiary->kelayakan_rumah }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Rp {{ number_format($beneficiary->pendapatan_perbulan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data penerima</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($decisionResult->beneficiaries->count() > 0)
        <div class="mt-4 text-sm text-gray-500">
            Total {{ $decisionResult->beneficiaries->count() }} penerima ditampilkan
        </div>
        @endif
    </div>
</div>
@endsection 