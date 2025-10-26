@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="card mb-3"><div class="card-body">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <div class="text-muted">当前余额</div>
      <div class="h4">￥{{ $balance['amount'] ?? 0 }}</div>
    </div>
    <a href="#" class="btn btn-primary btn-sm disabled" aria-disabled="true">申请提现（示例）</a>
  </div>
</div></div>

<h5 class="mt-4">提现记录</h5>
<div class="table-responsive p-2">
  <table class="table table-bordered align-middle">
    <thead><tr>
      <th>ID</th>
      <th>金额</th>
      <th>状态</th>
      <th>时间</th>
    </tr></thead>
    <tbody>
      @foreach(($withdrawals['list'] ?? $withdrawals) as $w)
        <tr>
          <td>{{ $w['id'] ?? '' }}</td>
          <td>￥{{ $w['amount'] ?? 0 }}</td>
          <td>{{ $w['status'] ?? '' }}</td>
          <td>{{ $w['createdAt'] ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection