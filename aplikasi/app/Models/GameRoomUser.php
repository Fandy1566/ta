<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRoomUser extends Model
{
    public function gameRoom()
    {
        return $this->belongsTo(GameRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
