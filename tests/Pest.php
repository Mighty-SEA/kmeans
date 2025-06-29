<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

pest()->extend(PHPUnit\Framework\TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeSuccessful', function () {
    return $this->status()->toBe(200);
});

expect()->extend('toBeRedirect', function () {
    return $this->status()->toBe(302);
});

expect()->extend('toHaveJsonStructure', function (array $structure) {
    return $this->json()->toHaveStructure($structure);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Membuat user dan login secara otomatis
 */
function actingAs($user = null)
{
    $user = $user ?: \App\Models\User::factory()->create();
    
    return test()->actingAs($user);
}

/**
 * Membuat user dengan role admin dan login secara otomatis
 */
function actingAsAdmin()
{
    $admin = \App\Models\User::factory()->create([
        'email' => 'admin@example.com',
        // tambahkan field lain sesuai kebutuhan untuk admin
    ]);
    
    return test()->actingAs($admin);
}

/**
 * Helper untuk membuat data beneficiary untuk testing
 */
function createBeneficiary($attributes = [])
{
    return \App\Models\Beneficiary::factory()->create($attributes);
}

/**
 * Helper untuk membuat beberapa data beneficiary untuk testing
 */
function createBeneficiaries($count = 3, $attributes = [])
{
    return \App\Models\Beneficiary::factory($count)->create($attributes);
}

/**
 * Helper untuk membuat data clustering result untuk testing
 */
function createClusteringResult($attributes = [])
{
    return \App\Models\ClusteringResult::factory()->create($attributes);
}
