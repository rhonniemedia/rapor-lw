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
        Schema::create('orang_tua_walis', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke pelajar
            $table->foreignUuid('pelajar_id')
                ->constrained('pelajars')
                ->onDelete('cascade');

            $table->string('nama');
            $table->string('hubungan'); // contoh: Ayah, Ibu, Wali
            $table->string('pekerjaan')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orang_tua_walis');
    }
};
