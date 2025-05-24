@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-8 text-[#1b1b18]">Statistik KMeans (3 Cluster)</h1>
    @if($message)
        <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-4 text-center">{{ $message }}</div>
    @else
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
@endsection
