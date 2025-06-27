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
            <a href="{{ route('beneficiary.create') }}" class="flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-md">
                <i class="fas fa-plus mr-2"></i> Tambah Penerima
            </a>
        </div>
    </div>
    
    <div class="flex items-center space-x-2 mb-4">
        <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Export Excel</button>
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Import Excel</button>
    </div>
    <form id="bulkDeleteForm" action="{{ route('beneficiary.bulkDelete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data terpilih?')">
        @csrf
        @method('DELETE')
        <div class="flex items-center space-x-2 mb-4" id="bulkActionBar" style="display:none">
            <span id="selectedCount" class="text-sm text-gray-600"></span>
            <button type="button" id="selectAllDataBtn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Pilih Semua Data</button>
            <input type="hidden" name="select_all" id="selectAllInput" value="0">
            <button id="bulkDeleteBtn" type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Hapus Terpilih</button>
        </div>
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3"><input type="checkbox" id="checkAll"></th>
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
                        <td class="px-6 py-4 text-sm text-gray-500"><input type="checkbox" name="ids[]" value="{{ $p->id }}"></td>
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
                                <a href="{{ route('beneficiary.edit', $p->id) }}" class="px-3 py-1.5 rounded bg-amber-500 text-white text-xs font-medium hover:bg-amber-600 transition flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('beneficiary.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
    </form>
    
    <div class="mt-8">
        {{ $penerima->links('vendor.pagination.tailwind') }}
    </div>
</div>

<div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-lg font-semibold mb-4">Pilih Kolom yang Akan Diexport</h2>
        <form action="{{ route('beneficiary.export') }}" method="POST">
            @csrf
            <div class="space-y-2 mb-4">
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="nama" checked class="mr-2"> Nama</label>
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="alamat" checked class="mr-2"> Alamat</label>
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="no_hp" checked class="mr-2"> No HP</label>
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="usia" checked class="mr-2"> Usia</label>
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="jumlah_anak" checked class="mr-2"> Jumlah Anak</label>
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="kelayakan_rumah" checked class="mr-2"> Kelayakan Rumah</label>
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="pendapatan_perbulan" checked class="mr-2"> Pendapatan Perbulan</label>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Export</button>
            </div>
        </form>
    </div>
</div>

<div id="importModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-lg font-semibold mb-4">Import Data Penerima dari Excel</h2>
        <form action="{{ route('beneficiary.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" class="block w-full border rounded px-2 py-1 text-sm mb-4" required accept=".xlsx,.xls">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Import</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateBulkDeleteUI() {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        const checked = document.querySelectorAll('input[name="ids[]"]:checked');
        const bar = document.getElementById('bulkActionBar');
        const btn = document.getElementById('bulkDeleteBtn');
        const info = document.getElementById('selectedCount');
        const selectAllDataBtn = document.getElementById('selectAllDataBtn');
        const selectAllInput = document.getElementById('selectAllInput');
        if (checked.length > 0 || selectAllInput.value == '1') {
            bar.style.display = '';
            btn.classList.remove('hidden');
            selectAllDataBtn.classList.remove('hidden');
            if (selectAllInput.value == '1') {
                info.textContent = `Dipilih semua data ({{ $penerima->total() }})`;
            } else {
                info.textContent = `Dipilih ${checked.length} data`;
            }
        } else {
            bar.style.display = 'none';
        }
    }
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        for (const cb of checkboxes) {
            cb.checked = this.checked;
        }
        document.getElementById('selectAllInput').value = 0;
        updateBulkDeleteUI();
    });
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
        cb.addEventListener('change', function() {
            document.getElementById('selectAllInput').value = 0;
            updateBulkDeleteUI();
        });
    });
    document.getElementById('selectAllDataBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        for (const cb of checkboxes) {
            cb.checked = true;
        }
        document.getElementById('selectAllInput').value = 1;
        updateBulkDeleteUI();
    });
    // Inisialisasi info saat load
    updateBulkDeleteUI();
</script>
@endsection 