@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-8 text-[#1b1b18]">Statistik KMeans (3 Cluster)</h1>
    @if($message)
        <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-4 text-center">{{ $message }}</div>
    @else
        <form action="{{ route('statistic.recalculate') }}" method="POST" class="mb-4 text-center">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">Hitung Ulang Clustering</button>
        </form>
        <div class="mb-8">
            <div class="flex flex-wrap gap-4 items-center mb-4">
                <label for="xAxis" class="font-medium">Sumbu X:</label>
                <select id="xAxis" class="border rounded px-2 py-1">
                    <option value="usia">Usia</option>
                    <option value="jumlah_anak">Jumlah Anak</option>
                    <option value="kelayakan_rumah">Kelayakan Rumah</option>
                    <option value="pendapatan">Pendapatan</option>
                    <option value="cluster">Cluster</option>
                </select>
                <label for="yAxis" class="font-medium ml-4">Sumbu Y:</label>
                <select id="yAxis" class="border rounded px-2 py-1">
                    <option value="pendapatan">Pendapatan</option>
                    <option value="usia">Usia</option>
                    <option value="jumlah_anak">Jumlah Anak</option>
                    <option value="kelayakan_rumah">Kelayakan Rumah</option>
                    <option value="cluster">Cluster</option>
                </select>
            </div>
            <canvas id="scatterChart" height="120"></canvas>
        </div>
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2 text-center">Proporsi Anggota per Cluster</h2>
                <canvas id="pieChart" height="120"></canvas>
            </div>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2 text-center">Rata-rata Fitur per Cluster</h2>
                <canvas id="barChart" height="120"></canvas>
            </div>
        </div>
        <div class="mb-8 bg-white rounded shadow p-4">
            <h2 class="text-lg font-semibold mb-4 text-center">Tabel Statistik Ringkasan per Cluster</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs md:text-sm border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">Cluster</th>
                            <th class="border px-2 py-1">Fitur</th>
                            <th class="border px-2 py-1">Min</th>
                            <th class="border px-2 py-1">Max</th>
                            <th class="border px-2 py-1">Mean</th>
                            <th class="border px-2 py-1">Median</th>
                            <th class="border px-2 py-1">Std</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clusterStats as $c => $stat)
                            @foreach(['usia','jumlah_anak','kelayakan_rumah','pendapatan'] as $f)
                                <tr>
                                    @if($f === 'usia')
                                        <td class="border px-2 py-1 text-center" rowspan="4">Cluster {{ $c+1 }}</td>
                                    @endif
                                    <td class="border px-2 py-1">{{ ucfirst(str_replace('_',' ',$f)) }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($stat[$f]['min'],2) }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($stat[$f]['max'],2) }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($stat[$f]['mean'],2) }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($stat[$f]['median'],2) }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($stat[$f]['std'],2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($clusters as $key => $cluster)
                <div class="bg-white shadow rounded p-4">
                    <h2 class="text-xl font-semibold mb-2">Cluster {{ $key + 1 }}</h2>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="border px-2 py-1">Nama</th>
                                <th class="border px-2 py-1">Usia</th>
                                <th class="border px-2 py-1">Jumlah Anak</th>
                                <th class="border px-2 py-1">Kelayakan Rumah</th>
                                <th class="border px-2 py-1">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($cluster, 0, 5) as $row)
                                <tr>
                                    <td class="border px-2 py-1">{{ $row->nama ?? '-' }}</td>
                                    <td class="border px-2 py-1">{{ $row->usia }}</td>
                                    <td class="border px-2 py-1">{{ $row->jumlah_anak }}</td>
                                    <td class="border px-2 py-1">{{ $row->kelayakan_rumah }}</td>
                                    <td class="border px-2 py-1">{{ number_format($row->pendapatan_perbulan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2 text-gray-500">Total: {{ count($cluster) }} data</div>
                    @if(count($cluster) > 5)
                        <a href="{{ route('statistic.cluster', ['cluster' => $key]) }}" class="text-blue-600 hover:underline mt-2 inline-block">Lihat Semua</a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scatterData = @json($scatterData);
    const colors = ['#f87171', '#60a5fa', '#34d399'];
    const fieldLabels = {
        usia: 'Usia',
        jumlah_anak: 'Jumlah Anak',
        kelayakan_rumah: 'Kelayakan Rumah',
        pendapatan: 'Pendapatan',
        cluster: 'Cluster',
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
        }));
    }
    let xField = document.getElementById('xAxis').value;
    let yField = document.getElementById('yAxis').value;
    const ctx = document.getElementById('scatterChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'scatter',
        data: { datasets: getDatasets(xField, yField) },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const d = context.raw;
                            return d.nama + ' (' + fieldLabels[xField] + ': ' + d.x + ', ' + fieldLabels[yField] + ': ' + d.y + ')';
                        }
                    }
                }
            },
            scales: {
                x: { title: { display: true, text: fieldLabels[xField] } },
                y: { title: { display: true, text: fieldLabels[yField] }, beginAtZero: true }
            }
        }
    });
    document.getElementById('xAxis').addEventListener('change', function() {
        xField = this.value;
        chart.data.datasets = getDatasets(xField, yField);
        chart.options.scales.x.title.text = fieldLabels[xField];
        chart.update();
    });
    document.getElementById('yAxis').addEventListener('change', function() {
        yField = this.value;
        chart.data.datasets = getDatasets(xField, yField);
        chart.options.scales.y.title.text = fieldLabels[yField];
        chart.update();
    });

    // Pie Chart
    const pieData = @json(array_values($clusterCounts));
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Cluster 1', 'Cluster 2', 'Cluster 3'],
            datasets: [{
                data: pieData,
                backgroundColor: colors,
            }]
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });

    // Bar Chart
    const barData = @json(array_values($clusterMeans));
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Usia', 'Jumlah Anak', 'Kelayakan Rumah', 'Pendapatan'],
            datasets: [0,1,2].map(i => ({
                label: 'Cluster ' + (i+1),
                data: [barData[i].usia, barData[i].jumlah_anak, barData[i].kelayakan_rumah, barData[i].pendapatan],
                backgroundColor: colors[i],
            }))
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                x: { stacked: true },
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endsection
