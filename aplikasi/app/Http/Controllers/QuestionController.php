<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function store(Request $request, GameRoom $room)
    {
        $request->validate([
            'question_text' => 'required|string',
            'time_limit' => 'nullable|integer|min:0',
        ]);

        if ($room->status !== 'waiting') {
            return back()->with('error', 'Room sudah dimulai. Tidak bisa menambahkan soal.');
        }

        Question::create([
            'game_room_id' => $room->id,
            'question_text' => $request->question_text,
            'time_limit' => $request->time_limit,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        
        if ($question->gameRoom->status !== 'waiting') {
            return back()->with('error', 'Soal tidak bisa dihapus setelah game dimulai.');
        }

        if ($question->gameRoom->host_user_id !== Auth::id()) {
            abort(403, 'Kamu bukan host dari room ini.');
        }

        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required|string|max:255',
            'time_limit' => 'nullable|integer|min:0',
        ]);

        $question = Question::findOrFail($id);

        if ($question->gameRoom->status !== 'waiting') {
            return back()->with('error', 'Soal tidak bisa diedit setelah game dimulai.');
        }

        $question->question_text = $request->question_text;
        $question->time_limit = $request->time_limit ?? null;
        $question->save();

        return back()->with('success', 'Soal berhasil diperbarui.');
    }
}
