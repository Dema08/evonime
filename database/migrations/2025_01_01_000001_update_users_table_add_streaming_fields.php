<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel users bawaan Laravel sudah ada (0001_01_01_000000).
     * Migration ini HANYA menambah kolom streaming, tidak membuat ulang tabel.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin', 'moderator'])
                ->default('user')
                ->after('password')
                ->comment('Hak akses: user=penonton, admin=full, moderator=CRUD konten');
            $table->string('avatar', 255)
                ->nullable()
                ->after('role')
                ->comment('Path avatar di disk public, null jika pakai default');
            $table->boolean('is_active')
                ->default(true)
                ->after('avatar')
                ->comment('false=banned/nonaktif, tidak bisa login & stream');

            $table->index('role', 'users_role_index');
            $table->index('is_active', 'users_is_active_index');
            // Kolom email sudah unique di migration bawaan -> tidak perlu index lagi.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_is_active_index');
            $table->dropColumn(['role', 'avatar', 'is_active']);
        });
    }
};
