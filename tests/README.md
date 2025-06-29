# Dokumentasi Testing K-Means

Dokumentasi ini berisi informasi tentang pengaturan dan penggunaan Pest PHP untuk testing pada aplikasi K-Means.

## Struktur Testing

Struktur testing pada aplikasi ini dibagi menjadi dua kategori utama:

1. **Unit Tests** - Pengujian pada komponen individual (model, helper, dll)
2. **Feature Tests** - Pengujian pada fitur-fitur aplikasi (controller, request, dll)

## Menjalankan Tests

Untuk menjalankan semua tests:

```bash
php artisan test
```

Untuk menjalankan test spesifik:

```bash
php artisan test --filter=BeneficiaryTest
```

Untuk menjalankan test dengan coverage report:

```bash
php artisan test --coverage
```

## Helpers

Beberapa helper functions telah disediakan untuk mempermudah penulisan tests:

- `actingAs($user = null)` - Login sebagai user tertentu
- `actingAsAdmin()` - Login sebagai admin
- `createBeneficiary($attributes = [])` - Membuat data beneficiary
- `createBeneficiaries($count = 3, $attributes = [])` - Membuat beberapa data beneficiary
- `createClusteringResult($attributes = [])` - Membuat data clustering result

## Expectations

Beberapa custom expectations juga telah ditambahkan:

- `toBeOne()` - Mengecek apakah nilai adalah 1
- `toBeSuccessful()` - Mengecek apakah response berhasil (status 200)
- `toBeRedirect()` - Mengecek apakah response adalah redirect (status 302)
- `toHaveJsonStructure(array $structure)` - Mengecek struktur JSON response

## Factories

Factories yang tersedia:

- `UserFactory`
- `BeneficiaryFactory`
- `ClusteringResultFactory`
- `DecisionResultFactory`
- `NormalizationResultFactory`
- `ClusteringSettingFactory`

## Contoh Penggunaan

```php
test('user dapat melihat daftar beneficiary', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/beneficiary');
    
    $response->assertStatus(200);
});

test('beneficiary memiliki atribut yang benar', function () {
    $beneficiary = Beneficiary::factory()->create([
        'name' => 'Nama Test',
        'nik' => '1234567890123456',
        'address' => 'Alamat Test',
    ]);
    
    expect($beneficiary->name)->toBe('Nama Test');
    expect($beneficiary->nik)->toBe('1234567890123456');
    expect($beneficiary->address)->toBe('Alamat Test');
});
``` 