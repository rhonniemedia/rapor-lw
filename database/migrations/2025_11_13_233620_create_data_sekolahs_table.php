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
            // Menggunakan UUID sebagai Primary Key
            $table->uuid('id')->primary();

            // Data Utama Sekolah
            $table->string('nama_sekolah', 100);
            $table->string('npsn', 15)->unique()->nullable();
            $table->string('nis', 20)->nullable();
            $table->string('nss', 20)->nullable();
            $table->string('nds', 20)->nullable();

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

            // Kolom untuk Logo (Tambahan Terbaru)
            $table->string('logo_sekolah_path')->nullable();
            $table->string('logo_pemda_path')->nullable();

            // Kolom waktu standar
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
