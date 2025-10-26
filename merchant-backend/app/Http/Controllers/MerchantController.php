<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MerchantController extends Controller
{
    protected $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * 商家仪表盘
     */
    public function dashboard()
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            // 获取真实统计数据
            $stats = [
                'todayRevenue' => \App\Models\Order::forMerchant($merchantId)
                    ->where('payment_status', 'paid')
                    ->whereDate('created_at', today())
                    ->sum('total_amount'),
                'todayOrders' => \App\Models\Order::forMerchant($merchantId)
                    ->whereDate('created_at', today())
                    ->count(),
                'totalProducts' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->count(),
                'pendingShipment' => \App\Models\Order::forMerchant($merchantId)
                    ->where('status', 'confirmed')
                    ->count(),
            ];
            
            // 获取最近订单
            $recentOrders = \App\Models\Order::forMerchant($merchantId)
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'orderNumber' => $order->order_number,
                        'customerName' => $order->customer_name,
                        'totalAmount' => $order->total_amount,
                        'status' => $order->status,
                        'statusText' => $order->status_text,
                        'statusColor' => $order->status_color,
                        'createdAt' => $order->created_at->toISOString(),
                        'itemsCount' => $order->items->count(),
                    ];
                })
                ->toArray();
            
            // 获取热销商品
            $topProducts = \App\Models\OrderItem::whereHas('order', function($query) use ($merchantId) {
                    $query->forMerchant($merchantId);
                })
                ->selectRaw('merchant_product_id, product_name, SUM(quantity) as total_sales, AVG(unit_price) as avg_price')
                ->groupBy('merchant_product_id', 'product_name')
                ->orderBy('total_sales', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->merchant_product_id,
                        'name' => $item->product_name,
                        'sales' => $item->total_sales,
                        'price' => $item->avg_price,
                    ];
                })
                ->toArray();
            
            // 获取财务统计
            $financeStats = \App\Models\FinanceRecord::getStatsForMerchant($merchantId, 30);
            
            return view('merchant.dashboard', [
                'stats' => $stats,
                'recentOrders' => $recentOrders,
                'topProducts' => $topProducts,
                'financeStats' => $financeStats,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Dashboard error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // 如果出错，提供基础数据
            return view('merchant.dashboard', [
                'stats' => [
                    'todayRevenue' => 0,
                    'todayOrders' => 0,
                    'totalProducts' => 0,
                    'pendingShipment' => 0,
                ],
                'recentOrders' => [],
                'topProducts' => [],
                'financeStats' => [
                    'total_income' => 0,
                    'total_expense' => 0,
                    'current_balance' => 0,
                    'transaction_count' => 0,
                ],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 订单管理
     */
    public function orders(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            $query = \App\Models\Order::forMerchant($merchantId)->with('items');
            
            // 搜索功能
            $keyword = $request->get('keyword', '');
            if (!empty($keyword)) {
                $query->search($keyword);
            }
            
            // 状态筛选
            $status = $request->get('status', '');
            if (!empty($status)) {
                $query->byStatus($status);
            }
            
            // 支付状态筛选
            $paymentStatus = $request->get('payment_status', '');
            if (!empty($paymentStatus)) {
                $query->byPaymentStatus($paymentStatus);
            }
            
            // 日期范围筛选
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            if ($startDate || $endDate) {
                $query->byDateRange($startDate, $endDate);
            }
            
            // 排序
            $sort = $request->get('sort', 'created_at');
            switch ($sort) {
                case 'amount_asc':
                    $query->orderBy('total_amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('total_amount', 'desc');
                    break;
                case 'order_number':
                    $query->orderBy('order_number', 'asc');
                    break;
                case 'created_at':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
            
            // 分页
            $pageSize = 15;
            $orders = $query->paginate($pageSize);
            
            // 统计信息
            $stats = [
                'total' => \App\Models\Order::forMerchant($merchantId)->count(),
                'pending' => \App\Models\Order::forMerchant($merchantId)->where('status', 'pending')->count(),
                'confirmed' => \App\Models\Order::forMerchant($merchantId)->where('status', 'confirmed')->count(),
                'shipped' => \App\Models\Order::forMerchant($merchantId)->where('status', 'shipped')->count(),
                'delivered' => \App\Models\Order::forMerchant($merchantId)->where('status', 'delivered')->count(),
                'total_revenue' => \App\Models\Order::forMerchant($merchantId)->where('payment_status', 'paid')->sum('total_amount'),
            ];
            
            return view('merchant.orders', [
                'orders' => $orders->items(),
                'totalPages' => $orders->lastPage(),
                'currentPage' => $orders->currentPage(),
                'searchKeyword' => $keyword,
                'selectedStatus' => $status,
                'selectedPaymentStatus' => $paymentStatus,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'selectedSort' => $sort,
                'pagination' => $orders,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Orders error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return view('merchant.orders', [
                'orders' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'searchKeyword' => '',
                'selectedStatus' => '',
                'selectedPaymentStatus' => '',
                'startDate' => '',
                'endDate' => '',
                'selectedSort' => 'created_at',
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'confirmed' => 0,
                    'shipped' => 0,
                    'delivered' => 0,
                    'total_revenue' => 0,
                ],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 更新订单状态
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
                'notes' => 'nullable|string|max:500',
                'tracking_number' => 'nullable|string|max:100',
            ]);

            $order = \App\Models\Order::forMerchant($merchantId)->findOrFail($id);
            
            $oldStatus = $order->status;
            $newStatus = $request->input('status');
            
            // 更新订单状态
            $order->updateStatus($newStatus, $request->input('notes'));
            
            // 如果提供了快递单号
            if ($request->has('tracking_number')) {
                $order->tracking_number = $request->input('tracking_number');
                $order->save();
            }
            
            // 记录状态变更日志
            \Log::info('Order status updated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'merchant_id' => $merchantId,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => '订单状态已更新',
                'data' => [
                    'order_id' => $order->id,
                    'status' => $newStatus,
                    'status_text' => $order->status_text,
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Update order status error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '更新失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取订单详情
     */
    public function orderDetail($id)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            $order = \App\Models\Order::forMerchant($merchantId)
                ->with(['items.merchantProduct'])
                ->findOrFail($id);

            return view('merchant.order-detail', compact('order'));
        } catch (\Throwable $e) {
            return redirect('/merchant/orders')->with('error', '订单不存在');
        }
    }

    /**
     * 平台选品
     */
    public function selectProducts(Request $request)
    {
        try {
            $query = \App\Models\PlatformProduct::active();
            
            // 搜索功能
            $keyword = $request->get('keyword', '');
            if (!empty($keyword)) {
                $query->search($keyword);
            }
            
            // 分类筛选
            $category = $request->get('category', '');
            if (!empty($category)) {
                $query->byCategory($category);
            }
            
            // 品牌筛选
            $brand = $request->get('brand', '');
            if (!empty($brand)) {
                $query->byBrand($brand);
            }
            
            // 排序
            $sort = $request->get('sort', 'rating');
            switch ($sort) {
                case 'price_asc':
                    $query->orderByPrice('asc');
                    break;
                case 'price_desc':
                    $query->orderByPrice('desc');
                    break;
                case 'sales':
                    $query->orderBySales('desc');
                    break;
                case 'rating':
                default:
                    $query->orderByRating('desc');
                    break;
            }
            
            // 分页
            $pageSize = 12;
            $products = $query->paginate($pageSize);
            
            // 获取分类和品牌列表用于筛选
            $categories = \App\Models\PlatformProduct::active()
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();
                
            $brands = \App\Models\PlatformProduct::active()
                ->distinct()
                ->pluck('brand')
                ->filter()
                ->values();
            
            return view('merchant.select-products', [
                'products' => $products->items(),
                'totalPages' => $products->lastPage(),
                'currentPage' => $products->currentPage(),
                'searchKeyword' => $keyword,
                'selectedCategory' => $category,
                'selectedBrand' => $brand,
                'selectedSort' => $sort,
                'categories' => $categories,
                'brands' => $brands,
                'pagination' => $products,
            ]);
        } catch (\Throwable $e) {
            return view('merchant.select-products', [
                'products' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'searchKeyword' => '',
                'selectedCategory' => '',
                'selectedBrand' => '',
                'selectedSort' => 'rating',
                'categories' => [],
                'brands' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 财务管理
     */
    public function finance(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            // 获取筛选参数
            $type = $request->get('type', '');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $keyword = $request->get('keyword', '');
            
            // 构建查询
            $query = \App\Models\FinanceRecord::forMerchant($merchantId);
            
            // 类型筛选
            if (!empty($type)) {
                $query->byType($type);
            }
            
            // 日期范围筛选
            if ($startDate || $endDate) {
                $query->byDateRange($startDate, $endDate);
            }
            
            // 关键词搜索
            if (!empty($keyword)) {
                $query->where(function($q) use ($keyword) {
                    $q->where('description', 'like', "%{$keyword}%")
                      ->orWhere('transaction_id', 'like', "%{$keyword}%")
                      ->orWhere('notes', 'like', "%{$keyword}%");
                });
            }
            
            // 排序和分页
            $query->orderBy('created_at', 'desc');
            $pageSize = 20;
            $records = $query->paginate($pageSize);
            
            // 获取财务统计
            $stats = \App\Models\FinanceRecord::getStatsForMerchant($merchantId, 30);
            
            // 获取最近7天的收入趋势
            $incomeTrend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $income = \App\Models\FinanceRecord::forMerchant($merchantId)
                    ->where('type', 'income')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('amount');
                $incomeTrend[] = [
                    'date' => $date,
                    'income' => $income,
                ];
            }
            
            // 获取收入类型统计
            $incomeByType = \App\Models\FinanceRecord::forMerchant($merchantId)
                ->where('type', 'income')
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('type, SUM(amount) as total_amount, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->map(function($item) {
                    return [
                        'type' => $item->type,
                        'type_text' => $item->type_text,
                        'total_amount' => $item->total_amount,
                        'count' => $item->count,
                    ];
                });
            
            return view('merchant.finance', [
                'records' => $records->items(),
                'totalPages' => $records->lastPage(),
                'currentPage' => $records->currentPage(),
                'pagination' => $records,
                'stats' => $stats,
                'incomeTrend' => $incomeTrend,
                'incomeByType' => $incomeByType,
                'selectedType' => $type,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'searchKeyword' => $keyword,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Finance error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return view('merchant.finance', [
                'records' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'stats' => [
                    'total_income' => 0,
                    'total_expense' => 0,
                    'current_balance' => 0,
                    'transaction_count' => 0,
                ],
                'incomeTrend' => [],
                'incomeByType' => [],
                'selectedType' => '',
                'startDate' => '',
                'endDate' => '',
                'searchKeyword' => '',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 提现管理
     */
    public function withdrawals(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            // 获取筛选参数
            $status = $request->get('status', '');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            // 构建查询
            $query = \App\Models\Withdrawal::forMerchant($merchantId);
            
            // 状态筛选
            if (!empty($status)) {
                $query->byStatus($status);
            }
            
            // 日期范围筛选
            if ($startDate || $endDate) {
                $query->byDateRange($startDate, $endDate);
            }
            
            // 排序和分页
            $query->orderBy('created_at', 'desc');
            $pageSize = 15;
            $withdrawals = $query->paginate($pageSize);
            
            // 获取提现统计
            $stats = \App\Models\Withdrawal::getStatsForMerchant($merchantId, 30);
            
            // 获取当前余额
            $currentBalance = \App\Models\FinanceRecord::getMerchantBalance($merchantId);
            
            return view('merchant.withdrawals', [
                'withdrawals' => $withdrawals->items(),
                'totalPages' => $withdrawals->lastPage(),
                'currentPage' => $withdrawals->currentPage(),
                'pagination' => $withdrawals,
                'stats' => $stats,
                'currentBalance' => $currentBalance,
                'selectedStatus' => $status,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Withdrawals error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return view('merchant.withdrawals', [
                'withdrawals' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'stats' => [
                    'total_withdrawals' => 0,
                    'total_amount' => 0,
                    'completed_amount' => 0,
                    'pending_amount' => 0,
                    'failed_amount' => 0,
                ],
                'currentBalance' => 0,
                'selectedStatus' => '',
                'startDate' => '',
                'endDate' => '',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 创建提现申请
     */
    public function createWithdrawal(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'amount' => 'required|numeric|min:10|max:50000',
                'method' => 'required|in:bank_transfer,alipay,wechat,paypal',
                'payment_info' => 'required|array',
                'payment_info.account_name' => 'required|string|max:100',
                'payment_info.account_number' => 'required|string|max:50',
                'payment_info.bank_name' => 'required_if:method,bank_transfer|string|max:100',
                'payment_info.alipay_account' => 'required_if:method,alipay|string|max:100',
                'payment_info.wechat_account' => 'required_if:method,wechat|string|max:100',
                'payment_info.paypal_email' => 'required_if:method,paypal|email|max:100',
            ]);

            // 检查余额
            $currentBalance = \App\Models\FinanceRecord::getMerchantBalance($merchantId);
            if ($request->input('amount') > $currentBalance) {
                return response()->json([
                    'success' => false,
                    'message' => '提现金额不能超过当前余额'
                ]);
            }

            // 检查是否有待处理的提现申请
            $pendingWithdrawal = \App\Models\Withdrawal::forMerchant($merchantId)
                ->where('status', 'pending')
                ->first();
                
            if ($pendingWithdrawal) {
                return response()->json([
                    'success' => false,
                    'message' => '您有未处理的提现申请，请等待处理完成后再申请'
                ]);
            }

            // 创建提现申请
            $withdrawal = \App\Models\Withdrawal::createWithdrawal(
                $merchantId,
                $request->input('amount'),
                $request->input('method'),
                $request->input('payment_info')
            );

            return response()->json([
                'success' => true,
                'message' => '提现申请已提交，请等待审核',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'withdrawal_number' => $withdrawal->withdrawal_number,
                    'amount' => $withdrawal->amount,
                    'actual_amount' => $withdrawal->actual_amount,
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Create withdrawal error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '提现申请失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 店铺管理
     */
    public function shop(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            // 获取或创建店铺设置
            $shopSetting = \App\Models\ShopSetting::getOrCreateForMerchant($merchantId);
            
            // 如果是POST请求，更新设置
            if ($request->isMethod('post')) {
                $request->validate([
                    'shop_name' => 'required|string|max:100',
                    'shop_description' => 'nullable|string|max:1000',
                    'contact_email' => 'nullable|email|max:100',
                    'contact_phone' => 'nullable|string|max:20',
                    'contact_address' => 'nullable|string|max:200',
                    'contact_city' => 'nullable|string|max:50',
                    'contact_state' => 'nullable|string|max:50',
                    'contact_country' => 'nullable|string|max:50',
                    'contact_zip' => 'nullable|string|max:10',
                    'website_url' => 'nullable|url|max:200',
                    'facebook_url' => 'nullable|url|max:200',
                    'instagram_url' => 'nullable|url|max:200',
                    'twitter_url' => 'nullable|url|max:200',
                    'youtube_url' => 'nullable|url|max:200',
                    'timezone' => 'nullable|string|max:50',
                    'currency' => 'nullable|string|max:3',
                    'language' => 'nullable|string|max:5',
                    'free_shipping_threshold' => 'nullable|numeric|min:0',
                    'default_shipping_fee' => 'nullable|numeric|min:0',
                    'return_policy' => 'nullable|string|max:2000',
                    'privacy_policy' => 'nullable|string|max:2000',
                    'terms_of_service' => 'nullable|string|max:2000',
                    'is_active' => 'nullable|boolean',
                    'allow_reviews' => 'nullable|boolean',
                    'show_contact_info' => 'nullable|boolean',
                    'meta_title' => 'nullable|string|max:200',
                    'meta_description' => 'nullable|string|max:500',
                    'meta_keywords' => 'nullable|string|max:200',
                ]);

                $shopSetting->updateSettings($request->all());
                
                return redirect('/merchant/shop')->with('success', '店铺设置已保存');
            }

            // 获取店铺统计信息
            $stats = [
                'total_products' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->count(),
                'active_products' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->where('is_active', true)->count(),
                'total_orders' => \App\Models\Order::forMerchant($merchantId)->count(),
                'total_revenue' => \App\Models\Order::forMerchant($merchantId)->where('payment_status', 'paid')->sum('total_amount'),
                'shop_views' => rand(1000, 5000), // 模拟数据
                'customer_count' => \App\Models\Order::forMerchant($merchantId)->distinct('customer_id')->count(),
            ];

            return view('merchant.shop', [
                'shopSetting' => $shopSetting,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Shop settings error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return redirect('/merchant/shop')->with('error', '加载店铺设置失败: ' . $e->getMessage());
        }
    }

    /**
     * 信用评级
     */
    public function creditRating()
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            // 获取或创建信用评级
            $creditRating = \App\Models\CreditRating::getOrCreateForMerchant($merchantId);
            
            // 更新评级数据
            $creditRating->updateRating();
            
            // 获取评级趋势
            $trend = $creditRating->getRatingTrend(30);
            
            // 获取评级历史（模拟数据）
            $ratingHistory = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $ratingHistory[] = [
                    'date' => $date,
                    'score' => $creditRating->overall_score + rand(-5, 5), // 模拟波动
                ];
            }
            
            // 获取评级分布（模拟数据）
            $ratingDistribution = [
                'A+' => rand(0, 5),
                'A' => rand(5, 15),
                'B+' => rand(10, 25),
                'B' => rand(15, 30),
                'C+' => rand(20, 35),
                'C' => rand(10, 25),
                'D' => rand(0, 10),
            ];
            
            // 获取改进建议
            $suggestions = $this->getImprovementSuggestions($creditRating);
            
            return view('merchant.credit-rating', [
                'creditRating' => $creditRating,
                'trend' => $trend,
                'ratingHistory' => $ratingHistory,
                'ratingDistribution' => $ratingDistribution,
                'suggestions' => $suggestions,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Credit rating error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return redirect('/merchant/credit-rating')->with('error', '加载信用评级失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取改进建议
     */
    private function getImprovementSuggestions($creditRating)
    {
        $suggestions = [];
        
        if ($creditRating->service_score < 80) {
            $suggestions[] = [
                'category' => '服务',
                'title' => '提升客户服务质量',
                'description' => '建议加强客户服务培训，提高响应速度，改善客户体验。',
                'priority' => 'high',
            ];
        }
        
        if ($creditRating->quality_score < 80) {
            $suggestions[] = [
                'category' => '商品质量',
                'title' => '改善商品质量控制',
                'description' => '建议加强商品质量检查，减少退货率，提升客户满意度。',
                'priority' => 'high',
            ];
        }
        
        if ($creditRating->shipping_score < 80) {
            $suggestions[] = [
                'category' => '配送',
                'title' => '优化配送服务',
                'description' => '建议选择更快的物流服务商，缩短发货和配送时间。',
                'priority' => 'medium',
            ];
        }
        
        if ($creditRating->communication_score < 80) {
            $suggestions[] = [
                'category' => '沟通',
                'title' => '提高沟通效率',
                'description' => '建议设置自动回复，提高客户咨询响应速度。',
                'priority' => 'medium',
            ];
        }
        
        if ($creditRating->refund_rate > 10) {
            $suggestions[] = [
                'category' => '退款',
                'title' => '降低退款率',
                'description' => '建议改善商品描述准确性，提供更详细的商品信息。',
                'priority' => 'high',
            ];
        }
        
        if (!$creditRating->is_verified) {
            $suggestions[] = [
                'category' => '认证',
                'title' => '申请商家认证',
                'description' => '完成商家认证可以提升客户信任度，获得更多平台支持。',
                'priority' => 'low',
            ];
        }
        
        return $suggestions;
    }

    /**
     * 系统设置
     */
    public function settings(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            $user = \App\Models\User::find($merchantId);
            $merchantProfile = $user->merchantProfile ?? null;
            
            // 如果是POST请求，更新设置
            if ($request->isMethod('post')) {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'email' => 'required|email|max:100',
                    'phone' => 'nullable|string|max:20',
                    'current_password' => 'nullable|string',
                    'new_password' => 'nullable|string|min:6|confirmed',
                    'notification_email' => 'nullable|boolean',
                    'notification_sms' => 'nullable|boolean',
                    'notification_push' => 'nullable|boolean',
                    'language' => 'nullable|string|in:zh,en,ms',
                    'timezone' => 'nullable|string|max:50',
                    'currency' => 'nullable|string|max:3',
                    'date_format' => 'nullable|string|max:20',
                    'time_format' => 'nullable|string|max:10',
                ]);

                // 更新用户基本信息
                $user->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                ]);

                // 更新商家资料
                if ($merchantProfile) {
                    $merchantProfile->update([
                        'contact_name' => $request->input('name'),
                        'contact_phone' => $request->input('phone'),
                    ]);
                }

                // 更新密码
                if ($request->filled('new_password')) {
                    if (!\Hash::check($request->input('current_password'), $user->password)) {
                        return redirect('/merchant/settings')->with('error', '当前密码不正确');
                    }
                    $user->update([
                        'password' => \Hash::make($request->input('new_password')),
                    ]);
                }

                // 更新用户设置
                $userSettings = $user->settings ?? [];
                $userSettings = array_merge($userSettings, [
                    'notification_email' => $request->boolean('notification_email'),
                    'notification_sms' => $request->boolean('notification_sms'),
                    'notification_push' => $request->boolean('notification_push'),
                    'language' => $request->input('language', 'zh'),
                    'timezone' => $request->input('timezone', 'Asia/Kuala_Lumpur'),
                    'currency' => $request->input('currency', 'MYR'),
                    'date_format' => $request->input('date_format', 'Y-m-d'),
                    'time_format' => $request->input('time_format', 'H:i'),
                ]);
                
                $user->update(['settings' => $userSettings]);
                
                return redirect('/merchant/settings')->with('success', '设置已保存');
            }

            // 获取用户设置
            $userSettings = $user->settings ?? [
                'notification_email' => true,
                'notification_sms' => false,
                'notification_push' => true,
                'language' => 'zh',
                'timezone' => 'Asia/Kuala_Lumpur',
                'currency' => 'MYR',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
            ];

            // 获取账户统计
            $stats = [
                'login_count' => rand(50, 200), // 模拟数据
                'last_login' => $user->updated_at,
                'account_created' => $user->created_at,
                'total_orders' => \App\Models\Order::forMerchant($merchantId)->count(),
                'total_products' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->count(),
                'total_revenue' => \App\Models\Order::forMerchant($merchantId)->where('payment_status', 'paid')->sum('total_amount'),
            ];

            return view('merchant.settings', [
                'user' => $user,
                'merchantProfile' => $merchantProfile,
                'userSettings' => $userSettings,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Settings error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return redirect('/merchant/settings')->with('error', '加载设置失败: ' . $e->getMessage());
        }
    }

    /**
     * 更新设置
     */
    public function updateSettings(Request $request)
    {
        $token = session('merchant_token');
        
        try {
            if ($request->has('profile')) {
                $this->apiClient->patch('merchant/profile', $request->get('profile'), $token);
            }
            
            if ($request->has('shop')) {
                $this->apiClient->patch('merchant/shop', $request->get('shop'), $token);
            }
            
            return redirect()->back()->with('success', '设置已保存');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', '保存失败: ' . $e->getMessage());
        }
    }

    /**
     * 商品管理
     */
    public function products(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            $query = \App\Models\MerchantProduct::where('merchant_id', $merchantId);
            
            // 搜索功能
            $keyword = $request->get('keyword', '');
            if (!empty($keyword)) {
                $query->search($keyword);
            }
            
            // 分类筛选
            $category = $request->get('category', '');
            if (!empty($category)) {
                $query->byCategory($category);
            }
            
            // 品牌筛选
            $brand = $request->get('brand', '');
            if (!empty($brand)) {
                $query->byBrand($brand);
            }
            
            // 状态筛选
            $status = $request->get('status', '');
            if (!empty($status)) {
                $query->byStatus($status);
            }
            
            // 排序
            $sort = $request->get('sort', 'created_at');
            switch ($sort) {
                case 'price_asc':
                    $query->orderByPrice('asc');
                    break;
                case 'price_desc':
                    $query->orderByPrice('desc');
                    break;
                case 'stock':
                    $query->orderByStock('desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'created_at':
                default:
                    $query->orderByCreated('desc');
                    break;
            }
            
            // 分页
            $pageSize = 12;
            $products = $query->paginate($pageSize);
            
            // 获取分类和品牌列表用于筛选
            $categories = \App\Models\MerchantProduct::where('merchant_id', $merchantId)
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();
                
            $brands = \App\Models\MerchantProduct::where('merchant_id', $merchantId)
                ->distinct()
                ->pluck('brand')
                ->filter()
                ->values();
            
            // 统计信息
            $stats = [
                'total' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->count(),
                'active' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->where('is_active', true)->count(),
                'low_stock' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->whereRaw('stock <= min_stock')->count(),
                'out_of_stock' => \App\Models\MerchantProduct::where('merchant_id', $merchantId)->where('stock', 0)->count(),
            ];
            
            return view('merchant.products', [
                'products' => $products->items(),
                'totalPages' => $products->lastPage(),
                'currentPage' => $products->currentPage(),
                'searchKeyword' => $keyword,
                'selectedCategory' => $category,
                'selectedBrand' => $brand,
                'selectedStatus' => $status,
                'selectedSort' => $sort,
                'categories' => $categories,
                'brands' => $brands,
                'pagination' => $products,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            return view('merchant.products', [
                'products' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'searchKeyword' => '',
                'selectedCategory' => '',
                'selectedBrand' => '',
                'selectedStatus' => '',
                'selectedSort' => 'created_at',
                'categories' => [],
                'brands' => [],
                'stats' => ['total' => 0, 'active' => 0, 'low_stock' => 0, 'out_of_stock' => 0],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 添加商品到店铺
     */
    public function addProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'sale_price' => 'required|numeric|min:0.01',
        ]);

        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $platformProduct = \App\Models\PlatformProduct::findOrFail($request->input('product_id'));
            $salePrice = $request->input('sale_price');
            
            // 检查是否已经添加过
            $existingProduct = \App\Models\MerchantProduct::where('merchant_id', $merchantId)
                ->where('platform_product_id', $platformProduct->id)
                ->first();
                
            if ($existingProduct) {
                return response()->json([
                    'success' => false,
                    'message' => '该商品已经添加到您的店铺中'
                ]);
            }
            
            // 创建商家商品
            $merchantProduct = \App\Models\MerchantProduct::createFromPlatformProduct(
                $platformProduct, 
                $merchantId, 
                $salePrice
            );
            
            return response()->json([
                'success' => true,
                'message' => '商品已成功添加到您的店铺！',
                'data' => [
                    'product_id' => $merchantProduct->id,
                    'name' => $merchantProduct->name,
                    'sale_price' => $merchantProduct->sale_price,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '添加失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 编辑商品
     */
    public function editProduct(Request $request, $id)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return redirect('/merchant/login')->with('error', '请先登录');
            }

            $product = \App\Models\MerchantProduct::where('merchant_id', $merchantId)
                ->findOrFail($id);

            if ($request->isMethod('post')) {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'sale_price' => 'required|numeric|min:0.01',
                    'cost_price' => 'nullable|numeric|min:0',
                    'sku' => 'required|string|max:255|unique:merchant_products,sku,' . $id,
                    'stock' => 'required|integer|min:0',
                    'min_stock' => 'required|integer|min:0',
                    'category' => 'nullable|string|max:255',
                    'brand' => 'nullable|string|max:255',
                    'is_active' => 'boolean',
                    'is_featured' => 'boolean',
                    'notes' => 'nullable|string',
                ]);

                $product->update($request->only([
                    'name', 'description', 'sale_price', 'cost_price', 'sku',
                    'stock', 'min_stock', 'category', 'brand', 'is_active',
                    'is_featured', 'notes'
                ]));

                return redirect('/merchant/products')->with('success', '商品更新成功！');
            }

            return view('merchant.edit-product', compact('product'));
        } catch (\Throwable $e) {
            return redirect('/merchant/products')->with('error', '编辑失败: ' . $e->getMessage());
        }
    }

    /**
     * 删除商品
     */
    public function deleteProduct($id)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $product = \App\Models\MerchantProduct::where('merchant_id', $merchantId)
                ->findOrFail($id);

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => '商品已删除'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '删除失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量操作商品
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:merchant_products,id'
        ]);

        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $products = \App\Models\MerchantProduct::where('merchant_id', $merchantId)
                ->whereIn('id', $request->input('product_ids'));

            switch ($request->input('action')) {
                case 'activate':
                    $products->update(['is_active' => true]);
                    $message = '商品已激活';
                    break;
                case 'deactivate':
                    $products->update(['is_active' => false]);
                    $message = '商品已停用';
                    break;
                case 'delete':
                    $products->delete();
                    $message = '商品已删除';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '操作失败: ' . $e->getMessage()
            ], 500);
        }
    }
}
