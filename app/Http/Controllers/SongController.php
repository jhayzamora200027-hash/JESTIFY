<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSongRequest;
use App\Http\Requests\UpdateSongRequest;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends ApiController
{
    public function index(Request $request)
    {
        $songs = Song::query()->latest()->paginate($request->integer('per_page', 20));
        return $this->paginated($songs, 'Songs retrieved', SongResource::collection($songs->items()));
    }

    public function show(Song $song)
    {
        return $this->success(new SongResource($song), 'Song retrieved');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $songs = Song::query()
            ->when($query !== '', fn ($builder) => $builder->where(fn ($search) => $search
                ->where('title', 'ilike', "%{$query}%")
                ->orWhere('artist', 'ilike', "%{$query}%")
                ->orWhere('album', 'ilike', "%{$query}%")
                ->orWhere('genre', 'ilike', "%{$query}%")))
            ->latest()->paginate($request->integer('per_page', 20));

        return $this->paginated($songs, 'Search results retrieved', SongResource::collection($songs->items()));
    }

    public function store(StoreSongRequest $request)
    {
        $song = Song::create([...$request->validated(), 'created_by' => $request->user()->id]);
        return $this->success(new SongResource($song), 'Song metadata created', 201);
    }

    public function update(UpdateSongRequest $request, Song $song)
    {
        $this->authorize('update', $song);
        $song->update($request->validated());
        return $this->success(new SongResource($song->refresh()), 'Song metadata updated');
    }

    public function destroy(Song $song)
    {
        $this->authorize('delete', $song);
        $song->delete();
        return $this->success(null, 'Song metadata deleted');
    }
}
