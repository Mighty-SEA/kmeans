<?php

use App\Models\User;
use App\Models\Beneficiary;

test('user yang login dapat melihat daftar beneficiary', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/beneficiary');
    
    $response->assertStatus(200);
});

test('user yang tidak login akan diarahkan ke halaman login', function () {
    $response = $this->get('/beneficiary');
    
    $response->assertRedirect('/login');
});

test('user dapat membuat beneficiary baru', function () {
    $user = User::factory()->create();
    
    $beneficiaryData = [
        'nama' => 'Nama Beneficiary',
        'nik' => '1234567890123456',
        'alamat' => 'Alamat Beneficiary',
        'no_hp' => '081234567890',
        'usia' => 35,
        'jumlah_anak' => 3,
        'kelayakan_rumah' => 'Layak',
        'pendapatan_perbulan' => 2000000,
    ];
    
    $response = $this->actingAs($user)
                     ->post('/beneficiary', $beneficiaryData);
    
    $response->assertRedirect('/beneficiary');
    $this->assertDatabaseHas('beneficiaries', [
        'nama' => 'Nama Beneficiary',
        'nik' => '1234567890123456',
    ]);
});

test('user dapat mengupdate beneficiary', function () {
    $user = User::factory()->create();
    $beneficiary = Beneficiary::factory()->create();
    
    $updatedData = [
        'nama' => 'Nama Baru',
        'nik' => $beneficiary->nik,
        'alamat' => 'Alamat Baru',
        'no_hp' => $beneficiary->no_hp,
        'usia' => $beneficiary->usia,
        'jumlah_anak' => $beneficiary->jumlah_anak,
        'kelayakan_rumah' => $beneficiary->kelayakan_rumah,
        'pendapatan_perbulan' => $beneficiary->pendapatan_perbulan,
    ];
    
    $response = $this->actingAs($user)
                     ->put("/beneficiary/{$beneficiary->id}", $updatedData);
    
    $response->assertRedirect('/beneficiary');
    $this->assertDatabaseHas('beneficiaries', [
        'id' => $beneficiary->id,
        'nama' => 'Nama Baru',
        'alamat' => 'Alamat Baru',
    ]);
});

test('user dapat menghapus beneficiary', function () {
    $user = User::factory()->create();
    $beneficiary = Beneficiary::factory()->create();
    
    $response = $this->actingAs($user)
                     ->delete("/beneficiary/{$beneficiary->id}");
    
    $response->assertRedirect('/beneficiary');
    $this->assertDatabaseMissing('beneficiaries', [
        'id' => $beneficiary->id,
    ]);
}); 