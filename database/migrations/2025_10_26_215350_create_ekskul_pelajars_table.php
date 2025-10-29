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
        Schema::create('ekskul_pelajars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahun_ajaran_semester_id'); // tambahkan langsung di awal
            $table->uuid('ekstrakurikuler_id');
            $table->uuid('pelajar_id');
            $table->enum('nilai', ['A', 'B', 'C']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Relasi foreign key
            $table->foreign('tahun_ajaran_semester_id')
                ->references('id')->on('tahun_ajaran_semesters')
                ->onDelete('cascade');
            $table->foreign('ekstrakurikuler_id')
                ->references('id')->on('ekstrakurikulers')
                ->onDelete('cascade');
            $table->foreign('pelajar_id')
                ->references('id')->on('pelajars')
                ->onDelete('cascade');

            // Unik per semester agar 1 siswa hanya punya 1 nilai per ekskul tiap semester
            $table->unique(
                ['tahun_ajaran_semester_id', 'ekstrakurikuler_id', 'pelajar_id'],
                'unique_ekskul_pelajar_semester'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekskul_pelajars');
    }
};
