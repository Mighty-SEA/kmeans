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
        Schema::create('clustering_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penerima_id');
            $table->integer('cluster');
            $table->timestamps();

            $table->foreign('penerima_id')->references('id')->on('penerima')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clustering_results');
    }
};
