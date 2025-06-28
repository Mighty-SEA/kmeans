<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clustering_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('num_clusters')->default(3);
            $table->enum('normalization', ['none', 'minmax', 'standard', 'robust'])->default('robust');
            $table->timestamps();
        });
        
        // Masukkan data default
        DB::table('clustering_settings')->insert([
            'num_clusters' => 3,
            'normalization' => 'robust',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clustering_settings');
    }
};
