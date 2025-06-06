<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Aprila Rizkianti',
            'email' => 'april@kmeans.com',
            'password' => Hash::make('password123***'),
        ]);
    }
} 