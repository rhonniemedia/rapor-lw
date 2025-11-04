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
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('kode')->unique();

            // --- Kolom Agama Ditambahkan TANPA AFTER() ---
            $table->boolean('is_mapel_agama')->default(false);
            $table->string('agama_terkait')->nullable();
            $table->char('agama_terkait_hash', 64)->nullable()->index(); // Indexing untuk pencarian cepat
            // ---------------------------------------------

            $table->enum('status', ['aktif', 'arsip'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_pelajarans');
    }
};
