<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_sources', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name', 100)->comment('Nama provider legal, mis. OfficialAPI-X');
            $table->string('external_id', 150)->comment('ID di sisi provider');
            $table->enum('entity_type', ['anime', 'episode'])->comment('Jenis entitas yang di-fetch');
            $table->json('payload')->nullable()->comment('Snapshot mentah response (audit saja, JANGAN di-query rutin)');
            $table->dateTime('fetched_at')->comment('Waktu fetch ke provider');
            $table->enum('status', ['success', 'failed'])->comment('Hasil fetch');
            $table->text('error_message')->nullable()->comment('Pesan error jika failed');
            $table->timestamps();

            $table->index('provider_name', 'content_sources_provider_index');
            $table->index('entity_type', 'content_sources_entity_type_index');
            $table->index('fetched_at', 'content_sources_fetched_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_sources');
    }
};
