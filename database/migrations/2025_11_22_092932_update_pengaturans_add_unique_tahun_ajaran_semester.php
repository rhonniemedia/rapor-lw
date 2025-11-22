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
        Schema::table('pengaturans', function (Blueprint $table) {
            // Tambah unique constraint
            $table->unique('tahun_ajaran_semester_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            // Hapus unique constraint jika di-rollback
            $table->dropUnique(['tahun_ajaran_semester_id']);
        });
    }
};
