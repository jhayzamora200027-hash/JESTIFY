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
        $songs = [
            ['Midnight Drive', 'Jestify Sessions', 'After Hours', 'Electronic', 218],
            ['Open Skies', 'Jestify Sessions', 'After Hours', 'Ambient', 264],
            ['First Light', 'Jestify Sessions', 'Daybreak', 'Indie', 191],
            ['Neon Rain', 'Jestify Sessions', 'Night Signals', 'Electronic', 203],
            ['Golden Hour', 'Jestify Sessions', 'Daybreak', 'Lo-fi', 227],
            ['Coastal Lines', 'Jestify Sessions', 'Open Water', 'Chill', 246],
            ['Quiet Motion', 'Jestify Sessions', 'Open Water', 'Ambient', 188],
            ['City Bloom', 'Jestify Sessions', 'Night Signals', 'Indie', 212],
            ['Paper Planes', 'Jestify Sessions', 'Daybreak', 'Pop', 176],
            ['Afterglow', 'Jestify Sessions', 'After Hours', 'Electronic', 231],
        ];

        foreach ($songs as $index => [$title, $artist, $album, $genre, $duration]) {
            $track = $index + 1;
            Song::updateOrCreate(['title' => $title, 'artist' => $artist], [
                'album' => $album,
                'genre' => $genre,
                'duration' => $duration,
                'cover_url' => "https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800&auto=format&fit=crop&q=80&sig={$track}",
                'audio_url' => "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-{$track}.mp3",
                'cover_image' => null,
                'local_file_identifier' => null,
                'is_published' => true,
                'created_by' => $user->id,
            ]);
        }
    }
}
