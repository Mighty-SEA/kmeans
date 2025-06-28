<?php

namespace App\Http\Controllers\Decision;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\ClusteringResult;
use App\Models\DecisionResult;
use App\Models\DecisionResultItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class DecisionController extends Controller
{
    /**
     * Tampilkan halaman utama panel keputusan
     */
    public function index()
    {
        // Hitung jumlah anggota per cluster
        $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as count'))
            ->groupBy('cluster')
            ->pluck('count', 'cluster')
            ->toArray();
        
        if (empty($clusterCounts)) {
            $clusterCounts = [0 => 0, 1 => 0, 2 => 0];
        }
        
        // Ambil semua decision results untuk ditampilkan
        $decisionResults = DecisionResult::orderBy('created_at', 'desc')->get();
        
        return view('decision.index', [
            'clusterCounts' => $clusterCounts,
            'decisionResults' => $decisionResults
        ]);
    }
    
    /**
     * Tampilkan form untuk membuat keputusan baru
     */
    public function create()
    {
        // Hitung jumlah anggota per cluster
        $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as count'))
            ->groupBy('cluster')
            ->pluck('count', 'cluster')
            ->toArray();
            
        if (empty($clusterCounts)) {
            return redirect()->route('decision.index')->with('error', 'Belum ada data clustering. Silakan lakukan clustering terlebih dahulu.');
        }
        
        return view('decision.create', [
            'clusterCounts' => $clusterCounts
        ]);
    }
    
    /**
     * Simpan keputusan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cluster' => 'required|integer|min:0|max:2',
            'count' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Cek apakah count tidak melebihi jumlah anggota cluster
        $clusterCount = ClusteringResult::where('cluster', $validated['cluster'])->count();
        if ($validated['count'] > $clusterCount) {
            return back()->withErrors(['count' => "Jumlah yang dipilih melebihi jumlah anggota dalam cluster ({$clusterCount})"])->withInput();
        }
        
        DB::beginTransaction();
        try {
            // Simpan decision result
            $decisionResult = DecisionResult::create($validated);
            
            // Ambil ID penerima dalam cluster yang dipilih
            $beneficiaryIds = ClusteringResult::where('cluster', $validated['cluster'])
                ->inRandomOrder()
                ->limit($validated['count'])
                ->pluck('beneficiary_id')
                ->toArray();
            
            // Buat item untuk setiap penerima yang dipilih
            foreach ($beneficiaryIds as $beneficiaryId) {
                DecisionResultItem::create([
                    'decision_result_id' => $decisionResult->id,
                    'beneficiary_id' => $beneficiaryId
                ]);
            }
            
            DB::commit();
            return redirect()->route('decision.show', $decisionResult->id)->with('success', 'Keputusan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Tampilkan detail keputusan
     */
    public function show($id)
    {
        $decisionResult = DecisionResult::with('beneficiaries')->findOrFail($id);
        
        return view('decision.show', [
            'decisionResult' => $decisionResult
        ]);
    }
    
    /**
     * Hapus keputusan
     */
    public function destroy($id)
    {
        $decisionResult = DecisionResult::findOrFail($id);
        $decisionResult->delete();
        
        return redirect()->route('decision.index')->with('success', 'Keputusan berhasil dihapus!');
    }
} 