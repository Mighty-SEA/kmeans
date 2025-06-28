@extends('layouts.app')

@section('title', 'Edit Penerima - Admin Panel')
@section('header', 'Edit Penerima')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8 w-full">
    <div class="mb-8">
        <h3 class="text-xl font-medium text-gray-700">Form Edit Penerima Bantuan</h3>
        <p class="text-sm text-gray-500 mt-1">Silakan perbarui data penerima bantuan</p>
    </div>
    
    <form action="{{ route('beneficiary.update', $penerima->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                <input type="text" name="nik" value="{{ old('nik', $penerima->nik) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('nik')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $penerima->nama) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('nama')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat', $penerima->alamat) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('alamat')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $penerima->no_hp) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('no_hp')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Usia</label>
                <input type="number" name="usia" value="{{ old('usia', $penerima->usia) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('usia')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Anak</label>
                <input type="number" name="jumlah_anak" value="{{ old('jumlah_anak', $penerima->jumlah_anak) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('jumlah_anak')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelayakan Rumah (1-5)</label>
                <select name="kelayakan_rumah" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    <option value="">Pilih Kelayakan</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ old('kelayakan_rumah', $penerima->kelayakan_rumah) == $i ? 'selected' : '' }}>{{ $i }} - {{ $i == 1 ? 'Sangat Tidak Layak' : ($i == 2 ? 'Tidak Layak' : ($i == 3 ? 'Cukup' : ($i == 4 ? 'Layak' : 'Sangat Layak'))) }}</option>
                    @endfor
                </select>
                @error('kelayakan_rumah')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pendapatan Per Bulan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500">Rp</span>
                    </div>
                    <input type="number" name="pendapatan_perbulan" value="{{ old('pendapatan_perbulan', $penerima->pendapatan_perbulan) }}" class="w-full border border-gray-300 rounded-lg pl-12 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                @error('pendapatan_perbulan')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="mt-10 flex items-center justify-end space-x-4">
            <a href="{{ route('beneficiary.index') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center">
                <i class="fas fa-save mr-2"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection 