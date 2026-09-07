<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use App\Models\Song;

class PlaylistController extends ApiController
{
    public function index() { return $this->success(PlaylistResource::collection(request()->user()->playlists()->with('songs')->latest()->get()), 'Playlists retrieved'); }

    public function store(StorePlaylistRequest $request)
    {
        $playlist = $request->user()->playlists()->create($request->validated());
        return $this->success(new PlaylistResource($playlist), 'Playlist created', 201);
    }

    public function update(UpdatePlaylistRequest $request, Playlist $playlist)
    {
        $this->authorize('update', $playlist);
        $playlist->update($request->validated());
        return $this->success(new PlaylistResource($playlist->refresh()->load('songs')), 'Playlist updated');
    }

    public function destroy(Playlist $playlist)
    {
        $this->authorize('delete', $playlist);
        $playlist->delete();
        return $this->success(null, 'Playlist deleted');
    }

    public function addSong(Playlist $playlist, Song $song)
    {
        $this->authorize('update', $playlist);
        $playlist->songs()->syncWithoutDetaching([$song->id]);
        return $this->success(new PlaylistResource($playlist->load('songs')), 'Song added to playlist');
    }

    public function removeSong(Playlist $playlist, Song $song)
    {
        $this->authorize('update', $playlist);
        $playlist->songs()->detach($song->id);
        return $this->success(new PlaylistResource($playlist->load('songs')), 'Song removed from playlist');
    }
}
