<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $publicRooms = GameRoom::where('is_private', false)
            ->whereIn('status', ['waiting'])
            ->orderByDesc('updated_at')
            ->get();

        $joinedRooms = GameRoom::whereIn('id', function ($query) use ($userId) {
            $query->select('game_room_id')
                    ->from('game_room_users')
                    ->where('user_id', $userId);
        })->orderByDesc('updated_at')->get();

        $hostedRooms = GameRoom::where('host_user_id', $userId)
        ->orderByDesc(column: 'updated_at')
        ->get();

        return view('page.room.index', [
            'joinedRooms' => $joinedRooms,
            'publicRooms' => $publicRooms,
            'hostedRooms' => $hostedRooms,
        ]);
    }
}
