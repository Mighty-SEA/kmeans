@extends('layouts.app')

@section('title', 'Statistik Clustering - Admin Panel')
@section('header', 'Statistik K-Means')

@section('content')
<div class="space-y-8 w-full">
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-lg shadow-sm flex items-center border border-red-200">
            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow-sm flex items-center border border-green-200">
            <i class="fas fa-check-circle mr-2 text-green-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if($message)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg shadow-sm flex items-center border border-yellow-200">
            <i class="fas fa-exclamation-circle mr-2 text-yellow-600"></i>
            <span>{{ $message }}</span>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-medium text-gray-700">{{ isset($clustered) && $clustered ? 'Hasil Clustering K-Means' : 'Analisis Clustering K-Means' }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if(isset($clustered) && $clustered)
                            Visualisasi dan analisis hasil clustering dengan metode K-Means ({{ $clusterCount }} Cluster)
                        @else
                            Lakukan clustering untuk melihat analisis data
                        @endif
                    </p>
                    <div class="mt-2 bg-blue-50 text-blue-700 px-3 py-1 rounded-md text-xs inline-flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span>Powered by Rubix ML (PHP Machine Learning)</span>
                    </div>
                </div>
                
                @if(isset($clustered) && $clustered)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <form action="{{ route('statistic.recalculate') }}" method="POST" class="flex flex-col md:flex-row items-end gap-4">
                        @csrf
                        <div>
                            <label for="num_clusters" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Cluster:</label>
                            <div class="relative">
                                <select id="num_clusters" name="num_clusters" class="appearance-none block w-full rounded-lg border border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @for ($i = 2; $i <= min(10, isset($dataCount) ? $dataCount : 10); $i++)
                                        <option value="{{ $i }}" {{ $clusterCount == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="normalization" class="block text-sm font-medium text-gray-700 mb-1">Metode Normalisasi:</label>
                            <div class="relative">
                                <select id="normalization" name="normalization" class="appearance-none block w-full rounded-lg border border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="none" {{ ($lastNormalization ?? '') == 'none' ? 'selected' : '' }}>Tanpa Normalisasi</option>
                                    <option value="minmax" {{ ($lastNormalization ?? '') == 'minmax' ? 'selected' : '' }}>Min-Max</option>
                                    <option value="standard" {{ ($lastNormalization ?? '') == 'standard' ? 'selected' : '' }}>Standard (Z-Score)</option>
                                    <option value="robust" {{ ($lastNormalization ?? 'robust') == 'robust' ? 'selected' : '' }}>Robust</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="flex items-center px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-md">
                            <i class="fas fa-sync-alt mr-2"></i> Hitung Ulang Clustering
                        </button>
                    </form>
                </div>
                @else
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <form action="{{ route('statistic.clustering') }}" method="POST" class="flex flex-col md:flex-row items-end gap-4">
                        @csrf
                        <div>
                            <label for="num_clusters" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Cluster:</label>
                            <div class="relative">
                                <select id="num_clusters" name="num_clusters" class="appearance-none block w-full rounded-lg border border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @for ($i = 2; $i <= min(10, isset($dataCount) ? $dataCount : 10); $i++)
                                        <option value="{{ $i }}" {{ ($lastNumClusters ?? 3) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="normalization" class="block text-sm font-medium text-gray-700 mb-1">Metode Normalisasi:</label>
                            <div class="relative">
                                <select id="normalization" name="normalization" class="appearance-none block w-full rounded-lg border border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="none" {{ ($lastNormalization ?? '') == 'none' ? 'selected' : '' }}>Tanpa Normalisasi</option>
                                    <option value="minmax" {{ ($lastNormalization ?? '') == 'minmax' ? 'selected' : '' }}>Min-Max</option>
                                    <option value="standard" {{ ($lastNormalization ?? '') == 'standard' ? 'selected' : '' }}>Standard (Z-Score)</option>
                                    <option value="robust" {{ ($lastNormalization ?? 'robust') == 'robust' ? 'selected' : '' }}>Robust</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-md">
                            <i class="fas fa-chart-pie mr-2"></i> Hitung Clustering
                        </button>
                    </form>
                </div>
                @endif
            </div>
            
            @if(isset($clustered) && $clustered)
            <!-- Ringkasan Cluster -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                @foreach($clusterCounts as $key => $count)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    @php
                        $clusterColors = ['red', 'blue', 'green', 'purple', 'pink', 'yellow', 'indigo', 'teal', 'orange', 'gray'];
                        $colorIndex = min($key, count($clusterColors) - 1);
                        $bgClass = 'bg-' . $clusterColors[$colorIndex] . '-100';
                        $textClass = 'text-' . $clusterColors[$colorIndex] . '-600';
                        $borderClass = 'border-' . $clusterColors[$colorIndex] . '-500';
                        
                        // Silhouette score color
                        $silhouetteValue = isset($avgSilhouettes[$key]) ? $avgSilhouettes[$key] : 0;
                        $silhouetteClass = 'text-gray-500';
                        $silhouetteIcon = 'fa-minus';
                        
                        if ($silhouetteValue > 0.7) {
                            $silhouetteClass = 'text-green-600';
                            $silhouetteIcon = 'fa-check-circle';
                        } elseif ($silhouetteValue > 0.5) {
                            $silhouetteClass = 'text-blue-600';
                            $silhouetteIcon = 'fa-check';
                        } elseif ($silhouetteValue > 0.25) {
                            $silhouetteClass = 'text-yellow-600';
                            $silhouetteIcon = 'fa-exclamation-circle';
                        } elseif ($silhouetteValue > 0) {
                            $silhouetteClass = 'text-orange-600';
                            $silhouetteIcon = 'fa-exclamation-triangle';
                        } else {
                            $silhouetteClass = 'text-red-600';
                            $silhouetteIcon = 'fa-times-circle';
                        }
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
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-500">Silhouette Score:</span>
                            <span class="text-xs font-medium {{ $silhouetteClass }}">
                                <i class="fas {{ $silhouetteIcon }} mr-1"></i>
                                {{ number_format($silhouetteValue, 2) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $silhouetteValue > 0 ? $bgClass : 'bg-gray-300' }}" style="width: {{ min(max(($silhouetteValue + 1) / 2 * 100, 5), 100) }}%;"></div>
                        </div>
                    </div>
                    
                    <!-- Statistik Cluster -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="text-xs">
                            <p class="text-gray-500">Rata-rata Usia</p>
                            <p class="font-medium text-gray-800">{{ number_format(isset($clusterMeans[$key]) ? $clusterMeans[$key]['usia'] : 0, 1) }}</p>
                        </div>
                        <div class="text-xs">
                            <p class="text-gray-500">Rata-rata Jumlah Anak</p>
                            <p class="font-medium text-gray-800">{{ number_format(isset($clusterMeans[$key]) ? $clusterMeans[$key]['jumlah_anak'] : 0, 1) }}</p>
                        </div>
                        <div class="text-xs">
                            <p class="text-gray-500">Rata-rata Kelayakan</p>
                            <p class="font-medium text-gray-800">{{ number_format(isset($clusterMeans[$key]) ? $clusterMeans[$key]['kelayakan_rumah'] : 0, 1) }}</p>
                        </div>
                        <div class="text-xs">
                            <p class="text-gray-500">Rata-rata Pendapatan</p>
                            <p class="font-medium text-gray-800">Rp {{ number_format(isset($clusterMeans[$key]) ? $clusterMeans[$key]['pendapatan'] : 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <a href="{{ route('statistic.cluster', $key + 1) }}" class="flex items-center justify-center px-4 py-2 rounded bg-gray-50 text-gray-700 hover:bg-gray-100 transition w-full">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Overall Silhouette Score -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
                <h4 class="text-lg font-medium text-gray-800 mb-4">Kualitas Clustering (Silhouette Score)</h4>
                <div class="mb-2 text-sm text-gray-600">
                    Silhouette score mengukur seberapa baik data dikelompokkan. Nilai berkisar dari -1 (buruk) hingga 1 (sangat baik).
                </div>
                
                @php
                    // Overall silhouette color
                    $overallClass = 'text-gray-500';
                    $overallText = 'Netral';
                    
                    if ($overallSilhouette > 0.7) {
                        $overallClass = 'text-green-600';
                        $overallText = 'Sangat Baik';
                    } elseif ($overallSilhouette > 0.5) {
                        $overallClass = 'text-blue-600';
                        $overallText = 'Baik';
                    } elseif ($overallSilhouette > 0.25) {
                        $overallClass = 'text-yellow-600';
                        $overallText = 'Cukup';
                    } elseif ($overallSilhouette > 0) {
                        $overallClass = 'text-orange-600';
                        $overallText = 'Kurang';
                    } else {
                        $overallClass = 'text-red-600';
                        $overallText = 'Buruk';
                    }
                @endphp
                
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center {{ $overallClass }} mr-4">
                            <span class="text-2xl font-bold">{{ number_format($overallSilhouette, 2) }}</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Rata-rata Silhouette Score</p>
                            <p class="text-lg font-medium {{ $overallClass }}">{{ $overallText }}</p>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-2/3">
                        <div class="h-4 w-full bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-4 rounded-full 
                                @if($overallSilhouette > 0.7) bg-green-500
                                @elseif($overallSilhouette > 0.5) bg-blue-500
                                @elseif($overallSilhouette > 0.25) bg-yellow-500
                                @elseif($overallSilhouette > 0) bg-orange-500
                                @else bg-red-500 @endif"
                                style="width: {{ min(max(($overallSilhouette + 1) / 2 * 100, 5), 100) }}%;">
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>-1.0</span>
                            <span>0.0</span>
                            <span>1.0</span>
                        </div>
                    </div>
                </div>
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
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-medium text-gray-800">Perbandingan Rata-rata Fitur</h4>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="mr-2 text-sm text-gray-600">Normalisasi:</span>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="normalizeBarChart" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <select id="barXAxis" class="border rounded-lg px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 min-w-[180px]">
                                <option value="cluster">Cluster</option>
                                <option value="usia">Usia</option>
                                <option value="jumlah_anak">Jumlah Anak</option>
                                <option value="kelayakan_rumah">Kelayakan Rumah</option>
                                <option value="pendapatan">Pendapatan</option>
                            </select>
                        </div>
                    </div>
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
                            <option value="silhouette">Silhouette Score</option>
                            <option value="usia_normalized">Usia (Normalisasi)</option>
                            <option value="jumlah_anak_normalized">Jumlah Anak (Normalisasi)</option>
                            <option value="kelayakan_rumah_normalized">Kelayakan Rumah (Normalisasi)</option>
                            <option value="pendapatan_normalized">Pendapatan (Normalisasi)</option>
                        </select>
                        
                        <label for="yAxis" class="font-medium text-gray-700 ml-4">Sumbu Y:</label>
                        <select id="yAxis" class="border rounded-lg px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 min-w-[180px]">
                            <option value="pendapatan">Pendapatan</option>
                            <option value="usia">Usia</option>
                            <option value="jumlah_anak">Jumlah Anak</option>
                            <option value="kelayakan_rumah">Kelayakan Rumah</option>
                            <option value="silhouette">Silhouette Score</option>
                            <option value="usia_normalized">Usia (Normalisasi)</option>
                            <option value="jumlah_anak_normalized">Jumlah Anak (Normalisasi)</option>
                            <option value="kelayakan_rumah_normalized">Kelayakan Rumah (Normalisasi)</option>
                            <option value="pendapatan_normalized">Pendapatan (Normalisasi)</option>
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
                <div class="text-xs text-gray-500 italic mb-4">
                    * Semua statistik dihitung menggunakan Rubix ML - Library Machine Learning untuk PHP
                </div>
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
            @else
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="mb-6 text-gray-400">
                        <i class="fas fa-chart-scatter text-6xl"></i>
                    </div>
                    <h4 class="text-xl font-medium text-gray-700 mb-2">Belum Ada Hasil Clustering</h4>
                    <p class="text-sm text-gray-500 mb-8 text-center">Silahkan lakukan clustering untuk melihat hasil analisis data</p>
                    
                    <div class="flex flex-col items-center">
                        <div class="text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                <span>Dengan clustering K-Means Anda dapat:</span>
                            </div>
                        </div>
                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-2 mb-8">
                            <li>Mengelompokkan data ke dalam beberapa cluster</li>
                            <li>Melihat karakteristik setiap cluster</li>
                            <li>Melakukan analisis berdasarkan kesamaan fitur</li>
                            <li>Mengambil keputusan berdasarkan hasil clustering</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

@if(!$message && isset($clustered) && $clustered)
@push('scripts')
<script>
window.statisticData = {
    clustered: true,
    clusterLabels: [
        @for($i = 0; $i < $clusterCount; $i++)
            'Cluster {{ $i + 1 }}'{{ $i < $clusterCount-1 ? ',' : '' }}
        @endfor
    ],
    clusterData: [
        @for($i = 0; $i < $clusterCount; $i++)
            {{ $clusterCounts[$i] ?? 0 }}{{ $i < $clusterCount-1 ? ',' : '' }}
        @endfor
    ],
    backgroundColors: [
        'rgba(248, 113, 113, 0.7)', // red
        'rgba(96, 165, 250, 0.7)',  // blue
        'rgba(52, 211, 153, 0.7)',  // green
        'rgba(167, 139, 250, 0.7)', // purple
        'rgba(249, 168, 212, 0.7)', // pink
        'rgba(251, 191, 36, 0.7)',  // yellow
        'rgba(129, 140, 248, 0.7)', // indigo
        'rgba(45, 212, 191, 0.7)',  // teal
        'rgba(249, 115, 22, 0.7)',  // orange
        'rgba(156, 163, 175, 0.7)'  // gray
    ],
    borderColors: [
        'rgb(220, 38, 38)',    // red
        'rgb(37, 99, 235)',    // blue
        'rgb(5, 150, 105)',    // green
        'rgb(124, 58, 237)',   // purple
        'rgb(236, 72, 153)',   // pink
        'rgb(245, 158, 11)',   // yellow
        'rgb(67, 56, 202)',    // indigo
        'rgb(20, 184, 166)',   // teal
        'rgb(234, 88, 12)',    // orange
        'rgb(107, 114, 128)'   // gray
    ],
    barDatasets: [
        @for($i = 0; $i < $clusterCount; $i++)
        {
            label: 'Cluster {{ $i + 1 }}',
            data: [
                {{ $clusterMeans[$i]['usia'] ?? 0 }},
                {{ $clusterMeans[$i]['jumlah_anak'] ?? 0 }},
                {{ $clusterMeans[$i]['kelayakan_rumah'] ?? 0 }},
                {{ $clusterMeans[$i]['pendapatan'] ?? 0 }}
            ],
            backgroundColor: @if($i == 0) 'rgba(248, 113, 113, 0.7)' @elseif($i == 1) 'rgba(96, 165, 250, 0.7)' @elseif($i == 2) 'rgba(52, 211, 153, 0.7)' @elseif($i == 3) 'rgba(167, 139, 250, 0.7)' @elseif($i == 4) 'rgba(249, 168, 212, 0.7)' @elseif($i == 5) 'rgba(251, 191, 36, 0.7)' @elseif($i == 6) 'rgba(129, 140, 248, 0.7)' @elseif($i == 7) 'rgba(45, 212, 191, 0.7)' @elseif($i == 8) 'rgba(249, 115, 22, 0.7)' @else 'rgba(156, 163, 175, 0.7)' @endif,
            borderColor: @if($i == 0) 'rgb(220, 38, 38)' @elseif($i == 1) 'rgb(37, 99, 235)' @elseif($i == 2) 'rgb(5, 150, 105)' @elseif($i == 3) 'rgb(124, 58, 237)' @elseif($i == 4) 'rgb(236, 72, 153)' @elseif($i == 5) 'rgb(245, 158, 11)' @elseif($i == 6) 'rgb(67, 56, 202)' @elseif($i == 7) 'rgb(20, 184, 166)' @elseif($i == 8) 'rgb(234, 88, 12)' @else 'rgb(107, 114, 128)' @endif,
            borderWidth: 2
        }{{ $i < $clusterCount-1 ? ',' : '' }}
        @endfor
    ],
    scatterData: @json($scatterData),
    fieldLabels: {
        usia: 'Usia',
        jumlah_anak: 'Jumlah Anak',
        kelayakan_rumah: 'Kelayakan Rumah',
        pendapatan: 'Pendapatan',
        silhouette: 'Silhouette Score',
        cluster: 'Cluster',
        usia_normalized: 'Usia (Normalisasi)',
        jumlah_anak_normalized: 'Jumlah Anak (Normalisasi)',
        kelayakan_rumah_normalized: 'Kelayakan Rumah (Normalisasi)',
        pendapatan_normalized: 'Pendapatan (Normalisasi)'
    },
    clusterCount: {{ $clusterCount }},
    normalizedClusterMeans: @json($normalizedClusterMeans)
};
</script>
<script type="module" src="{{ Vite::asset('resources/js/statistic.js') }}"></script>
@endpush
@endif
@endsection
