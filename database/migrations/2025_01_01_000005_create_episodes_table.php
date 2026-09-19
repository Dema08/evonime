<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes')->cascadeOnDelete()->comment('FK ke animes');
            $table->unsignedInteger('episode_number')->comment('Nomor episode dalam satu anime, mulai dari 1');
            $table->string('title', 255)->nullable()->comment('Judul episode, null jika belum ada');
            $table->text('synopsis')->nullable()->comment('Sinopsis episode');
            $table->unsignedInteger('duration')->nullable()->comment('Durasi dalam DETIK');
            $table->string('thumbnail_path', 255)->nullable()->comment('Path thumbnail di disk public');
            $table->dateTime('aired_at')->nullable()->comment('Jadwal tayang, null jika belum tayang');
            $table->enum('status', ['draft', 'processing', 'ready', 'failed', 'hidden'])->default('draft')->comment('Status pipeline video, mapping ke App\\Enums\\VideoStatus');
            $table->unsignedBigInteger('views_count')->default(0)->comment('Counter denormalisasi per episode');
            $table->timestamps();

            $table->unique(['anime_id', 'episode_number'], 'episodes_anime_episode_unique');
            $table->index('status', 'episodes_status_index');
            $table->index('aired_at', 'episodes_aired_at_index');
            // FK anime_id otomatis di-index oleh foreignId().
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
