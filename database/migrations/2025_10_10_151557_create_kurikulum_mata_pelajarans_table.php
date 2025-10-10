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
        Schema::create('kurikulum_mata_pelajarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            $table->foreignUuid('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignUuid('kelompok_id')->nullable()->constrained('mata_pelajaran_kelompoks')->nullOnDelete();

            // Untuk menandai tingkat kelas (misal 10, 11, 12)
            $table->unsignedTinyInteger('tingkat');

            // Urutan tampil di daftar mapel
            $table->unsignedTinyInteger('urutan')->nullable();

            $table->timestamps();

            // Kombinasi unik: kurikulum + mapel + tingkat
            $table->unique(['kurikulum_id', 'mata_pelajaran_id', 'tingkat'], 'kurikulum_mtp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurikulum_mata_pelajarans');
    }
};
