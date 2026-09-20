<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->string('external_id_consumet', 255)
                ->nullable()
                ->after('anime_id')
                ->comment('ID episode di provider Gogoanime/Consumet');

            $table->string('external_id_otakudesu', 255)
                ->nullable()
                ->after('external_id_consumet')
                ->comment('ID episode di provider Otakudesu');

            $table->index('external_id_consumet', 'episodes_external_id_consumet_index');
            $table->index('external_id_otakudesu', 'episodes_external_id_otakudesu_index');
        });
    }

    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropIndex('episodes_external_id_consumet_index');
            $table->dropIndex('episodes_external_id_otakudesu_index');

            $table->dropColumn('external_id_consumet');
            $table->dropColumn('external_id_otakudesu');
        });
    }
};