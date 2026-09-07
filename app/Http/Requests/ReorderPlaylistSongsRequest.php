<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderPlaylistSongsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'song_ids' => ['required', 'array', 'min:1'],
            'song_ids.*' => ['required', 'integer', 'distinct', 'exists:songs,id'],
        ];
    }
}
