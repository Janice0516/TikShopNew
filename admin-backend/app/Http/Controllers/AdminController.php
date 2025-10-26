<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * 显示仪表盘
     */
    public function dashboard()
    {
        // 模拟数据 - 实际项目中应该从数据库获取
        $stats = [
            'total_merchants' => 1250,
            'total_orders' => 15680,
            'total_products' => 8950,
            'total_users' => 45620,
            'today_orders' => 245,
            'today_revenue' => 125000,
            'pending_orders' => 89,
            'active_merchants' => 1180,
        ];

        $recentOrders = [
            [
                'id' => 1,
                'order_number' => 'ORD-2024-001',
                'merchant_name' => '商家A',
                'customer_name' => '张三',
                'total_amount' => 299.00,
                'status' => 'pending',
                'created_at' => now()->subMinutes(15),
            ],
            [
                'id' => 2,
                'order_number' => 'ORD-2024-002',
                'merchant_name' => '商家B',
                'customer_name' => '李四',
                'total_amount' => 599.00,
                'status' => 'shipped',
                'created_at' => now()->subMinutes(30),
            ],
            [
                'id' => 3,
                'order_number' => 'ORD-2024-003',
                'merchant_name' => '商家C',
                'customer_name' => '王五',
                'total_amount' => 199.00,
                'status' => 'delivered',
                'created_at' => now()->subHour(),
            ],
        ];

        $topMerchants = [
            [
                'id' => 1,
                'name' => '商家A',
                'orders_count' => 1250,
                'revenue' => 125000,
                'rating' => 4.8,
            ],
            [
                'id' => 2,
                'name' => '商家B',
                'orders_count' => 980,
                'revenue' => 98000,
                'rating' => 4.7,
            ],
            [
                'id' => 3,
                'name' => '商家C',
                'orders_count' => 750,
                'revenue' => 75000,
                'rating' => 4.6,
            ],
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topMerchants'));
    }

    /**
     * 显示商家管理页面
     */
    public function merchants()
    {
        return redirect()->route('admin.merchants.index');
    }

    /**
     * 显示订单管理页面
     */
    public function orders()
    {
        return redirect()->route('admin.orders.index');
    }

    /**
     * 显示商品管理页面
     */
    public function products()
    {
        return redirect()->route('admin.products.index');
    }

    /**
     * 显示用户管理页面
     */
    public function users()
    {
        return redirect()->route('admin.users.index');
    }

    /**
     * 显示分类管理页面
     */
    public function categories()
    {
        return redirect()->route('admin.categories.index');
    }

    /**
     * 显示财务管理页面
     */
    public function finance()
    {
        return view('admin.finance');
    }

    /**
     * 显示资金管理页面
     */
    public function fundManagement()
    {
        return redirect()->route('admin.fund-management.index');
    }
}
