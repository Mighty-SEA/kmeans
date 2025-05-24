@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-8 text-[#1b1b18]">Detail Cluster {{ $clusterIndex + 1 }}</h1>
    <div class="mb-4">
        <a href="{{ route('statistic.index') }}" class="text-blue-600 hover:underline">&larr; Kembali ke Statistik</a>
    </div>
    <div class="bg-white shadow rounded p-4">
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
                @foreach($cluster as $row)
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
        <div class="mt-2 text-gray-500">Total: {{ $total }} data</div>
    </div>
</div>
@endsection 