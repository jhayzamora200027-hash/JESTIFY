<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListeningHistoryRequest;
use App\Http\Resources\ListeningHistoryResource;
use App\Models\Song;

class ListeningHistoryController extends ApiController
{
    public function index()
    {
        return $this->success(ListeningHistoryResource::collection(request()->user()->listeningHistories()->with('song')->latest('played_at')->paginate(30)), 'Listening history retrieved');
    }

    public function store(StoreListeningHistoryRequest $request, Song $song)
    {
        $history = $request->user()->listeningHistories()->create([...$request->validated(), 'song_id' => $song->id, 'played_at' => $request->validated('played_at') ?? now()]);
        return $this->success(new ListeningHistoryResource($history->load('song')), 'Listening history recorded', 201);
    }
}
