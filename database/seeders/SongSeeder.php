<?php

namespace Database\Seeders;

use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Seeder;

class SongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(['email' => 'demo@jestify.app'], ['name' => 'Jestify Demo', 'password' => 'password']);
        foreach ([['title' => 'Midnight Drive', 'artist' => 'Jestify Sessions', 'album' => 'After Hours', 'genre' => 'Electronic', 'duration' => 218], ['title' => 'Open Skies', 'artist' => 'Jestify Sessions', 'album' => 'After Hours', 'genre' => 'Ambient', 'duration' => 264], ['title' => 'First Light', 'artist' => 'Jestify Sessions', 'album' => 'Daybreak', 'genre' => 'Indie', 'duration' => 191]] as $song) {
            Song::updateOrCreate(['title' => $song['title'], 'artist' => $song['artist']], [...$song, 'cover_image' => null, 'local_file_identifier' => 'music/' . str($song['title'])->slug() . '.mp3', 'created_by' => $user->id]);
        }
    }
}
