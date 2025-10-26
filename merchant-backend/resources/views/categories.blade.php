@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
@if(empty($categories))
  <div class="empty-state text-center">
    <i class="bi bi-grid-3x3-gap" style="font-size:42px"></i>
    <div class="mt-2">暂无分类数据（请检查后端 API）</div>
  </div>
@else
<div class="table-responsive p-2">
  <table class="table table-hover align-middle">
    <thead>
    <tr>
      <th>ID</th>
      <th>名称</th>
      <th class="text-end">操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach($categories as $c)
      <tr>
        <td>{{ $c['id'] ?? '-' }}</td>
        <td><i class="bi bi-tags me-1"></i>{{ $c['name'] ?? '-' }}</td>
        <td class="text-end actions">
          <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> 编辑</button>
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> 删除</button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif
@endsection