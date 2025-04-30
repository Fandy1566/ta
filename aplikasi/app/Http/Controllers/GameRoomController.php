<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use App\Models\GameRoomUser;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class GameRoomController extends Controller
{
    public function index($id)
    {
        $room = GameRoom::findOrFail($id);
    
        $players = GameRoomUser::with('user')
            ->where('game_room_id', $id)
            ->get();
    
        $questions = $room->questions;

        // dd($questions);
    
        return view('page.gameRoom.index', [
            'room' => $room,
            'players' => $players,
            'questions' => $questions,
        ]);
    }
    
    public function waiting($id)
    {
        $room = GameRoom::findOrFail($id);
        $players = GameRoomUser::with('user')->where('game_room_id', $id)->get();
    
        return view('page.gameRoom.waiting', compact('room', 'players'));
    }

    public function finished($id)
    {
        $userId = auth()->id();
    
        // Cek apakah user pernah bermain di room ini
        $gameRoomUser = GameRoomUser::where('game_room_id', $id)
            ->where('user_id', $userId)
            ->first();
    
        return view('page.gameRoom.finished', [
            'score' => $gameRoomUser?->score,
        ]);
    }
    public function create()
    {
        return view('page.gameRoom.create');
    }

    public function store(Request $request)
    {


        if (Auth::user()->is_premium()) {
            $room = $this->create_room($request);
        } else {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $getRoomCountForHostThisMonth = GameRoom::where('host_user_id', Auth::id())->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

            if (
                $getRoomCountForHostThisMonth <=10 &&
                $request->max_players <=15 &&
                !$request->is_private &&
                ($request->room_code == '' || $request->room_code == null)
            ) {
                $room = $this->create_room($request);
            } else {
                return redirect()->back()->with('error', 'Game room gagal dibuat, jumlah room telah mencapai batas maksimum untuk bulan ini');
            }
        }

        return redirect()->route('gameRoom.host', $room->id)->with('success', 'Game room berhasil dibuat');
    }

    private function create_room(Request $request)
    {
        $room = new GameRoom();
        $room->name = $request->name;
        $room->host_user_id = Auth::id();
        $room->max_players = $request->max_players ?? 4;
        $room->current_players = $request->current_players ?? 0;
        $room->is_private = $request->has('is_private') ? 1 : 0;
        $room->password = $request->password;
        $room->status = $request->status ?? 'waiting';
        $room->room_code = $request->room_code?? $this->generateCodeRoom();

        $room->save();

        if (is_array($request->question_text) && is_array($request->time_limit)) {
            $questions = [];
    
            for ($i = 0; $i < count($request->question_text); $i++) {
                $questions[] = [
                    'game_room_id' => $room->id,
                    'question_text' => $request->question_text[$i],
                    'time_limit' => $request->time_limit[$i] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            Question::insert($questions);
        }

        return $room;
    }

    private function generateCodeRoom($length = 8)
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        do {
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
        } while (GameRoom::where('room_code', $randomString)->exists()); // Pastikan unik

        return $randomString;
    }

    public function show($id)
    {
        $room = GameRoom::findOrFail($id);
        return view('game_rooms.show', compact('room'));
    }

    public function edit($id)
    {
        $room = GameRoom::findOrFail($id);
        return view('game_rooms.edit', compact('room'));
    }

    public function update(Request $request, $id)
    {
        $room = GameRoom::findOrFail($id);
        $room->name = $request->name ?? $room->name;
        $room->host_user_id = $request->host_user_id ?? $room->host_user_id;
        $room->max_players = $request->max_players ?? $room->max_players;
        $room->current_players = $request->current_players ?? $room->current_players;
        $room->is_private = $request->has('is_private') ? 1 : 0;
        $room->password = $request->password ?? $room->password;
        $room->status = $request->status ?? $room->status;
        $room->settings = $request->settings ? json_encode($request->settings) : $room->settings;
        $room->save();

        return redirect()->back()->with('success', 'Game room berhasil diperbarui');
    }

    public function destroy($id)
    {
        $room = GameRoom::findOrFail($id);
        $room->delete();

        return redirect()->back()->with('success', 'Game room berhasil dihapus');
    }

    public function join($roomId)
    {
        $userId = Auth::id();
    
        $room = GameRoom::findOrFail($roomId);
        $user = User::findOrFail($userId);

        if ($room->host_user_id == Auth::id()) {
            return redirect()->route('gameRoom.host', ['id' => $room->id]);
        }
    
        $alreadyJoined = GameRoomUser::where('game_room_id', $room->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($room->status === 'active' && $alreadyJoined) {
            return redirect()->route('gameRoom.index', ['id' => $room->id]);
        }
    
        if (($room->status === 'active' || $room->status === 'finished') && !$alreadyJoined) {
            return redirect()->back()->with('error', 'Permainan sudah dimulai atau selesai. Tidak bisa bergabung.');
        }
    
        if ($room->current_players >= $room->max_players && !$alreadyJoined) {
            return redirect()->back()->with('error', 'Room sudah penuh');
        }
    
        if (!$alreadyJoined) {
            $roomUser = new GameRoomUser();
            $roomUser->game_room_id = $room->id;
            $roomUser->user_id = $user->id;
            $roomUser->save();
    
            $room->increment('current_players');
        }

        return redirect()->route('gameRoom.waiting', ['id' => $room->id]);
    }
    
    public function leave($roomId)
    {
        $userId = Auth::id();
        $room = GameRoom::findOrFail($roomId);

        $roomUser = GameRoomUser::where('game_room_id', $roomId)
            ->where('user_id', $userId)
            ->first();

        if ($roomUser) {
            $roomUser->delete();
            $room->decrement('current_players');
        }

        return redirect()->route('home.index')->with('success', 'Kamu telah keluar dari room.');
    }

    public function host($id)
    {
        $room = GameRoom::with('questions')->findOrFail($id);
    
        if ($room->host_user_id !== auth()->id()) {
            abort(403, 'Kamu bukan host dari room ini.');
        }
    
        $players = GameRoomUser::where('game_room_id', $id)->with('user')->get();
    
        return view('page.gameRoom.host', [
            'room' => $room,
            'players' => $players,
            'questions' => $room->questions,
        ]);
    }

    public function startGame($id)
    {
        $room = GameRoom::findOrFail($id);

        if ($room->host_user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Hanya host yang dapat memulai permainan.');
        }

        $room->status = 'active';
        $room->save();

        return redirect()->back()->with('success', 'Permainan telah dimulai!');
    }

    public function status($id)
    {
        $room = GameRoom::findOrFail($id);
        return response()->json([
            'status' => $room->status
        ]);
    }

    public function finishGame($id)
    {
        $room = GameRoom::findOrFail($id);

        if (Auth::id() !== $room->host_user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyelesaikan game ini.');
        }

        $room->status = 'finished';
        $room->save();

        return redirect()->route('gameRoom.host', $room->id)->with('success', 'Game telah diselesaikan.');
    }

}