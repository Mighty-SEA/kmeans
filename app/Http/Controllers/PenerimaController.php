<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\ClusteringResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaController extends Controller
{
    public function dashboard()
    {
        // Mengambil total penerima
        $totalPenerima = Penerima::count();
        
        // Mengambil 5 data penerima terbaru
        $latestData = Penerima::latest()->take(5)->get();
        
        // Menambahkan data cluster ke penerima terbaru
        foreach ($latestData as $penerima) {
            $clusterResult = ClusteringResult::where('penerima_id', $penerima->id)->first();
            $penerima->cluster = $clusterResult ? $clusterResult->cluster : null;
        }
        
        // Mengambil distribusi cluster
        $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as total'))
            ->groupBy('cluster')
            ->pluck('total', 'cluster')
            ->toArray();
        
        // Jika belum ada data clustering, buat array kosong
        if (empty($clusterCounts)) {
            $clusterCounts = [0 => 0, 1 => 0, 2 => 0];
        }
        
        // Menghitung rata-rata fitur per cluster
        $clusterMeans = [];
        for ($i = 0; $i < 3; $i++) {
            $clusterData = Penerima::join('clustering_results', 'penerima.id', '=', 'clustering_results.penerima_id')
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
        $penerima = Penerima::paginate(10); // 10 data per halaman
        return view('penerima.indexpenerima', compact('penerima'));
    }

    public function create()
    {
        return view('penerima.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer',
            'jumlah_anak' => 'required|integer',
            'kelayakan_rumah' => 'required',
            'pendapatan_perbulan' => 'required|numeric',
        ]);
        Penerima::create($validated);
        return redirect()->route('penerima.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $penerima = Penerima::findOrFail($id);
        return view('penerima.edit', compact('penerima'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer',
            'jumlah_anak' => 'required|integer',
            'kelayakan_rumah' => 'required',
            'pendapatan_perbulan' => 'required|numeric',
        ]);
        $penerima = Penerima::findOrFail($id);
        $penerima->update($validated);
        return redirect()->route('penerima.index')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {
        $penerima = Penerima::findOrFail($id);
        $penerima->delete();
        return redirect()->route('penerima.index')->with('success', 'Data berhasil dihapus!');
    }
}
