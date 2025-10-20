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
        Schema::create('pelajars', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nama_lengkap');
            $table->string('nomor_induk')->nullable()->unique();

            // Gunakan TEXT agar cukup untuk hasil enkripsi (panjang bisa 255+ karakter)
            $table->text('nisn')->nullable();

            // Hash untuk menjaga nilai unik tanpa mengganggu enkripsi
            $table->string('nisn_hash', 64)->nullable()->unique();

            $table->string('tempat_lahir')->nullable();
            $table->text('tanggal_lahir')->nullable();

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable(); // L = Laki-laki, P = Perempuan

            $table->text('agama')->nullable();

            $table->string('status_dalam_keluarga')->nullable();
            $table->string('anak_ke')->nullable();

            // Alamat dan telepon juga dienkripsi, jadi ubah ke TEXT agar cukup panjang
            $table->text('alamat')->nullable();
            $table->text('telepon')->nullable();

            $table->string('sekolah_asal')->nullable();

            $table->string('diterima_di_kelas')->nullable();
            $table->date('pada_tanggal')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelajars');
    }
};
