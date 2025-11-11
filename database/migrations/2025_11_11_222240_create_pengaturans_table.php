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
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahun_ajaran_semester_id');
            $table->foreign('tahun_ajaran_semester_id')
                ->references('id')
                ->on('tahun_ajaran_semesters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->uuid('kepala_sekolah_id');
            $table->foreign('kepala_sekolah_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('tanggal_rapor');
            $table->json('konfigurasi_tampilan')->nullable(); // fleksibel: logo, ttd, warna, dsb
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
