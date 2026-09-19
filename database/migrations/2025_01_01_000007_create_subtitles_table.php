<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtitles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained('episodes')->cascadeOnDelete()->comment('FK ke episodes');
            $table->enum('language', ['id', 'en', 'jp'])->comment('Kode bahasa, mapping ke App\\Enums\\SubtitleLanguage');
            $table->string('label', 50)->comment('Label tampil di player, mis. Indonesia');
            $table->enum('format', ['vtt', 'srt', 'ass'])->default('vtt')->comment('Format file subtitle');
            $table->text('url')->comment('Path relatif file di disk public atau CDN');
            $table->boolean('is_default')->default(false)->comment('true=subtitle aktif otomatis saat player dibuka');
            $table->timestamps();
            // TANPA softDeletes: subtitle basi harus hard delete agar tidak terkirim ke player.

            $table->unique(['episode_id', 'language'], 'subtitles_episode_language_unique');
            $table->index('language', 'subtitles_language_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtitles');
    }
};
