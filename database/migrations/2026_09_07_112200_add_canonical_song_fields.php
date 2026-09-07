<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('songs', 'audio_url')) {
            Schema::table('songs', fn (Blueprint $table) => $table->text('audio_url')->nullable());
        }

        if (! Schema::hasColumn('songs', 'cover_url')) {
            Schema::table('songs', fn (Blueprint $table) => $table->text('cover_url')->nullable());
        }

        if (! Schema::hasColumn('songs', 'is_published')) {
            Schema::table('songs', fn (Blueprint $table) => $table->boolean('is_published')->default(true)->index());
        }

        if (! Schema::hasColumn('playlist_song', 'position')) {
            Schema::table('playlist_song', fn (Blueprint $table) => $table->unsignedInteger('position')->default(0));
        }

        Schema::table('listening_histories', function (Blueprint $table) {
            $table->index(['user_id', 'played_at']);
        });
    }

    public function down(): void
    {
        Schema::table('listening_histories', fn (Blueprint $table) => $table->dropIndex(['user_id', 'played_at']));
        if (Schema::hasColumn('playlist_song', 'position')) Schema::table('playlist_song', fn (Blueprint $table) => $table->dropColumn('position'));
        if (Schema::hasColumn('songs', 'is_published')) Schema::table('songs', fn (Blueprint $table) => $table->dropColumn('is_published'));
        if (Schema::hasColumn('songs', 'cover_url')) Schema::table('songs', fn (Blueprint $table) => $table->dropColumn('cover_url'));
        if (Schema::hasColumn('songs', 'audio_url')) Schema::table('songs', fn (Blueprint $table) => $table->dropColumn('audio_url'));
    }
};
