<?php

use App\Http\Controllers\API\APIGameRoomController;
use App\Http\Controllers\GameRoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/join-room', [HomeController::class, 'join_by_code'])->name('room.joinByCode');


Route::get('/premium', function () {
    return view('page.info.premium');
})->name('premium');

Route::get('/premium-only', function () {
    return view('page.info.premium_only');
})->name('premium-only');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(GameRoomController::class)->prefix('/room')->name('gameRoom.')->group(function () {
        Route::get('/game/{id}', 'index')->name('index');
        Route::post('/start/{id}', 'startGame')->name('startGame');
        Route::post('/leave/{id}',  'leave')->name('leave');
        Route::get('/host/{id}', 'host')->name('host');
        Route::get('/waiting/{id}', 'waiting')->name('waiting');
        Route::get('/finished/{id}', 'finished')->name('finished');
        Route::post('/finished/{id}', 'finishGame')->name('finished');
        Route::get('/join/{id}', 'join')->name('join');
        Route::get('/create','create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::patch('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
    });

    Route::controller(RoomController::class)->prefix('/room')->name('room.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::post('/game-room/{room}/questions', [QuestionController::class, 'store'])->name('question.store');
    Route::delete('/question/{id}', [QuestionController::class, 'destroy'])->name('question.destroy');
    Route::put('/question/{id}', [QuestionController::class, 'update'])->name('question.update');


    // ==================== API ========================

    // Route::middleware('auth')->group(function () {    
    Route::get('/game-rooms/{id}/players', [APIGameRoomController::class, 'getPlayers']);
    Route::get('/game-room/{id}/status', [GameRoomController::class, 'status'])->name('gameRoom.status');

    // });
    


});



require __DIR__.'/auth.php';
