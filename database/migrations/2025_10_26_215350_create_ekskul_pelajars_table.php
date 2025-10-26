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
        Schema::create('ekskul_pelajars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ekstrakurikuler_id');
            $table->uuid('pelajar_id');
            $table->enum('nilai', ['A', 'B', 'C']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('ekstrakurikuler_id')->references('id')->on('ekstrakurikulers')->onDelete('cascade');
            $table->foreign('pelajar_id')->references('id')->on('pelajars')->onDelete('cascade');

            // Unique constraint: satu siswa hanya punya 1 nilai per ekstrakurikuler
            $table->unique(['ekstrakurikuler_id', 'pelajar_id'], 'unique_ekskul_pelajar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekskul_pelajars');
    }
};
