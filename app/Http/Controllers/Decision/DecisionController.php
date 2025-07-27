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

        // Ambil statistik cluster (mean, silhouette, prioritas)
        $clusterMeans = [];
        $avgSilhouettes = [];
        foreach ($clusterCounts as $cluster => $count) {
            $means = ClusteringResult::where('cluster', $cluster)
                ->join('beneficiaries', 'clustering_results.beneficiary_id', '=', 'beneficiaries.id')
                ->select(
                    DB::raw('AVG(usia) as usia'),
                    DB::raw('AVG(jumlah_anak) as jumlah_anak'),
                    DB::raw('AVG(kelayakan_rumah) as kelayakan_rumah'),
                    DB::raw('AVG(pendapatan_perbulan) as pendapatan'),
                    DB::raw('AVG(silhouette) as silhouette')
                )
                ->first();
            $clusterMeans[$cluster] = [
                'usia' => (float) $means->usia,
                'jumlah_anak' => (float) $means->jumlah_anak,
                'kelayakan_rumah' => (float) $means->kelayakan_rumah,
                'pendapatan' => (float) $means->pendapatan,
            ];
            $avgSilhouettes[$cluster] = (float) $means->silhouette;
        }
        // Hitung prioritas
        $pendapatanArr = array_column($clusterMeans, 'pendapatan');
        $kelayakanArr = array_column($clusterMeans, 'kelayakan_rumah');
        $jumlahAnakArr = array_column($clusterMeans, 'jumlah_anak');
        $needScores = [];
        foreach ($clusterMeans as $idx => $mean) {
            $pendapatan = $mean['pendapatan'] ?? 0;
            $kelayakan = $mean['kelayakan_rumah'] ?? 0;
            $jumlah_anak = $mean['jumlah_anak'] ?? 0;
            $score = (max($pendapatanArr) - $pendapatan)
                + (max($kelayakanArr) - $kelayakan)
                + ($jumlah_anak - min($jumlahAnakArr));
            $needScores[$idx] = $score;
        }
        arsort($needScores);
        $rankMap = [];
        $rank = 1;
        foreach(array_keys($needScores) as $idx) {
            $rankMap[$idx] = $rank++;
        }
        // Ambil semua decision results untuk ditampilkan
        $decisionResults = DecisionResult::orderBy('created_at', 'asc')->get();
        $allBeneficiaryIds = ClusteringResult::pluck('beneficiary_id')->toArray();
        $usedBeneficiaryIds = [];
        $decisionResultsWithTotal = $decisionResults->map(function($result) use (&$usedBeneficiaryIds, $allBeneficiaryIds) {
            // Ambil semua beneficiary yang sudah pernah dapat bantuan sebelum keputusan ini
            $currentBeneficiaryIds = $result->items()->pluck('beneficiary_id')->toArray();
            $available = array_diff($allBeneficiaryIds, $usedBeneficiaryIds);
            $result->total_available = count($available);
            // Setelah ini, beneficiary yang sudah dapat bantuan ditandai
            $usedBeneficiaryIds = array_merge($usedBeneficiaryIds, $currentBeneficiaryIds);
            return $result;
        });
        
        return view('decision.index', [
            'clusterCounts' => $clusterCounts,
            'decisionResults' => $decisionResultsWithTotal,
            'clusterMeans' => $clusterMeans,
            'avgSilhouettes' => $avgSilhouettes,
            'rankMap' => $rankMap
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

        // Ambil rata-rata fitur per cluster untuk prioritas
        $clusterMeans = [];
        foreach ($clusterCounts as $cluster => $count) {
            $means = ClusteringResult::where('cluster', $cluster)
                ->join('beneficiaries', 'clustering_results.beneficiary_id', '=', 'beneficiaries.id')
                ->select(
                    DB::raw('AVG(usia) as usia'),
                    DB::raw('AVG(jumlah_anak) as jumlah_anak'),
                    DB::raw('AVG(kelayakan_rumah) as kelayakan_rumah'),
                    DB::raw('AVG(pendapatan_perbulan) as pendapatan')
                )
                ->first();
            $clusterMeans[$cluster] = [
                'usia' => (float) $means->usia,
                'jumlah_anak' => (float) $means->jumlah_anak,
                'kelayakan_rumah' => (float) $means->kelayakan_rumah,
                'pendapatan' => (float) $means->pendapatan,
            ];
        }

        // Hitung skor kebutuhan bantuan untuk setiap cluster
        $pendapatanArr = array_column($clusterMeans, 'pendapatan');
        $kelayakanArr = array_column($clusterMeans, 'kelayakan_rumah');
        $jumlahAnakArr = array_column($clusterMeans, 'jumlah_anak');
        $needScores = [];
        foreach ($clusterMeans as $idx => $mean) {
            $pendapatan = $mean['pendapatan'] ?? 0;
            $kelayakan = $mean['kelayakan_rumah'] ?? 0;
            $jumlah_anak = $mean['jumlah_anak'] ?? 0;
            $score = (max($pendapatanArr) - $pendapatan)
                + (max($kelayakanArr) - $kelayakan)
                + ($jumlah_anak - min($jumlahAnakArr));
            $needScores[$idx] = $score;
        }
        arsort($needScores);
        $rankMap = [];
        $rank = 1;
        foreach(array_keys($needScores) as $idx) {
            $rankMap[$idx] = $rank++;
        }

        return view('decision.create', [
            'clusterCounts' => $clusterCounts,
            'rankMap' => $rankMap,
            'decisionResults' => \App\Models\DecisionResult::all(), // Tambahkan baris ini
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
            'cluster' => 'required',
            'count' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'excluded_decisions' => 'nullable|array', // validasi baru
            'excluded_decisions.*' => 'integer|exists:decision_results,id',
        ]);

        $cluster = $validated['cluster'];
        $totalNeeded = $validated['count'];
        $beneficiaryIds = [];
        $excludedBeneficiaryIds = [];
        // Ambil semua beneficiary_id dari keputusan yang dikecualikan
        if (!empty($validated['excluded_decisions'])) {
            $excludedBeneficiaryIds = \App\Models\DecisionResultItem::whereIn('decision_result_id', $validated['excluded_decisions'])
                ->pluck('beneficiary_id')
                ->unique()
                ->toArray();
        }

        if ($cluster === 'all') {
            // Hitung prioritas (rankMap) seperti di create()
            $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as count'))
                ->groupBy('cluster')
                ->pluck('count', 'cluster')
                ->toArray();
            $clusterMeans = [];
            foreach ($clusterCounts as $cl => $count) {
                $means = ClusteringResult::where('cluster', $cl)
                    ->join('beneficiaries', 'clustering_results.beneficiary_id', '=', 'beneficiaries.id')
                    ->select(
                        DB::raw('AVG(usia) as usia'),
                        DB::raw('AVG(jumlah_anak) as jumlah_anak'),
                        DB::raw('AVG(kelayakan_rumah) as kelayakan_rumah'),
                        DB::raw('AVG(pendapatan_perbulan) as pendapatan')
                    )
                    ->first();
                $clusterMeans[$cl] = [
                    'usia' => (float) $means->usia,
                    'jumlah_anak' => (float) $means->jumlah_anak,
                    'kelayakan_rumah' => (float) $means->kelayakan_rumah,
                    'pendapatan' => (float) $means->pendapatan,
                ];
            }
            $pendapatanArr = array_column($clusterMeans, 'pendapatan');
            $kelayakanArr = array_column($clusterMeans, 'kelayakan_rumah');
            $jumlahAnakArr = array_column($clusterMeans, 'jumlah_anak');
            $needScores = [];
            foreach ($clusterMeans as $idx => $mean) {
                $pendapatan = $mean['pendapatan'] ?? 0;
                $kelayakan = $mean['kelayakan_rumah'] ?? 0;
                $jumlah_anak = $mean['jumlah_anak'] ?? 0;
                $score = (max($pendapatanArr) - $pendapatan)
                    + (max($kelayakanArr) - $kelayakan)
                    + ($jumlah_anak - min($jumlahAnakArr));
                $needScores[$idx] = $score;
            }
            arsort($needScores);
            $prioritasClusters = array_keys($needScores);

            // Ambil penerima dari prioritas 1, 2, dst
            $remaining = $totalNeeded;
            foreach ($prioritasClusters as $cl) {
                if ($remaining <= 0) break;
                $ids = ClusteringResult::where('cluster', $cl)
                    ->whereNotIn('beneficiary_id', $beneficiaryIds)
                    ->whereNotIn('beneficiary_id', $excludedBeneficiaryIds) // filter baru
                    ->inRandomOrder()
                    ->limit($remaining)
                    ->pluck('beneficiary_id')
                    ->toArray();
                $beneficiaryIds = array_merge($beneficiaryIds, $ids);
                $remaining = $totalNeeded - count($beneficiaryIds);
            }
            if (count($beneficiaryIds) < $totalNeeded) {
                return back()->withErrors(['count' => "Jumlah yang dipilih melebihi total seluruh cluster (" . count($beneficiaryIds) . ")"])->withInput();
            }
        } else {
            // Validasi cluster harus integer dan ada di data
            if (!is_numeric($cluster) || !ClusteringResult::where('cluster', $cluster)->exists()) {
                return back()->withErrors(['cluster' => 'Cluster tidak valid'])->withInput();
            }
            $clusterCount = ClusteringResult::where('cluster', $cluster)
                ->whereNotIn('beneficiary_id', $excludedBeneficiaryIds) // filter baru
                ->count();
            if ($totalNeeded > $clusterCount) {
                return back()->withErrors(['count' => "Jumlah yang dipilih melebihi jumlah anggota dalam cluster ({$clusterCount})"])->withInput();
            }
            $beneficiaryIds = ClusteringResult::where('cluster', $cluster)
                ->whereNotIn('beneficiary_id', $excludedBeneficiaryIds) // filter baru
                ->inRandomOrder()
                ->limit($totalNeeded)
                ->pluck('beneficiary_id')
                ->toArray();
        }

        DB::beginTransaction();
        try {
            // Simpan decision result
            $decisionResult = DecisionResult::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'cluster' => $cluster === 'all' ? -1 : $cluster,
                'count' => $totalNeeded,
                'notes' => $validated['notes'] ?? null,
            ]);

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
        // Hitung total_available seperti di index
        $allBeneficiaryIds = \App\Models\ClusteringResult::pluck('beneficiary_id')->toArray();
        $usedBeneficiaryIds = [];
        $decisionResults = DecisionResult::orderBy('created_at', 'asc')->get();
        foreach ($decisionResults as $result) {
            $currentBeneficiaryIds = $result->items()->pluck('beneficiary_id')->toArray();
            $available = array_diff($allBeneficiaryIds, $usedBeneficiaryIds);
            if ($result->id == $decisionResult->id) {
                $decisionResult->total_available = count($available);
                break;
            }
            $usedBeneficiaryIds = array_merge($usedBeneficiaryIds, $currentBeneficiaryIds);
        }
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