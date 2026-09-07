<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description, 'user_id' => $this->user_id, 'songs' => SongResource::collection($this->whenLoaded('songs')), 'created_at' => $this->created_at, 'updated_at' => $this->updated_at];
    }
}
