<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MerchantAuthController;

Route::post('/merchant/register', [MerchantAuthController::class, 'register']);
Route::post('/merchant/login', [MerchantAuthController::class, 'login']);
Route::get('/merchant/profile', [MerchantAuthController::class, 'profile']);