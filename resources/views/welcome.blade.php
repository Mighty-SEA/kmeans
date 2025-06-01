@extends('layouts.app')

@section('title', 'Dashboard - Admin Panel')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Statistik Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Penerima</p>
                    <h3 class="text-2xl font-bold text-gray-700">{{ $totalPenerima }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Cluster 1</p>
                    <h3 class="text-2xl font-bold text-gray-700">{{ $clusterCounts[0] ?? 0 }} <span class="text-sm font-normal text-gray-500">data</span></h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Cluster 2</p>
                    <h3 class="text-2xl font-bold text-gray-700">{{ $clusterCounts[1] ?? 0 }} <span class="text-sm font-normal text-gray-500">data</span></h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Cluster 3</p>
                    <h3 class="text-2xl font-bold text-gray-700">{{ $clusterCounts[2] ?? 0 }} <span class="text-sm font-normal text-gray-500">data</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-xl font-medium text-gray-700 mb-6">Proporsi Cluster</h3>
            <div class="h-[350px]">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-xl font-medium text-gray-700 mb-6">Rata-rata Fitur per Cluster</h3>
            <div class="h-[350px]">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Grafik Pendapatan -->
    <div class="bg-white rounded-lg shadow-md p-8">
        <h3 class="text-xl font-medium text-gray-700 mb-6">Rata-rata Pendapatan per Cluster</h3>
        <div class="h-[350px]">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>
    
    <!-- Tabel Data Terbaru -->
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-medium text-gray-700">Data Penerima Terbaru</h3>
            <a href="{{ route('penerima.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <span>Lihat Semua</span>
                <i class="fas fa-chevron-right ml-2 text-xs"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Anak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelayakan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($latestData as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-indigo-600">{{ $item->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->usia }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->jumlah_anak }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->kelayakan_rumah }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Rp {{ number_format($item->pendapatan_perbulan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($item->cluster !== null)
                                @php
                                    $clusterColors = ['red', 'blue', 'green'];
                                    $bgClass = 'bg-' . $clusterColors[$item->cluster] . '-100';
                                    $textClass = 'text-' . $clusterColors[$item->cluster] . '-800';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $bgClass }} {{ $textClass }}">
                                    Cluster {{ $item->cluster + 1 }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Belum dicluster
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data penerima</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = ['rgba(248, 113, 113, 0.7)', 'rgba(96, 165, 250, 0.7)', 'rgba(52, 211, 153, 0.7)'];
    const borderColors = ['rgb(220, 38, 38)', 'rgb(37, 99, 235)', 'rgb(5, 150, 105)'];
    
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Cluster 1', 'Cluster 2', 'Cluster 3'],
            datasets: [{
                data: [
                    {{ $clusterCounts[0] ?? 0 }},
                    {{ $clusterCounts[1] ?? 0 }},
                    {{ $clusterCounts[2] ?? 0 }}
                ],
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${value} data (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart (Fitur tanpa pendapatan)
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Usia', 'Jumlah Anak', 'Kelayakan Rumah'],
            datasets: [
                {
                    label: 'Cluster 1',
                    data: [
                        {{ isset($clusterMeans[0]) ? $clusterMeans[0]['usia'] : 0 }},
                        {{ isset($clusterMeans[0]) ? $clusterMeans[0]['jumlah_anak'] : 0 }},
                        {{ isset($clusterMeans[0]) ? $clusterMeans[0]['kelayakan_rumah'] : 0 }}
                    ],
                    backgroundColor: colors[0],
                    borderColor: borderColors[0],
                    borderWidth: 2
                },
                {
                    label: 'Cluster 2',
                    data: [
                        {{ isset($clusterMeans[1]) ? $clusterMeans[1]['usia'] : 0 }},
                        {{ isset($clusterMeans[1]) ? $clusterMeans[1]['jumlah_anak'] : 0 }},
                        {{ isset($clusterMeans[1]) ? $clusterMeans[1]['kelayakan_rumah'] : 0 }}
                    ],
                    backgroundColor: colors[1],
                    borderColor: borderColors[1],
                    borderWidth: 2
                },
                {
                    label: 'Cluster 3',
                    data: [
                        {{ isset($clusterMeans[2]) ? $clusterMeans[2]['usia'] : 0 }},
                        {{ isset($clusterMeans[2]) ? $clusterMeans[2]['jumlah_anak'] : 0 }},
                        {{ isset($clusterMeans[2]) ? $clusterMeans[2]['kelayakan_rumah'] : 0 }}
                    ],
                    backgroundColor: colors[2],
                    borderColor: borderColors[2],
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Perbandingan Fitur (Usia, Jumlah Anak, Kelayakan)',
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                x: { 
                    grid: { display: false }
                },
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
    
    // Income Chart (Khusus pendapatan)
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    new Chart(incomeCtx, {
        type: 'bar',
        data: {
            labels: ['Cluster 1', 'Cluster 2', 'Cluster 3'],
            datasets: [{
                label: 'Rata-rata Pendapatan',
                data: [
                    {{ isset($clusterMeans[0]) ? $clusterMeans[0]['pendapatan'] : 0 }},
                    {{ isset($clusterMeans[1]) ? $clusterMeans[1]['pendapatan'] : 0 }},
                    {{ isset($clusterMeans[2]) ? $clusterMeans[2]['pendapatan'] : 0 }}
                ],
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            scales: {
                x: { 
                    grid: { display: false }
                },
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection 