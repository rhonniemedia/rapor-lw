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
        Schema::create('template_ekstrakurikuler_deskripsis', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke Tahun Ajaran dan Semester
            $table->uuid('tahun_ajaran_semester_id');
            // Relasi ke Ekstrakurikuler (Nullable, untuk templat umum)
            $table->uuid('ekstrakurikuler_id')->nullable();

            $table->string('predikat', 100);
            $table->text('deskripsi');
            $table->boolean('gunakan_placeholder')->default(false);
            $table->boolean('aktif')->default(true);

            // Audit
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();

            // 🔹 Foreign Keys dengan nama pendek
            $table->foreign('tahun_ajaran_semester_id', 'fk_tesemester')
                ->references('id')
                ->on('tahun_ajaran_semesters')
                ->onDelete('cascade');

            $table->foreign('ekstrakurikuler_id', 'fk_ekskul_template')
                ->references('id')
                ->on('ekstrakurikulers')
                ->onDelete('set null');

            // 🔹 Unique constraint juga diberi nama pendek
            $table->unique(
                ['tahun_ajaran_semester_id', 'ekstrakurikuler_id', 'predikat'],
                'unique_ekskul_template'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_ekstrakurikuler_deskripses');
    }
};
