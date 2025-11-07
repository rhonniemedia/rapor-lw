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
        Schema::create('nilais', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign Keys - Kolom harus didefinisikan pertama
            $table->uuid('pelajar_id');
            $table->uuid('mata_pelajaran_id');
            $table->uuid('rombel_pengajar_id');
            $table->uuid('tahun_ajaran_semester_id');
            // Hanya definisikan kolom guru_id sekali, dan buat nullable
            $table->uuid('guru_id')->nullable(); // ⬅️ Didefinisikan hanya di sini

            // Data Nilai
            $table->decimal('nilai_angka', 5, 2);
            $table->enum('predikat', ['A', 'B', 'C', 'D'])
                ->nullable()
                ->comment('A = Sangat Baik, B = Baik, C = Cukup, D = Kurang');
            $table->text('capaian_kompetensi')->nullable();

            // Audit Trail
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints - Didefinisikan setelah semua kolom
            $table->foreign('pelajar_id')
                ->references('id')->on('pelajars')->onDelete('restrict');

            $table->foreign('mata_pelajaran_id')
                ->references('id')->on('mata_pelajarans')->onDelete('restrict');

            $table->foreign('rombel_pengajar_id')
                ->references('id')->on('rombel_pengajars')->onDelete('restrict');

            $table->foreign('tahun_ajaran_semester_id')
                ->references('id')->on('tahun_ajaran_semesters')->onDelete('restrict');

            // Foreign Key untuk guru_id (referensi ke users)
            $table->foreign('guru_id') // ⬅️ Foreign Key untuk guru_id didefinisikan sekali
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('updated_by')
                ->references('id')->on('users')->onDelete('set null');

            // Indexes untuk performa query
            // Index 'idx_nilai_guru' dihilangkan karena guru_id sudah nullable dan mungkin tidak efisien
            $table->index(['rombel_pengajar_id', 'tahun_ajaran_semester_id'], 'idx_nilai_rombel');

            // Unique constraint
            $table->unique(
                ['pelajar_id', 'mata_pelajaran_id', 'tahun_ajaran_semester_id'],
                'unique_nilai_siswa_mapel_semester'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
