<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = GameRoom::where('is_private', false)->with('user')->get();
        return view('page.home.index', compact('rooms'));
    }
    
}
