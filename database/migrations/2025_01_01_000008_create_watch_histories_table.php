<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('FK ke users');
            $table->foreignId('episode_id')->constrained('episodes')->cascadeOnDelete()->comment('FK ke episodes');
            $table->unsignedInteger('progress_seconds')->default(0)->comment('Posisi terakhir ditonton (detik)');
            $table->unsignedInteger('duration_seconds')->nullable()->comment('Total durasi episode saat ditonton (snapshot)');
            $table->boolean('completed')->default(false)->comment('true=jika progress >= ~95% durasi');
            $table->dateTime('last_watched_at')->comment('Waktu terakhir nonton, untuk sorting Continue Watching');
            $table->timestamps();
            // TANPA softDeletes: histori user dihapus permanen saat user minta hapus riwayat / akun dihapus.

            $table->unique(['user_id', 'episode_id'], 'watch_histories_user_episode_unique');
            $table->index('last_watched_at', 'watch_histories_last_watched_at_index');
            $table->index(['user_id', 'last_watched_at'], 'watch_histories_user_last_watched_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
