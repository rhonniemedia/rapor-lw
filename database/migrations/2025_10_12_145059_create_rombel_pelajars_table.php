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
        Schema::create('rombel_pelajars', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('pelajar_id')
                ->constrained('pelajars')
                ->onDelete('cascade');

            $table->foreignUuid('rombel_id')
                ->constrained('rombels')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel_pelajars');
    }
};
