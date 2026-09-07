<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'song_id', 'played_at'];

    protected function casts(): array
    {
        return ['played_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
