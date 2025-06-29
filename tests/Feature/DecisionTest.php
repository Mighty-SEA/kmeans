<?php

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\DecisionResult;
use App\Models\ClusteringResult;

test('user dapat mengakses halaman decision', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/decision');
    
    $response->assertStatus(200);
});

test('user dapat membuat decision baru', function () {
    $user = User::factory()->create();
    
    // Buat beberapa data beneficiary
    $beneficiaries = Beneficiary::factory(10)->create();
    
    // Buat data clustering result untuk beneficiary
    foreach ($beneficiaries as $index => $beneficiary) {
        ClusteringResult::create([
            'beneficiary_id' => $beneficiary->id,
            'cluster' => $index % 3, // Membagi ke 3 cluster (0, 1, 2)
            'silhouette' => 0.75,
        ]);
    }
    
    $response = $this->actingAs($user)
                     ->post('/decision', [
                         'title' => 'Decision Test',
                         'description' => 'Deskripsi decision test',
                         'cluster' => '0', // Menggunakan cluster 0 yang valid
                         'count' => 3,     // Jumlah penerima dari cluster 0
                         'notes' => 'Catatan untuk decision test',
                     ]);
    
    // Controller me-redirect ke halaman detail decision yang baru dibuat
    $response->assertRedirect();
    
    $this->assertDatabaseHas('decision_results', [
        'title' => 'Decision Test',
        'description' => 'Deskripsi decision test',
        'cluster' => 0,
        'count' => 3,
    ]);
});

test('user dapat melihat detail decision', function () {
    $user = User::factory()->create();
    
    // Buat data decision result
    $decisionResult = DecisionResult::create([
        'title' => 'Decision Test',
        'description' => 'Deskripsi decision test',
        'cluster' => 0,
        'count' => 5,
        'notes' => 'Catatan untuk decision test',
    ]);
    
    $response = $this->actingAs($user)->get("/decision/{$decisionResult->id}");
    
    $response->assertStatus(200);
});

test('user dapat menghapus decision', function () {
    $user = User::factory()->create();
    
    // Buat data decision result
    $decisionResult = DecisionResult::create([
        'title' => 'Decision Test',
        'description' => 'Deskripsi decision test',
        'cluster' => 0,
        'count' => 5,
        'notes' => 'Catatan untuk decision test',
    ]);
    
    $response = $this->actingAs($user)->delete("/decision/{$decisionResult->id}");
    
    $response->assertRedirect('/decision');
    
    $this->assertDatabaseMissing('decision_results', [
        'id' => $decisionResult->id,
    ]);
}); 