@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card card-gradient-blue">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="h6 mb-1">商品数</div>
          <div class="display-6">{{ $stats['productCount'] }}</div>
        </div>
        <i class="bi bi-box-seam" style="font-size:44px"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-gradient-purple">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="h6 mb-1">订单数</div>
          <div class="display-6">{{ $stats['orderCount'] }}</div>
        </div>
        <i class="bi bi-receipt" style="font-size:44px"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-gradient-green">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="h6 mb-1">收入</div>
          <div class="display-6">￥{{ number_format($stats['revenue'], 2) }}</div>
        </div>
        <i class="bi bi-cash-stack" style="font-size:44px"></i>
      </div>
    </div>
  </div>
</div>
@endsection