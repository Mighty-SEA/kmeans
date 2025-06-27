<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('normalization_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->float('usia_normalized')->nullable();
            $table->float('jumlah_anak_normalized')->nullable();
            $table->float('kelayakan_rumah_normalized')->nullable();
            $table->float('pendapatan_perbulan_normalized')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('normalization_results');
    }
};
