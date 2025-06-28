<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TeamController::class, 'create'])->name('home');
Route::get('/story', [TeamController::class, 'story'])->name('teams.story');
Route::get('/top-players', [App\Http\Controllers\StatsController::class, 'index'])->name('stats.top-players')->middleware('auth');

Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store')->middleware('auth');

Route::resource('teams', TeamController::class)
    ->except(['create', 'store', 'show'])
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::get('/teams/{team}/players', [TeamController::class, 'players'])->name('teams.players');
});

Route::get('/dashboard', function () {
    // return view('dashboard');
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
