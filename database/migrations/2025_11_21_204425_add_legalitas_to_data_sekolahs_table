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
        Schema::table('data_sekolahs', function (Blueprint $table) {

            // Identitas & Legalitas Sekolah
            $table->string('status_sekolah', 20)->nullable()->after('nds');
            $table->string('jenjang_pendidikan', 20)->nullable()->after('status_sekolah');
            $table->string('status_akreditasi', 5)->nullable()->after('jenjang_pendidikan');
            $table->year('tahun_akreditasi')->nullable()->after('status_akreditasi');

            // SK Pendirian
            $table->string('sk_pendirian_sekolah', 100)->nullable()->after('tahun_akreditasi');
            $table->date('tanggal_sk_pendirian')->nullable()->after('sk_pendirian_sekolah');

            // SK Izin Operasional
            $table->string('sk_izin_operasional', 100)->nullable()->after('tanggal_sk_pendirian');
            $table->date('tanggal_sk_izin_operasional')->nullable()->after('sk_izin_operasional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'status_sekolah',
                'jenjang_pendidikan',
                'status_akreditasi',
                'tahun_akreditasi',
                'sk_pendirian_sekolah',
                'tanggal_sk_pendirian',
                'sk_izin_operasional',
                'tanggal_sk_izin_operasional',
            ]);
        });
    }
};
