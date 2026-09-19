<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Nama genre, mis. Action');
            $table->string('slug', 120)->unique()->comment('Slug URL, mis. action');
            $table->text('description')->nullable()->comment('Deskripsi opsional untuk halaman genre');
            $table->timestamps();
            // UNIQUE(name) & UNIQUE(slug) otomatis membuat index -> tidak perlu index tambahan.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
