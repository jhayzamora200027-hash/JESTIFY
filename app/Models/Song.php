<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'artist', 'album', 'genre', 'duration', 'cover_image', 'local_file_identifier', 'created_by',
    ];

    protected function casts(): array
    {
        return ['duration' => 'integer'];
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_song')->withTimestamps();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function listeningHistories()
    {
        return $this->hasMany(ListeningHistory::class);
    }
}
