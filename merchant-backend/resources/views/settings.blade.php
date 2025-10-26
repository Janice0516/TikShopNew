@extends('layouts.app')

@section('content')
<h3 class="mb-3">{{ $title }}</h3>
<div class="form-card">
  <form class="row g-3">
    <div class="col-md-6">
      <label class="form-label">店铺名称</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-shop"></i></span>
        <input type="text" class="form-control" value="{{ $settings['shopName'] }}" />
      </div>
    </div>
    <div class="col-md-6">
      <label class="form-label">联系人</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input type="text" class="form-control" value="{{ $settings['contactName'] }}" />
      </div>
    </div>
    <div class="col-md-6">
      <label class="form-label">联系电话</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
        <input type="text" class="form-control" value="{{ $settings['contactPhone'] }}" />
      </div>
    </div>
    <div class="col-12">
      <button type="button" class="btn btn-primary"><i class="bi bi-save"></i> 保存</button>
      <button type="button" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-counterclockwise"></i> 重置</button>
    </div>
  </form>
</div>
@endsection