<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ListeningHistoryController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/songs', [SongController::class, 'index']);
Route::get('/songs/{song}', [SongController::class, 'show']);
Route::get('/search', [SongController::class, 'search']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::put('/settings', [SettingsController::class, 'update']);
    Route::post('/songs', [SongController::class, 'store']);
    Route::put('/songs/{song}', [SongController::class, 'update']);
    Route::delete('/songs/{song}', [SongController::class, 'destroy']);

    Route::apiResource('playlists', PlaylistController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'addSong']);
    Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{song}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{song}', [FavoriteController::class, 'destroy']);

    Route::get('/history', [ListeningHistoryController::class, 'index']);
    Route::post('/history/{song}', [ListeningHistoryController::class, 'store']);
});
