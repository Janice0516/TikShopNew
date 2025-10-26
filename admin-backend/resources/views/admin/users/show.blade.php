@extends('admin.layouts.app')

@section('title', '用户详情')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">用户管理</a></li>
                        <li class="breadcrumb-item active">用户详情</li>
                    </ol>
                </div>
                <h4 class="page-title">用户详情</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- 基本信息 -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">基本信息</h5>
                                    <div>
                                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'danger' : 'warning') }}">
                                            {{ $user->status == 'active' ? '正常' : ($user->status == 'inactive' ? '禁用' : '暂停') }}
                                        </span>
                                        <span class="badge bg-{{ $user->type == 'merchant' ? 'primary' : 'info' }}">
                                            {{ $user->type == 'merchant' ? '商家' : '客户' }}
                                        </span>
                                        @if($user->type == 'merchant' && $user->is_verified)
                                            <span class="badge bg-success">已认证</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-primary">{{ $user->name }}</h4>
                                            <p class="text-muted">{{ $user->email }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                @if($user->type == 'merchant')
                                                    <div class="col-6">
                                                        <div class="text-center">
                                                            <h5 class="text-success">{{ $user->products_count ?? 0 }}</h5>
                                                            <small class="text-muted">商品数量</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-center">
                                                            <h5 class="text-info">{{ $user->orders_count ?? 0 }}</h5>
                                                            <small class="text-muted">订单数量</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-6">
                                                        <div class="text-center">
                                                            <h5 class="text-success">{{ $user->orders_count ?? 0 }}</h5>
                                                            <small class="text-muted">订单数量</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-center">
                                                            <h5 class="text-info">RM {{ number_format($user->total_spent ?? 0, 2) }}</h5>
                                                            <small class="text-muted">总消费</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>联系电话:</strong><br>
                                                <span class="text-muted">{{ $user->phone ?? '未设置' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>用户类型:</strong><br>
                                                <span class="text-muted">{{ $user->type == 'merchant' ? '商家' : '客户' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>状态:</strong><br>
                                                <span class="text-muted">{{ $user->status == 'active' ? '正常' : ($user->status == 'inactive' ? '禁用' : '暂停') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>认证状态:</strong><br>
                                                <span class="text-muted">{{ $user->type == 'merchant' ? ($user->is_verified ? '已认证' : '未认证') : '不适用' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->address)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <strong>地址:</strong><br>
                                                <span class="text-muted">{{ $user->address }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 商家信息 -->
                            @if($user->type == 'merchant')
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">商家信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>公司名称:</strong><br>
                                                <span class="text-muted">{{ $user->company_name ?? '未设置' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>营业执照号:</strong><br>
                                                <span class="text-muted">{{ $user->business_license ?? '未设置' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>联系人:</strong><br>
                                                <span class="text-muted">{{ $user->contact_person ?? '未设置' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>联系人电话:</strong><br>
                                                <span class="text-muted">{{ $user->contact_phone ?? '未设置' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 财务信息 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">财务信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-success">RM {{ number_format($user->balance ?? 0, 2) }}</h4>
                                                <small class="text-muted">当前余额</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-warning">RM {{ number_format($user->frozen_amount ?? 0, 2) }}</h4>
                                                <small class="text-muted">冻结金额</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-primary">RM {{ number_format($user->total_sales ?? 0, 2) }}</h4>
                                                <small class="text-muted">总销售额</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 最近商品 -->
                            @if($user->products && $user->products->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">最近商品</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>商品名称</th>
                                                    <th>SKU</th>
                                                    <th>价格</th>
                                                    <th>库存</th>
                                                    <th>状态</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->products->take(10) as $product)
                                                    <tr>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->sku }}</td>
                                                        <td>RM {{ number_format($product->price, 2) }}</td>
                                                        <td>{{ $product->stock }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $product->status == 'active' ? 'success' : ($product->status == 'inactive' ? 'danger' : 'warning') }}">
                                                                {{ $product->status == 'active' ? '上架' : ($product->status == 'inactive' ? '下架' : '草稿') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($user->products->count() > 10)
                                        <div class="text-center mt-2">
                                            <a href="{{ route('admin.products.index', ['merchant_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                                查看所有商品 ({{ $user->products->count() }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endif

                            <!-- 最近订单 -->
                            @if($user->orders && $user->orders->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">最近订单</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>订单号</th>
                                                    <th>金额</th>
                                                    <th>状态</th>
                                                    <th>支付状态</th>
                                                    <th>下单时间</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->orders->take(10) as $order)
                                                    <tr>
                                                        <td>{{ $order->order_sn }}</td>
                                                        <td>RM {{ number_format($order->total_amount, 2) }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'primary') }}">
                                                                {{ $order->getStatusLabel() }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                                {{ $order->getPaymentStatusLabel() }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($user->orders->count() > 10)
                                        <div class="text-center mt-2">
                                            <a href="{{ route('admin.orders.index', ['customer_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                                查看所有订单 ({{ $user->orders->count() }})
                                            </a>
                                        </div>
                                    @endif
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
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> 编辑用户
                                        </a>
                                        
                                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $user->status == 'active' ? 'warning' : 'success' }} w-100">
                                                <i class="fas fa-{{ $user->status == 'active' ? 'pause' : 'play' }}"></i> 
                                                {{ $user->status == 'active' ? '禁用' : '启用' }}
                                            </button>
                                        </form>

                                        @if($user->type == 'merchant')
                                        <form action="{{ route('admin.users.update-verification', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $user->is_verified ? 'secondary' : 'info' }} w-100">
                                                <i class="fas fa-certificate"></i> 
                                                {{ $user->is_verified ? '取消认证' : '设为认证' }}
                                            </button>
                                        </form>
                                        @endif

                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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
                                        <strong>注册时间:</strong><br>
                                        <span class="text-muted">{{ $user->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>最后登录:</strong><br>
                                        <span class="text-muted">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '从未登录' }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>更新时间:</strong><br>
                                        <span class="text-muted">{{ $user->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 邀请码信息（商家专用） -->
                            @if($user->type == 'merchant' && $user->invite_codes && $user->invite_codes->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">邀请码</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($user->invite_codes->take(3) as $inviteCode)
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $inviteCode->code }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $inviteCode->used_count }}/{{ $inviteCode->max_uses ?? '∞' }}</small>
                                                </div>
                                                <span class="badge bg-{{ $inviteCode->is_active ? 'success' : 'danger' }}">
                                                    {{ $inviteCode->is_active ? '有效' : '无效' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($user->invite_codes->count() > 3)
                                        <div class="text-center">
                                            <a href="{{ route('admin.invite-codes.index', ['merchant_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                                查看所有邀请码
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
