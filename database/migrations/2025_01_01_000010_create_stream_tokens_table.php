<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * stream_tokens: tabel AUDIT opsional.
     * Hot path (setiap request /stream) TIDAK membaca tabel ini — validasi token
     * murni HMAC stateless via StreamTokenService (cepat, tanpa query).
     * Tabel ini hanya untuk audit/forensik: siapa akses apa, kapan, dari IP mana.
     * Tulis via job async (dispatch after response) agar tidak memperlambat stream.
     * Bersihkan rutin (mis. hapus >90 hari) agar tidak bengkak.
     */
    public function up(): void
    {
        Schema::create('stream_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->comment('Nullable: guest boleh stream');
            $table->foreignId('episode_id')->constrained('episodes')->cascadeOnDelete()->comment('FK ke episodes');
            $table->char('token_hash', 64)->comment('SHA256 dari token, JANGAN simpan token mentah');
            $table->string('ip_address', 45)->nullable()->comment('IPv4/IPv6 peminta');
            $table->text('user_agent')->nullable()->comment('User-Agent peminta');
            $table->dateTime('expires_at')->comment('Kedaluwarsa token');
            $table->dateTime('used_at')->nullable()->comment('Kapan token pertama dipakai');
            $table->timestamps();

            $table->index('token_hash', 'stream_tokens_hash_index');
            $table->index('expires_at', 'stream_tokens_expires_at_index');
            $table->index('user_id', 'stream_tokens_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_tokens');
    }
};
