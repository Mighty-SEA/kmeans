@extends('layouts.app')

@section('title', 'Dashboard - Admin Panel')
@section('header', 'Dashboard Analitik')
@section('subheader', 'Visualisasi dan analisis data cluster dengan metode K-Means')

@section('content')
<div class="space-y-8">
    <!-- Statistik Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="dashboard-card card-hover border-l-4 border-indigo-500">
            <div class="dashboard-stat">
                <div class="stat-icon bg-indigo-100 text-indigo-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="stat-content">
                    <p>Total Penerima</p>
                    <h3>{{ $totalPenerima }}</h3>
                    <span class="text-xs text-indigo-600 font-medium">Data terdaftar</span>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card card-hover border-l-4 border-red-500">
            <div class="dashboard-stat">
                <div class="stat-icon bg-red-100 text-red-600">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div class="stat-content">
                    <p>Cluster 1</p>
                    <h3>{{ $clusterCounts[0] ?? 0 }}</h3>
                    <span class="text-xs text-red-600 font-medium">{{ $clusterCounts[0] ? number_format(($clusterCounts[0] / $totalPenerima) * 100, 1) : 0 }}% dari total</span>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card card-hover border-l-4 border-blue-500">
            <div class="dashboard-stat">
                <div class="stat-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div class="stat-content">
                    <p>Cluster 2</p>
                    <h3>{{ $clusterCounts[1] ?? 0 }}</h3>
                    <span class="text-xs text-blue-600 font-medium">{{ $clusterCounts[1] ? number_format(($clusterCounts[1] / $totalPenerima) * 100, 1) : 0 }}% dari total</span>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card card-hover border-l-4 border-green-500">
            <div class="dashboard-stat">
                <div class="stat-icon bg-green-100 text-green-600">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div class="stat-content">
                    <p>Cluster 3</p>
                    <h3>{{ $clusterCounts[2] ?? 0 }}</h3>
                    <span class="text-xs text-green-600 font-medium">{{ $clusterCounts[2] ? number_format(($clusterCounts[2] / $totalPenerima) * 100, 1) : 0 }}% dari total</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="dashboard-card p-6 col-span-1 lg:col-span-3">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Ringkasan Analisis K-Means</h3>
                    <p class="text-sm text-gray-500 mt-1">Analisis pengelompokan data berdasarkan karakteristik</p>
                </div>
                <div>
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-info-circle mr-1"></i> Periode: {{ date('F Y') }}
                    </span>
                </div>
            </div>
            
            <div class="prose max-w-none text-gray-600">
                <p>Analisis cluster menggunakan algoritma <strong>K-Means</strong> telah menghasilkan 3 kelompok penerima bantuan berdasarkan fitur-fitur berikut:</p>
                <ul class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <li class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Usia</span>
                        <div class="text-xs text-gray-500">Umur penerima bantuan</div>
                    </li>
                    <li class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Jumlah Anak</span>
                        <div class="text-xs text-gray-500">Tanggungan dalam keluarga</div>
                    </li>
                    <li class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Kelayakan Rumah</span>
                        <div class="text-xs text-gray-500">Kondisi tempat tinggal</div>
                    </li>
                    <li class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Pendapatan Perbulan</span>
                        <div class="text-xs text-gray-500">Penghasilan rata-rata</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Grafik Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="chart-container card-hover">
            <div class="chart-title">
                <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                <span>Proporsi Cluster</span>
            </div>
            <div class="h-[350px]">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
        
        <div class="chart-container card-hover">
            <div class="chart-title">
                <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                <span>Rata-rata Fitur per Cluster</span>
            </div>
            <div class="h-[350px]">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Grafik Pendapatan -->
    <div class="chart-container card-hover">
        <div class="chart-title">
            <i class="fas fa-money-bill-wave text-indigo-500 mr-2"></i>
            <span>Rata-rata Pendapatan per Cluster</span>
        </div>
        <div class="h-[350px]">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>
    
    <!-- Tabel Data Terbaru -->
    <div class="dashboard-card p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Data Penerima Terbaru</h3>
                <p class="text-sm text-gray-500 mt-1">Data yang paling baru ditambahkan ke sistem</p>
            </div>
            <a href="{{ route('beneficiary.index') }}" class="flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                <span>Lihat Semua</span>
                <i class="fas fa-chevron-right ml-2 text-xs"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Usia</th>
                        <th>Jumlah Anak</th>
                        <th>Kelayakan</th>
                        <th>Pendapatan</th>
                        <th>Cluster</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($latestData as $item)
                    <tr>
                        <td class="font-medium text-indigo-600">{{ $item->nama }}</td>
                        <td class="text-gray-700">{{ $item->usia }}</td>
                        <td class="text-gray-700">{{ $item->jumlah_anak }}</td>
                        <td class="text-gray-700">{{ $item->kelayakan_rumah }}</td>
                        <td class="text-gray-700">Rp {{ number_format($item->pendapatan_perbulan, 0, ',', '.') }}</td>
                        <td>
                            @if($item->cluster !== null)
                                @php
                                    $clusterColors = ['red', 'blue', 'green'];
                                    $bgClass = 'bg-' . $clusterColors[$item->cluster] . '-100';
                                    $textClass = 'text-' . $clusterColors[$item->cluster] . '-800';
                                @endphp
                                <span class="badge {{ $bgClass }} {{ $textClass }}">
                                    Cluster {{ $item->cluster + 1 }}
                                </span>
                            @else
                                <span class="badge bg-gray-100 text-gray-800">
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
    const colors = ['rgba(248, 113, 113, 0.8)', 'rgba(96, 165, 250, 0.8)', 'rgba(52, 211, 153, 0.8)'];
    const borderColors = ['rgb(220, 38, 38)', 'rgb(37, 99, 235)', 'rgb(5, 150, 105)'];
    
    // Pie Chart dengan animasi dan tampilan yang lebih baik
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut', // Menggunakan doughnut chart untuk tampilan yang lebih modern
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
                borderWidth: 2,
                hoverOffset: 15 // Efek hover yang lebih menonjol
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%', // Lubang tengah untuk doughnut chart
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        usePointStyle: true, // Menggunakan point style untuk legend
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#334155',
                    bodyColor: '#334155',
                    bodyFont: {
                        family: "'Plus Jakarta Sans', sans-serif"
                    },
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${value} data (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000 // Animasi yang lebih lama untuk efek lebih baik
            }
        }
    });

    // Bar Chart dengan tampilan yang lebih informatif
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
                    borderWidth: 2,
                    borderRadius: 6
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
                    borderWidth: 2,
                    borderRadius: 6
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
                    borderWidth: 2,
                    borderRadius: 6
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
                            size: 14,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#334155',
                    bodyColor: '#334155',
                    bodyFont: {
                        family: "'Plus Jakarta Sans', sans-serif"
                    },
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                },
                y: {
                    grid: {
                        borderDash: [2, 4],
                        color: '#e2e8f0'
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                }
            },
            animation: {
                delay: function(context) {
                    return context.dataIndex * 100 + context.datasetIndex * 300;
                },
                duration: 1000
            }
        }
    });

    // Income Chart dengan tampilan lebih profesional
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    new Chart(incomeCtx, {
        type: 'bar',
        data: {
            labels: ['Cluster 1', 'Cluster 2', 'Cluster 3'],
            datasets: [{
                label: 'Rata-rata Pendapatan (Rp)',
                data: [
                    {{ isset($clusterMeans[0]) ? $clusterMeans[0]['pendapatan'] : 0 }},
                    {{ isset($clusterMeans[1]) ? $clusterMeans[1]['pendapatan'] : 0 }},
                    {{ isset($clusterMeans[2]) ? $clusterMeans[2]['pendapatan'] : 0 }}
                ],
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#334155',
                    bodyColor: '#334155',
                    bodyFont: {
                        family: "'Plus Jakarta Sans', sans-serif"
                    },
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                },
                y: {
                    grid: {
                        borderDash: [2, 4],
                        color: '#e2e8f0'
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            animation: {
                delay: function(context) {
                    return context.dataIndex * 300;
                },
                duration: 1000
            }
        }
    });
});
</script>
@endpush
@endsection 