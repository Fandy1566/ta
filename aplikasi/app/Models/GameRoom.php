<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRoom extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

}
