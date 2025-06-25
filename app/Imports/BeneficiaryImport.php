<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BeneficiaryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Beneficiary([
            'nik' => $row['nik'],
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