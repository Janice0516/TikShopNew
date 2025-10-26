@extends('admin.layouts.app')

@section('title', '订单详情')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">订单管理</a></li>
                        <li class="breadcrumb-item active">订单详情</li>
                    </ol>
                </div>
                <h4 class="page-title">订单详情</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- 订单基本信息 -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">订单信息</h5>
                                    <div>
                                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'primary') }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                            {{ $order->getPaymentStatusLabel() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-primary">{{ $order->order_sn }}</h4>
                                            <p class="text-muted">订单总额: <strong>RM {{ number_format($order->total_amount, 2) }}</strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h5 class="text-success">{{ $order->items_count ?? 0 }}</h5>
                                                        <small class="text-muted">商品数量</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h5 class="text-info">RM {{ number_format($order->shipping_fee ?? 0, 2) }}</h5>
                                                        <small class="text-muted">运费</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>订单状态:</strong><br>
                                                <span class="text-muted">{{ $order->getStatusLabel() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>支付状态:</strong><br>
                                                <span class="text-muted">{{ $order->getPaymentStatusLabel() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>物流状态:</strong><br>
                                                <span class="text-muted">{{ $order->getShippingStatusLabel() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>所属商家:</strong><br>
                                                <span class="text-muted">{{ $order->merchant->name ?? '未知商家' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 订单商品 -->
                            @if($order->items && $order->items->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">订单商品</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>商品信息</th>
                                                    <th>SKU</th>
                                                    <th>单价</th>
                                                    <th>数量</th>
                                                    <th>小计</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->items as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($item->product && $item->product->images && count($item->product->images) > 0)
                                                                    <img src="{{ $item->product->images[0] }}" alt="商品图片" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $item->product_name ?? '商品已删除' }}</strong>
                                                                    @if($item->product_variant)
                                                                        <br><small class="text-muted">{{ $item->product_variant }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $item->product_sku ?? '-' }}</td>
                                                        <td>RM {{ number_format($item->price, 2) }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>RM {{ number_format($item->price * $item->quantity, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-end">商品总额:</th>
                                                    <th>RM {{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-end">运费:</th>
                                                    <th>RM {{ number_format($order->shipping_fee ?? 0, 2) }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-end">订单总额:</th>
                                                    <th>RM {{ number_format($order->total_amount, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- 客户信息 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">客户信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <strong>客户姓名:</strong><br>
                                                <span class="text-muted">{{ $order->customer_name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <strong>联系电话:</strong><br>
                                                <span class="text-muted">{{ $order->customer_phone }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <strong>邮箱地址:</strong><br>
                                                <span class="text-muted">{{ $order->customer_email ?? '未提供' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 收货地址 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">收货地址</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>收货地址:</strong><br>
                                        <span class="text-muted">{{ $order->shipping_address }}</span>
                                    </div>
                                    @if($order->shipping_city || $order->shipping_postal_code)
                                        <div class="row">
                                            @if($order->shipping_city)
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <strong>城市:</strong><br>
                                                        <span class="text-muted">{{ $order->shipping_city }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($order->shipping_postal_code)
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <strong>邮编:</strong><br>
                                                        <span class="text-muted">{{ $order->shipping_postal_code }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 物流信息 -->
                            @if($order->tracking_number || $order->shipping_company)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">物流信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($order->tracking_number)
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong>快递单号:</strong><br>
                                                    <span class="text-muted">{{ $order->tracking_number }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        @if($order->shipping_company)
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong>快递公司:</strong><br>
                                                    <span class="text-muted">{{ $order->shipping_company }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- 订单备注 -->
                            @if($order->notes)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">订单备注</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">{{ $order->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- 操作按钮 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">操作</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> 编辑订单
                                        </a>
                                        
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <div class="mb-2">
                                                <label for="new_status" class="form-label">更新状态</label>
                                                <select class="form-select" id="new_status" name="status">
                                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>待处理</option>
                                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>已确认</option>
                                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>处理中</option>
                                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>已发货</option>
                                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>已送达</option>
                                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>已完成</option>
                                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>已取消</option>
                                                    <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>已退款</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fas fa-sync"></i> 更新状态
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> 返回列表
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 时间信息 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">时间信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>下单时间:</strong><br>
                                        <span class="text-muted">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    @if($order->paid_at)
                                        <div class="mb-2">
                                            <strong>支付时间:</strong><br>
                                            <span class="text-muted">{{ $order->paid_at->format('Y-m-d H:i:s') }}</span>
                                        </div>
                                    @endif
                                    @if($order->shipped_at)
                                        <div class="mb-2">
                                            <strong>发货时间:</strong><br>
                                            <span class="text-muted">{{ $order->shipped_at->format('Y-m-d H:i:s') }}</span>
                                        </div>
                                    @endif
                                    @if($order->delivered_at)
                                        <div class="mb-2">
                                            <strong>送达时间:</strong><br>
                                            <span class="text-muted">{{ $order->delivered_at->format('Y-m-d H:i:s') }}</span>
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <strong>更新时间:</strong><br>
                                        <span class="text-muted">{{ $order->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 订单统计 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">订单统计</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <h4 class="text-primary">{{ $order->items_count ?? 0 }}</h4>
                                                <small class="text-muted">商品种类</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <h4 class="text-success">{{ $order->items->sum('quantity') ?? 0 }}</h4>
                                                <small class="text-muted">商品总数</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
