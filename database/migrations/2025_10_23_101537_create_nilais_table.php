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

            // Foreign Keys - Relasi
            $table->uuid('pelajar_id');
            $table->uuid('mata_pelajaran_id');
            $table->uuid('rombel_pengajar_id');
            $table->uuid('tahun_ajaran_semester_id');
            $table->uuid('guru_id');

            // Data Nilai
            $table->decimal('nilai_angka', 5, 2); // format: 100.00
            $table->enum('predikat', ['A', 'B', 'C', 'D'])
                ->nullable()
                ->comment('A = Sangat Baik, B = Baik, C = Cukup, D = Kurang')
                ->nullable();
            $table->text('capaian_kompetensi')
                ->nullable(); // deskripsi pencapaian

            // Audit Trail
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes(); // untuk keamanan data, pakai soft delete

            // Foreign Key Constraints
            $table->foreign('pelajar_id')
                ->references('id')
                ->on('pelajars')
                ->onDelete('restrict'); // jangan hapus siswa jika sudah ada nilai

            $table->foreign('mata_pelajaran_id')
                ->references('id')
                ->on('mata_pelajarans')
                ->onDelete('restrict');

            $table->foreign('rombel_pengajar_id')
                ->references('id')
                ->on('rombel_pengajars')
                ->onDelete('restrict');

            $table->foreign('tahun_ajaran_semester_id')
                ->references('id')
                ->on('tahun_ajaran_semesters')
                ->onDelete('restrict');

            $table->foreign('guru_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes untuk performa query
            $table->index(['guru_id', 'tahun_ajaran_semester_id'], 'idx_nilai_guru');
            $table->index(['rombel_pengajar_id', 'tahun_ajaran_semester_id'], 'idx_nilai_rombel');

            // Unique constraint: satu siswa hanya punya satu nilai per mapel per semester
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
