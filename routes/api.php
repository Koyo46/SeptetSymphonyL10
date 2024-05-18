<?php

use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/set-session-id', [GameController::class, 'setSessionId']);

Route::post('/game/deal', function (Request $request) {
    return GameController::dealCard($request->gameId, $request->playerCount);
});
Route::post('/game/createGame', [GameController::class, 'createGame']);
Route::post('/game/joinGame', function (Request $request) {
    return GameController::joinGame($request->gameId);
});
Route::post('/player/ready', function (Request $request) {
    return GameController::beReady($request->name, $request->gameId);
});
Route::get('/game/{gameId}/players', function ($gameId) {
    return GameController::getPlayers($gameId);
});
Route::get('/game/{gameId}/myHandCards', function ($gameId) {
    return GameController::getMyHandCards($gameId);
});
Route::post('/game/{gameId}/start', function ($gameId) {
    return GameController::startGame($gameId);
});

