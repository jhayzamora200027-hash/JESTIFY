<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSongRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'], 'artist' => ['required', 'string', 'max:255'],
            'album' => ['nullable', 'string', 'max:255'], 'genre' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'cover_url' => ['nullable', 'url:https', 'max:2048'],
            'audio_url' => ['nullable', 'url:https', 'max:2048'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'local_file_identifier' => ['nullable', 'string', 'max:1024'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
