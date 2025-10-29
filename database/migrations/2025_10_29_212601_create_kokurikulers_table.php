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
        Schema::create('kokurikulers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke tabel pelajars
            $table->uuid('pelajar_id');
            $table->foreign('pelajar_id')->references('id')->on('pelajars')->onDelete('cascade');

            // Relasi ke tabel users (guru)
            $table->uuid('guru_id');
            $table->foreign('guru_id')->references('id')->on('users')->onDelete('cascade');

            // Relasi ke tabel tahun_ajaran_semesters
            $table->uuid('tahun_ajaran_semester_id');
            $table->foreign('tahun_ajaran_semester_id')->references('id')->on('tahun_ajaran_semesters')->onDelete('cascade');

            // Kolom utama
            $table->string('predikat', 2)->nullable(); // misal A/B/C
            $table->text('capaian')->nullable();
            $table->date('tanggal_input')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kokurikulers');
    }
};
