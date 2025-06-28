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
        
        return view('welcome', compact(
            'totalPenerima',
            'latestData',
            'clusterCounts',
            'clusterMeans'
        ));
    }

    public function index()
    {
        $penerima = Beneficiary::paginate(10); // 10 data per halaman
        return view('beneficiaries.index', compact('penerima'));
    }

    public function create()
    {
        return view('beneficiaries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer',
            'jumlah_anak' => 'required|integer',
            'kelayakan_rumah' => 'required',
            'pendapatan_perbulan' => 'required|numeric',
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
            'nik' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer',
            'jumlah_anak' => 'required|integer',
            'kelayakan_rumah' => 'required',
            'pendapatan_perbulan' => 'required|numeric',
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
        Excel::import(new BeneficiaryImport, $request->file('file'));
        return redirect()->route('beneficiary.index')->with('success', 'Data berhasil diimport!');
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
