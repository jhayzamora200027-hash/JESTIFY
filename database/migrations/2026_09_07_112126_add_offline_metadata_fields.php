<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('songs', 'uploaded_by')) Schema::table('songs', fn (Blueprint $table) => $table->renameColumn('uploaded_by', 'created_by'));
        if (Schema::hasColumn('songs', 'cover_url')) Schema::table('songs', fn (Blueprint $table) => $table->renameColumn('cover_url', 'cover_image'));
        if (! Schema::hasColumn('songs', 'local_file_identifier')) Schema::table('songs', fn (Blueprint $table) => $table->string('local_file_identifier')->nullable());
        $columnsToDrop = array_values(array_filter(['audio_url', 'file_size'], fn (string $column) => Schema::hasColumn('songs', $column)));
        if ($columnsToDrop) Schema::table('songs', fn (Blueprint $table) => $table->dropColumn($columnsToDrop));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('songs', 'created_by')) Schema::table('songs', fn (Blueprint $table) => $table->renameColumn('created_by', 'uploaded_by'));
        if (Schema::hasColumn('songs', 'cover_image')) Schema::table('songs', fn (Blueprint $table) => $table->renameColumn('cover_image', 'cover_url'));
        if (Schema::hasColumn('songs', 'local_file_identifier')) Schema::table('songs', fn (Blueprint $table) => $table->dropColumn('local_file_identifier'));
        if (! Schema::hasColumn('songs', 'audio_url')) Schema::table('songs', fn (Blueprint $table) => $table->text('audio_url')->nullable());
        if (! Schema::hasColumn('songs', 'file_size')) Schema::table('songs', fn (Blueprint $table) => $table->unsignedBigInteger('file_size')->nullable());
    }
};
