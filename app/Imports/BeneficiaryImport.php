<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BeneficiaryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Validasi: skip baris jika kolom wajib kosong
        if (
            empty($row['nik']) ||
            empty($row['nama']) ||
            empty($row['alamat']) ||
            empty($row['no_hp']) ||
            empty($row['usia']) ||
            empty($row['jumlah_anak']) ||
            !isset($row['kelayakan_rumah']) || $row['kelayakan_rumah'] === '' ||
            !isset($row['pendapatan_perbulan']) || $row['pendapatan_perbulan'] === ''
        ) {
            return null; // skip baris ini
        }

        return new Beneficiary([
            'nik' => (string) $row['nik'],
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'no_hp' => $row['no_hp'],
            'usia' => $row['usia'],
            'jumlah_anak' => $row['jumlah_anak'],
            'kelayakan_rumah' => $row['kelayakan_rumah'],
            'pendapatan_perbulan' => $row['pendapatan_perbulan'],
        ]);
    }
} 