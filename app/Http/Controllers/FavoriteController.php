<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\Song;

class FavoriteController extends ApiController
{
    public function index()
    {
        return $this->success(FavoriteResource::collection(Favorite::where('user_id', request()->user()->id)->with('song')->latest()->get()), 'Favorites retrieved');
    }

    public function store(Song $song)
    {
        $favorite = Favorite::firstOrCreate(['user_id' => request()->user()->id, 'song_id' => $song->id]);
        return $this->success(new FavoriteResource($favorite->load('song')), 'Song favorited', $favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Song $song)
    {
        Favorite::where('user_id', request()->user()->id)->where('song_id', $song->id)->delete();
        return $this->success(null, 'Song unfavorited');
    }
}
