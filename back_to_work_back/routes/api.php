<?php

use App\Http\Controllers\PassportLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AddOfferController;
use App\Http\Controllers\AddController;
use App\Http\Controllers\AddChatController;

Route::apiResource('offers', AddOfferController::class);
Route::apiResource('adds', AddController::class);
Route::get('chats', [AddChatController::class, 'index']); // Listar todos los chats
Route::post('chats', [AddChatController::class, 'store']); // Enviar un mensaje
Route::get('chats/ad/{ad_id}', [AddChatController::class, 'getMessagesByAd']); // Mensajes de un anuncio

Route::post('/login', [LoginController::class, 'login']);
Route::post('/signup', [LoginController::class, 'signup']);


Route::post('/login2', [PassportLoginController::class, 'login']);
Route::post('/signup2', [PassportLoginController::class, 'signup']);

Route::middleware(['auth.validation2'])->group(function () {
    Route::post('/userData2', [PassportLoginController::class, 'userProfile']);
});

Route::middleware(['auth.validation'])->group(function(){
    
    Route::middleware(['id.validation'])->group(function () {
        Route::get('/students/{id}', [StudentController::class, 'show']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    });

    Route::apiResource('/students', StudentController::class)->except('show', 'update', 'destroy');
    Route::get('/students/{id}/subject', [TeacherController::class, 'getSubjects']);

    Route::apiResource('/subjects', SubjectController::class);
    Route::get('/subjects/{id}/student', [SubjectController::class, 'getStudents']);
    Route::get('/subjects/{id}/teacher', [SubjectController::class, 'getTeachers']);

    Route::apiResource('/teachers', TeacherController::class);
    Route::get('/teachers/{id}/subject', [TeacherController::class, 'getSubjects']);
    Route::get('/teachers/{id}/classroom', [TeacherController::class, 'getClassrooms']);

    Route::apiResource('/classrooms', ClassroomController::class);
    Route::get('/classrooms/{id}/teacher', [ClassroomController::class, 'getTeachers']);

    Route::get('/userdata', [LoginController::class, 'userProfile']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/logout2', [PassportLoginController::class, 'logout']);

});  
 