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

Route::apiResource('categories',AdCategoryController::class);
Route::apiResource('offers', AdOfferController::class);
Route::get('/offers/{bid}/ad', [AdOfferController::class, 'getAdIdByBidId']);
Route::apiResource('ads', AdController::class);
<<<<<<< Updated upstream
Route::apiResource('userstats', UserStatsController::class);
=======
Route::get('getAdsByUser/{id}', [AdController::class, 'getAdsByUserId']);
//Route::apiResource('userstats', UserStatsController::class);
>>>>>>> Stashed changes
Route::apiResource('users', UserController::class);
Route::middleware('auth:api')->apiResource('userstats', UserStatsController::class);


Route::put('users/block/{id}', [UserController::class, 'blockUser']);
Route::put('users/unblock/{id}', [UserController::class, 'unblockUser']);

Route::get('chats', [AdChatController::class, 'index']); // Listar todos los chats
Route::post('chats', [AdChatController::class, 'store']); // Enviar un mensaje
Route::get('chats/ad/{ad_id}', [AdChatController::class, 'getMessagesByAd']); // Mensajes de un anuncio
Route::get('offers/ad/{ad_id}', [AdOfferController::class, 'getOffersByAdId']); // Pujas de un anuncio
Route::post('/offers/{id}/mark-paid', [AdOfferController::class, 'markAsPaid']);

Route::post('/login', [PassportLoginController::class, 'login']);
Route::post('/signup', [PassportLoginController::class, 'signup']);

Route::middleware(['auth.validation2'])->group(function () {
    Route::post('/userData2', [PassportLoginController::class, 'userProfile']);
});

Route::middleware(['auth.validation'])->group(function(){
    
    Route::middleware(['id.validation'])->group(function () {
    });

    Route::post('/logout', [PassportLoginController::class, 'logout']);

});  
 