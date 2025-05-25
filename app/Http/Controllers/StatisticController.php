<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\ClusteringResult;
use Illuminate\Http\Request;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;

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
        // Bar chart: rata-rata tiap fitur per cluster
        $clusterMeans = [];
        foreach ($result as $key => $cluster) {
            $count = count($cluster);
            $sum = [
                'usia' => 0,
                'jumlah_anak' => 0,
                'kelayakan_rumah' => 0,
                'pendapatan' => 0,
            ];
            foreach ($cluster as $row) {
                $sum['usia'] += (float) $row->usia;
                $sum['jumlah_anak'] += (float) $row->jumlah_anak;
                $sum['kelayakan_rumah'] += is_numeric($row->kelayakan_rumah) ? (float) $row->kelayakan_rumah : (float) preg_replace('/[^0-9.]/', '', $row->kelayakan_rumah);
                $sum['pendapatan'] += (float) $row->pendapatan_perbulan;
            }
            $clusterMeans[$key] = [
                'usia' => $count ? $sum['usia'] / $count : 0,
                'jumlah_anak' => $count ? $sum['jumlah_anak'] / $count : 0,
                'kelayakan_rumah' => $count ? $sum['kelayakan_rumah'] / $count : 0,
                'pendapatan' => $count ? $sum['pendapatan'] / $count : 0,
            ];
        }
        // Statistik ringkasan per cluster
        function getStats($arr) {
            sort($arr);
            $count = count($arr);
            $mean = $count ? array_sum($arr) / $count : 0;
            $min = $count ? $arr[0] : 0;
            $max = $count ? $arr[$count-1] : 0;
            $median = $count ? ($count % 2 ? $arr[$count/2|0] : ($arr[$count/2-1] + $arr[$count/2]) / 2) : 0;
            $std = $count ? sqrt(array_sum(array_map(fn($v) => pow($v-$mean,2), $arr))/$count) : 0;
            return compact('min','max','mean','median','std');
        }
        $clusterStats = [];
        foreach ($result as $key => $cluster) {
            $arrUsia = array_map(fn($r)=>(float)$r->usia, $cluster);
            $arrAnak = array_map(fn($r)=>(float)$r->jumlah_anak, $cluster);
            $arrRumah = array_map(fn($r)=>is_numeric($r->kelayakan_rumah)?(float)$r->kelayakan_rumah:(float)preg_replace('/[^0-9.]/','',$r->kelayakan_rumah), $cluster);
            $arrPendapatan = array_map(fn($r)=>(float)$r->pendapatan_perbulan, $cluster);
            $clusterStats[$key] = [
                'usia' => getStats($arrUsia),
                'jumlah_anak' => getStats($arrAnak),
                'kelayakan_rumah' => getStats($arrRumah),
                'pendapatan' => getStats($arrPendapatan),
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
        $data = Penerima::all(['nama', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
        if ($data->count() < 3) {
            return redirect()->route('statistic.index')->with('message', 'Data kurang dari 3, tidak bisa melakukan clustering.');
        }
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
        $result = [];
        foreach ($data as $i => $row) {
            $result[$labels[$i]][] = $row;
        }
        $selectedCluster = $result[$cluster] ?? [];
        return view('penerima.cluster_detail', [
            'cluster' => $selectedCluster,
            'clusterIndex' => $cluster,
            'total' => count($selectedCluster)
        ]);
    }
} 