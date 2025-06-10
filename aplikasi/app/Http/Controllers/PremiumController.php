<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function index()
    {
        return view('page.info.premium');
    }
}
