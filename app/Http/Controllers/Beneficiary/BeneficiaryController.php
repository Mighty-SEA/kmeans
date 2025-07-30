<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\ClusteringResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeneficiaryExport;
use App\Imports\BeneficiaryImport;

class BeneficiaryController extends Controller
{
    public function dashboard()
    {
        // Mengambil total penerima
        $totalPenerima = Beneficiary::count();
        
        // Mengambil 5 data penerima terbaru
        $latestData = Beneficiary::latest()->take(5)->get();
        
        // Menambahkan data cluster ke penerima terbaru
        foreach ($latestData as $beneficiary) {
            $clusterResult = ClusteringResult::where('beneficiary_id', $beneficiary->id)->first();
            $beneficiary->cluster = $clusterResult ? $clusterResult->cluster : null;
        }
        
        // Mengambil distribusi cluster
        $clusterDistribution = ClusteringResult::select('cluster', DB::raw('count(*) as total'))
            ->groupBy('cluster')
            ->pluck('total', 'cluster')
            ->toArray();
        
        // Memastikan semua indeks cluster (0, 1, 2) tersedia
        $clusterCounts = [
            0 => $clusterDistribution[0] ?? 0,
            1 => $clusterDistribution[1] ?? 0,
            2 => $clusterDistribution[2] ?? 0
        ];
        
        // Menghitung rata-rata fitur per cluster
        $clusterMeans = [];
        for ($i = 0; $i < 3; $i++) {
            $clusterData = Beneficiary::join('clustering_results', 'beneficiaries.id', '=', 'clustering_results.beneficiary_id')
                ->where('clustering_results.cluster', $i)
                ->get();
            
            $count = $clusterData->count();
            $clusterMeans[$i] = [
                'usia' => $count ? $clusterData->avg('usia') : 0,
                'jumlah_anak' => $count ? $clusterData->avg('jumlah_anak') : 0,
                'kelayakan_rumah' => $count ? $clusterData->avg('kelayakan_rumah') : 0,
                'pendapatan' => $count ? $clusterData->avg('pendapatan_perbulan') : 0,
            ];
        }
        
        return view('dashboard', compact(
            'totalPenerima',
            'latestData',
            'clusterCounts',
            'clusterMeans'
        ));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        
        $query = Beneficiary::query();
        
        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }
        
        $penerima = $query->paginate($perPage)->withQueryString();
        
        return view('beneficiaries.index', compact('penerima', 'search', 'perPage'));
    }

    public function create()
    {
        return view('beneficiaries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|unique:beneficiaries,nik',
            'nama' => 'required|max:255',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer|min:1|max:120',
            'jumlah_anak' => 'required|integer|min:0|max:20',
            'kelayakan_rumah' => 'required|numeric|min:0|max:5',
            'pendapatan_perbulan' => 'required|numeric|min:0',
        ], [
            'nik.required' => 'NIK tidak boleh kosong',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama.required' => 'Nama tidak boleh kosong',
            'nama.max' => 'Nama maksimal 255 karakter',
            'alamat.required' => 'Alamat tidak boleh kosong',
            'no_hp.required' => 'No HP tidak boleh kosong',
            'usia.required' => 'Usia tidak boleh kosong',
            'usia.integer' => 'Usia harus berupa angka',
            'usia.min' => 'Usia minimal 1 tahun',
            'usia.max' => 'Usia maksimal 120 tahun',
            'jumlah_anak.required' => 'Jumlah anak tidak boleh kosong',
            'jumlah_anak.integer' => 'Jumlah anak harus berupa angka',
            'jumlah_anak.min' => 'Jumlah anak minimal 0',
            'jumlah_anak.max' => 'Jumlah anak maksimal 20',
            'kelayakan_rumah.required' => 'Kelayakan rumah tidak boleh kosong',
            'kelayakan_rumah.numeric' => 'Kelayakan rumah harus berupa angka',
            'kelayakan_rumah.min' => 'Kelayakan rumah minimal 0 (0=tidak punya rumah/ngontrak, 1-5=tingkat kelayakan)',
            'kelayakan_rumah.max' => 'Kelayakan rumah maksimal 5',
            'pendapatan_perbulan.required' => 'Pendapatan per bulan tidak boleh kosong',
            'pendapatan_perbulan.numeric' => 'Pendapatan per bulan harus berupa angka',
            'pendapatan_perbulan.min' => 'Pendapatan per bulan tidak boleh negatif',
        ]);
        Beneficiary::create($validated);
        return redirect()->route('beneficiary.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $penerima = Beneficiary::findOrFail($id);
        return view('beneficiaries.edit', compact('penerima'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nik' => 'required|unique:beneficiaries,nik,' . $id,
            'nama' => 'required|max:255',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer|min:1|max:120',
            'jumlah_anak' => 'required|integer|min:0|max:20',
            'kelayakan_rumah' => 'required|numeric|min:0|max:5',
            'pendapatan_perbulan' => 'required|numeric|min:0',
        ], [
            'nik.required' => 'NIK tidak boleh kosong',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama.required' => 'Nama tidak boleh kosong',
            'nama.max' => 'Nama maksimal 255 karakter',
            'alamat.required' => 'Alamat tidak boleh kosong',
            'no_hp.required' => 'No HP tidak boleh kosong',
            'usia.required' => 'Usia tidak boleh kosong',
            'usia.integer' => 'Usia harus berupa angka',
            'usia.min' => 'Usia minimal 1 tahun',
            'usia.max' => 'Usia maksimal 120 tahun',
            'jumlah_anak.required' => 'Jumlah anak tidak boleh kosong',
            'jumlah_anak.integer' => 'Jumlah anak harus berupa angka',
            'jumlah_anak.min' => 'Jumlah anak minimal 0',
            'jumlah_anak.max' => 'Jumlah anak maksimal 20',
            'kelayakan_rumah.required' => 'Kelayakan rumah tidak boleh kosong',
            'kelayakan_rumah.numeric' => 'Kelayakan rumah harus berupa angka',
            'kelayakan_rumah.min' => 'Kelayakan rumah minimal 0 (0=tidak punya rumah/ngontrak, 1-5=tingkat kelayakan)',
            'kelayakan_rumah.max' => 'Kelayakan rumah maksimal 5',
            'pendapatan_perbulan.required' => 'Pendapatan per bulan tidak boleh kosong',
            'pendapatan_perbulan.numeric' => 'Pendapatan per bulan harus berupa angka',
            'pendapatan_perbulan.min' => 'Pendapatan per bulan tidak boleh negatif',
        ]);
        $penerima = Beneficiary::findOrFail($id);
        $penerima->update($validated);
        return redirect()->route('beneficiary.index')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Cari data penerima bantuan berdasarkan ID
        $beneficiary = Beneficiary::findOrFail($id);
        
        // Hapus data normalisasi dan clustering jika ada
        $beneficiary->normalizationResult()->delete();
        $beneficiary->clusteringResult()->delete();
        
        // Hapus data penerima
        $beneficiary->delete();
        
        return redirect()->route('beneficiary.index')->with('success', 'Data penerima bantuan berhasil dihapus');
    }

    public function exportExcel(Request $request)
    {
        $columns = $request->input('columns', [
            'nik',
            'nama',
            'alamat',
            'no_hp',
            'usia',
            'jumlah_anak',
            'kelayakan_rumah',
            'pendapatan_perbulan',
        ]);
        return Excel::download(new BeneficiaryExport($columns), 'beneficiary.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new BeneficiaryImport;
        
        try {
            Excel::import($import, $request->file('file'));
            
            // Jika ada error atau failure
            if ($import->hasErrors()) {
                $errorDetails = [];
                
                // Collect validation failures
                foreach ($import->getFailures() as $failure) {
                    $rowNumber = $failure['row'];
                    foreach ($failure['errors'] as $error) {
                        $errorDetails[] = "Baris {$rowNumber}: {$error}";
                    }
                }
                
                // Collect general errors
                foreach ($import->getErrors() as $error) {
                    $errorDetails[] = "Baris {$error['row']}: {$error['error']}";
                }
                
                return redirect()->route('beneficiary.index')
                    ->with('import_errors', $errorDetails)
                    ->with('error', 'Import selesai dengan beberapa error. Total ' . count($errorDetails) . ' baris gagal diimport.');
            }
            
            return redirect()->route('beneficiary.index')
                ->with('success', 'Data berhasil diimport tanpa error!');
                
        } catch (\Exception $e) {
            return redirect()->route('beneficiary.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        if ($request->input('select_all') == 1) {
            Beneficiary::query()->delete();
            return redirect()->route('beneficiary.index')->with('success', 'Semua data berhasil dihapus!');
        }
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Beneficiary::whereIn('id', $ids)->delete();
            return redirect()->route('beneficiary.index')->with('success', 'Data terpilih berhasil dihapus!');
        }
        return redirect()->route('beneficiary.index')->with('success', 'Tidak ada data yang dipilih.');
    }
}
