@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">当前等级</div>
      <div class="h4">{{ $current['level'] ?? '—' }}</div>
      <div class="small">分数：{{ $current['score'] ?? '—' }}</div>
    </div></div>
  </div>
  <div class="col-md-8">
    <div class="card"><div class="card-body">
      <div class="text-muted">提升指南</div>
      <div>{!! nl2br(e($guide['content'] ?? '暂无指南')) !!}</div>
    </div></div>
  </div>
</div>

<h5 class="mt-4">历史记录</h5>
<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead><tr>
      <th>时间</th>
      <th>等级</th>
      <th>分数</th>
      <th>备注</th>
    </tr></thead>
    <tbody>
      @foreach(($history['list'] ?? $history) as $h)
        <tr>
          <td>{{ $h['time'] ?? $h['createdAt'] ?? '' }}</td>
          <td>{{ $h['level'] ?? '' }}</td>
          <td>{{ $h['score'] ?? '' }}</td>
          <td>{{ $h['remark'] ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection