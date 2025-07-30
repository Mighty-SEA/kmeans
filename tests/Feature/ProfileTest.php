<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user dapat mengakses halaman profile', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/profile');
    
    $response->assertStatus(200);
});

test('user dapat mengupdate profile', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
                     ->put('/profile', [
                         'name' => 'Nama Baru',
                         'email' => $user->email,
                     ]);
    
    $response->assertRedirect('/profile');
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nama Baru',
    ]);
});

test('user dapat mengupdate password', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
                     ->put('/profile/password', [
                         'current_password' => 'password',
                         'password' => 'new-password',
                         'password_confirmation' => 'new-password',
                     ]);
    
    $response->assertRedirect('/profile');
});

test('user dapat mengupdate avatar', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    
    $file = UploadedFile::fake()->image('avatar.jpg');
    
    $response = $this->actingAs($user)
                     ->post('/profile/avatar', [
                         'avatar' => $file,
                     ]);
    
    $response->assertRedirect('/profile');
    
    // Refresh user dari database
    $user->refresh();
    
    // Pastikan avatar telah diupdate
    expect($user->avatar)->not->toBeNull();
    
    // Pastikan file avatar ada di storage
    Storage::disk('public')->assertExists($user->avatar);
}); 

test('validasi gagal saat update profile', function () {
    $user = \App\Models\User::factory()->create();
    $response = $this->actingAs($user)->put('/profile', ['name' => '']);
    $response->assertSessionHasErrors();
});

test('validasi gagal saat update password', function () {
    $user = \App\Models\User::factory()->create();
    $response = $this->actingAs($user)->put('/profile/password', [
        'current_password' => '',
        'password' => 'new',
        'password_confirmation' => 'beda',
    ]);
    $response->assertSessionHasErrors();
}); 