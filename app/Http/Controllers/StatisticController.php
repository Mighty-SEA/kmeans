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
                        'silhouette' => $row->silhouette,
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
            
            // Hitung silhouette score
            $silhouetteScores = $this->calculateSilhouetteScores($samples, $labels);
            
            foreach ($data as $i => $row) {
                $result[$labels[$i]][] = $row;
                $scatterData[] = [
                    'usia' => (float) $row->usia,
                    'jumlah_anak' => (float) $row->jumlah_anak,
                    'kelayakan_rumah' => is_numeric($row->kelayakan_rumah) ? (float) $row->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $row->kelayakan_rumah),
                    'pendapatan' => (float) $row->pendapatan_perbulan,
                    'cluster' => (int) $labels[$i],
                    'nama' => $row->nama,
                    'silhouette' => $silhouetteScores[$i],
                ];
                // Simpan ke tabel clustering_results
                ClusteringResult::updateOrCreate([
                    'penerima_id' => $row->id
                ], [
                    'cluster' => $labels[$i],
                    'silhouette' => $silhouetteScores[$i]
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
        
        // Hitung rata-rata silhouette score per cluster
        $clusterSilhouettes = [];
        foreach ($scatterData as $item) {
            if (!isset($clusterSilhouettes[$item['cluster']])) {
                $clusterSilhouettes[$item['cluster']] = [];
            }
            if (isset($item['silhouette'])) {
                $clusterSilhouettes[$item['cluster']][] = $item['silhouette'];
            }
        }
        
        $avgSilhouettes = [];
        foreach ($clusterSilhouettes as $cluster => $scores) {
            if (!empty($scores)) {
                $avgSilhouettes[$cluster] = Stats::mean($scores);
            } else {
                $avgSilhouettes[$cluster] = 0;
            }
        }
        
        // Hitung rata-rata silhouette score keseluruhan
        $allSilhouettes = [];
        foreach ($clusterSilhouettes as $scores) {
            $allSilhouettes = array_merge($allSilhouettes, $scores);
        }
        $overallSilhouette = !empty($allSilhouettes) ? Stats::mean($allSilhouettes) : 0;
        
        return view('penerima.statistic', [
            'clusters' => $result,
            'message' => null,
            'scatterData' => $scatterData,
            'clusterCounts' => $clusterCounts,
            'clusterMeans' => $clusterMeans,
            'clusterStats' => $clusterStats,
            'avgSilhouettes' => $avgSilhouettes,
            'overallSilhouette' => $overallSilhouette,
        ]);
    }

    /**
     * Menghitung Silhouette Score untuk setiap data point
     * 
     * @param array $samples Data samples
     * @param array $labels Cluster labels
     * @return array Silhouette scores
     */
    private function calculateSilhouetteScores(array $samples, array $labels): array
    {
        $silhouettes = [];
        $clusterPoints = [];
        
        // Kelompokkan points berdasarkan cluster
        foreach ($labels as $i => $cluster) {
            if (!isset($clusterPoints[$cluster])) {
                $clusterPoints[$cluster] = [];
            }
            $clusterPoints[$cluster][] = $samples[$i];
        }
        
        // Hitung silhouette score untuk setiap point
        foreach ($samples as $i => $sample) {
            $a = $this->calculateAverageDistance($sample, $clusterPoints[$labels[$i]]);
            
            $b = PHP_FLOAT_MAX;
            foreach ($clusterPoints as $cluster => $points) {
                if ($cluster != $labels[$i]) {
                    $distance = $this->calculateAverageDistance($sample, $points);
                    $b = min($b, $distance);
                }
            }
            
            if ($a == 0 && $b == 0) {
                $silhouettes[$i] = 0;
            } else {
                $silhouettes[$i] = ($b - $a) / max($a, $b);
            }
        }
        
        return $silhouettes;
    }
    
    /**
     * Menghitung rata-rata jarak antara satu point dengan kumpulan points lainnya
     * 
     * @param array $point Data point
     * @param array $points Kumpulan data points
     * @return float Rata-rata jarak
     */
    private function calculateAverageDistance(array $point, array $points): float
    {
        if (count($points) <= 1) {
            return 0;
        }
        
        $sum = 0;
        $count = 0;
        
        foreach ($points as $otherPoint) {
            // Skip jika point yang sama
            if ($point === $otherPoint) {
                continue;
            }
            
            $sum += $this->euclideanDistance($point, $otherPoint);
            $count++;
        }
        
        return $count > 0 ? $sum / $count : 0;
    }
    
    /**
     * Menghitung jarak Euclidean antara dua point
     * 
     * @param array $point1 Data point 1
     * @param array $point2 Data point 2
     * @return float Jarak Euclidean
     */
    private function euclideanDistance(array $point1, array $point2): float
    {
        $sum = 0;
        
        foreach ($point1 as $i => $value) {
            $sum += pow($value - $point2[$i], 2);
        }
        
        return sqrt($sum);
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
            $silhouettes = [0 => [], 1 => [], 2 => []];
            foreach ($clustering as $row) {
                $penerima = $data->firstWhere('id', $row->penerima_id);
                if ($penerima) {
                    $result[$row->cluster][] = $penerima;
                    if ($row->silhouette !== null) {
                        $silhouettes[$row->cluster][] = $row->silhouette;
                    }
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
            
            // Hitung silhouette score
            $silhouetteScores = $this->calculateSilhouetteScores($samples, $labels);
            $silhouettes = [0 => [], 1 => [], 2 => []];
            
            foreach ($data as $i => $row) {
                $result[$labels[$i]][] = $row;
                $silhouettes[$labels[$i]][] = $silhouetteScores[$i];
                
                // Simpan ke tabel clustering_results
                ClusteringResult::updateOrCreate([
                    'penerima_id' => $row->id
                ], [
                    'cluster' => $labels[$i],
                    'silhouette' => $silhouetteScores[$i]
                ]);
            }
        }
        
        $selectedCluster = $result[$cluster] ?? [];
        $selectedSilhouettes = $silhouettes[$cluster] ?? [];
        
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
        
        // Hitung statistik silhouette
        $silhouetteStats = [];
        if (!empty($selectedSilhouettes)) {
            $silhouetteStats = [
                'min' => min($selectedSilhouettes),
                'max' => max($selectedSilhouettes),
                'mean' => Stats::mean($selectedSilhouettes),
                'median' => Stats::median($selectedSilhouettes),
                'std' => sqrt(Stats::variance($selectedSilhouettes)),
            ];
        }
        
        return view('penerima.cluster_detail', [
            'cluster' => $selectedCluster,
            'clusterIndex' => $cluster,
            'total' => count($selectedCluster),
            'clusterStats' => $clusterStats,
            'silhouetteStats' => $silhouetteStats,
        ]);
    }
} 