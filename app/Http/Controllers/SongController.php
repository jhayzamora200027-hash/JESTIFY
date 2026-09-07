<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSongRequest;
use App\Http\Requests\UpdateSongRequest;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SongController extends ApiController
{
    public function index(Request $request)
    {
        $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $songs = Song::query()
            ->where('is_published', true)
            ->when($request->filled('q'), fn ($query) => $query->where(function ($search) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $search->where('title', $operator, $term)->orWhere('artist', $operator, $term)->orWhere('album', $operator, $term)->orWhere('genre', $operator, $term);
            }))
            ->when($request->filled('genre'), fn ($query) => $query->where('genre', $request->string('genre')))
            ->when($request->filled('artist'), fn ($query) => $query->where('artist', $request->string('artist')))
            ->when($request->filled('album'), fn ($query) => $query->where('album', $request->string('album')))
            ->latest()->paginate(min($request->integer('per_page', 20), 100));
        return $this->paginated($songs, 'Songs retrieved', SongResource::collection($songs->items()));
    }

    public function show(Song $song)
    {
        abort_unless($song->is_published, 404);
        return $this->success(new SongResource($song), 'Song retrieved');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if ($query === '') return $this->success([], 'Search results retrieved');
        $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $songs = Song::query()
            ->where('is_published', true)
            ->where(fn ($search) => $search
            ->where('title', $operator, "%{$query}%")
            ->orWhere('artist', $operator, "%{$query}%")
            ->orWhere('album', $operator, "%{$query}%")
            ->orWhere('genre', $operator, "%{$query}%"))
            ->latest()->paginate(min($request->integer('per_page', 20), 100));

        return $this->success(SongResource::collection($songs->items()), 'Search results retrieved');
    }

    public function store(StoreSongRequest $request)
    {
        $data = $request->validated();
        $data['cover_url'] ??= $data['cover_image'] ?? null;
        $data['audio_url'] ??= $data['local_file_identifier'] ?? null;
        $song = Song::create([...$data, 'created_by' => $request->user()->id]);
        return $this->success(new SongResource($song), 'Song metadata created', 201);
    }

    public function update(UpdateSongRequest $request, Song $song)
    {
        $this->authorize('update', $song);
        $data = $request->validated();
        $data['cover_url'] ??= $data['cover_image'] ?? null;
        $data['audio_url'] ??= $data['local_file_identifier'] ?? null;
        $song->update($data);
        return $this->success(new SongResource($song->refresh()), 'Song metadata updated');
    }

    public function destroy(Song $song)
    {
        $this->authorize('delete', $song);
        $song->delete();
        return $this->success(null, 'Song metadata deleted');
    }
}
