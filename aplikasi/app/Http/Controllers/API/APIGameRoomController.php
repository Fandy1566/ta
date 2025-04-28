<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GameRoom;
use App\Models\GameRoomUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIGameRoomController extends Controller
{
    public function getPlayers($id)
    {
        $players = GameRoomUser::where('game_room_id', $id)
            ->with('user:id,name')
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->user->id,
                    'name' => $player->user->name,
                    'score' => $player->score ?? 0,
                ];
            });

        return response()->json([
            'players' => $players
        ]);
    }
}
