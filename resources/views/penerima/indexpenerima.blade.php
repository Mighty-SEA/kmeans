@extends('layouts.app')
@section('content')
<div class="w-full max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-center mb-8 text-[#1b1b18] text-5xl">Daftar Penerima</h1>
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 text-center">
            {{ session('success') }}
        </div>
    @endif
    <div class="flex justify-end mb-4">
        <a href="{{ route('penerima.create') }}" class="px-5 py-2 rounded-lg bg-[#f53003] text-white font-semibold hover:bg-[#d41e00] shadow transition">+ Tambah Penerima</a>
    </div>
    <div class="overflow-x-auto rounded-xl shadow-lg bg-white">
        <table class="min-w-full divide-y divide-[#e3e3e0]">
            <thead class="bg-[#f8fafc]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase ">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">Alamat</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">No HP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">Usia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">Jumlah Anak</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">Kelayakan Rumah</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[#1b1b18] uppercase">Pendapatan/Bulan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-[#1b1b18] uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e3e3e0]">
                @foreach ($penerima as $i => $p)
                <tr class="hover:bg-[#fdf6f6] transition">
                    <td class="px-4 py-3 text-sm text-[#706f6c]">{{ $i+1 }}</td>
                    <td class="px-4 py-3 font-medium text-[#f53003]">{{ $p->nama }}</td>
                    <td class="px-4 py-3 text-[#1b1b18]">{{ $p->alamat }}</td>
                    <td class="px-4 py-3 text-[#1b1b18]">{{ $p->no_hp }}</td>
                    <td class="px-4 py-3 text-[#1b1b18]">{{ $p->usia }}</td>
                    <td class="px-4 py-3 text-[#1b1b18]">{{ $p->jumlah_anak }}</td>
                    <td class="px-4 py-3 text-[#1b1b18]">{{ $p->kelayakan_rumah }}</td>
                    <td class="px-4 py-3 text-[#1b1b18]">{{ $p->pendapatan_perbulan }}</td>
                    <td class="px-4 py-3 text-center flex gap-2 justify-center">
                        <a href="{{ route('penerima.edit', $p->id) }}" class="px-3 py-1 rounded bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600 transition">Edit</a>
                        <form action="{{ route('penerima.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 rounded bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $penerima->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection 