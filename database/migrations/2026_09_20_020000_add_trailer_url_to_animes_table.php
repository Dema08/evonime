<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('animes', 'trailer_url')) {
            Schema::table('animes', function (Blueprint $table) {
                $table->string('trailer_url', 500)->nullable()->after('banner_path')->comment('URL trailer video YouTube/mp4 untuk hero banner');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('animes', 'trailer_url')) {
            Schema::table('animes', function (Blueprint $table) {
                $table->dropColumn('trailer_url');
            });
        }
    }
};
