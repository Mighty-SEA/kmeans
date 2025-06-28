<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
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
            return view('beneficiaries.statistics', [
                'clusters' => [],
                'message' => 'Data kurang dari 3, tidak bisa melakukan clustering.'
            ]);
        }
        
        // Ambil hasil cluster dari tabel clustering_results jika sudah ada
        $clustering = ClusteringResult::all();
        $clustered = $clustering->count() === $data->count();
        
        if ($clustered) {
            // Sudah ada hasil cluster, gunakan data ini
            $result = [0 => [], 1 => [], 2 => []];
            $scatterData = [];
            $clusterCount = $clustering->max('cluster') + 1; // Mendapatkan jumlah cluster (0-indexed)
            
            // Inisialisasi array hasil dengan jumlah cluster yang sesuai
            $result = [];
            for ($i = 0; $i < $clusterCount; $i++) {
                $result[$i] = [];
            }
            
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
            
            return view('beneficiaries.statistics', [
                'clusters' => $result,
                'message' => null,
                'scatterData' => $scatterData,
                'clusterCounts' => $clusterCounts,
                'clusterMeans' => $clusterMeans,
                'clusterStats' => $clusterStats,
                'avgSilhouettes' => $avgSilhouettes,
                'overallSilhouette' => $overallSilhouette,
                'clustered' => $clustered,
                'clusterCount' => $clusterCount,
            ]);
        } else {
            // Belum ada hasil cluster, tampilkan halaman tanpa clustering
            return view('beneficiaries.statistics', [
                'clusters' => [],
                'message' => null,
                'scatterData' => [],
                'clusterCounts' => [],
                'clusterMeans' => [],
                'clusterStats' => [],
                'avgSilhouettes' => [],
                'overallSilhouette' => 0,
                'clustered' => false,
                'dataCount' => $data->count(),
            ]);
        }
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

    public function recalculate(Request $request)
    {
        $data = Beneficiary::all(['id']);
        if ($data->count() < 3) {
            return redirect()->route('statistic.index')->with('error', 'Data kurang dari 3, tidak bisa melakukan clustering.');
        }
        
        // Validasi input
        $validated = $request->validate([
            'num_clusters' => 'required|integer|min:2|max:10',
            'normalization' => 'required|in:none,minmax,standard,robust',
        ]);
        
        $numClusters = $validated['num_clusters'];
        $normalization = $validated['normalization'];
        
        // Hapus semua hasil clustering lama
        ClusteringResult::truncate();
        // Hapus semua hasil normalisasi lama
        NormalizationResult::truncate();
        
        // Ubah pesan sukses untuk memberikan informasi parameter yang digunakan
        $normalizationName = [
            'none' => 'Tanpa Normalisasi',
            'minmax' => 'Min-Max',
            'standard' => 'Standard (Z-Score)',
            'robust' => 'Robust'
        ][$normalization] ?? 'Robust';
        
        // Panggil doClustering dengan parameter yang sama
        return $this->doClustering($request, "Clustering berhasil dihitung ulang dengan $numClusters cluster dan normalisasi $normalizationName.");
    }

    public function showCluster($cluster, Request $request)
    {
        $clusterIndex = (int) $cluster - 1;
        
        // Periksa apakah cluster valid
        $maxCluster = ClusteringResult::max('cluster');
        if ($maxCluster === null || $clusterIndex < 0 || $clusterIndex > $maxCluster) {
            return redirect()->route('statistic.index')->with('error', 'Cluster tidak valid.');
        }
        
        $search = $request->input('search');
        
        $query = Beneficiary::join('clustering_results', 'beneficiaries.id', '=', 'clustering_results.beneficiary_id')
            ->where('clustering_results.cluster', $clusterIndex);
            
        // Tambahkan pencarian jika ada
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        
        $data = $query->paginate(10)->withQueryString(); // Menambahkan withQueryString agar pagination tetap membawa parameter search
            
        if ($data->isEmpty() && !$search) {
            return redirect()->route('statistic.index')->with('error', 'Tidak ada data dalam cluster ini.');
        }
        
        // Ambil semua data untuk statistik (tanpa pagination dan search)
        $allClusterData = Beneficiary::join('clustering_results', 'beneficiaries.id', '=', 'clustering_results.beneficiary_id')
            ->where('clustering_results.cluster', $clusterIndex)
            ->get();
        
        // Ambil data normalisasi
        $normalizedData = NormalizationResult::whereIn('beneficiary_id', $allClusterData->pluck('id'))->get()
            ->keyBy('beneficiary_id');
        
        // Hitung statistik untuk cluster ini
        $usiaValues = $allClusterData->pluck('usia')->map(function($item) { return (float) $item; })->toArray();
        $anakValues = $allClusterData->pluck('jumlah_anak')->map(function($item) { return (float) $item; })->toArray();
        $rumahValues = $allClusterData->pluck('kelayakan_rumah')->map(function($item) { 
            return is_numeric($item) ? (float) $item : (float) preg_replace('/[^0-9.]/', '', $item);
        })->toArray();
        $pendapatanValues = $allClusterData->pluck('pendapatan_perbulan')->map(function($item) { return (float) $item; })->toArray();
        
        // Hitung silhouette stats jika ada
        $silhouetteValues = $allClusterData->pluck('silhouette')->filter()->toArray();
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
        
        return view('beneficiaries.cluster_detail', [
            'clusterIndex' => $clusterIndex,
            'cluster' => $data,
            'normalizedData' => $normalizedData,
            'clusterStats' => $clusterStats,
            'silhouetteStats' => $silhouetteStats,
            'total' => $allClusterData->count(),
            'search' => $search
        ]);
    }

    /**
     * Menormalisasi data menggunakan Robust Scaler
     *
     * @param \Illuminate\Support\Collection $data Data yang akan dinormalisasi
     * @param array $usiaValues Array nilai usia
     * @param array $anakValues Array nilai jumlah anak
     * @param array $rumahValues Array nilai kelayakan rumah
     * @param array $pendapatanValues Array nilai pendapatan
     * @return array Data hasil normalisasi
     */
    private function normalizeData($data, array $usiaValues, array $anakValues, array $rumahValues, array $pendapatanValues)
    {
        // Normalisasi dengan Robust Scaler dari Rubix ML
        $features = [];
        foreach ($data as $i => $row) {
            $features[] = [
                $usiaValues[$i],
                $anakValues[$i],
                $rumahValues[$i],
                $pendapatanValues[$i],
            ];
        }
        $dataset = new \Rubix\ML\Datasets\Unlabeled($features);
        $transformer = new \Rubix\ML\Transformers\RobustStandardizer(true); // center = true
        $transformer->fit($dataset);
        $features = $dataset->samples(); // ambil array 2D dari dataset
        $transformer->transform($features); // transform array, hasilnya by reference
        $normalized = $features;

        // Simpan hasil normalisasi ke array asosiatif
        $normalizedData = [];
        foreach ($data as $i => $row) {
            $normalizedData[] = [
                'beneficiary_id' => $row->id,
                'usia_normalized' => $normalized[$i][0],
                'jumlah_anak_normalized' => $normalized[$i][1],
                'kelayakan_rumah_normalized' => $normalized[$i][2],
                'pendapatan_perbulan_normalized' => $normalized[$i][3],
            ];
        }

        return $normalizedData;
    }

    /**
     * Melakukan proses clustering
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doClustering(Request $request, $successMessage = null)
    {
        // Validasi input
        $validated = $request->validate([
            'num_clusters' => 'required|integer|min:2|max:10',
            'normalization' => 'required|in:none,minmax,standard,robust',
        ]);
        
        $numClusters = $validated['num_clusters'];
        $normalization = $validated['normalization'];
        
        $data = Beneficiary::all(['id', 'nama', 'nik', 'alamat', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
        if ($data->count() < $numClusters) {
            return redirect()->route('statistic.index')->with('error', 'Jumlah data lebih sedikit dari jumlah cluster yang diminta.');
        }
        
        // Hapus hasil cluster lama
        ClusteringResult::truncate();
        NormalizationResult::truncate();
        
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
        
        // Pilih metode normalisasi
        $features = [];
        foreach ($data as $i => $row) {
            $features[] = [
                $usiaValues[$i],
                $anakValues[$i],
                $rumahValues[$i],
                $pendapatanValues[$i],
            ];
        }
        $normalized = $features;
        if ($normalization === 'minmax') {
            $dataset = new \Rubix\ML\Datasets\Unlabeled($features);
            $transformer = new \Rubix\ML\Transformers\MinMaxNormalizer();
            $transformer->fit($dataset);
            $normalized = $dataset->samples();
            $transformer->transform($normalized);
        } elseif ($normalization === 'standard') {
            $dataset = new \Rubix\ML\Datasets\Unlabeled($features);
            $transformer = new \Rubix\ML\Transformers\ZScaleStandardizer();
            $transformer->fit($dataset);
            $normalized = $dataset->samples();
            $transformer->transform($normalized);
        } elseif ($normalization === 'robust') {
            $dataset = new \Rubix\ML\Datasets\Unlabeled($features);
            $transformer = new \Rubix\ML\Transformers\RobustStandardizer(true);
            $transformer->fit($dataset);
            $normalized = $dataset->samples();
            $transformer->transform($normalized);
        }
        // Simpan hasil normalisasi ke array asosiatif
        $normalizedData = [];
        foreach ($data as $i => $row) {
            $normalizedData[] = [
                'beneficiary_id' => $row->id,
                'usia_normalized' => $normalized[$i][0],
                'jumlah_anak_normalized' => $normalized[$i][1],
                'kelayakan_rumah_normalized' => $normalized[$i][2],
                'pendapatan_perbulan_normalized' => $normalized[$i][3],
            ];
        }
        // Gunakan data hasil normalisasi untuk K-Means Clustering
        $samples = collect($normalizedData)->map(function ($item) {
            return [
                $item['usia_normalized'],
                $item['jumlah_anak_normalized'],
                $item['kelayakan_rumah_normalized'],
                $item['pendapatan_perbulan_normalized'],
            ];
        })->toArray();
        $clusterer = new KMeans($numClusters);
        $clusterer->train(new Unlabeled($samples));
        $labels = $clusterer->predict(new Unlabeled($samples));
        // Hitung silhouette score
        $silhouetteScores = $this->calculateSilhouetteScores($samples, $labels);
        foreach ($data as $i => $row) {
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
        return redirect()->route('statistic.index')->with('success', $successMessage ?? "Clustering berhasil dilakukan dengan $numClusters cluster.");
    }
} 