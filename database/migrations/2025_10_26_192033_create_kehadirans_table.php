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
        Schema::create('kehadirans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelajar_id');
            $table->uuid('rombel_id');  // 👈 Ganti dari rombel_pengajar_id
            $table->uuid('tahun_ajaran_semester_id');
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_tanpa_keterangan')->default(0);
            $table->timestamps();

            $table->foreign('pelajar_id')->references('id')->on('pelajars')->onDelete('cascade');
            $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
            $table->foreign('tahun_ajaran_semester_id')->references('id')->on('tahun_ajaran_semesters')->onDelete('cascade');

            // Unique constraint: satu siswa hanya punya 1 record kehadiran per rombel per semester
            $table->unique(['pelajar_id', 'rombel_id', 'tahun_ajaran_semester_id'], 'unique_kehadiran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
