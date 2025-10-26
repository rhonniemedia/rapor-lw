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
        Schema::create('catatan_wali_kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelajar_id');
            $table->uuid('guru_id');
            $table->uuid('tahun_ajaran_semester_id');
            $table->enum('jenis_catatan', ['sikap', 'prestasi', 'kedisiplinan', 'sosial', 'akademik', 'lainnya']);
            $table->text('catatan');
            $table->timestamp('tanggal_input');
            $table->timestamps();

            $table->foreign('pelajar_id')->references('id')->on('pelajars')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tahun_ajaran_semester_id')->references('id')->on('tahun_ajaran_semesters')->onDelete('cascade');

            // Index custom dengan nama pendek
            $table->index(['pelajar_id', 'tahun_ajaran_semester_id', 'tanggal_input'], 'idx_catatan_wali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_wali_kelas');
    }
};
