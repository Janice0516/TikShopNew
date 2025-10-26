<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * 显示订单列表
     */
    public function index(Request $request)
    {
        $query = Order::with(['merchant', 'customer', 'orderItems']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // 按支付状态筛选
        if ($request->filled('payment_status')) {
            $query->paymentStatus($request->payment_status);
        }

        // 按商家筛选
        if ($request->filled('merchant_id')) {
            $query->forMerchant($request->merchant_id);
        }

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $statuses = [
            'pending' => '待确认',
            'confirmed' => '已确认',
            'shipped' => '已发货',
            'delivered' => '已送达',
            'cancelled' => '已取消',
            'returned' => '已退货',
        ];

        $paymentStatuses = [
            'pending' => '待支付',
            'paid' => '已支付',
            'failed' => '支付失败',
            'refunded' => '已退款',
        ];

        $merchants = User::where('type', 'merchant')->get();

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses', 'merchants'));
    }

    /**
     * 显示订单详情
     */
    public function show(Order $order)
    {
        $order->load(['merchant', 'customer', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * 显示编辑订单表单
     */
    public function edit(Order $order)
    {
        $merchants = User::where('type', 'merchant')->get();
        return view('admin.orders.edit', compact('order', 'merchants'));
    }

    /**
     * 更新订单
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_sn' => 'required|string|max:255|unique:orders,order_sn,' . $order->id,
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'shipping_status' => 'nullable|in:pending,shipped,in_transit,delivered,returned',
            'total_amount' => 'required|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:20',
            'merchant_id' => 'required|exists:users,id',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order->update($request->all());

        return redirect()->route('admin.orders.index')
            ->with('success', '订单更新成功');
    }

    /**
     * 更新订单状态
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled,returned',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // 更新相关时间戳
        switch ($request->status) {
            case 'shipped':
                if (!$order->shipped_at) {
                    $order->update(['shipped_at' => now()]);
                }
                break;
            case 'delivered':
                if (!$order->delivered_at) {
                    $order->update(['delivered_at' => now()]);
                }
                break;
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', '订单状态已更新');
    }

    /**
     * 更新支付状态
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'nullable|string',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'paid_at' => $request->payment_status === 'paid' ? now() : null,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', '支付状态已更新');
    }

    /**
     * 更新物流信息
     */
    public function updateShipping(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_notes' => 'nullable|string',
        ]);

        $shippingInfo = $order->shipping_info ?? [];
        $shippingInfo = array_merge($shippingInfo, [
            'tracking_number' => $request->tracking_number,
            'shipping_company' => $request->shipping_company,
            'shipping_notes' => $request->shipping_notes,
            'updated_at' => now()->toISOString(),
        ]);

        $order->update([
            'shipping_info' => $shippingInfo,
            'status' => $request->tracking_number ? 'shipped' : $order->status,
            'shipped_at' => $request->tracking_number && !$order->shipped_at ? now() : $order->shipped_at,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', '物流信息已更新');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,ship,deliver,cancel,refund',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $orderIds = $request->order_ids;

        switch ($request->action) {
            case 'confirm':
                Order::whereIn('id', $orderIds)->update(['status' => 'confirmed']);
                $message = '订单已批量确认';
                break;
            case 'ship':
                Order::whereIn('id', $orderIds)->update([
                    'status' => 'shipped',
                    'shipped_at' => now(),
                ]);
                $message = '订单已批量发货';
                break;
            case 'deliver':
                Order::whereIn('id', $orderIds)->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
                $message = '订单已批量送达';
                break;
            case 'cancel':
                Order::whereIn('id', $orderIds)->update(['status' => 'cancelled']);
                $message = '订单已批量取消';
                break;
            case 'refund':
                Order::whereIn('id', $orderIds)->update(['payment_status' => 'refunded']);
                $message = '订单已批量退款';
                break;
        }

        return redirect()->route('admin.orders.index')
            ->with('success', $message);
    }

    /**
     * 获取订单统计
     */
    public function statistics(Request $request)
    {
        $query = Order::query();

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $stats = [
            'total_orders' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'pending_orders' => $query->clone()->status('pending')->count(),
            'paid_orders' => $query->clone()->paymentStatus('paid')->count(),
            'shipped_orders' => $query->clone()->status('shipped')->count(),
            'delivered_orders' => $query->clone()->status('delivered')->count(),
        ];

        return response()->json($stats);
    }
}
