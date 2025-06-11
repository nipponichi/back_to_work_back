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
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\StripePaymentController;


Route::post('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')
    ->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')
    ->middleware('auth:sanctum');
Route::post('verify-email', [VerificationController::class, 'verify']);
Route::post('validate-reset-token', [VerificationController::class, 'validateResetToken']);



Route::apiResource('offers', AdOfferController::class);
Route::get('/offers/{bid}/ad', [AdOfferController::class, 'getAdIdByBidId']);
Route::get('offers/ad/{ad_id}', [AdOfferController::class, 'getOffersByAdId']);
Route::post('/offers/{id}/mark-paid', [AdOfferController::class, 'markAsPaid']);

Route::post('signup', [UserController::class, 'store']);


Route::apiResource('userstats', UserStatsController::class);
Route::middleware('auth:api')->apiResource('userstats', UserStatsController::class);


Route::post('update-password', [UserController::class, 'updatePassword']);

Route::get('chats', [AdChatController::class, 'index']);
Route::post('chats', [AdChatController::class, 'store']);
Route::get('chats/ad/{ad_id}', [AdChatController::class, 'getMessagesByAd']);

Route::apiResource('provinces', ProvinceController::class);


Route::get('welcome-mail', [MailController::class, 'welcomeMessage']);
Route::get('bid-notification-mail', [MailController::class, 'newBid']);
Route::post('reset-password', [MailController::class, 'requestPasswordReset']);

Route::post('/signup', [PassportLoginController::class, 'signup']);
Route::middleware(['mail.verification'])->group(function () {
    Route::post('/login', [PassportLoginController::class, 'login']);
});

Route::get('provinces', [ProvinceController::class, 'index']);
Route::get('categories/public', [AdCategoryController::class, 'index']);
Route::post('user/public', [UserController::class, 'store']);

Route::middleware(['auth.validation'])->group(function () {
    Route::apiResource('categories', AdCategoryController::class);
    Route::apiResource('users', UserController::class);
    Route::get('users/block/{id}', [UserController::class, 'blockUser']);
    Route::post('users/updateImage/{id}', [UserController::class, 'updateImage']);
    Route::get('getAdsWhereIAm', [AdController::class, 'getAdsWhereIAm']);
    Route::post('/logout', [PassportLoginController::class, 'logout']);
    Route::apiResource('chats', AdChatController::class);
    
    Route::apiResource('ads', AdController::class);
    Route::get('getAdsByUser/{id}', [AdController::class, 'getAdsByUserId']);
    Route::post('ad/done', [AdController::class, 'markAsDone']);
    Route::post('ads/pictures/upload', [AdController::class, 'uploadPicture']);
    Route::delete('ads/pictures/{id}', [AdController::class, 'deletePicture']);
    Route::get('/ads/verify/{id}', [AdController::class, 'verifyAd']);
    Route::apiResource('userstats', UserStatsController::class);
    Route::apiResource('claims', ClaimController::class);
    Route::post('/checkout', [StripePaymentController::class, 'createCheckoutSession']);

});  
 