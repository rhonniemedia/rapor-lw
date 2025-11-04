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
        Schema::create('template_nilai_capaians', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke Tahun Ajaran dan Semester
            $table->uuid('tahun_ajaran_semester_id');
            // Relasi ke Mata Pelajaran (Nullable, mungkin ada template umum/global)
            $table->uuid('mata_pelajaran_id')->nullable();

            // --- KOLOM BARU YANG DIMINTA ---
            // Tingkat kelas (misalnya: 10, 11, 12 atau X, XI, XII. Menggunakan string untuk fleksibilitas)
            $table->string('tingkat')->nullable();
            // -------------------------------

            $table->integer('rentang_min');
            $table->integer('rentang_max');
            $table->string('predikat');
            $table->text('deskripsi');
            $table->boolean('aktif')->default(true); // Status aktif/tidak

            // Audit
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('tahun_ajaran_semester_id')
                ->references('id')
                ->on('tahun_ajaran_semesters')
                ->onDelete('cascade');

            $table->foreign('mata_pelajaran_id')
                ->references('id')
                ->on('mata_pelajarans')
                ->onDelete('set null'); // Jika mapel dihapus, ID-nya di-set null

            // Unique Constraint: Memastikan tidak ada dua template dengan rentang MIN yang sama untuk kombinasi yang sama.
            $table->unique(['tahun_ajaran_semester_id', 'mata_pelajaran_id', 'tingkat', 'rentang_min'], 'unique_nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_nilai_capaians');
    }
};
