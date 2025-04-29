<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = GameRoom::where('is_private', false)
        ->whereIn('status', ['waiting'])
        ->orderByDesc('updated_at')
        ->get();

        return view('page.home.index', compact('rooms'));
    }

    public function join_by_code(Request $request)
    {
        $room = GameRoom::where('room_code', $request->room_code)->first();
    
        if (!$room) {
            return back()->with('error', 'Kode ruangan tidak ditemukan.');
        }
    
        return redirect()->route('gameRoom.join', $room->id);
    }
    
}
