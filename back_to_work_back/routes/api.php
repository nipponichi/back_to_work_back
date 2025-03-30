<?php

use App\Http\Controllers\PassportLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddOfferController;
use App\Http\Controllers\AddController;
use App\Http\Controllers\AddChatController;



Route::post('/login', [PassportLoginController::class, 'login']);
Route::post('/signup', [PassportLoginController::class, 'signup']);

Route::middleware(['auth.validation'])->group(function () {
    Route::post('/userData', [PassportLoginController::class, 'userProfile']);

    Route::post('/logout', [PassportLoginController::class, 'logout']);

    Route::apiResource('offers', AddOfferController::class);
    
    Route::apiResource('adds', AddController::class);
    
    Route::apiResource('chats', AddChatController::class);
    Route::post('/destroyMessage', [AddChatController::class, 'destroyMessage']);
});
 