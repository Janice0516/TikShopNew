@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="card mb-3"><div class="card-body">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="text-muted">店铺名称</label>
      <div class="h5">{{ $shop['shopName'] ?? $shop['name'] ?? '—' }}</div>
    </div>
    <div class="col-md-6">
      <label class="text-muted">店铺ID</label>
      <div>{{ $shop['shopId'] ?? $shop['id'] ?? '—' }}</div>
    </div>
    <div class="col-md-6">
      <label class="text-muted">联系人</label>
      <div>{{ $shop['contactName'] ?? '—' }}</div>
    </div>
    <div class="col-md-6">
      <label class="text-muted">联系电话</label>
      <div>{{ $shop['contactPhone'] ?? '—' }}</div>
    </div>
  </div>
</div></div>

<h5 class="mt-4">店铺统计</h5>
<div class="row g-3">
  <div class="col-md-3"><div class="card"><div class="card-body">
    <div class="text-muted">商品数</div>
    <div class="h4">{{ $stats['productCount'] ?? 0 }}</div>
  </div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body">
    <div class="text-muted">订单数</div>
    <div class="h4">{{ $stats['orderCount'] ?? 0 }}</div>
  </div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body">
    <div class="text-muted">GMV</div>
    <div class="h4">￥{{ $stats['gmv'] ?? 0 }}</div>
  </div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body">
    <div class="text-muted">粉丝数</div>
    <div class="h4">{{ $stats['fans'] ?? 0 }}</div>
  </div></div></div>
</div>
@endsection