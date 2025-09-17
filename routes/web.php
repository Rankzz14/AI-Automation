<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RunController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/run', [RunController::class, 'index'])->name('run.index');
Route::post('/run', [RunController::class, 'run'])->name('run');   // <-- eksik olan bu
Route::get('/runs/{id}', [RunController::class, 'show'])->name('runs.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
