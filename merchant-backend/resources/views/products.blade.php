@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="table-responsive p-2">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>名称</th>
        <th>价格</th>
        <th>库存</th>
        <th class="text-end">操作</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $p)
        <tr>
          <td>{{ $p['id'] }}</td>
          <td><i class="bi bi-box-seam me-1"></i>{{ $p['name'] }}</td>
          <td><span class="fw-semibold text-success">￥{{ $p['price'] }}</span></td>
          <td>
            @if(($p['stock'] ?? 0) <= 10)
              <span class="badge bg-danger">{{ $p['stock'] }} 低库存</span>
            @elseif(($p['stock'] ?? 0) <= 50)
              <span class="badge bg-warning text-dark">{{ $p['stock'] }} 中等</span>
            @else
              <span class="badge bg-success">{{ $p['stock'] }} 充足</span>
            @endif
          </td>
          <td class="text-end actions">
            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> 编辑</button>
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> 删除</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection