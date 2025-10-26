<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiClient;

class MerchantDashboardController extends Controller
{
    protected ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $stats = [
            'productCount' => 128,
            'orderCount' => 56,
            'revenue' => 52340.75,
        ];

        return view('dashboard', [
            'title' => '商家仪表盘',
            'stats' => $stats,
        ]);
    }

    public function products()
    {
        $token = session('merchant_token');
        $res = $this->api->get('merchant/products', [], $token);
        $products = $res['data'] ?? ($res['items'] ?? []);

        return view('products', [
            'title' => '商品管理',
            'products' => $products,
        ]);
    }

    public function orders()
    {
        $token = session('merchant_token');
        $res = $this->api->get('merchant/orders', [], $token);
        $orders = $res['data'] ?? ($res['items'] ?? []);

        return view('orders', [
            'title' => '订单管理',
            'orders' => $orders,
        ]);
    }

    public function settings()
    {
        $token = session('merchant_token');
        $profileRes = $this->api->get('merchant/profile', [], $token);
        $shopRes = $this->api->get('merchant/shop', [], $token);

        $settings = [
            'shopName' => $shopRes['data']['shopName'] ?? ($shopRes['shopName'] ?? '我的店铺'),
            'contactName' => $profileRes['data']['contactName'] ?? null,
            'contactPhone' => $profileRes['data']['contactPhone'] ?? null,
        ];

        return view('settings', [
            'title' => '设置',
            'settings' => $settings,
        ]);
    }

    public function categories()
    {
        $res = $this->api->get('public-categories');
        $categories = $res['data'] ?? [];

        return view('categories', [
            'title' => '分类管理',
            'categories' => $categories,
        ]);
    }

    public function finance()
    {
        $token = session('merchant_token');
        $stats = $this->api->get('merchant/finance/stats', [], $token);
        $flows = $this->api->get('merchant/finance/fund-flow', [], $token);

        return view('finance', [
            'title' => '财务中心',
            'stats' => $stats['data'] ?? $stats,
            'flows' => $flows['data'] ?? $flows,
        ]);
    }

    public function withdrawals()
    {
        $token = session('merchant_token');
        $balance = $this->api->get('withdrawal/balance', [], $token);
        $list = $this->api->get('withdrawal/merchant/list', [], $token);

        return view('withdrawals', [
            'title' => '提现管理',
            'balance' => $balance['data'] ?? $balance,
            'withdrawals' => $list['data'] ?? $list,
        ]);
    }

    public function shop()
    {
        $token = session('merchant_token');
        $shop = $this->api->get('merchant/shop', [], $token);
        $stats = $this->api->get('merchant/shop/stats', [], $token);

        return view('shop', [
            'title' => '店铺信息',
            'shop' => $shop['data'] ?? $shop,
            'stats' => $stats['data'] ?? $stats,
        ]);
    }

    public function creditRating()
    {
        $token = session('merchant_token');
        $current = $this->api->get('credit-rating/merchant/current', [], $token);
        $history = $this->api->get('credit-rating/merchant/history', [], $token);
        $guide = $this->api->get('credit-rating/guide');

        return view('credit-rating', [
            'title' => '信用评级',
            'current' => $current['data'] ?? $current,
            'history' => $history['data'] ?? $history,
            'guide' => $guide['data'] ?? $guide,
        ]);
    }
}
