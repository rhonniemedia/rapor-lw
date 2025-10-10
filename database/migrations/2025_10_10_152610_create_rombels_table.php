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
        Schema::create('rombels', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke tahun ajaran dan kurikulum
            $table->foreignUuid('tahun_ajaran_kurikulum_id')
                ->constrained('tahun_ajaran_kurikulums')
                ->cascadeOnDelete();

            $table->foreignUuid('kelas_id')->constrained('kelas')->cascadeOnDelete();

            // Wali kelas menggunakan slug (bukan UUID)
            $table->string('wali_kelas_slug')->nullable();

            $table->string('nama'); // Contoh: XII RPL 1
            $table->timestamps();

            // Unik agar tidak duplikat
            $table->unique(['tahun_ajaran_kurikulum_id', 'kelas_id', 'nama']);

            // Opsional: index untuk slug agar pencarian cepat
            $table->index('wali_kelas_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombels');
    }
};
