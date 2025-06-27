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

        // Tambahan data acak agar total 300 data
        $firstNames = [
            'Asep', 'Ujang', 'Dede', 'Yayan', 'Iis', 'Nani', 'Euis', 'Teti', 'Yati', 'Cucu',
            'Oman', 'Oni', 'Enok', 'Iim', 'Ijah', 'Ikin', 'Ika', 'Iwan', 'Nenden', 'Neng',
            'Nia', 'Nina', 'Nining', 'Nunu', 'Titin', 'Yani', 'Sari', 'Dian', 'Rina', 'Tata',
            'Dewi', 'Siti', 'Lilis', 'Nana', 'Tini', 'Yuyun', 'Eni', 'Neni', 'Lina', 'Imas',
            'Atikah', 'Karlina', 'Intan', 'Ida', 'Cici', 'Sinta', 'Rini', 'Rika', 'Ririn', 'Rahma',
            'Desi', 'Ajeng', 'Maya', 'Mega', 'Indri', 'Putri', 'Febri', 'Dinda', 'Vina', 'Ratu',
            'Fitri', 'Elsa', 'Cahya', 'Wulan', 'Anisa', 'Yuliana', 'Nadya', 'Anggi', 'Shinta', 'Bella',
            'Della', 'Anindya', 'Melani', 'Yasmin', 'Citra', 'Annisa', 'Hani', 'Rizka', 'Dianita', 'Tasya',
            'Bayu', 'Galih', 'Fajar', 'Rizky', 'Rendi', 'Andi', 'Agus', 'Jajang', 'Tatang', 'Yusuf',
            'Tedi', 'Oding', 'Dadan', 'Aceng', 'Budi', 'Ucup', 'Jamal', 'Ajat', 'Eman', 'Cecep',
            'Slamet', 'Roni', 'Eko', 'Irfan', 'Ilham', 'Arif', 'Rian', 'Doni', 'Hendra', 'Fauzi',
            'Taufik', 'Anton', 'Adi', 'Haris', 'Heru', 'Zaki', 'Farhan', 'Reza', 'Bagas', 'Yoga',
            'Gilang', 'Rio', 'Alif', 'Kevin', 'Rafi', 'Zidan', 'Azka', 'Iqbal', 'Danang', 'Fikri',
            'Alam', 'Wahyu', 'Johan', 'Steven', 'Nathan', 'Yogi', 'Evan', 'Hafiz', 'Ridwan', 'Akbar'
        ];
        
        $lastNames = [
            'Suryani', 'Mulyani', 'Setiawati', 'Kurniawati', 'Sulastri', 'Suryana', 'Supriatna',
            'Permana', 'Suryadi', 'Sopian', 'Suryaman', 'Saputra', 'Gunawan', 'Herlina', 'Rahayu',
            'Wulandari', 'Sukmawati', 'Rosdiana', 'Sukardi', 'Sukarna', 'Sukirman', 'Sukarsa',
            'Santosa', 'Hidayat', 'Wijaya', 'Nasution', 'Maulana', 'Ramadhan', 'Nurhidayat',
            'Wibowo', 'Utami', 'Fauziah', 'Pratiwi', 'Anggraini', 'Nuraini', 'Ardiansyah',
            'Prasetya', 'Nurhalim', 'Kusnadi', 'Yuliani', 'Hartati', 'Astuti', 'Handayani',
            'Wijayani', 'Komalasari', 'Kusuma', 'Rahadian', 'Hernawan', 'Halimah', 'Hikmat',
            'Saputri', 'Putri', 'Sasmita', 'Indrawati', 'Iskandar', 'Alamsyah', 'Taufik', 'Husna',
            'Hidayanti', 'Syahrani', 'Maulani', 'Firdaus', 'Mustofa', 'Yulianti', 'Salim', 'Wahyuni',
            'Martadinata', 'Hardiansyah', 'Kuswandari', 'Darwis', 'Rahmat', 'Hamdan', 'Subekti',
            'Rohim', 'Basuki', 'Subagyo', 'Sunarto', 'Handoko', 'Yulianto', 'Ismail', 'Fathurrahman',
            'Suhendar', 'Saepudin', 'Mubarok', 'Solehudin', 'Sulaeman', 'Zulkifli', 'Sapriadi',
            'Ridwan', 'Sutrisna', 'Salamah', 'Faridah', 'Kusnandar', 'Purwanto', 'Kuncoro',
            'Wardani', 'Fitriawan', 'Harjono', 'Suwito', 'Amirudin', 'Sudrajat', 'Sumarna',
            'Sudirman', 'Sugiarto', 'Sugiharto', 'Rosyid', 'Mardiana', 'Fitriah', 'Syamsudin'
        ];
        
        $total = 300;
        $current = count($data);
        for ($i = 0; $i < $total - $current; $i++) {
            $nama = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
            // Usia: mayoritas 40-60, sedikit 30-39 dan 61-80
            $rand = rand(1, 100);
            if ($rand <= 70) {
                $usia = rand(40, 60);
            } elseif ($rand <= 85) {
                $usia = rand(30, 39);
            } else {
                $usia = rand(61, 80);
            }
            // Jumlah anak: mayoritas 2-4, ada 0,1,5+
            $rand = rand(1, 100);
            if ($rand <= 60) {
                $jumlah_anak = rand(2, 4);
            } elseif ($rand <= 75) {
                $jumlah_anak = 1;
            } elseif ($rand <= 80) {
                $jumlah_anak = 0;
            } elseif ($rand <= 95) {
                $jumlah_anak = 5;
            } else {
                $jumlah_anak = rand(6, 9);
            }
            // Kelayakan rumah: mayoritas 2-3, ada 0,1,4
            $rand = rand(1, 100);
            if ($rand <= 40) {
                $kelayakan_rumah = 2;
            } elseif ($rand <= 80) {
                $kelayakan_rumah = 3;
            } elseif ($rand <= 85) {
                $kelayakan_rumah = 1;
            } elseif ($rand <= 90) {
                $kelayakan_rumah = 0;
            } else {
                $kelayakan_rumah = 4;
            }
            // Pendapatan per bulan: mayoritas 1-2 juta, ada 0, sedikit 2.5 juta
            $rand = rand(1, 100);
            if ($rand <= 60) {
                $pendapatan = 1000000 * rand(1, 2);
            } elseif ($rand <= 80) {
                $pendapatan = 0;
            } else {
                $pendapatan = 2500000;
            }
            Beneficiary::create([
                'nik' => $generateNik(),
                'nama' => $nama,
                'alamat' => 'Alamat ' . $nama,
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'usia' => $usia,
                'jumlah_anak' => $jumlah_anak,
                'kelayakan_rumah' => $kelayakan_rumah,
                'pendapatan_perbulan' => $pendapatan,
            ]);
        }
    }
}
