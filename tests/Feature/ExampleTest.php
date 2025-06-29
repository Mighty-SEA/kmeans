<?php

test('the application returns a successful response', function () {
    // Halaman utama memerlukan autentikasi, jadi kita login terlebih dahulu
    $user = \App\Models\User::factory()->create();
    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});
