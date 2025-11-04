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
        Schema::create('template_kokurikuler_capaians', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke Tahun Ajaran dan Semester
            $table->uuid('tahun_ajaran_semester_id');

            // Tema kegiatan sekolah (subdimensi)
            $table->string('subdimensi', 191)->nullable();

            // Tingkat kelas (misalnya: 10, 11, 12)
            $table->string('tingkat', 10)->nullable();

            $table->string('predikat', 100);
            $table->text('deskripsi');
            $table->boolean('aktif')->default(true);

            // Audit
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('tahun_ajaran_semester_id')
                ->references('id')
                ->on('tahun_ajaran_semesters')
                ->onDelete('cascade');

            // Unique constraint aman (tanpa prefix length)
            $table->unique(
                ['tahun_ajaran_semester_id', 'tingkat', 'subdimensi', 'predikat'],
                'unique_kokurikuler_criteria'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_kokurikuler_capaians');
    }
};
