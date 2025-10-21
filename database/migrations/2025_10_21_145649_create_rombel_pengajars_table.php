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
        Schema::create('rombel_pengajars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rombel_id');
            $table->uuid('mata_pelajaran_id');
            $table->uuid('guru_id');
            $table->unsignedTinyInteger('jam_pelajaran')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('rombel_id')
                ->references('id')
                ->on('rombels')
                ->onDelete('cascade');

            $table->foreign('mata_pelajaran_id')
                ->references('id')
                ->on('mata_pelajarans')
                ->onDelete('cascade');

            $table->foreign('guru_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Unique constraint: satu mata pelajaran hanya bisa diajar oleh satu guru di satu rombel
            $table->unique(['rombel_id', 'mata_pelajaran_id'], 'unique_rombel_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel_pengajars');
    }
};
