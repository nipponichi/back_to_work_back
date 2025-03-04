<?php

use App\Http\Controllers\PassportLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddsController;
use App\Http\Controllers\LoginController;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/signup', [LoginController::class, 'signup']);


Route::post('/login2', [PassportLoginController::class, 'login']);
Route::post('/signup2', [PassportLoginController::class, 'signup']);

Route::middleware(['auth.validation2'])->group(function () {
    Route::post('/userData2', [PassportLoginController::class, 'userProfile']);
});

Route::middleware(['auth.validation'])->group(function(){
    
    Route::middleware(['id.validation'])->group(function () {
        Route::get('/adds/{id}', [AddsController::class, 'show']);
        Route::put('/adds/{id}', [AddsController::class, 'update']);
        Route::delete('adds/{id}', [AddsController::class, 'destroy']);
    });

    Route::apiResource('/adds', AddsController::class)->except('show', 'update', 'destroy');

    Route::get('/userdata', [LoginController::class, 'userProfile']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/logout2', [PassportLoginController::class, 'logout']);

});  
 