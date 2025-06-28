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
        <div class="flex items-center space-x-2">
            <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" class="flex items-center px-4 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition shadow-md">
                <i class="fas fa-file-export mr-2"></i> Export Excel
            </button>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="flex items-center px-4 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-md">
                <i class="fas fa-file-import mr-2"></i> Import Excel
            </button>
            <a href="{{ route('beneficiary.create') }}" class="flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-md">
                <i class="fas fa-plus mr-2"></i> Tambah Penerima
            </a>
        </div>
    </div>
    
    <div class="mb-6 p-6 rounded-lg bg-blue-100 border border-blue-300">
        <div class="flex items-center">
            <div class="mr-5 p-4 rounded-full bg-white text-blue-800">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 text-lg">Informasi Data Penerima</h4>
                <p class="text-sm text-blue-800">Total {{ $penerima->total() }} data penerima bantuan terdaftar</p>
            </div>
        </div>
    </div>
    
    <!-- Form Pencarian -->
    <div class="mb-6">
        <form action="{{ route('beneficiary.index') }}" method="GET" class="flex items-center">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama, NIK, alamat..." 
                    class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Cari
            </button>
            @if(!empty($search))
                <a href="{{ route('beneficiary.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Reset
                </a>
            @endif
            <input type="hidden" name="perPage" value="{{ $perPage ?? 10 }}">
        </form>
    </div>
    
    <div class="mb-4 flex justify-between items-center">
        <h4 class="font-medium text-gray-700 text-lg">Data Penerima Bantuan</h4>
        
        <form id="bulkDeleteForm" action="{{ route('beneficiary.bulkDelete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data terpilih?')">
            @csrf
            @method('DELETE')
            <div class="flex items-center space-x-2" id="bulkActionBar" style="display:none">
                <span id="selectedCount" class="text-sm text-gray-600"></span>
                <button type="button" id="selectAllDataBtn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Pilih Semua Data</button>
                <input type="hidden" name="select_all" id="selectAllInput" value="0">
                <button id="bulkDeleteBtn" type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Hapus Terpilih</button>
            </div>
        </form>
    </div>
    
    <div class="overflow-x-auto rounded-lg shadow">
        @if($penerima->isEmpty() && isset($search) && $search)
        <div class="bg-gray-50 p-8 text-center">
            <div class="text-gray-500 mb-2">
                <i class="fas fa-search text-3xl"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-700 mb-1">Tidak ada hasil</h4>
            <p class="text-sm text-gray-500">Tidak ditemukan data dengan kata kunci "{{ $search }}"</p>
            <div class="mt-4">
                <a href="{{ route('beneficiary.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke semua data
                </a>
            </div>
        </div>
        @else
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3"><input type="checkbox" id="checkAll"></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
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
                    <td class="px-6 py-4 text-sm text-gray-500">{{ ($penerima->currentPage()-1) * $penerima->perPage() + $i + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-indigo-600">{{ $p->nama }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $p->nik }}</td>
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
        @endif
    </div>
    
    <div class="mt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                {{ $penerima->links('vendor.pagination.tailwind') }}
                <div class="mt-2 text-sm text-gray-600">
                    Total data: <span class="font-semibold">{{ $penerima->total() }}</span> penerima bantuan
                </div>
                @if(!empty($search))
                <div class="mt-1 text-sm text-gray-600">
                    Menampilkan hasil pencarian untuk: <span class="font-semibold">{{ $search }}</span>
                </div>
                @endif
            </div>
            
            <!-- Tombol untuk mengubah jumlah data per halaman -->
            <form action="{{ route('beneficiary.index') }}" method="GET" class="flex items-center space-x-2">
                @if(!empty($search))
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <label class="text-sm text-gray-600">Tampilkan:</label>
                <select name="perPage" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ ($perPage ?? 10) == 20 ? 'selected' : '' }}>20</option>
                    <option value="30" {{ ($perPage ?? 10) == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span class="text-sm text-gray-600">data per halaman</span>
            </form>
        </div>
    </div>
</div>

<div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-lg font-semibold mb-4">Pilih Kolom yang Akan Diexport</h2>
        <form action="{{ route('beneficiary.export') }}" method="POST">
            @csrf
            <div class="space-y-2 mb-4">
                <label class="flex items-center"><input type="checkbox" name="columns[]" value="nik" checked class="mr-2"> NIK</label>
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