<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// Public routes
Route::get('/artists', [ArtistController::class, 'index']);
Route::get('/artists/{id}', [ArtistController::class, 'show']);
Route::get('/albums', [AlbumController::class, 'index']);
Route::get('/albums/{id}', [AlbumController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/artists', [ArtistController::class, 'store']);
    Route::put('/artists/{id}', [ArtistController::class, 'update']);
    Route::patch('/artists/{id}', [ArtistController::class, 'update']);
    Route::delete('/artists/{id}', [ArtistController::class, 'destroy']);

    Route::post('/albums', [AlbumController::class, 'store']);
    Route::put('/albums/{id}', [AlbumController::class, 'update']);
    Route::patch('/albums/{id}', [AlbumController::class, 'update']);
    Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard routes
    Route::get('/dashboard/total-albums-sold-per-artist', [DashboardController::class, 'totalAlbumsSoldPerArtist']);
    Route::get('/dashboard/combined-album-sales-per-artist', [DashboardController::class, 'combinedAlbumSalesPerArtist']);
    Route::get('/dashboard/top-artist-by-combined-sales', [DashboardController::class, 'topArtistByCombinedSales']);
    Route::get('/dashboard/albums-by-artist', [DashboardController::class, 'albumsBySearchedArtist']);
});
