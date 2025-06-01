@extends('layouts.app')

@section('title', 'Data Penerima - Admin Panel')
@section('header', 'Data Penerima')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8 w-full">
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 flex items-center">
            <i class="fas fa-check-circle mr-2 text-xl text-green-600"></i>
            <span class="text-base">{{ session('success') }}</span>
        </div>
    @endif
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h3 class="text-xl font-medium text-gray-700">Daftar Semua Penerima Bantuan</h3>
            <p class="text-sm text-gray-500 mt-1">Menampilkan semua data penerima bantuan yang terdaftar</p>
        </div>
        <div class="flex items-center">
            <a href="{{ route('penerima.create') }}" class="flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-md">
                <i class="fas fa-plus mr-2"></i> Tambah Penerima
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Anak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelayakan Rumah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan/Bulan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($penerima as $i => $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $i+1 }}</td>
                    <td class="px-6 py-4 font-medium text-indigo-600">{{ $p->nama }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $p->alamat }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $p->no_hp }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $p->usia }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $p->jumlah_anak }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $p->kelayakan_rumah }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($p->pendapatan_perbulan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        <div class="flex justify-center space-x-3">
                            <a href="{{ route('penerima.edit', $p->id) }}" class="px-3 py-1.5 rounded bg-amber-500 text-white text-xs font-medium hover:bg-amber-600 transition flex items-center">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form action="{{ route('penerima.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded bg-red-500 text-white text-xs font-medium hover:bg-red-600 transition flex items-center">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-8">
        {{ $penerima->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection 