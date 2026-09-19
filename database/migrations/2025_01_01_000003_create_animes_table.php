<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('Judul utama (EN/ID)');
            $table->string('title_alternative', 255)->nullable()->comment('Judul JP/romaji, mis. Ore dake Level Up na Ken');
            $table->string('slug', 255)->unique()->comment('Slug URL unik');
            $table->text('synopsis')->comment('Sinopsis lengkap');
            $table->enum('type', ['tv', 'movie', 'ova', 'ona', 'special'])->default('tv')->comment('Format rilis');
            $table->enum('status', ['ongoing', 'completed', 'upcoming', 'hiatus'])->default('ongoing')->comment('Status penayangan');
            $table->date('release_date')->nullable()->comment('Tanggal rilis episode pertama');
            $table->date('end_date')->nullable()->comment('Tanggal tamat, null jika ongoing');
            $table->decimal('rating', 3, 1)->nullable()->comment('Skor 0.0-10.0, null jika belum ada rating');
            $table->unsignedInteger('total_episodes')->nullable()->comment('Jumlah episode, null jika ongoing/belum pasti');
            $table->unsignedInteger('duration')->nullable()->comment('Menit per episode');
            $table->string('studio', 150)->nullable()->comment('Nama studio, mis. A-1 Pictures');
            $table->enum('season', ['winter', 'spring', 'summer', 'fall'])->nullable()->comment('Musim rilis');
            $table->unsignedSmallInteger('year')->nullable()->comment('Tahun rilis, mis. 2024');
            $table->string('poster_path', 255)->nullable()->comment('Path poster di disk public');
            $table->string('banner_path', 255)->nullable()->comment('Path banner di disk public');
            $table->boolean('is_published')->default(false)->comment('false=draft, tidak tampil di frontend');
            $table->boolean('is_featured')->default(false)->comment('true=tampil di hero/featured');
            $table->unsignedBigInteger('views_count')->default(0)->comment('Counter denormalisasi, update via job');
            $table->timestamps();
            $table->softDeletes()->comment('Anime mahal (relasi banyak) -> trash dulu, bukan hard delete');

            $table->index('status', 'animes_status_index');
            $table->index('type', 'animes_type_index');
            $table->index('year', 'animes_year_index');
            $table->index('is_published', 'animes_is_published_index');
            $table->index('is_featured', 'animes_is_featured_index');
            $table->index(['status', 'is_published'], 'animes_status_published_index');
            if (Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'title_alternative', 'synopsis'], 'animes_search_fulltext');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
