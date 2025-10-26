@extends('admin.layouts.app')

@section('title', '商品详情')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">商品管理</a></li>
                        <li class="breadcrumb-item active">商品详情</li>
                    </ol>
                </div>
                <h4 class="page-title">商品详情</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- 基本信息 -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">基本信息</h5>
                                    <div>
                                        <span class="badge bg-{{ $product->status == 'active' ? 'success' : ($product->status == 'inactive' ? 'danger' : 'warning') }}">
                                            {{ $product->status == 'active' ? '上架' : ($product->status == 'inactive' ? '下架' : '草稿') }}
                                        </span>
                                        @if($product->is_featured)
                                            <span class="badge bg-primary">推荐商品</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-primary">{{ $product->name }}</h4>
                                            <p class="text-muted">{{ $product->description }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h5 class="text-success">RM {{ number_format($product->price, 2) }}</h5>
                                                        <small class="text-muted">销售价格</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h5 class="text-info">{{ $product->stock }}</h5>
                                                        <small class="text-muted">库存数量</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>商品SKU:</strong><br>
                                                <span class="text-muted">{{ $product->sku }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>商品分类:</strong><br>
                                                <span class="text-muted">{{ $product->category->name ?? '未分类' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>所属商家:</strong><br>
                                                <span class="text-muted">{{ $product->merchant->name ?? '未知商家' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>品牌:</strong><br>
                                                <span class="text-muted">{{ $product->brand ?? '无' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <strong>成本价格:</strong><br>
                                                <span class="text-muted">RM {{ number_format($product->cost_price ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <strong>重量:</strong><br>
                                                <span class="text-muted">{{ $product->weight ?? 0 }} kg</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <strong>排序:</strong><br>
                                                <span class="text-muted">{{ $product->sort_order ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 商品图片 -->
                            @if($product->images && count($product->images) > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">商品图片</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($product->images as $image)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <img src="{{ $image }}" alt="商品图片" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- 商品规格 -->
                            @if($product->specifications && count($product->specifications) > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">商品规格</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($product->specifications as $spec)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $spec['key'] ?? '' }}:</strong>
                                                    <span class="text-muted">{{ $spec['value'] ?? '' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- 统计信息 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">统计信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <h3 class="text-primary">{{ $product->views_count ?? 0 }}</h3>
                                                <small class="text-muted">浏览次数</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <h3 class="text-success">{{ $product->sales_count ?? 0 }}</h3>
                                                <small class="text-muted">销售数量</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <h3 class="text-warning">{{ $product->rating ?? 0 }}</h3>
                                            <small class="text-muted">评分</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 操作按钮 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">操作</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> 编辑商品
                                        </a>
                                        
                                        <form action="{{ route('admin.products.update-status', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $product->status == 'active' ? 'warning' : 'success' }} w-100">
                                                <i class="fas fa-{{ $product->status == 'active' ? 'pause' : 'play' }}"></i> 
                                                {{ $product->status == 'active' ? '下架' : '上架' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.products.update-featured', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $product->is_featured ? 'secondary' : 'info' }} w-100">
                                                <i class="fas fa-star"></i> 
                                                {{ $product->is_featured ? '取消推荐' : '设为推荐' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> 返回列表
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 时间信息 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">时间信息</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>创建时间:</strong><br>
                                        <span class="text-muted">{{ $product->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>更新时间:</strong><br>
                                        <span class="text-muted">{{ $product->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
