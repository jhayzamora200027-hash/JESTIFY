<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_local_song_metadata_and_settings(): void
    {
        $registration = $this->postJson('/api/register', [
            'name' => 'Flutter User',
            'email' => 'flutter@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $token = $registration->json('data.token');
        $headers = ['Authorization' => "Bearer {$token}"];

        $this->postJson('/api/songs', [
            'title' => 'Local Song',
            'artist' => 'Local Artist',
            'album' => 'Local Album',
            'genre' => 'Ambient',
            'duration' => 180,
            'cover_image' => 'covers/local-song.jpg',
            'local_file_identifier' => 'music/local-song.mp3',
        ], $headers)->assertCreated()->assertJsonPath('data.local_file_identifier', 'music/local-song.mp3');

        $this->putJson('/api/settings', ['settings' => ['theme' => 'dark', 'autoplay' => true]], $headers)
            ->assertOk()->assertJsonFragment(['key' => 'theme', 'value' => 'dark']);

        $this->assertDatabaseHas('songs', ['local_file_identifier' => 'music/local-song.mp3']);
        $this->assertDatabaseMissing('songs', ['audio_url' => 'music/local-song.mp3']);
    }
}