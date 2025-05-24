<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Http\Request;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;

class StatisticController extends Controller
{
    public function index()
    {
        // Ambil data penerima
        $data = Penerima::all(['nama', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
        if ($data->count() < 3) {
            return view('penerima.statistic', [
                'clusters' => [],
                'message' => 'Data kurang dari 3, tidak bisa melakukan clustering.'
            ]);
        }
        // Siapkan data untuk KMeans
        $samples = $data->map(function ($item) {
            return [
                (float) $item->usia,
                (float) $item->jumlah_anak,
                (float) $item->kelayakan_rumah,
                (float) $item->pendapatan_perbulan,
            ];
        })->toArray();

        // Jalankan KMeans
        $clusterer = new KMeans(3); // 3 cluster
        $clusterer->train(new Unlabeled($samples));
        $labels = $clusterer->predict(new Unlabeled($samples));

        // Gabungkan hasil cluster dengan data asli
        $result = [];
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
        }

        return view('penerima.statistic', [
            'clusters' => $result,
            'message' => null,
            'scatterData' => $scatterData
        ]);
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