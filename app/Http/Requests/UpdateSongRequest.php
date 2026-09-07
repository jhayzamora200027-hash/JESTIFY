<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSongRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'], 'artist' => ['sometimes', 'required', 'string', 'max:255'],
            'album' => ['sometimes', 'nullable', 'string', 'max:255'], 'genre' => ['sometimes', 'nullable', 'string', 'max:100'],
            'duration' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'cover_url' => ['sometimes', 'nullable', 'url:https', 'max:2048'],
            'audio_url' => ['sometimes', 'nullable', 'url:https', 'max:2048'],
            'cover_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'local_file_identifier' => ['sometimes', 'nullable', 'string', 'max:1024'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
