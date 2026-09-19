<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained('episodes')->cascadeOnDelete()->comment('FK ke episodes');
            $table->string('server_name', 50)->comment('Nama server, mis. Server 1');
            $table->enum('quality', ['360p', '480p', '720p', '1080p'])->comment('Kualitas video, mapping ke App\\Enums\\VideoQuality');
            $table->text('url')->comment('Path relatif di disk streams atau URL master.m3u8; JANGAN absolute berulang jika bisa relative');
            $table->enum('format', ['hls', 'mp4', 'dash'])->default('hls')->comment('Format stream');
            $table->boolean('is_active')->default(true)->comment('false=server maintenance/nonaktif');
            $table->tinyInteger('priority')->default(0)->comment('Urutan preferensi server, makin besar makin utama');
            $table->timestamps();
            // TANPA softDeletes: server/quality yang dihapus harus hilang total agar player tidak salah pilih.

            $table->unique(['episode_id', 'server_name', 'quality'], 'stream_sources_episode_server_quality_unique');
            $table->index('is_active', 'stream_sources_is_active_index');
            $table->index(['episode_id', 'is_active', 'priority'], 'stream_sources_ep_active_priority_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_sources');
    }
};
