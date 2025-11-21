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
        Schema::create('data_sekolahs', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Data Utama Sekolah
            $table->string('nama_sekolah', 100);
            $table->string('npsn', 15)->unique()->nullable();
            $table->string('nis', 20)->nullable();
            $table->string('nss', 20)->nullable();
            $table->string('nds', 20)->nullable();

            // Identitas & Legalitas Sekolah (Gabungan Tambahan)
            $table->string('status_sekolah', 20)->nullable();          // negeri / swasta
            $table->string('jenjang_pendidikan', 20)->nullable();      // SD, SMP, SMA, SMK, SLB, dll
            $table->string('status_akreditasi', 5)->nullable();        // A/B/C/Belum
            $table->year('tahun_akreditasi')->nullable();              // tahun akreditasi terakhir
            $table->string('sk_pendirian_sekolah', 100)->nullable();   // nomor SK pendirian
            $table->date('tanggal_sk_pendirian')->nullable();          // tanggal SK pendirian
            $table->string('sk_izin_operasional', 100)->nullable();    // nomor SK operasional
            $table->date('tanggal_sk_izin_operasional')->nullable();   // tanggal SK operasional

            // Data Lokasi
            $table->string('alamat', 255)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('kelurahan', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('kota_kabupaten', 50)->nullable();
            $table->string('provinsi', 50)->nullable();

            // Data Kontak
            $table->string('telepon', 20)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('email', 100)->nullable();

            // Logo
            $table->string('logo_sekolah_path')->nullable();
            $table->string('logo_pemda_path')->nullable();

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_sekolahs');
    }
};
