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

test('user dapat mencari beneficiary', function () {
    $user = User::factory()->create();
    Beneficiary::factory()->create(['nama' => 'CariNama']);
    $this->actingAs($user)->get('/beneficiary?search=CariNama')
        ->assertSee('CariNama');
});

test('user dapat reset pencarian beneficiary', function () {
    $user = User::factory()->create();
    Beneficiary::factory()->create(['nama' => 'NamaReset']);
    $response = $this->actingAs($user)->get('/beneficiary?search=NamaReset');
    $response->assertSee('NamaReset');
    $response = $this->actingAs($user)->get('/beneficiary');
    $response->assertSee('NamaReset');
});

test('user dapat mengubah jumlah data per halaman', function () {
    $user = User::factory()->create();
    Beneficiary::factory(15)->create();
    $response = $this->actingAs($user)->get('/beneficiary?perPage=10');
    $response->assertSee('data per halaman');
});

test('user dapat bulk delete beneficiary', function () {
    $user = User::factory()->create();
    $beneficiaries = Beneficiary::factory(3)->create();
    $ids = $beneficiaries->pluck('id')->toArray();
    $response = $this->actingAs($user)->delete('/beneficiary-bulk-delete', [
        'ids' => $ids
    ]);
    $response->assertRedirect('/beneficiary');
    foreach ($ids as $id) {
        $this->assertDatabaseMissing('beneficiaries', ['id' => $id]);
    }
});

test('user dapat export data beneficiary ke Excel', function () {
    $user = User::factory()->create();
    Beneficiary::factory()->create();
    $response = $this->actingAs($user)->post('/beneficiary-export', [
        'columns' => ['nama', 'nik']
    ]);
    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});

test('user dapat import data beneficiary dari Excel', function () {
    $user = User::factory()->create();
    $dummyPath = base_path('tests/Feature/dummy.xlsx');
    if (!file_exists($dummyPath)) {
        \PhpOffice\PhpSpreadsheet\IOFactory::createWriter(new \PhpOffice\PhpSpreadsheet\Spreadsheet(), 'Xlsx')->save($dummyPath);
    }
    $file = new \Illuminate\Http\UploadedFile($dummyPath, 'dummy.xlsx', null, null, true);
    $response = $this->actingAs($user)->post('/beneficiary-import', [
        'file' => $file
    ]);
    $response->assertRedirect('/beneficiary');
});

test('user dapat mengakses halaman create beneficiary', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/beneficiary/create');
    $response->assertStatus(200);
});

test('user dapat mengakses halaman edit beneficiary', function () {
    $user = User::factory()->create();
    $beneficiary = Beneficiary::factory()->create();
    $response = $this->actingAs($user)->get("/beneficiary/{$beneficiary->id}/edit");
    $response->assertStatus(200);
});

test('validasi gagal saat tambah beneficiary', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/beneficiary', []);
    $response->assertSessionHasErrors();
});

test('validasi gagal saat edit beneficiary', function () {
    $user = User::factory()->create();
    $beneficiary = Beneficiary::factory()->create();
    $response = $this->actingAs($user)->put("/beneficiary/{$beneficiary->id}", ['nama' => '']);
    $response->assertSessionHasErrors();
}); 