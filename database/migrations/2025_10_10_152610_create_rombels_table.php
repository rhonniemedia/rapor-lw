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

            // Relasi ke tabel jurusans
            $table->uuid('jurusan_id');
            $table->foreign('jurusan_id')
                ->references('id')
                ->on('jurusans')
                ->onDelete('cascade');

            // Relasi ke tabel tahun_ajaran_kurikulums (nullable)
            $table->uuid('tahun_ajaran_kurikulum_id')->nullable();
            $table->foreign('tahun_ajaran_kurikulum_id')
                ->references('id')
                ->on('tahun_ajaran_kurikulums')
                ->onDelete('set null');

            // Wali kelas menggunakan slug (bukan UUID)
            $table->string('wali_kelas_slug')->nullable();

            // Atribut rombel
            $table->unsignedTinyInteger('tingkat')->comment('10 = X, 11 = XI, 12 = XII');
            $table->string('nama');

            $table->timestamps();

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
