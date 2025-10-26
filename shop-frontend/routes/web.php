<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Desktop\HomeController as DesktopHomeController;
use App\Http\Controllers\Desktop\ProductController as DesktopProductController;
use App\Http\Controllers\Desktop\CategoryController as DesktopCategoryController;
use App\Http\Controllers\Mobile\HomeController as MobileHomeController;
use App\Http\Controllers\Mobile\ProductController as MobileProductController;
use App\Http\Controllers\Mobile\CategoryController as MobileCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 根路径路由（设备检测中间件会自动重定向）
Route::get('/', function () {
    return redirect('/desktop/');
});

// 设备切换路由（在中间件之前）
Route::get('/switch-device/{device}', function ($device) {
    if (in_array($device, ['desktop', 'mobile'])) {
        session(['device_type' => $device]);
        return redirect('/' . $device . '/');
    }
    return redirect()->back();
})->name('switch.device');

// 语言切换
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'zh', 'ms'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('locale');

// 设备检测中间件会自动重定向到对应的设备路径
// 桌面端路由
Route::prefix('desktop')->group(function () {
    // 首页
    Route::get('/', [DesktopHomeController::class, 'index'])->name('desktop.home');
    
    // 商品路由
    Route::get('/products', [DesktopProductController::class, 'index'])->name('desktop.products.index');
    Route::get('/products/{product}', [DesktopProductController::class, 'show'])->name('desktop.products.show');
    
    // 分类路由
    Route::get('/categories', [DesktopCategoryController::class, 'index'])->name('desktop.categories');
    Route::get('/categories/{category}', [DesktopCategoryController::class, 'show'])->name('desktop.categories.show');
    
    // 认证路由
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('desktop.login');
        Route::post('/login', [LoginController::class, 'login']);
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('desktop.register');
        Route::post('/register', [RegisterController::class, 'register']);
    });
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('desktop.logout');
    
    // 需要认证的路由
    Route::middleware('auth')->group(function () {
        // 购物车
        Route::get('/cart', [CartController::class, 'index'])->name('desktop.cart');
        Route::post('/cart/add', [CartController::class, 'add'])->name('desktop.cart.add');
        Route::put('/cart/{cart}', [CartController::class, 'update'])->name('desktop.cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('desktop.cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('desktop.cart.clear');
        Route::get('/cart/count', [CartController::class, 'count'])->name('desktop.cart.count');
        
        // 订单
        Route::get('/orders', [OrderController::class, 'index'])->name('desktop.orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('desktop.orders.show');
        Route::post('/orders', [OrderController::class, 'store'])->name('desktop.orders.store');
        Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('desktop.orders.cancel');
        
        // 个人中心
        Route::get('/profile', [ProfileController::class, 'show'])->name('desktop.profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('desktop.profile.update');
    });
});

// 移动端路由
Route::prefix('mobile')->group(function () {
    // 首页
    Route::get('/', [MobileHomeController::class, 'index'])->name('mobile.home');
    
    // 商品路由
    Route::get('/products', [MobileProductController::class, 'index'])->name('mobile.products.index');
    Route::get('/products/{product}', [MobileProductController::class, 'show'])->name('mobile.products.show');
    
    // 分类路由
    Route::get('/categories', [MobileCategoryController::class, 'index'])->name('mobile.categories');
    Route::get('/categories/{category}', [MobileCategoryController::class, 'show'])->name('mobile.categories.show');
    
    // 认证路由
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('mobile.login');
        Route::post('/login', [LoginController::class, 'login']);
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('mobile.register');
        Route::post('/register', [RegisterController::class, 'register']);
    });
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('mobile.logout');
    
    // 需要认证的路由
    Route::middleware('auth')->group(function () {
        // 购物车
        Route::get('/cart', [CartController::class, 'index'])->name('mobile.cart');
        Route::post('/cart/add', [CartController::class, 'add'])->name('mobile.cart.add');
        Route::put('/cart/{cart}', [CartController::class, 'update'])->name('mobile.cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('mobile.cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('mobile.cart.clear');
        Route::get('/cart/count', [CartController::class, 'count'])->name('mobile.cart.count');
        
        // 订单
        Route::get('/orders', [OrderController::class, 'index'])->name('mobile.orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('mobile.orders.show');
        Route::post('/orders', [OrderController::class, 'store'])->name('mobile.orders.store');
        Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('mobile.orders.cancel');
        
        // 个人中心
        Route::get('/profile', [ProfileController::class, 'show'])->name('mobile.profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('mobile.profile.update');
    });
});
