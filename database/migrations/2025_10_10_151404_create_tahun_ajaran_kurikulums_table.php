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
        Schema::create('tahun_ajaran_kurikulums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            $table->foreignUuid('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            // Mencegah duplikasi kombinasi
            $table->unique(['kurikulum_id', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran_kurikulums');
    }
};
