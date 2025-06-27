<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\ClusteringResult;
use App\Models\NormalizationResult;
use Illuminate\Http\Request;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Helpers\Stats;

class StatisticController extends Controller
{
    public function index()
    {
        $data = Beneficiary::all(['id', 'nama', 'nik', 'alamat', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
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
                $beneficiary = $data->firstWhere('id', $row->beneficiary_id);
                if ($beneficiary) {
                    $result[$row->cluster][] = $beneficiary;
                    $scatterData[] = [
                        'usia' => (float) $beneficiary->usia,
                        'jumlah_anak' => (float) $beneficiary->jumlah_anak,
                        'kelayakan_rumah' => is_numeric($beneficiary->kelayakan_rumah) ? (float) $beneficiary->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $beneficiary->kelayakan_rumah),
                        'pendapatan' => (float) $beneficiary->pendapatan_perbulan,
                        'cluster' => (int) $row->cluster,
                        'nama' => $beneficiary->nama,
                        'silhouette' => $row->silhouette,
                        'nik' => $beneficiary->nik,
                        'alamat' => $beneficiary->alamat,
                    ];
                }
            }
        } else {
            // Belum ada hasil cluster, lakukan normalisasi dan clustering lalu simpan
            
            // Ekstrak data yang akan dinormalisasi
            $usiaValues = $data->pluck('usia')->map(function($item) {
                return (float) $item;
            })->toArray();
            
            $anakValues = $data->pluck('jumlah_anak')->map(function($item) {
                return (float) $item;
            })->toArray();
            
            $rumahValues = $data->pluck('kelayakan_rumah')->map(function($item) {
                return is_numeric($item) ? (float) $item : (float) preg_replace('/[^0-9.]/', '', $item);
            })->toArray();
            
            $pendapatanValues = $data->pluck('pendapatan_perbulan')->map(function($item) {
                return (float) $item;
            })->toArray();
            
            // Normalisasi data menggunakan Min-Max scaling
            $normalizedData = $this->normalizeData($data, $usiaValues, $anakValues, $rumahValues, $pendapatanValues);
            
            // Gunakan data hasil normalisasi untuk K-Means Clustering
            $samples = collect($normalizedData)->map(function ($item) {
                return [
                    $item['usia_normalized'],
                    $item['jumlah_anak_normalized'],
                    $item['kelayakan_rumah_normalized'],
                    $item['pendapatan_perbulan_normalized'],
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
                    'nik' => $row->nik,
                    'alamat' => $row->alamat,
                ];
                // Simpan ke tabel clustering_results
                ClusteringResult::updateOrCreate([
                    'beneficiary_id' => $row->id
                ], [
                    'cluster' => $labels[$i],
                    'silhouette' => $silhouetteScores[$i]
                ]);
                
                // Simpan hasil normalisasi
                NormalizationResult::updateOrCreate([
                    'beneficiary_id' => $row->id
                ], [
                    'usia_normalized' => $normalizedData[$i]['usia_normalized'],
                    'jumlah_anak_normalized' => $normalizedData[$i]['jumlah_anak_normalized'],
                    'kelayakan_rumah_normalized' => $normalizedData[$i]['kelayakan_rumah_normalized'],
                    'pendapatan_perbulan_normalized' => $normalizedData[$i]['pendapatan_perbulan_normalized']
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
        $data = Beneficiary::all(['id']);
        if ($data->count() < 3) {
            return redirect()->route('statistic.index')->with('message', 'Data kurang dari 3, tidak bisa melakukan clustering.');
        }
        
        // Hapus semua hasil clustering lama
        ClusteringResult::truncate();
        // Hapus semua hasil normalisasi lama
        NormalizationResult::truncate();
        
        // Redirect ke halaman index yang akan melakukan clustering ulang
        return redirect()->route('statistic.index')->with('success', 'Proses clustering sedang dihitung ulang.');
    }

    public function showCluster($cluster)
    {
        $clusterIndex = (int) $cluster - 1;
        
        if ($clusterIndex < 0 || $clusterIndex > 2) {
            return redirect()->route('statistic.index')->with('error', 'Cluster tidak valid.');
        }
        
        $data = Beneficiary::join('clustering_results', 'beneficiaries.id', '=', 'clustering_results.beneficiary_id')
            ->where('clustering_results.cluster', $clusterIndex)
            ->get();
            
        if ($data->isEmpty()) {
            return redirect()->route('statistic.index')->with('error', 'Tidak ada data dalam cluster ini.');
        }
        
        // Ambil data normalisasi
        $normalizedData = NormalizationResult::whereIn('beneficiary_id', $data->pluck('id'))->get()
            ->keyBy('beneficiary_id');
        
        // Hitung statistik untuk cluster ini
        $usiaValues = $data->pluck('usia')->map(function($item) { return (float) $item; })->toArray();
        $anakValues = $data->pluck('jumlah_anak')->map(function($item) { return (float) $item; })->toArray();
        $rumahValues = $data->pluck('kelayakan_rumah')->map(function($item) { 
            return is_numeric($item) ? (float) $item : (float) preg_replace('/[^0-9.]/', '', $item);
        })->toArray();
        $pendapatanValues = $data->pluck('pendapatan_perbulan')->map(function($item) { return (float) $item; })->toArray();
        
        // Hitung silhouette stats jika ada
        $silhouetteValues = $data->pluck('silhouette')->filter()->toArray();
        $silhouetteStats = !empty($silhouetteValues) ? [
            'min' => min($silhouetteValues),
            'max' => max($silhouetteValues),
            'mean' => Stats::mean($silhouetteValues),
            'median' => Stats::median($silhouetteValues),
            'std' => sqrt(Stats::variance($silhouetteValues)),
        ] : null;
        
        // Hitung statistik untuk cluster ini
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
            ]
        ];
        
        return view('penerima.cluster_detail', [
            'clusterIndex' => $clusterIndex,
            'cluster' => $data,
            'normalizedData' => $normalizedData,
            'clusterStats' => $clusterStats,
            'silhouetteStats' => $silhouetteStats,
            'total' => $data->count()
        ]);
    }

    /**
     * Menormalisasi data menggunakan Min-Max scaling
     * 
     * @param Collection $data Data yang akan dinormalisasi
     * @param array $usiaValues Array nilai usia
     * @param array $anakValues Array nilai jumlah anak
     * @param array $rumahValues Array nilai kelayakan rumah
     * @param array $pendapatanValues Array nilai pendapatan
     * @return array Data hasil normalisasi
     */
    private function normalizeData($data, $usiaValues, $anakValues, $rumahValues, $pendapatanValues)
    {
        // Cari nilai min dan max untuk setiap fitur
        $usiaMin = min($usiaValues);
        $usiaMax = max($usiaValues);
        $anakMin = min($anakValues);
        $anakMax = max($anakValues);
        $rumahMin = min($rumahValues);
        $rumahMax = max($rumahValues);
        $pendapatanMin = min($pendapatanValues);
        $pendapatanMax = max($pendapatanValues);

        // Normalisasi dengan Min-Max scaling: (x - min) / (max - min)
        $normalizedData = [];
        foreach ($data as $i => $row) {
            $usiaNormalized = $usiaMax > $usiaMin ? 
                ($usiaValues[$i] - $usiaMin) / ($usiaMax - $usiaMin) : 0;
                
            $anakNormalized = $anakMax > $anakMin ? 
                ($anakValues[$i] - $anakMin) / ($anakMax - $anakMin) : 0;
                
            $rumahNormalized = $rumahMax > $rumahMin ? 
                ($rumahValues[$i] - $rumahMin) / ($rumahMax - $rumahMin) : 0;
                
            $pendapatanNormalized = $pendapatanMax > $pendapatanMin ? 
                ($pendapatanValues[$i] - $pendapatanMin) / ($pendapatanMax - $pendapatanMin) : 0;

            $normalizedData[] = [
                'beneficiary_id' => $row->id,
                'usia_normalized' => $usiaNormalized,
                'jumlah_anak_normalized' => $anakNormalized,
                'kelayakan_rumah_normalized' => $rumahNormalized,
                'pendapatan_perbulan_normalized' => $pendapatanNormalized
            ];
        }

        return $normalizedData;
    }
} 