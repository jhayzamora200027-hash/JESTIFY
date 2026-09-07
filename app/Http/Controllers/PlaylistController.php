<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use App\Models\Song;
use App\Http\Requests\ReorderPlaylistSongsRequest;
use Illuminate\Support\Facades\DB;

class PlaylistController extends ApiController
{
    private function ensureOwner(Playlist $playlist): void
    {
        abort_unless($playlist->user_id === request()->user()->id, 403);
    }

    public function index() { return $this->success(PlaylistResource::collection(request()->user()->playlists()->with('songs')->latest()->get()), 'Playlists retrieved'); }

    public function show(Playlist $playlist)
    {
        $this->ensureOwner($playlist);
        return $this->success(new PlaylistResource($playlist->load('songs')), 'Playlist retrieved');
    }

    public function store(StorePlaylistRequest $request)
    {
        $playlist = $request->user()->playlists()->create($request->validated());
        return $this->success(new PlaylistResource($playlist), 'Playlist created', 201);
    }

    public function update(UpdatePlaylistRequest $request, Playlist $playlist)
    {
        $this->ensureOwner($playlist);
        $playlist->update($request->validated());
        return $this->success(new PlaylistResource($playlist->refresh()->load('songs')), 'Playlist updated');
    }

    public function destroy(Playlist $playlist)
    {
        $this->ensureOwner($playlist);
        $playlist->delete();
        return $this->success(null, 'Playlist deleted');
    }

    public function addSong(Playlist $playlist, Song $song)
    {
        $this->ensureOwner($playlist);
        $playlist->songs()->syncWithoutDetaching([$song->id => ['position' => $playlist->songs()->count()]]);
        return $this->success(new PlaylistResource($playlist->load('songs')), 'Song added to playlist');
    }

    public function removeSong(Playlist $playlist, Song $song)
    {
        $this->ensureOwner($playlist);
        $playlist->songs()->detach($song->id);
        return $this->success(new PlaylistResource($playlist->load('songs')), 'Song removed from playlist');
    }

    public function reorderSongs(ReorderPlaylistSongsRequest $request, Playlist $playlist)
    {
        $this->ensureOwner($playlist);
        $songIds = $request->validated('song_ids');
        $playlistSongIds = $playlist->songs()->whereIn('songs.id', $songIds)->pluck('songs.id')->all();
        if (count($playlistSongIds) !== count($songIds)) return $this->error('All songs must belong to this playlist.', ['song_ids' => ['One or more songs are not in this playlist.']], 422);
        DB::transaction(function () use ($playlist, $songIds) {
            foreach ($songIds as $position => $songId) $playlist->songs()->updateExistingPivot($songId, ['position' => $position]);
        });
        return $this->success(new PlaylistResource($playlist->load('songs')), 'Playlist reordered');
    }
}
