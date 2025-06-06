<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\ClusteringResult;
use Illuminate\Http\Request;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Helpers\Stats;

class StatisticController extends Controller
{
    public function index()
    {
        $data = Penerima::all(['id', 'nama', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
        if ($data->count() < 3) {
            return view('penerima.statistic', [
                'clusters' => [],
                'message' => 'Data kurang dari 3, tidak bisa melakukan clustering.'
            ]);
        }
        // Ambil hasil cluster dari tabel clustering_results jika sudah ada
        $clustering = ClusteringResult::all();
        if ($clustering->count() === $data->count()) {
            // Sudah ada hasil cluster, gunakan data ini
            $result = [0 => [], 1 => [], 2 => []];
            $scatterData = [];
            foreach ($clustering as $row) {
                $penerima = $data->firstWhere('id', $row->penerima_id);
                if ($penerima) {
                    $result[$row->cluster][] = $penerima;
                    $scatterData[] = [
                        'usia' => (float) $penerima->usia,
                        'jumlah_anak' => (float) $penerima->jumlah_anak,
                        'kelayakan_rumah' => is_numeric($penerima->kelayakan_rumah) ? (float) $penerima->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $penerima->kelayakan_rumah),
                        'pendapatan' => (float) $penerima->pendapatan_perbulan,
                        'cluster' => (int) $row->cluster,
                        'nama' => $penerima->nama,
                    ];
                }
            }
        } else {
            // Belum ada hasil cluster, lakukan clustering dan simpan
            $samples = $data->map(function ($item) {
                return [
                    (float) $item->usia,
                    (float) $item->jumlah_anak,
                    (float) $item->kelayakan_rumah,
                    (float) $item->pendapatan_perbulan,
                ];
            })->toArray();
            $clusterer = new KMeans(3);
            $clusterer->train(new Unlabeled($samples));
            $labels = $clusterer->predict(new Unlabeled($samples));
            $result = [0 => [], 1 => [], 2 => []];
            $scatterData = [];
            foreach ($data as $i => $row) {
                $result[$labels[$i]][] = $row;
                $scatterData[] = [
                    'usia' => (float) $row->usia,
                    'jumlah_anak' => (float) $row->jumlah_anak,
                    'kelayakan_rumah' => is_numeric($row->kelayakan_rumah) ? (float) $row->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $row->kelayakan_rumah),
                    'pendapatan' => (float) $row->pendapatan_perbulan,
                    'cluster' => (int) $labels[$i],
                    'nama' => $row->nama,
                ];
                // Simpan ke tabel clustering_results
                ClusteringResult::updateOrCreate([
                    'penerima_id' => $row->id
                ], [
                    'cluster' => $labels[$i]
                ]);
            }
        }
        // Pie chart: jumlah anggota per cluster
        $clusterCounts = array_map('count', $result);
        
        // Bar chart: rata-rata tiap fitur per cluster menggunakan Rubix ML
        $clusterMeans = [];
        foreach ($result as $key => $cluster) {
            if (count($cluster) === 0) {
                $clusterMeans[$key] = [
                    'usia' => 0,
                    'jumlah_anak' => 0,
                    'kelayakan_rumah' => 0,
                    'pendapatan' => 0,
                ];
                continue;
            }
            
            $usiaValues = array_map(fn($r) => (float) $r->usia, $cluster);
            $anakValues = array_map(fn($r) => (float) $r->jumlah_anak, $cluster);
            $rumahValues = array_map(fn($r) => is_numeric($r->kelayakan_rumah) ? (float) $r->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $r->kelayakan_rumah), $cluster);
            $pendapatanValues = array_map(fn($r) => (float) $r->pendapatan_perbulan, $cluster);
            
            $clusterMeans[$key] = [
                'usia' => Stats::mean($usiaValues),
                'jumlah_anak' => Stats::mean($anakValues),
                'kelayakan_rumah' => Stats::mean($rumahValues),
                'pendapatan' => Stats::mean($pendapatanValues),
            ];
        }
        
        // Statistik ringkasan per cluster menggunakan Rubix ML
        $clusterStats = [];
        foreach ($result as $key => $cluster) {
            if (count($cluster) === 0) {
                $clusterStats[$key] = [
                    'usia' => ['min' => 0, 'max' => 0, 'mean' => 0, 'median' => 0, 'std' => 0],
                    'jumlah_anak' => ['min' => 0, 'max' => 0, 'mean' => 0, 'median' => 0, 'std' => 0],
                    'kelayakan_rumah' => ['min' => 0, 'max' => 0, 'mean' => 0, 'median' => 0, 'std' => 0],
                    'pendapatan' => ['min' => 0, 'max' => 0, 'mean' => 0, 'median' => 0, 'std' => 0],
                ];
                continue;
            }
            
            $usiaValues = array_map(fn($r) => (float) $r->usia, $cluster);
            $anakValues = array_map(fn($r) => (float) $r->jumlah_anak, $cluster);
            $rumahValues = array_map(fn($r) => is_numeric($r->kelayakan_rumah) ? (float) $r->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $r->kelayakan_rumah), $cluster);
            $pendapatanValues = array_map(fn($r) => (float) $r->pendapatan_perbulan, $cluster);
            
            // Hitung statistik menggunakan Rubix ML Stats helper
            $clusterStats[$key] = [
                'usia' => [
                    'min' => min($usiaValues),
                    'max' => max($usiaValues),
                    'mean' => Stats::mean($usiaValues),
                    'median' => Stats::median($usiaValues),
                    'std' => sqrt(Stats::variance($usiaValues)),
                ],
                'jumlah_anak' => [
                    'min' => min($anakValues),
                    'max' => max($anakValues),
                    'mean' => Stats::mean($anakValues),
                    'median' => Stats::median($anakValues),
                    'std' => sqrt(Stats::variance($anakValues)),
                ],
                'kelayakan_rumah' => [
                    'min' => min($rumahValues),
                    'max' => max($rumahValues),
                    'mean' => Stats::mean($rumahValues),
                    'median' => Stats::median($rumahValues),
                    'std' => sqrt(Stats::variance($rumahValues)),
                ],
                'pendapatan' => [
                    'min' => min($pendapatanValues),
                    'max' => max($pendapatanValues),
                    'mean' => Stats::mean($pendapatanValues),
                    'median' => Stats::median($pendapatanValues),
                    'std' => sqrt(Stats::variance($pendapatanValues)),
                ],
            ];
        }
        
        return view('penerima.statistic', [
            'clusters' => $result,
            'message' => null,
            'scatterData' => $scatterData,
            'clusterCounts' => $clusterCounts,
            'clusterMeans' => $clusterMeans,
            'clusterStats' => $clusterStats,
        ]);
    }

    public function recalculate()
    {
        // Hapus semua hasil clustering lama
        ClusteringResult::truncate();
        // Lakukan clustering ulang (panggil ulang index)
        return redirect()->route('statistic.index');
    }

    public function showCluster($cluster)
    {
        $data = Penerima::all(['id', 'nama', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
        if ($data->count() < 3) {
            return redirect()->route('statistic.index')->with('message', 'Data kurang dari 3, tidak bisa melakukan clustering.');
        }
        
        // Ambil hasil cluster dari tabel clustering_results jika sudah ada
        $clustering = ClusteringResult::all();
        
        if ($clustering->count() === $data->count()) {
            // Sudah ada hasil cluster, gunakan data ini
            $result = [0 => [], 1 => [], 2 => []];
            foreach ($clustering as $row) {
                $penerima = $data->firstWhere('id', $row->penerima_id);
                if ($penerima) {
                    $result[$row->cluster][] = $penerima;
                }
            }
        } else {
            // Belum ada hasil cluster, lakukan clustering dan simpan
            $samples = $data->map(function ($item) {
                return [
                    (float) $item->usia,
                    (float) $item->jumlah_anak,
                    (float) $item->kelayakan_rumah,
                    (float) $item->pendapatan_perbulan,
                ];
            })->toArray();
            $clusterer = new KMeans(3);
            $clusterer->train(new Unlabeled($samples));
            $labels = $clusterer->predict(new Unlabeled($samples));
            $result = [0 => [], 1 => [], 2 => []];
            foreach ($data as $i => $row) {
                $result[$labels[$i]][] = $row;
                // Simpan ke tabel clustering_results
                ClusteringResult::updateOrCreate([
                    'penerima_id' => $row->id
                ], [
                    'cluster' => $labels[$i]
                ]);
            }
        }
        
        $selectedCluster = $result[$cluster] ?? [];
        
        // Hitung statistik untuk cluster yang dipilih menggunakan Rubix ML
        $clusterStats = [];
        if (!empty($selectedCluster)) {
            $usiaValues = array_map(fn($r) => (float) $r->usia, $selectedCluster);
            $anakValues = array_map(fn($r) => (float) $r->jumlah_anak, $selectedCluster);
            $rumahValues = array_map(fn($r) => is_numeric($r->kelayakan_rumah) ? (float) $r->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $r->kelayakan_rumah), $selectedCluster);
            $pendapatanValues = array_map(fn($r) => (float) $r->pendapatan_perbulan, $selectedCluster);
            
            $clusterStats = [
                'usia' => [
                    'min' => min($usiaValues),
                    'max' => max($usiaValues),
                    'mean' => Stats::mean($usiaValues),
                    'median' => Stats::median($usiaValues),
                    'std' => sqrt(Stats::variance($usiaValues)),
                ],
                'jumlah_anak' => [
                    'min' => min($anakValues),
                    'max' => max($anakValues),
                    'mean' => Stats::mean($anakValues),
                    'median' => Stats::median($anakValues),
                    'std' => sqrt(Stats::variance($anakValues)),
                ],
                'kelayakan_rumah' => [
                    'min' => min($rumahValues),
                    'max' => max($rumahValues),
                    'mean' => Stats::mean($rumahValues),
                    'median' => Stats::median($rumahValues),
                    'std' => sqrt(Stats::variance($rumahValues)),
                ],
                'pendapatan' => [
                    'min' => min($pendapatanValues),
                    'max' => max($pendapatanValues),
                    'mean' => Stats::mean($pendapatanValues),
                    'median' => Stats::median($pendapatanValues),
                    'std' => sqrt(Stats::variance($pendapatanValues)),
                ],
            ];
        }
        
        return view('penerima.cluster_detail', [
            'cluster' => $selectedCluster,
            'clusterIndex' => $cluster,
            'total' => count($selectedCluster),
            'clusterStats' => $clusterStats
        ]);
    }
} 