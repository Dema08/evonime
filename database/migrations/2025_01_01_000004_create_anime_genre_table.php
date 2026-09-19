<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_genre', function (Blueprint $table) {
            $table->foreignId('anime_id')->constrained('animes')->cascadeOnDelete()->comment('FK ke animes');
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete()->comment('FK ke genres');
            $table->primary(['anime_id', 'genre_id']);
            // Query dari sisi genre (tampilkan semua anime per genre) butuh index genre_id di depan.
            $table->index('genre_id', 'anime_genre_genre_id_index');
            // Tidak ada timestamps: pivot murni, tidak perlu audit kapan relasi dibuat.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_genre');
    }
};
