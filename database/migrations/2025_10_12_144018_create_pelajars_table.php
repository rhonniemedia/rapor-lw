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
            $table->string('nisn')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable(); // L = Laki-laki, P = Perempuan

            $table->enum('agama', [
                'islam',
                'kristen',
                'katolik',
                'hindu',
                'buddha',
                'konghucu',
                'lainnya'
            ])->nullable();

            $table->string('status_dalam_keluarga')->nullable();
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('sekolah_asal')->nullable();

            $table->string('diterima_di_sekolah')->nullable();
            $table->string('di_kelas')->nullable();
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
