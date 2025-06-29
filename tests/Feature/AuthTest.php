<?php

use App\Models\User;

test('halaman login dapat diakses', function () {
    $response = $this->get('/login');
    
    $response->assertStatus(200);
});

test('halaman register dapat diakses', function () {
    $response = $this->get('/register');
    
    $response->assertStatus(200);
});

test('user dapat login dengan kredensial yang benar', function () {
    $user = User::factory()->create();
    
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    $response->assertRedirect('/');
    $this->assertAuthenticated();
});

test('user tidak dapat login dengan kredensial yang salah', function () {
    $user = User::factory()->create();
    
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);
    
    $response->assertRedirect('/');
    $this->assertGuest();
});

test('user dapat logout', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/logout');
    
    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('user dapat mendaftar', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    
    $response->assertRedirect('/');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
}); 