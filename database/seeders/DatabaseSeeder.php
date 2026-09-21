<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 1 admin user default
        User::updateOrCreate(
            ['email' => 'admin@evonime.test'],
            [
                'name' => 'Admin Evonime',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Buat 5 genre dasar
        $basicGenres = [
            ['Action', 'Pertarungan, aksi cepat, dan adegan intens.'],
            ['Adventure', 'Petualangan dan eksplorasi dunia baru.'],
            ['Fantasy', 'Dunia sihir, monster, dan kekuatan supranatural.'],
            ['Romance', 'Kisah cinta dan hubungan antar karakter.'],
            ['Sci-Fi', 'Teknologi masa depan dan luar angkasa.'],
        ];

        foreach ($basicGenres as [$name, $description]) {
            Genre::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description]
            );
        }

        // JANGAN seed anime (biar admin import sendiri via UI)

        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════╗');
        $this->command->info('║  ✅ Setup Selesai!                            ║');
        $this->command->info('║                                               ║');
        $this->command->info('║  Login Admin:                                 ║');
        $this->command->info('║    Email    : admin@evonime.test             ║');
        $this->command->info('║    Password : password                       ║');
        $this->command->info('║                                               ║');
        $this->command->info('║  Langkah selanjutnya:                         ║');
        $this->command->info('║  1. composer dev                              ║');
        $this->command->info('║  2. Buka http://127.0.0.1:8000                ║');
        $this->command->info('║  3. Login, lalu buka /admin/anime-import      ║');
        $this->command->info('╚═══════════════════════════════════════════════╝');
    }
}


