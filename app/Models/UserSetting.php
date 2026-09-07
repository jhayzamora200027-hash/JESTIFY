<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = ['user_id', 'key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
