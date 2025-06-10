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
            ->with('user:id,name,email')
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->user->id,
                    'name' => $player->user->name,
                    'email' => $player->user->email,
                    'score' => $player->score ?? 0,
                ];
            });

        return response()->json([
            'players' => $players
        ]);
    }

        public function updateScore(Request $request)
    {
        $player = GameRoomUser::where(
            'user_id', $request->user_id,
        )->where('game_room_id', $request->game_room_id)->first();
        $current_question = $player->correct + $player->wrong;
        $playersWithSameAnswers = GameRoomUser::where('game_room_id', $request->game_room_id)
            ->whereRaw('(correct + wrong) = ?', [$current_question])
            ->get();
        $base_score = 1000;
        $player->score += ($base_score / (count($playersWithSameAnswers)));
        if ($request->correct) {
            $player->correct += 1;
        } else {
            $player->wrong += 1;
        }
        $player->save();

        return response()->json(['message' => 'Answer status updated']);
    }

    public function updateHelp(Request $request)
    {
        $player = GameRoomUser::where(
            'user_id', $request->user_id,
        )->where('game_room_id', $request->game_room_id)->first();
        $player->help = !$player->help;
        $player->save();

        return response()->json(['message' => 'Help request logged']);
    }

        public function status($id)
    {
        $room = GameRoom::findOrFail($id);
        return response()->json([
            'status' => $room->status
        ]);
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'question_index' => 'required|integer',
            'score' => 'required|integer',
        ]);
    
        session(['current_question_index' => $request->question_index]);

        $player = GameRoomUser::where('game_room_id', $request->game_id)
                                          ->where('user_id', auth()->id())
                                          ->first();
    
        if ($player) {
            $player->score += $request->score;
            $player->save();
        }
    
        return response()->json(['status' => 'ok']);
    }
}
