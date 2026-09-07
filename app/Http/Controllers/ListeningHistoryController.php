<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListeningHistoryRequest;
use App\Http\Resources\ListeningHistoryResource;
use App\Models\Song;

class ListeningHistoryController extends ApiController
{
    public function index(\Illuminate\Http\Request $request)
    {
        $history = request()->user()->listeningHistories()->with('song')->latest('played_at')->paginate(min($request->integer('per_page', 20), 100));
        return $this->paginated($history, 'Listening history retrieved', ListeningHistoryResource::collection($history->items()));
    }

    public function store(StoreListeningHistoryRequest $request, Song $song)
    {
        $history = $request->user()->listeningHistories()->create([...$request->validated(), 'song_id' => $song->id, 'played_at' => $request->validated('played_at') ?? now()]);
        return $this->success(new ListeningHistoryResource($history->load('song')), 'Listening history recorded', 201);
    }

    public function destroy()
    {
        request()->user()->listeningHistories()->delete();
        return $this->success(null, 'Listening history deleted');
    }
}
