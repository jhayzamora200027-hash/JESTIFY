<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'song' => new SongResource($this->whenLoaded('song')), 'played_at' => $this->played_at, 'created_at' => $this->created_at];
    }
}
