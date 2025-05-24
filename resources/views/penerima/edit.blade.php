@extends('layouts.app')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#FDFDFC] py-10 px-4">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-[#1b1b18] text-center">Edit Penerima</h2>
        <form action="{{ route('penerima.update', $penerima->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $penerima->nama) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat', $penerima->alamat) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">No HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $penerima->no_hp) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Usia</label>
                <input type="number" name="usia" value="{{ old('usia', $penerima->usia) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Jumlah Anak</label>
                <input type="number" name="jumlah_anak" value="{{ old('jumlah_anak', $penerima->jumlah_anak) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Kelayakan Rumah</label>
                <input type="text" name="kelayakan_rumah" value="{{ old('kelayakan_rumah', $penerima->kelayakan_rumah) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Pendapatan/Bulan</label>
                <input type="number" name="pendapatan_perbulan" value="{{ old('pendapatan_perbulan', $penerima->pendapatan_perbulan) }}" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div class="flex justify-between mt-6">
                <a href="{{ route('penerima.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-[#1b1b18] hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-[#f53003] text-white font-semibold hover:bg-[#d41e00]">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection 