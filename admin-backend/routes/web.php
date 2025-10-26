<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendProductController;
use App\Http\Controllers\InviteCodeController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\RechargeController;
use App\Http\Controllers\CreditRatingController;
use App\Http\Controllers\FundManagementController;
use App\Http\Controllers\FeaturedProductController;

// 重定向根路径到管理后台
Route::get('/', function () {
    return redirect('/admin/dashboard');
});

// 管理员登录路由
Route::prefix('admin')->group(function () {
    // 登录页面
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    // 登出
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/logout', [AdminAuthController::class, 'logout']);
});

// 受保护的管理后台路由
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    // 仪表盘
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // 商家管理
    Route::get('/merchants', [AdminController::class, 'merchants'])->name('admin.merchants');
    Route::resource('merchants', MerchantController::class)->names([
        'index' => 'admin.merchants.index',
        'create' => 'admin.merchants.create',
        'store' => 'admin.merchants.store',
        'show' => 'admin.merchants.show',
        'edit' => 'admin.merchants.edit',
        'update' => 'admin.merchants.update',
        'destroy' => 'admin.merchants.destroy',
    ]);
    Route::post('/merchants/bulk-action', [MerchantController::class, 'bulkAction'])->name('admin.merchants.bulk-action');
    Route::post('/merchants/{merchant}/status', [MerchantController::class, 'updateStatus'])->name('admin.merchants.update-status');
    Route::post('/merchants/{merchant}/verify', [MerchantController::class, 'verify'])->name('admin.merchants.verify');
    Route::post('/merchants/{merchant}/unverify', [MerchantController::class, 'unverify'])->name('admin.merchants.unverify');
    
    // 订单管理
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::resource('orders', OrderController::class)->names([
        'index' => 'admin.orders.index',
        'show' => 'admin.orders.show',
    ]);
    Route::post('/orders/bulk-action', [OrderController::class, 'bulkAction'])->name('admin.orders.bulk-action');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::post('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('admin.orders.update-payment-status');
    Route::post('/orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('admin.orders.update-shipping');
    Route::get('/orders/statistics', [OrderController::class, 'statistics'])->name('admin.orders.statistics');
    
    // 商品管理
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::resource('products', ProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('admin.products.bulk-action');
    
    // 分类管理
    Route::resource('categories', CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);
    Route::post('/categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('admin.categories.bulk-action');
    Route::post('/categories/update-sort', [CategoryController::class, 'updateSort'])->name('admin.categories.update-sort');
    
    // 用户管理
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('admin.users.bulk-action');
    Route::post('/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.update-status');
    Route::get('/users/statistics', [UserController::class, 'statistics'])->name('admin.users.statistics');
    
    // 分类管理
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    
    // 财务管理
    Route::get('/finance', [AdminController::class, 'finance'])->name('admin.finance');
    
    // 提现管理
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('admin.withdrawals');
    
    // 管理员账户管理
    Route::resource('admin-accounts', AdminAccountController::class)->names([
        'index' => 'admin.admin-accounts.index',
        'create' => 'admin.admin-accounts.create',
        'store' => 'admin.admin-accounts.store',
        'show' => 'admin.admin-accounts.show',
        'edit' => 'admin.admin-accounts.edit',
        'update' => 'admin.admin-accounts.update',
        'destroy' => 'admin.admin-accounts.destroy',
    ]);
    Route::post('/admin-accounts/bulk-action', [AdminAccountController::class, 'bulkAction'])->name('admin.admin-accounts.bulk-action');
    Route::post('/admin-accounts/{admin}/status', [AdminAccountController::class, 'updateStatus'])->name('admin.admin-accounts.update-status');
    Route::post('/admin-accounts/{admin}/reset-password', [AdminAccountController::class, 'resetPassword'])->name('admin.admin-accounts.reset-password');
    Route::get('/admin-accounts/statistics', [AdminAccountController::class, 'statistics'])->name('admin.admin-accounts.statistics');
    
    // 个人中心
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::post('/profile/update-profile', [ProfileController::class, 'updateProfile'])->name('admin.profile.update-profile');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');
    Route::post('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->name('admin.profile.update-avatar');
    Route::post('/profile/update-notifications', [ProfileController::class, 'updateNotifications'])->name('admin.profile.update-notifications');
    Route::post('/profile/update-settings', [ProfileController::class, 'updateSettings'])->name('admin.profile.update-settings');
    Route::get('/profile/login-history', [ProfileController::class, 'loginHistory'])->name('admin.profile.login-history');
    Route::get('/profile/activity-log', [ProfileController::class, 'activityLog'])->name('admin.profile.activity-log');
    Route::get('/profile/export-data', [ProfileController::class, 'exportData'])->name('admin.profile.export-data');
    
    // 推荐商品管理
    Route::resource('recommend-products', RecommendProductController::class)->names([
        'index' => 'admin.recommend-products.index',
        'create' => 'admin.recommend-products.create',
        'store' => 'admin.recommend-products.store',
        'show' => 'admin.recommend-products.show',
        'edit' => 'admin.recommend-products.edit',
        'update' => 'admin.recommend-products.update',
        'destroy' => 'admin.recommend-products.destroy',
    ]);
    Route::post('/recommend-products/bulk-action', [RecommendProductController::class, 'bulkAction'])->name('admin.recommend-products.bulk-action');
    Route::post('/recommend-products/update-sort', [RecommendProductController::class, 'updateSort'])->name('admin.recommend-products.update-sort');
    Route::get('/recommend-products/statistics', [RecommendProductController::class, 'statistics'])->name('admin.recommend-products.statistics');
    Route::post('/recommend-products/auto-recommend', [RecommendProductController::class, 'autoRecommend'])->name('admin.recommend-products.auto-recommend');
    
    // 邀请码管理
    Route::resource('invite-codes', InviteCodeController::class)->names([
        'index' => 'admin.invite-codes.index',
        'create' => 'admin.invite-codes.create',
        'store' => 'admin.invite-codes.store',
        'show' => 'admin.invite-codes.show',
        'edit' => 'admin.invite-codes.edit',
        'update' => 'admin.invite-codes.update',
        'destroy' => 'admin.invite-codes.destroy',
    ]);
    Route::post('/invite-codes/bulk-action', [InviteCodeController::class, 'bulkAction'])->name('admin.invite-codes.bulk-action');
    Route::post('/invite-codes/{inviteCode}/status', [InviteCodeController::class, 'updateStatus'])->name('admin.invite-codes.update-status');
    Route::post('/invite-codes/batch-generate', [InviteCodeController::class, 'batchGenerate'])->name('admin.invite-codes.batch-generate');
    Route::get('/invite-codes/statistics', [InviteCodeController::class, 'statistics'])->name('admin.invite-codes.statistics');
    Route::get('/invite-codes/{inviteCode}/usages', [InviteCodeController::class, 'usages'])->name('admin.invite-codes.usages');
    
    // 提现管理
    Route::resource('withdrawals', WithdrawalController::class)->names([
        'index' => 'admin.withdrawals.index',
        'show' => 'admin.withdrawals.show',
    ]);
    Route::post('/withdrawals/{withdrawal}/process', [WithdrawalController::class, 'process'])->name('admin.withdrawals.process');
    Route::post('/withdrawals/{withdrawal}/complete', [WithdrawalController::class, 'complete'])->name('admin.withdrawals.complete');
    Route::post('/withdrawals/{withdrawal}/cancel', [WithdrawalController::class, 'cancel'])->name('admin.withdrawals.cancel');
    Route::post('/withdrawals/bulk-action', [WithdrawalController::class, 'bulkAction'])->name('admin.withdrawals.bulk-action');
    Route::get('/withdrawals/statistics', [WithdrawalController::class, 'statistics'])->name('admin.withdrawals.statistics');
    Route::get('/withdrawals/export', [WithdrawalController::class, 'export'])->name('admin.withdrawals.export');
    
    // 充值审核管理
    Route::resource('recharge', RechargeController::class)->names([
        'index' => 'admin.recharge.index',
        'show' => 'admin.recharge.show',
    ]);
    Route::post('/recharge/{rechargeRequest}/audit', [RechargeController::class, 'audit'])->name('admin.recharge.audit');
    Route::post('/recharge/{rechargeRequest}/complete', [RechargeController::class, 'complete'])->name('admin.recharge.complete');
    Route::post('/recharge/{rechargeRequest}/cancel', [RechargeController::class, 'cancel'])->name('admin.recharge.cancel');
    Route::post('/recharge/bulk-action', [RechargeController::class, 'bulkAction'])->name('admin.recharge.bulk-action');
    Route::get('/recharge/statistics', [RechargeController::class, 'statistics'])->name('admin.recharge.statistics');
    Route::get('/recharge/export', [RechargeController::class, 'export'])->name('admin.recharge.export');
    
    // 信用评级管理
    Route::resource('credit-ratings', CreditRatingController::class)->names([
        'index' => 'admin.credit-ratings.index',
        'show' => 'admin.credit-ratings.show',
        'update' => 'admin.credit-ratings.update',
    ]);
    Route::post('/credit-ratings/{creditRating}/recalculate', [CreditRatingController::class, 'recalculate'])->name('admin.credit-ratings.recalculate');
    Route::post('/credit-ratings/bulk-recalculate', [CreditRatingController::class, 'bulkRecalculate'])->name('admin.credit-ratings.bulk-recalculate');
    Route::post('/credit-rating-records/{record}/verify', [CreditRatingController::class, 'verifyRecord'])->name('admin.credit-rating-records.verify');
    Route::delete('/credit-rating-records/{record}', [CreditRatingController::class, 'deleteRecord'])->name('admin.credit-rating-records.delete');
    Route::get('/credit-ratings/statistics', [CreditRatingController::class, 'statistics'])->name('admin.credit-ratings.statistics');
    Route::get('/credit-ratings/export', [CreditRatingController::class, 'export'])->name('admin.credit-ratings.export');
    
    // 资金管理
    Route::resource('fund-management', FundManagementController::class)->names([
        'index' => 'admin.fund-management.index',
        'create' => 'admin.fund-management.create',
        'store' => 'admin.fund-management.store',
        'show' => 'admin.fund-management.show',
        'update' => 'admin.fund-management.update',
    ]);
    Route::post('/fund-management/{fundAccount}/adjust-balance', [FundManagementController::class, 'adjustBalance'])->name('admin.fund-management.adjust-balance');
    Route::post('/fund-management/transfer', [FundManagementController::class, 'transfer'])->name('admin.fund-management.transfer');
    Route::get('/fund-management/statistics', [FundManagementController::class, 'statistics'])->name('admin.fund-management.statistics');
    Route::get('/fund-management/export', [FundManagementController::class, 'export'])->name('admin.fund-management.export');
    
    // 特色商品管理
    Route::resource('featured-products', FeaturedProductController::class)->names([
        'index' => 'admin.featured-products.index',
        'store' => 'admin.featured-products.store',
        'update' => 'admin.featured-products.update',
        'destroy' => 'admin.featured-products.destroy',
    ]);
    Route::post('/featured-products/update-sort', [FeaturedProductController::class, 'updateSortOrder'])->name('admin.featured-products.update-sort');
    Route::post('/featured-products/{featuredProduct}/toggle-status', [FeaturedProductController::class, 'toggleStatus'])->name('admin.featured-products.toggle-status');
    Route::post('/featured-products/bulk-action', [FeaturedProductController::class, 'bulkAction'])->name('admin.featured-products.bulk-action');
});
