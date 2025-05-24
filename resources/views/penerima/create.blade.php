@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center bg-[#FDFDFC] py-4 px-2">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-4">
        <h2 class="text-2xl font-bold mb-4 text-[#1b1b18] text-center">Tambah Penerima</h2>
        <form action="{{ route('penerima.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Nama</label>
                <input type="text" name="nama" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Alamat</label>
                <input type="text" name="alamat" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">No HP</label>
                <input type="text" name="no_hp" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Usia</label>
                <input type="number" name="usia" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Jumlah Anak</label>
                <input type="number" name="jumlah_anak" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Kelayakan Rumah</label>
                <input type="text" name="kelayakan_rumah" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#1b1b18] mb-1">Pendapatan/Bulan</label>
                <input type="number" name="pendapatan_perbulan" class="w-full border border-[#e3e3e0] rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]" required>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-between mt-4">
                <a href="{{ route('penerima.index') }}" class="px-3 py-2 rounded-lg bg-gray-200 text-[#1b1b18] hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#f53003] text-white font-semibold hover:bg-[#d41e00]">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection 