<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['Action', 'Pertarungan, aksi cepat, dan adegan intens.'],
            ['Adventure', 'Petualangan dan eksplorasi dunia baru.'],
            ['Comedy', 'Humor dan situasi kocak.'],
            ['Drama', 'Cerita emosional dan konflik karakter.'],
            ['Fantasy', 'Dunia sihir, monster, dan kekuatan supranatural.'],
            ['Romance', 'Kisah cinta dan hubungan antar karakter.'],
            ['Horror', 'Misteri gelap dan suasana mencekam.'],
            ['Mystery', 'Teka-teki dan investigasi.'],
            ['Sci-Fi', 'Teknologi masa depan dan luar angkasa.'],
            ['Slice of Life', 'Kehidupan sehari-hari yang hangat.'],
            ['Sports', 'Kompetisi olahraga dan semangat tim.'],
            ['Supernatural', 'Fenomena gaib di dunia modern.'],
            ['Thriller', 'Ketegangan dan plot twist.'],
            ['Mecha', 'Robot raksasa dan perang futuristik.'],
            ['Isekai', 'Terjebak atau reinkarnasi di dunia lain.'],
            ['Shounen', 'Target remaja laki-laki, penuh aksi dan persahabatan.'],
        ];

        foreach ($genres as [$name, $description]) {
            Genre::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description]
            );
        }
    }
}
