<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan 'dropped' ke ENUM animes.status.
        // ENUM lama: ongoing, completed, upcoming, hiatus.
        // ENUM baru: ongoing, completed, upcoming, hiatus, dropped.
        // Otakudesu me-return "Drop" untuk anime yang di-drop -> map ke "dropped".
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE animes MODIFY COLUMN status ENUM('ongoing', 'completed', 'upcoming', 'hiatus', 'dropped') NOT NULL DEFAULT 'ongoing' COMMENT 'Status penayangan'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Kembalikan semua 'dropped' ke 'completed' sebelum shrink ENUM.
            DB::table('animes')->where('status', 'dropped')->update(['status' => 'completed']);
            DB::statement("ALTER TABLE animes MODIFY COLUMN status ENUM('ongoing', 'completed', 'upcoming', 'hiatus') NOT NULL DEFAULT 'ongoing' COMMENT 'Status penayangan'");
        }
    }
};
