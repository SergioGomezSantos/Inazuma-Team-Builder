<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamBuilderController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TeamBuilderController::class, 'index'])->name('home');
Route::get('/team-builder', [TeamBuilderController::class, 'index'])->name('team-builder');
Route::get('/team-builder/{id}', [TeamBuilderController::class, 'show'])->name('team-builder.show');

Route::resource('teams', TeamController::class)->middleware('auth');
Route::get('/story', [TeamController::class, 'story'])->name('teams.story');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
