<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'artist' => $this->artist, 'album' => $this->album, 'genre' => $this->genre, 'duration' => $this->duration, 'cover_image' => $this->cover_image, 'local_file_identifier' => $this->local_file_identifier, 'created_by' => $this->created_by, 'created_at' => $this->created_at, 'updated_at' => $this->updated_at];
    }
}
