<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        // 订单创建逻辑
        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);
        
        $order->update(['status' => 'cancelled']);
        
        return redirect()->back()->with('success', 'Order cancelled successfully!');
    }
}
