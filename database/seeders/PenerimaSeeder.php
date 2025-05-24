<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenerimaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        for ($i = 1; $i <= 1000; $i++) {
            $data[] = [
                'nama' => 'Penerima ' . $i,
                'alamat' => 'Alamat ' . $i,
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'usia' => rand(20, 70),
                'jumlah_anak' => rand(0, 5),
                'kelayakan_rumah' => rand(1, 5),
                'pendapatan_perbulan' => rand(1000000, 10000000),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('penerima')->insert($data);
    }
}