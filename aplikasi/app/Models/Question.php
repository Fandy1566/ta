<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function gameRoom()
    {
        return $this->belongsTo(GameRoom::class);
    }
}
