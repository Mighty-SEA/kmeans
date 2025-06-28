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
        $clusterColors = ['red', 'blue', 'green', 'purple', 'pink', 'yellow', 'indigo', 'teal', 'orange', 'gray'];
        $colorIndex = min($clusterIndex, count($clusterColors) - 1);
        $bgClass = 'bg-' . $clusterColors[$colorIndex] . '-100';
        $borderClass = 'border-' . $clusterColors[$colorIndex] . '-300';
        $textClass = 'text-' . $clusterColors[$colorIndex] . '-800';
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
        
        @if(isset($silhouetteStats) && !empty($silhouetteStats))
        <div class="mt-4 pt-4 border-t border-{{ $clusterColors[$colorIndex] }}-200">
            <h5 class="font-medium {{ $textClass }} mb-2">Silhouette Score</h5>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-{{ $clusterColors[$colorIndex] }}-700">Rata-rata:</span>
                        <span class="font-medium text-{{ $clusterColors[$colorIndex] }}-800">{{ number_format($silhouetteStats['mean'], 2) }}</span>
                    </div>
                    <div class="w-full bg-white rounded-full h-2.5 mb-3">
                        <div class="h-2.5 rounded-full bg-{{ $clusterColors[$colorIndex] }}-500" 
                             style="width: {{ min(max(($silhouetteStats['mean'] + 1) / 2 * 100, 5), 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-{{ $clusterColors[$colorIndex] }}-700">Min: {{ number_format($silhouetteStats['min'], 2) }}</span>
                        <span class="text-{{ $clusterColors[$colorIndex] }}-700">Max: {{ number_format($silhouetteStats['max'], 2) }}</span>
                    </div>
                    <div class="text-xs text-{{ $clusterColors[$colorIndex] }}-700 mt-1">
                        <span>Median: {{ number_format($silhouetteStats['median'], 2) }}</span> | 
                        <span>Std Dev: {{ number_format($silhouetteStats['std'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
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
    
    <div class="mb-4 flex justify-between items-center">
        <h4 class="font-medium text-gray-700 text-lg">Data Anggota Cluster</h4>
        
        <div class="flex items-center">
            <span class="text-sm text-gray-600 mr-2">Data Asli</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggleNormalized" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
            <span class="text-sm text-gray-600 ml-2">Data Normalisasi</span>
        </div>
    </div>
    
    <!-- Search Form -->
    <div class="mb-6">
        <form action="{{ route('statistic.cluster', $clusterIndex + 1) }}" method="GET" class="flex items-center">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama, NIK, atau alamat..." class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Cari
            </button>
            @if(isset($search) && $search)
            <a href="{{ route('statistic.cluster', $clusterIndex + 1) }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Reset
            </a>
            @endif
        </form>
    </div>
    
    <div class="overflow-x-auto rounded-lg shadow">
        @if($cluster->isEmpty() && isset($search) && $search)
        <div class="bg-gray-50 p-8 text-center">
            <div class="text-gray-500 mb-2">
                <i class="fas fa-search text-3xl"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-700 mb-1">Tidak ada hasil</h4>
            <p class="text-sm text-gray-500">Tidak ditemukan data dengan kata kunci "{{ $search }}"</p>
            <div class="mt-4">
                <a href="{{ route('statistic.cluster', $clusterIndex + 1) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke semua data
                </a>
            </div>
        </div>
        @else
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Anak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelayakan Rumah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
                    @if(isset($silhouetteStats))
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Silhouette</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($cluster as $i => $row)
                    @php
                        $silhouette = null;
                        if(isset($silhouetteStats)) {
                            $clusterResult = \App\Models\ClusteringResult::where('beneficiary_id', $row->id)->first();
                            if($clusterResult) {
                                $silhouette = $clusterResult->silhouette;
                            }
                        }
                        
                        $silhouetteClass = 'text-gray-500';
                        if($silhouette !== null) {
                            if($silhouette > 0.7) {
                                $silhouetteClass = 'text-green-600';
                            } elseif($silhouette > 0.5) {
                                $silhouetteClass = 'text-blue-600';
                            } elseif($silhouette > 0.25) {
                                $silhouetteClass = 'text-yellow-600';
                            } elseif($silhouette > 0) {
                                $silhouetteClass = 'text-orange-600';
                            } else {
                                $silhouetteClass = 'text-red-600';
                            }
                        }
                        
                        // Data normalisasi
                        $normalizedItem = isset($normalizedData[$row->id]) ? $normalizedData[$row->id] : null;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ ($cluster->currentPage() - 1) * $cluster->perPage() + $i + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-indigo-600">{{ $row->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $row->nik ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $row->alamat ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="data-original">{{ $row->usia }}</span>
                            @if($normalizedItem)
                                <span class="data-normalized hidden">{{ number_format($normalizedItem->usia_normalized, 4) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="data-original">{{ $row->jumlah_anak }}</span>
                            @if($normalizedItem)
                                <span class="data-normalized hidden">{{ number_format($normalizedItem->jumlah_anak_normalized, 4) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="data-original">{{ $row->kelayakan_rumah }}</span>
                            @if($normalizedItem)
                                <span class="data-normalized hidden">{{ number_format($normalizedItem->kelayakan_rumah_normalized, 4) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="data-original">Rp {{ number_format($row->pendapatan_perbulan, 0, ',', '.') }}</span>
                            @if($normalizedItem)
                                <span class="data-normalized hidden">{{ number_format($normalizedItem->pendapatan_perbulan_normalized, 4) }}</span>
                            @endif
                        </td>
                        @if(isset($silhouetteStats))
                        <td class="px-6 py-4">
                            @if($silhouette !== null)
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mr-2 max-w-[50px]">
                                        <div class="h-1.5 rounded-full bg-{{ $clusterColors[$colorIndex] }}-500" 
                                             style="width: {{ min(max(($silhouette + 1) / 2 * 100, 5), 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium {{ $silhouetteClass }}">{{ number_format($silhouette, 2) }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    
    <!-- Pagination Links -->
    <div class="mt-6">
        <div class="flex justify-between items-center mb-3">
            <div class="text-sm text-gray-600">
                @if(isset($search) && $search)
                    Menampilkan hasil pencarian "{{ $search }}" - {{ $cluster->total() }} data ditemukan
                @else
                    Menampilkan {{ $cluster->firstItem() ?? 0 }} sampai {{ $cluster->lastItem() ?? 0 }} dari {{ $cluster->total() }} data
                @endif
            </div>
            <div class="text-sm text-gray-600">
                Halaman {{ $cluster->currentPage() }} dari {{ $cluster->lastPage() }}
            </div>
        </div>
        <div class="pagination-container flex justify-center">
            {{ $cluster->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleSwitch = document.getElementById('toggleNormalized');
        const originalData = document.querySelectorAll('.data-original');
        const normalizedData = document.querySelectorAll('.data-normalized');
        
        toggleSwitch.addEventListener('change', function() {
            if (this.checked) {
                // Tampilkan data normalisasi
                originalData.forEach(el => el.classList.add('hidden'));
                normalizedData.forEach(el => el.classList.remove('hidden'));
            } else {
                // Tampilkan data asli
                originalData.forEach(el => el.classList.remove('hidden'));
                normalizedData.forEach(el => el.classList.add('hidden'));
            }
        });
    });
</script>
@endpush 