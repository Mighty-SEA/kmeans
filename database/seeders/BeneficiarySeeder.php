<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeneficiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate NIK acak dengan format yang benar (16 digit)
        $generateNik = function() {
            $prefix = rand(10, 99); // Kode wilayah (2 digit)
            $dob = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . // Bulan (2 digit)
                  str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT) . // Tanggal (2 digit)
                  rand(50, 99); // Tahun (2 digit)
            $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT); // 6 digit terakhir
            
            return $prefix . $dob . $random;
        };
        
        // Data dari tabel yang diberikan
        $data = [
            [
                'nama' => 'ALIP N',
                'umur' => 50,
                'jumlah_anak' => 3,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'TITI DATI',
                'umur' => 81,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 0,
            ],
            [
                'nama' => 'SITI WASITOH',
                'umur' => 43,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 2,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'LILIS AMANAH',
                'umur' => 47,
                'jumlah_anak' => 1,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'ELASARI',
                'umur' => 45,
                'jumlah_anak' => 3,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'SUANGSIH',
                'umur' => 57,
                'jumlah_anak' => 0,
                'kelayakan_rumah' => 2,
                'pendapatan_perbulan' => 0,
            ],
            [
                'nama' => 'TITING',
                'umur' => 51,
                'jumlah_anak' => 4,
                'kelayakan_rumah' => 2,
                'pendapatan_perbulan' => 1000000,
            ],
            [
                'nama' => 'WIWIT P',
                'umur' => 35,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 2,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'NINING SETIAWATI',
                'umur' => 40,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 1,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'EUIS SETIANI',
                'umur' => 68,
                'jumlah_anak' => 6,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'SINTAWATI',
                'umur' => 44,
                'jumlah_anak' => 3,
                'kelayakan_rumah' => 4,
                'pendapatan_perbulan' => 0,
            ],
            [
                'nama' => 'NINING DEWI',
                'umur' => 49,
                'jumlah_anak' => 4,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'EN JURIAH',
                'umur' => 60,
                'jumlah_anak' => 6,
                'kelayakan_rumah' => 1,
                'pendapatan_perbulan' => 2500000,
            ],
            [
                'nama' => 'AI ENA',
                'umur' => 42,
                'jumlah_anak' => 4,
                'kelayakan_rumah' => 4,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'JULAENI',
                'umur' => 44,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'IIS ISMAYANTI',
                'umur' => 56,
                'jumlah_anak' => 5,
                'kelayakan_rumah' => 4,
                'pendapatan_perbulan' => 2500000,
            ],
            [
                'nama' => 'YATI',
                'umur' => 47,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'IIS RATNINGSIH',
                'umur' => 62,
                'jumlah_anak' => 9,
                'kelayakan_rumah' => 1,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'IDAH JUBAEDAH',
                'umur' => 32,
                'jumlah_anak' => 1,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'NANIH',
                'umur' => 44,
                'jumlah_anak' => 4,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'LENI NURAENI',
                'umur' => 45,
                'jumlah_anak' => 6,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'IMAS',
                'umur' => 47,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 0,
            ],
            [
                'nama' => 'DANA',
                'umur' => 65,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 1000000,
            ],
            [
                'nama' => 'ATIKAH',
                'umur' => 46,
                'jumlah_anak' => 3,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'KARLINA',
                'umur' => 41,
                'jumlah_anak' => 2,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 2000000,
            ],
            [
                'nama' => 'INTAN',
                'umur' => 31,
                'jumlah_anak' => 1,
                'kelayakan_rumah' => 0,
                'pendapatan_perbulan' => 1000000,
            ],
            [
                'nama' => 'ADESUHARA',
                'umur' => 55,
                'jumlah_anak' => 5,
                'kelayakan_rumah' => 3,
                'pendapatan_perbulan' => 0,
            ],
            [
                'nama' => 'LINA SUMINAR',
                'umur' => 49,
                'jumlah_anak' => 4,
                'kelayakan_rumah' => 1,
                'pendapatan_perbulan' => 1500000,
            ],
            [
                'nama' => 'CICIH',
                'umur' => 67,
                'jumlah_anak' => 0,
                'kelayakan_rumah' => 2,
                'pendapatan_perbulan' => 1000000,
            ],
        ];

        // Masukkan data ke tabel beneficiaries
        foreach ($data as $item) {
            Beneficiary::create([
                'nik' => $generateNik(),
                'nama' => $item['nama'],
                'alamat' => 'Alamat ' . $item['nama'],
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'usia' => $item['umur'],
                'jumlah_anak' => $item['jumlah_anak'],
                'kelayakan_rumah' => $item['kelayakan_rumah'],
                'pendapatan_perbulan' => $item['pendapatan_perbulan'],
            ]);
        }
    }
}
