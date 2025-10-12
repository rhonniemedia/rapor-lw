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
        Schema::create('jurusan_mata_pelajarans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke jurusan
            $table->foreignUuid('jurusan_id')
                ->constrained('jurusans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relasi ke mata pelajaran
            $table->foreignUuid('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relasi ke kurikulum (opsional)
            $table->foreignUuid('kurikulum_id')
                ->nullable()
                ->constrained('kurikulums')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Status mata pelajaran: wajib / pilihan
            $table->enum('status', ['wajib', 'pilihan'])->default('wajib');

            $table->timestamps();

            // Pastikan tidak ada duplikat data untuk kombinasi yang sama
            $table->unique(['jurusan_id', 'mata_pelajaran_id', 'kurikulum_id'], 'uniq_jurusan_mapel_kurikulum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurusan_mata_pelajarans');
    }
};
