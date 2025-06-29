<?php

use App\Models\Beneficiary;

test('beneficiary model dapat dibuat', function () {
    $beneficiary = new Beneficiary([
        'nama' => 'Nama Test',
        'nik' => '1234567890123456',
        'alamat' => 'Alamat Test',
        'no_hp' => '081234567890',
        'usia' => 35,
        'jumlah_anak' => 3,
        'kelayakan_rumah' => 'Layak',
        'pendapatan_perbulan' => 2000000,
    ]);
    
    expect($beneficiary)->toBeInstanceOf(Beneficiary::class);
    expect($beneficiary->nama)->toBe('Nama Test');
    expect($beneficiary->nik)->toBe('1234567890123456');
    expect($beneficiary->alamat)->toBe('Alamat Test');
    expect($beneficiary->no_hp)->toBe('081234567890');
    expect($beneficiary->usia)->toBe(35);
    expect($beneficiary->jumlah_anak)->toBe(3);
    expect($beneficiary->kelayakan_rumah)->toBe('Layak');
    expect($beneficiary->pendapatan_perbulan)->toBe(2000000);
}); 