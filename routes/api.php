<?php

use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\ImageManipulationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource('albums', AlbumController::class);
        Route::get('images', [ImageManipulationController::class, 'index']);
        Route::get('images/by-album/{album}', [ImageManipulationController::class, 'byAlbum']);
        Route::get('images/{image}', [ImageManipulationController::class, 'show']);
        Route::post('images/resize', [ImageManipulationController::class, 'resize']);
        Route::delete('images/{image}', [ImageManipulationController::class, 'destroy']);
    });
});
