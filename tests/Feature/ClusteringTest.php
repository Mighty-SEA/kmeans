<?php

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\ClusteringResult;

test('user dapat mengakses halaman statistik', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/statistic');
    
    $response->assertStatus(200);
});

test('user dapat melakukan clustering', function () {
    $user = User::factory()->create();
    
    // Buat beberapa data beneficiary untuk clustering
    Beneficiary::factory(10)->create();
    
    $response = $this->actingAs($user)
                     ->post('/statistic/clustering', [
                         'num_clusters' => 3,
                         'max_iterations' => 100,
                         'normalization' => 'robust',
                     ]);
    
    $response->assertRedirect('/statistic');
});

test('user dapat melihat detail cluster', function () {
    $user = User::factory()->create();
    
    // Buat beberapa beneficiary
    $beneficiaries = Beneficiary::factory(9)->create();
    
    // Buat data clustering result untuk setiap beneficiary
    foreach ($beneficiaries as $index => $beneficiary) {
        ClusteringResult::create([
            'beneficiary_id' => $beneficiary->id,
            'cluster' => $index % 3, // Membagi ke 3 cluster (0, 1, 2)
            'silhouette' => 0.75,
        ]);
    }
    
    $response = $this->actingAs($user)->get('/statistic/cluster/1');
    
    $response->assertStatus(200);
}); 

test('user dapat recalculate cluster', function () {
    $user = \App\Models\User::factory()->create();
    \App\Models\Beneficiary::factory(10)->create();
    $response = $this->actingAs($user)->post('/statistic/recalculate', [
        'num_clusters' => 3,
        'normalization' => 'robust'
    ]);
    $response->assertRedirect('/statistic');
});

test('user dapat mencari data di detail cluster', function () {
    $user = \App\Models\User::factory()->create();
    $beneficiaries = \App\Models\Beneficiary::factory(9)->create();
    foreach ($beneficiaries as $index => $beneficiary) {
        \App\Models\ClusteringResult::create([
            'beneficiary_id' => $beneficiary->id,
            'cluster' => $index % 3,
            'silhouette' => 0.75,
        ]);
    }
    $nama = $beneficiaries[0]->nama;
    $response = $this->actingAs($user)->get('/statistic/cluster/1?search=' . $nama);
    $response->assertSee($nama);
});

test('user dapat reset pencarian di detail cluster', function () {
    $user = \App\Models\User::factory()->create();
    $beneficiaries = \App\Models\Beneficiary::factory(9)->create();
    foreach ($beneficiaries as $index => $beneficiary) {
        \App\Models\ClusteringResult::create([
            'beneficiary_id' => $beneficiary->id,
            'cluster' => $index % 3,
            'silhouette' => 0.75,
        ]);
    }
    $nama = $beneficiaries[0]->nama;
    $this->actingAs($user)->get('/statistic/cluster/1?search=' . $nama);
    $response = $this->actingAs($user)->get('/statistic/cluster/1');
    $response->assertSee($nama);
}); 