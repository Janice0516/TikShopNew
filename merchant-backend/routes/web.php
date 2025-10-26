<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MerchantAuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return redirect('/merchant/dashboard');
});

// 登录相关
Route::get('/merchant/login', function () {
    return view('auth.login');
});

Route::post('/merchant/login', [MerchantAuthController::class, 'login']);

Route::get('/merchant/logout', function () {
    session()->forget(['merchant_token', 'merchant_info']);
    return redirect('/merchant/login');
});

// 注册入口
Route::get('/merchant/register', function () {
    return view('auth.register');
});

Route::post('/merchant/register', [MerchantAuthController::class, 'register']);

// 受保护的商家端页面
Route::prefix('merchant')
    ->middleware(\App\Http\Middleware\MerchantAuth::class)
    ->group(function () {
        Route::get('/dashboard', [MerchantController::class, 'dashboard']);
        Route::get('/products', [MerchantController::class, 'products']);
        Route::get('/orders', [MerchantController::class, 'orders']);
        Route::get('/orders/{id}', [MerchantController::class, 'orderDetail']);
        Route::post('/orders/{id}/status', [MerchantController::class, 'updateOrderStatus']);
        Route::get('/select-products', [MerchantController::class, 'selectProducts']);
        Route::get('/finance', [MerchantController::class, 'finance']);
        Route::get('/withdrawals', [MerchantController::class, 'withdrawals']);
        Route::post('/withdrawals', [MerchantController::class, 'createWithdrawal']);
        Route::match(['GET', 'POST'], '/shop', [MerchantController::class, 'shop']);
        Route::get('/credit-rating', [MerchantController::class, 'creditRating']);
        Route::match(['GET', 'POST'], '/settings', [MerchantController::class, 'settings']);
        Route::post('/add-product', [MerchantController::class, 'addProduct']);
        
        // 数据导出
        Route::post('/export/orders', [ExportController::class, 'exportOrders']);
        Route::post('/export/products', [ExportController::class, 'exportProducts']);
        Route::post('/export/finance', [ExportController::class, 'exportFinance']);
        Route::post('/export/withdrawals', [ExportController::class, 'exportWithdrawals']);
        Route::post('/export/report', [ExportController::class, 'exportReport']);
        
        // 语言切换
        Route::post('/language/switch', [LanguageController::class, 'switch']);
        Route::get('/language/current', [LanguageController::class, 'current']);
        Route::get('/language/supported', [LanguageController::class, 'supported']);
        
        // 商品管理
        Route::get('/products/edit/{id}', [MerchantController::class, 'editProduct']);
        Route::post('/products/edit/{id}', [MerchantController::class, 'editProduct']);
        Route::delete('/products/{id}', [MerchantController::class, 'deleteProduct']);
        Route::post('/products/bulk-action', [MerchantController::class, 'bulkAction']);
    });