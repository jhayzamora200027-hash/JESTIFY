<?php

namespace Tests\Feature;

use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineApiTest extends TestCase
{
    use RefreshDatabase;

    private function headers(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

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
            ->assertOk()->assertJsonPath('data.settings.theme', 'dark');

        $this->assertDatabaseHas('songs', ['local_file_identifier' => 'music/local-song.mp3']);
        $this->assertDatabaseHas('songs', ['audio_url' => 'music/local-song.mp3']);
    }

    public function test_health_search_and_unpublished_song_visibility_follow_the_contract(): void
    {
        $published = Song::create(['title' => 'Night Drive', 'artist' => 'Test Artist', 'audio_url' => 'https://example.com/song.mp3', 'created_by' => User::factory()->create()->id, 'is_published' => true]);
        $hidden = Song::create(['title' => 'Hidden Track', 'artist' => 'Test Artist', 'audio_url' => 'https://example.com/hidden.mp3', 'created_by' => $published->created_by, 'is_published' => false]);

        $this->getJson('/api/health')->assertOk()->assertJsonPath('data.status', 'ok');
        $this->getJson('/api/search?q=night')->assertOk()->assertJsonPath('data.0.id', $published->id);
        $this->getJson('/api/songs/' . $published->id)->assertOk()->assertJsonPath('data.audio_url', 'https://example.com/song.mp3');
        $this->getJson('/api/songs/' . $hidden->id)->assertNotFound()->assertJsonPath('success', false);
    }

    public function test_invalid_login_and_protected_requests_return_api_errors(): void
    {
        $user = User::factory()->create(['email' => 'listener@example.com', 'password' => 'password123']);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertUnauthorized()->assertJsonPath('success', false);
        $this->getJson('/api/settings')->assertUnauthorized()->assertJsonPath('success', false);
    }

    public function test_favorites_are_idempotent_and_deletion_is_safe_to_repeat(): void
    {
        $user = User::factory()->create();
        $song = Song::create(['title' => 'Favorite Song', 'artist' => 'Artist', 'created_by' => $user->id, 'is_published' => true]);
        $headers = $this->headers($user);

        $this->postJson('/api/favorites/' . $song->id, [], $headers)->assertCreated();
        $this->postJson('/api/favorites/' . $song->id, [], $headers)->assertOk();
        $this->assertDatabaseCount('favorites', 1);
        $this->deleteJson('/api/favorites/' . $song->id, [], $headers)->assertOk();
        $this->deleteJson('/api/favorites/' . $song->id, [], $headers)->assertOk();
        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_playlist_ownership_and_history_deletion_are_enforced(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $song = Song::create(['title' => 'Playlist Song', 'artist' => 'Artist', 'created_by' => $owner->id, 'is_published' => true]);
        $playlist = $this->postJson('/api/playlists', ['name' => 'Road Mix'], $this->headers($owner))->assertCreated()->json('data.id');

        $this->actingAs($other, 'sanctum')->getJson('/api/playlists/' . $playlist)->assertForbidden();
        $this->actingAs($owner, 'sanctum')->postJson('/api/playlists/' . $playlist . '/songs/' . $song->id)->assertOk();
        $this->actingAs($owner, 'sanctum')->postJson('/api/history/' . $song->id)->assertCreated();
        $this->actingAs($owner, 'sanctum')->getJson('/api/history')->assertOk()->assertJsonPath('data.items.0.song.id', $song->id);
        $this->actingAs($owner, 'sanctum')->deleteJson('/api/history')->assertOk();
        $this->assertDatabaseCount('listening_histories', 0);
    }
}