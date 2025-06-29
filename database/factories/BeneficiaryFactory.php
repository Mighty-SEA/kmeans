<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Beneficiary::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $index = 0;
        $index++;
        
        return [
            'nama' => 'Nama Beneficiary ' . $index,
            'nik' => '12345678901234' . str_pad($index, 2, '0', STR_PAD_LEFT),
            'alamat' => 'Alamat Beneficiary ' . $index,
            'no_hp' => '08123456789' . $index,
            'usia' => 30 + ($index % 40),
            'jumlah_anak' => $index % 10,
            'kelayakan_rumah' => ($index % 2 == 0) ? 'Layak' : 'Tidak Layak',
            'pendapatan_perbulan' => 1000000 + ($index * 100000),
        ];
    }
} 