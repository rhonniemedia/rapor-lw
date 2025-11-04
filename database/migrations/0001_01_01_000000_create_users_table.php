<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();

            // Email: TEXT untuk enkripsi, hash untuk indexing/login
            $table->text('email');
            $table->char('email_hash', 64)->unique()->index();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            // NIP: TEXT untuk enkripsi, hash untuk indexing/login
            $table->text('nip')->nullable();
            $table->char('nip_hash', 64)->nullable()->unique()->index();

            // Telephone: TEXT untuk enkripsi (tidak perlu hash)
            $table->text('telephone')->nullable();

            $table->boolean('is_teacher')->default(false);

            // Guru agama
            $table->boolean('is_guru_agama')->default(false);

            // Spesialisasi agama: TEXT untuk enkripsi, hash untuk indexing
            $table->text('spesialisasi_agama')->nullable();
            $table->char('spesialisasi_agama_hash', 64)->nullable()->index();

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
