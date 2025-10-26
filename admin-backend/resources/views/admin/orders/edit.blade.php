@extends('admin.layouts.app')

@section('title', '编辑订单')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">订单管理</a></li>
                        <li class="breadcrumb-item active">编辑订单</li>
                    </ol>
                </div>
                <h4 class="page-title">编辑订单</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- 订单基本信息 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">订单信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="order_sn" class="form-label">订单号 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('order_sn') is-invalid @enderror" 
                                                           id="order_sn" name="order_sn" value="{{ old('order_sn', $order->order_sn) }}" required>
                                                    @error('order_sn')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">订单状态 <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('status') is-invalid @enderror" 
                                                            id="status" name="status" required>
                                                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>待处理</option>
                                                        <option value="confirmed" {{ old('status', $order->status) == 'confirmed' ? 'selected' : '' }}>已确认</option>
                                                        <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>处理中</option>
                                                        <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>已发货</option>
                                                        <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>已送达</option>
                                                        <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>已完成</option>
                                                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>已取消</option>
                                                        <option value="refunded" {{ old('status', $order->status) == 'refunded' ? 'selected' : '' }}>已退款</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="payment_status" class="form-label">支付状态 <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('payment_status') is-invalid @enderror" 
                                                            id="payment_status" name="payment_status" required>
                                                        <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>待支付</option>
                                                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>已支付</option>
                                                        <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>支付失败</option>
                                                        <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>已退款</option>
                                                    </select>
                                                    @error('payment_status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="shipping_status" class="form-label">物流状态</label>
                                                    <select class="form-select @error('shipping_status') is-invalid @enderror" 
                                                            id="shipping_status" name="shipping_status">
                                                        <option value="pending" {{ old('shipping_status', $order->shipping_status) == 'pending' ? 'selected' : '' }}>待发货</option>
                                                        <option value="shipped" {{ old('shipping_status', $order->shipping_status) == 'shipped' ? 'selected' : '' }}>已发货</option>
                                                        <option value="in_transit" {{ old('shipping_status', $order->shipping_status) == 'in_transit' ? 'selected' : '' }}>运输中</option>
                                                        <option value="delivered" {{ old('shipping_status', $order->shipping_status) == 'delivered' ? 'selected' : '' }}>已送达</option>
                                                        <option value="returned" {{ old('shipping_status', $order->shipping_status) == 'returned' ? 'selected' : '' }}>已退回</option>
                                                    </select>
                                                    @error('shipping_status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="total_amount" class="form-label">订单总额 (RM) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control @error('total_amount') is-invalid @enderror" 
                                                           id="total_amount" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}" required>
                                                    @error('total_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="shipping_fee" class="form-label">运费 (RM)</label>
                                                    <input type="number" step="0.01" class="form-control @error('shipping_fee') is-invalid @enderror" 
                                                           id="shipping_fee" name="shipping_fee" value="{{ old('shipping_fee', $order->shipping_fee) }}">
                                                    @error('shipping_fee')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 客户信息 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">客户信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="customer_name" class="form-label">客户姓名 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                                           id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
                                                    @error('customer_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="customer_phone" class="form-label">客户电话 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                                           id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                                    @error('customer_phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="customer_email" class="form-label">客户邮箱</label>
                                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                                   id="customer_email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}">
                                            @error('customer_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- 收货地址 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">收货地址</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="shipping_address" class="form-label">收货地址 <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                                      id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                            @error('shipping_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="shipping_city" class="form-label">城市</label>
                                                    <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                                           id="shipping_city" name="shipping_city" value="{{ old('shipping_city', $order->shipping_city) }}">
                                                    @error('shipping_city')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="shipping_postal_code" class="form-label">邮编</label>
                                                    <input type="text" class="form-control @error('shipping_postal_code') is-invalid @enderror" 
                                                           id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code', $order->shipping_postal_code) }}">
                                                    @error('shipping_postal_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- 商家信息 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">商家信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="merchant_id" class="form-label">所属商家 <span class="text-danger">*</span></label>
                                            <select class="form-select @error('merchant_id') is-invalid @enderror" 
                                                    id="merchant_id" name="merchant_id" required>
                                                <option value="">请选择商家</option>
                                                @foreach($merchants as $merchant)
                                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $order->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                                        {{ $merchant->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('merchant_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- 物流信息 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">物流信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="tracking_number" class="form-label">快递单号</label>
                                            <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                                   id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}">
                                            @error('tracking_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="shipping_company" class="form-label">快递公司</label>
                                            <input type="text" class="form-control @error('shipping_company') is-invalid @enderror" 
                                                   id="shipping_company" name="shipping_company" value="{{ old('shipping_company', $order->shipping_company) }}">
                                            @error('shipping_company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- 备注信息 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">备注信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">订单备注</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="4">{{ old('notes', $order->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- 操作按钮 -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 保存修改
                                            </button>
                                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> 返回列表
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
