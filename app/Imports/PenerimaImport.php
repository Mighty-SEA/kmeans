<?php

namespace App\Imports;

use App\Models\Penerima;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenerimaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Penerima([
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'no_hp' => $row['no_hp'],
            'usia' => $row['usia'],
            'jumlah_anak' => isset($row['jumlah_anak']) && $row['jumlah_anak'] !== '' ? $row['jumlah_anak'] : 0,
            'kelayakan_rumah' => $row['kelayakan_rumah'],
            'pendapatan_perbulan' => $row['pendapatan_perbulan'],
        ]);
    }
} 