@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="table-responsive p-2">
  <table class="table table-bordered align-middle">
    <thead>
    <tr>
      <th>订单号</th>
      <th>状态</th>
      <th>金额</th>
      <th class="text-end">操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $o)
      <tr>
        <td><i class="bi bi-hash me-1"></i>{{ $o['orderNo'] }}</td>
        <td>
          @php $status = $o['status']; @endphp
          @if($status === '待发货')
            <span class="badge-status badge-pending">待发货</span>
          @elseif($status === '已发货')
            <span class="badge-status badge-shipped">已发货</span>
          @else
            <span class="badge-status badge-cancel">{{ $status }}</span>
          @endif
        </td>
        <td><span class="fw-semibold">￥{{ $o['amount'] }}</span></td>
        <td class="text-end actions">
          <button class="btn btn-sm btn-outline-success"><i class="bi bi-truck"></i> 发货</button>
          <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i> 打印</button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endsection