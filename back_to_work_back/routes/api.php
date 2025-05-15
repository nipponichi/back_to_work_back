<?php

use App\Http\Controllers\PassportLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdOfferController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\AdChatController;
use App\Http\Controllers\AdCategoryController;
use App\Http\Controllers\UserStatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\VerificationController;


Route::post('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')
    ->name('verification.verify');

Route::post('email/resend', 'Auth\VerificationController@resend')
    ->middleware('auth:sanctum');

Route::post('verify-email', [VerificationController::class, 'verify']);

Route::apiResource('categories',AdCategoryController::class);
Route::apiResource('offers', AdOfferController::class);
Route::apiResource('ads', AdController::class);
Route::apiResource('userstats', UserStatsController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('provinces', ProvinceController::class);


Route::post('reset-password', [MailController::class, 'requestPasswordReset']);

Route::post('validate-reset-token', [VerificationController::class, 'validateResetToken']);

Route::post('update-password', [UserController::class, 'updatePassword']);

Route::get('welcome-mail', [MailController::class, 'welcomeMessage']);
Route::get('bid-notification-mail', [MailController::class, 'newBid']);


Route::get('chats', [AdChatController::class, 'index']);
Route::post('chats', [AdChatController::class, 'store']);
Route::get('chats/ad/{ad_id}', [AdChatController::class, 'getMessagesByAd']);
Route::get('offers/ad/{ad_id}', [AdOfferController::class, 'getOffersByAdId']);
//Route::post('/login', [PassportLoginController::class, 'login']);
Route::post('/signup', [PassportLoginController::class, 'signup']);

Route::middleware(['auth.validation2'])->group(function () {
    Route::post('/userData2', [PassportLoginController::class, 'userProfile']);
});

Route::middleware(['mail.verification'])->group(function () {
    Route::post('/login', [PassportLoginController::class, 'login']);
});

Route::middleware(['auth.validation'])->group(function(){
    
    Route::middleware(['id.validation'])->group(function () {
    });

    Route::post('/logout', [PassportLoginController::class, 'logout']);

});  
 