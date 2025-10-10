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
        Schema::create('kurikulums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama'); // contoh: Kurikulum Merdeka / 2013
            $table->string('kode'); // contoh: K13
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'arsip'])->default('arsip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurikulums');
    }
};
