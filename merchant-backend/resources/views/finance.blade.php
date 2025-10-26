@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">总收入</div>
      <div class="h4">￥{{ $stats['totalRevenue'] ?? ($stats['revenue'] ?? 0) }}</div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">订单数</div>
      <div class="h4">{{ $stats['orderCount'] ?? 0 }}</div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">余额</div>
      <div class="h4">￥{{ $stats['balance'] ?? 0 }}</div>
    </div></div>
  </div>
</div>

<h5 class="mt-4">资金流水</h5>
<div class="table-responsive p-2">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>时间</th>
        <th>类型</th>
        <th>金额</th>
        <th>备注</th>
      </tr>
    </thead>
    <tbody>
      @foreach(($flows['list'] ?? $flows) as $f)
        <tr>
          <td>{{ $f['time'] ?? $f['createdAt'] ?? '' }}</td>
          <td>{{ $f['type'] ?? '' }}</td>
          <td>{{ $f['amount'] ?? 0 }}</td>
          <td>{{ $f['remark'] ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection