<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\V1\SmsIntegration;

Route::post('/register', [JWTController::class, 'register']);
Route::post('/login', [JWTController::class, 'login']);

Route::group(['middleware' => ['jwt.verify']], function($router) {
    Route::post('/logout', [JWTController::class, 'logout']);
    Route::post('/refresh', [JWTController::class, 'refresh']);
    Route::post('/profile', [JWTController::class, 'profile']);
    Route::get('/authenticate', [SmsIntegration::class, 'authenticate']);
    Route::get('/send-message', [SmsIntegration::class, 'index']);


});