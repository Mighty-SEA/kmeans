@extends('layouts.app')

@section('title', 'Statistik Clustering - Admin Panel')
@section('header', 'Statistik K-Means')

@section('content')
<div class="space-y-8 w-full">
    @if($message)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg shadow-sm flex items-center border border-yellow-200">
            <i class="fas fa-exclamation-circle mr-2 text-yellow-600"></i>
            <span>{{ $message }}</span>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-medium text-gray-700">Hasil Clustering K-Means</h3>
                    <p class="text-sm text-gray-500 mt-1">Visualisasi dan analisis hasil clustering dengan metode K-Means (3 Cluster)</p>
                </div>
                <form action="{{ route('statistic.recalculate') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-md">
                        <i class="fas fa-sync-alt mr-2"></i> Hitung Ulang Clustering
                    </button>
                </form>
            </div>
            
            <!-- Ringkasan Cluster -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                @foreach($clusterCounts as $key => $count)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    @php
                        $clusterColors = ['red', 'blue', 'green'];
                        $bgClass = 'bg-' . $clusterColors[$key] . '-100';
                        $textClass = 'text-' . $clusterColors[$key] . '-600';
                        $borderClass = 'border-' . $clusterColors[$key] . '-500';
                    @endphp
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $bgClass }} {{ $textClass }} mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-800">Cluster {{ $key + 1 }}</h4>
                            <p class="text-sm text-gray-500">{{ $count }} data</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 pt-4">
                        <a href="{{ route('statistic.cluster', $key) }}" class="flex items-center justify-center px-4 py-2 rounded bg-gray-50 text-gray-700 hover:bg-gray-100 transition w-full">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Visualisasi -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Distribusi Cluster</h4>
                    <div class="h-[350px]">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Perbandingan Rata-rata Fitur</h4>
                    <div class="h-[350px]">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Scatter Plot -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
                <h4 class="text-lg font-medium text-gray-800 mb-4">Scatter Plot Clustering</h4>
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <div class="flex flex-wrap gap-6 items-center">
                        <label for="xAxis" class="font-medium text-gray-700">Sumbu X:</label>
                        <select id="xAxis" class="border rounded-lg px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 min-w-[180px]">
                            <option value="usia">Usia</option>
                            <option value="jumlah_anak">Jumlah Anak</option>
                            <option value="kelayakan_rumah">Kelayakan Rumah</option>
                            <option value="pendapatan">Pendapatan</option>
                        </select>
                        
                        <label for="yAxis" class="font-medium text-gray-700 ml-4">Sumbu Y:</label>
                        <select id="yAxis" class="border rounded-lg px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 min-w-[180px]">
                            <option value="pendapatan">Pendapatan</option>
                            <option value="usia">Usia</option>
                            <option value="jumlah_anak">Jumlah Anak</option>
                            <option value="kelayakan_rumah">Kelayakan Rumah</option>
                        </select>
                    </div>
                </div>
                <div class="h-[500px]">
                    <canvas id="scatterChart"></canvas>
                </div>
            </div>
            
            <!-- Statistik Detail -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-medium text-gray-800 mb-4">Statistik Detail per Cluster</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border px-6 py-3 text-center">Cluster</th>
                                <th class="border px-6 py-3">Fitur</th>
                                <th class="border px-6 py-3 text-center">Min</th>
                                <th class="border px-6 py-3 text-center">Max</th>
                                <th class="border px-6 py-3 text-center">Mean</th>
                                <th class="border px-6 py-3 text-center">Median</th>
                                <th class="border px-6 py-3 text-center">Std. Dev</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clusterStats as $c => $stat)
                                @foreach(['usia','jumlah_anak','kelayakan_rumah','pendapatan'] as $f)
                                    <tr class="{{ $f === 'usia' ? 'bg-gray-50' : '' }}">
                                        @if($f === 'usia')
                                            <td class="border px-6 py-3 text-center font-medium text-indigo-600" rowspan="4">Cluster {{ $c+1 }}</td>
                                        @endif
                                        <td class="border px-6 py-3 font-medium">{{ ucfirst(str_replace('_',' ',$f)) }}</td>
                                        <td class="border px-6 py-3 text-right">{{ number_format($stat[$f]['min'],2) }}</td>
                                        <td class="border px-6 py-3 text-right">{{ number_format($stat[$f]['max'],2) }}</td>
                                        <td class="border px-6 py-3 text-right">{{ number_format($stat[$f]['mean'],2) }}</td>
                                        <td class="border px-6 py-3 text-right">{{ number_format($stat[$f]['median'],2) }}</td>
                                        <td class="border px-6 py-3 text-right">{{ number_format($stat[$f]['std'],2) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($clusters as $key => $cluster)
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-medium text-gray-700">
                            @php
                                $clusterColors = ['red', 'blue', 'green'];
                                $bgClass = 'bg-' . $clusterColors[$key] . '-100';
                                $textClass = 'text-' . $clusterColors[$key] . '-600';
                            @endphp
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full mr-2 {{ $bgClass }} {{ $textClass }}">
                                {{ $key + 1 }}
                            </span>
                            Cluster {{ $key + 1 }}
                        </h3>
                        <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">{{ count($cluster) }} data</span>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-sm font-medium text-gray-500">Rata-rata Usia</span>
                            <span class="text-sm font-medium text-gray-800">{{ number_format($clusterMeans[$key]['usia'], 1) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-sm font-medium text-gray-500">Rata-rata Jumlah Anak</span>
                            <span class="text-sm font-medium text-gray-800">{{ number_format($clusterMeans[$key]['jumlah_anak'], 1) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-sm font-medium text-gray-500">Rata-rata Kelayakan</span>
                            <span class="text-sm font-medium text-gray-800">{{ number_format($clusterMeans[$key]['kelayakan_rumah'], 1) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Rata-rata Pendapatan</span>
                            <span class="text-sm font-medium text-gray-800">Rp {{ number_format($clusterMeans[$key]['pendapatan'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <a href="{{ route('statistic.cluster', $key) }}" class="block w-full text-center px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition">
                            Detail Anggota Cluster
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@if(!$message)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Cluster 1', 'Cluster 2', 'Cluster 3'],
            datasets: [{
                data: [{{ $clusterCounts[0] }}, {{ $clusterCounts[1] }}, {{ $clusterCounts[2] }}],
                backgroundColor: [
                    'rgba(248, 113, 113, 0.7)',
                    'rgba(96, 165, 250, 0.7)',
                    'rgba(52, 211, 153, 0.7)'
                ],
                borderColor: [
                    'rgb(220, 38, 38)',
                    'rgb(37, 99, 235)',
                    'rgb(5, 150, 105)'
                ],
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
    
    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Usia', 'Jumlah Anak', 'Kelayakan Rumah', 'Pendapatan'],
            datasets: [
                {
                    label: 'Cluster 1',
                    data: [
                        {{ $clusterMeans[0]['usia'] }},
                        {{ $clusterMeans[0]['jumlah_anak'] }},
                        {{ $clusterMeans[0]['kelayakan_rumah'] }},
                        {{ $clusterMeans[0]['pendapatan'] }}
                    ],
                    backgroundColor: 'rgba(248, 113, 113, 0.7)',
                    borderColor: 'rgb(220, 38, 38)',
                    borderWidth: 2
                },
                {
                    label: 'Cluster 2',
                    data: [
                        {{ $clusterMeans[1]['usia'] }},
                        {{ $clusterMeans[1]['jumlah_anak'] }},
                        {{ $clusterMeans[1]['kelayakan_rumah'] }},
                        {{ $clusterMeans[1]['pendapatan'] }}
                    ],
                    backgroundColor: 'rgba(96, 165, 250, 0.7)',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 2
                },
                {
                    label: 'Cluster 3',
                    data: [
                        {{ $clusterMeans[2]['usia'] }},
                        {{ $clusterMeans[2]['jumlah_anak'] }},
                        {{ $clusterMeans[2]['kelayakan_rumah'] }},
                        {{ $clusterMeans[2]['pendapatan'] }}
                    ],
                    backgroundColor: 'rgba(52, 211, 153, 0.7)',
                    borderColor: 'rgb(5, 150, 105)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });
    
    // Scatter Chart
    const scatterData = @json($scatterData);
    const colors = ['rgba(248, 113, 113, 0.7)', 'rgba(96, 165, 250, 0.7)', 'rgba(52, 211, 153, 0.7)'];
    const borderColors = ['rgb(220, 38, 38)', 'rgb(37, 99, 235)', 'rgb(5, 150, 105)'];
    const fieldLabels = {
        usia: 'Usia',
        jumlah_anak: 'Jumlah Anak',
        kelayakan_rumah: 'Kelayakan Rumah',
        pendapatan: 'Pendapatan',
    };
    
    function getDatasets(xField, yField) {
        return [0,1,2].map(cluster => ({
            label: 'Cluster ' + (cluster+1),
            data: scatterData.filter(d => d.cluster === cluster).map(d => ({
                x: Number(d[xField]),
                y: Number(d[yField]),
                nama: d.nama
            })),
            backgroundColor: colors[cluster],
            borderColor: borderColors[cluster],
            borderWidth: 1,
            pointRadius: 6,
            pointHoverRadius: 8,
        }));
    }
    
    let xField = document.getElementById('xAxis').value;
    let yField = document.getElementById('yAxis').value;
    const scatterCtx = document.getElementById('scatterChart').getContext('2d');
    let scatterChart = new Chart(scatterCtx, {
        type: 'scatter',
        data: { datasets: getDatasets(xField, yField) },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const d = context.raw;
                            return d.nama + ' (' + fieldLabels[xField] + ': ' + d.x + ', ' + fieldLabels[yField] + ': ' + d.y + ')';
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                }
            },
            scales: {
                x: { 
                    title: { 
                        display: true, 
                        text: fieldLabels[xField], 
                        font: { 
                            weight: 'bold',
                            size: 14
                        } 
                    },
                    grid: { display: true, color: 'rgba(0,0,0,0.05)' }
                },
                y: { 
                    title: { 
                        display: true, 
                        text: fieldLabels[yField], 
                        font: { 
                            weight: 'bold',
                            size: 14
                        } 
                    }, 
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
    
    document.getElementById('xAxis').addEventListener('change', function() {
        xField = this.value;
        scatterChart.data.datasets = getDatasets(xField, yField);
        scatterChart.options.scales.x.title.text = fieldLabels[xField];
        scatterChart.update();
    });
    
    document.getElementById('yAxis').addEventListener('change', function() {
        yField = this.value;
        scatterChart.data.datasets = getDatasets(xField, yField);
        scatterChart.options.scales.y.title.text = fieldLabels[yField];
        scatterChart.update();
    });
});
</script>
@endpush
@endif
@endsection
