<?php

namespace Database\Seeders;

use App\Enums\SubtitleLanguage;
use App\Enums\VideoQuality;
use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnimeSeeder extends Seeder
{
    public function run(): void
    {
        $animes = [
            ['t' => 'Solo Leveling', 'alt' => 'Ore dake Level Up na Ken', 's' => 'Hunter terlemah bangkit jadi terkuat.', 'type' => 'tv', 'st' => 'ongoing', 'y' => 2024, 'se' => 'winter', 'studio' => 'A-1 Pictures', 'r' => 9.1, 'd' => 24, 'f' => 1, 'g' => ['Action', 'Fantasy', 'Adventure']],
            ['t' => 'Frieren', 'alt' => 'Sousou no Frieren', 's' => 'Penyihir elf memahami manusia.', 'type' => 'tv', 'st' => 'completed', 'y' => 2023, 'se' => 'fall', 'studio' => 'Madhouse', 'r' => 9.3, 'd' => 24, 'f' => 1, 'g' => ['Adventure', 'Drama', 'Fantasy']],
            ['t' => 'Kaiju No. 8', 'alt' => 'Kaijuu 8-gou', 's' => 'Pembersih kaiju berubah jadi kaiju.', 'type' => 'tv', 'st' => 'ongoing', 'y' => 2024, 'se' => 'spring', 'studio' => 'Production I.G', 'r' => 8.5, 'd' => 24, 'f' => 0, 'g' => ['Action', 'Sci-Fi', 'Shounen']],
            ['t' => 'Your Name', 'alt' => 'Kimi no Na wa', 's' => 'Dua remaja bertukar tubuh melintasi waktu.', 'type' => 'movie', 'st' => 'completed', 'y' => 2016, 'se' => null, 'studio' => 'CoMix Wave Films', 'r' => 8.9, 'd' => 106, 'f' => 0, 'g' => ['Romance', 'Drama', 'Supernatural']],
            ['t' => 'Haikyuu Dumpster Battle', 'alt' => 'Haikyuu Gomisuteba no Kessen', 's' => 'Karasuno vs Nekoma di nasional.', 'type' => 'movie', 'st' => 'completed', 'y' => 2024, 'se' => null, 'studio' => 'Production I.G', 'r' => 8.7, 'd' => 85, 'f' => 0, 'g' => ['Sports', 'Comedy', 'Drama']],
        ];

        foreach ($animes as $row) {
            $anime = Anime::updateOrCreate(['slug' => Str::slug($row['t'])], [
                'title' => $row['t'], 'title_alternative' => $row['alt'],
                'slug' => Str::slug($row['t']), 'synopsis' => $row['s'],
                'type' => $row['type'], 'status' => $row['st'],
                'release_date' => $row['y'].'-01-07', 'rating' => $row['r'],
                'total_episodes' => 3, 'duration' => $row['d'],
                'studio' => $row['studio'], 'season' => $row['se'],
                'year' => $row['y'], 'is_published' => true,
                'is_featured' => (bool) $row['f'], 'views_count' => rand(10000, 500000),
            ]);
            $anime->genres()->sync(Genre::whereIn('name', $row['g'])->pluck('id')->all());
            $this->seedEpisodes($anime->id);
        }
    }

    private function seedEpisodes(int $animeId): void
    {
        $anime = Anime::find($animeId);
        for ($i = 1; $i <= 3; $i++) {
            $ep = $anime->episodes()->updateOrCreate(['episode_number' => $i], [
                'title' => "Episode {$i}", 'synopsis' => "Sinopsis episode {$i}.",
                'duration' => 1440, 'aired_at' => now()->subDays(30 - $i * 7),
                'status' => VideoStatus::Ready, 'views_count' => rand(1000, 50000),
            ]);
            $ep->streamSources()->updateOrCreate(
                ['server_name' => 'Server 1', 'quality' => VideoQuality::P720],
                ['url' => "episodes/{$ep->id}/720p/master.m3u8", 'format' => 'hls', 'is_active' => true, 'priority' => 10]
            );
            $ep->streamSources()->updateOrCreate(
                ['server_name' => 'Server 1', 'quality' => VideoQuality::P1080],
                ['url' => "episodes/{$ep->id}/1080p/master.m3u8", 'format' => 'hls', 'is_active' => true, 'priority' => 20]
            );
            $ep->subtitles()->updateOrCreate(
                ['language' => SubtitleLanguage::Id],
                ['label' => 'Indonesia', 'format' => 'vtt', 'url' => "subtitles/{$ep->id}/id.vtt", 'is_default' => true]
            );
            $ep->subtitles()->updateOrCreate(
                ['language' => SubtitleLanguage::En],
                ['label' => 'English', 'format' => 'vtt', 'url' => "subtitles/{$ep->id}/en.vtt", 'is_default' => false]
            );
        }
    }
}
