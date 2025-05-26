<?php

use App\Http\Controllers\PassportLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdOfferController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\AdChatController;
use App\Http\Controllers\AdCategoryController;
use App\Http\Controllers\UserStatsController;
use App\Http\Controllers\UserController;

Route::apiResource('categories', AdCategoryController::class);
Route::apiResource('offers', AdOfferController::class);
Route::get('/offers/{bid}/ad', [AdOfferController::class, 'getAdIdByBidId']);
Route::apiResource('ads', AdController::class);

// Rutas relacionadas con ads por usuario
Route::get('getAdsByUser/{id}', [AdController::class, 'getAdsByUserId']);

// Rutas para userstats protegidas con auth:api middleware
Route::apiResource('userstats', UserStatsController::class);
Route::apiResource('users', UserController::class);

// Bloquear y desbloquear usuario
Route::put('users/block/{id}', [UserController::class, 'blockUser']);
Route::put('users/unblock/{id}', [UserController::class, 'unblockUser']);

// Rutas para chats
Route::get('chats', [AdChatController::class, 'index']); // Listar todos los chats
Route::post('chats', [AdChatController::class, 'store']); // Enviar un mensaje
Route::get('chats/ad/{ad_id}', [AdChatController::class, 'getMessagesByAd']); // Mensajes de un anuncio

// Rutas para ofertas (pujas)
Route::get('offers/ad/{ad_id}', [AdOfferController::class, 'getOffersByAdId']); // Pujas de un anuncio
Route::post('/offers/{id}/mark-paid', [AdOfferController::class, 'markAsPaid']);

// Login y registro
Route::post('/login', [PassportLoginController::class, 'login']);
Route::post('/signup', [PassportLoginController::class, 'signup']);

// Rutas protegidas con middleware custom 'auth.validation2'
Route::middleware(['auth.validation2'])->group(function () {
    Route::post('/userData2', [PassportLoginController::class, 'userProfile']);
});

// Rutas protegidas con middleware custom 'auth.validation'
Route::middleware(['auth.validation'])->group(function () {
    // Aquí puedes añadir rutas protegidas que requieran 'auth.validation' y opcionalmente 'id.validation'
    Route::middleware(['id.validation'])->group(function () {
        // Añade aquí rutas si las hay
    });

    Route::get('/getStats', [UserStatsController::class, 'getStatsByUser']);

    Route::post('/logout', [PassportLoginController::class, 'logout']);
});

Route::get('/userstats/{userId}', [UserStatsController::class, 'countTotalRatingsByUser']);
Route::get('/userstats/list/{userId}', [UserStatsController::class, 'listRatingsByUser']);