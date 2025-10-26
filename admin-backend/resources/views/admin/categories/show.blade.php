@extends('admin.layouts.app')

@section('title', '分类详情')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">分类管理</a></li>
                        <li class="breadcrumb-item active">分类详情</li>
                    </ol>
                </div>
                <h4 class="page-title">分类详情</h4>
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
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                            {{ $category->is_active ? '启用' : '禁用' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-primary">{{ $category->name }}</h4>
                                            <p class="text-muted">{{ $category->description }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h5 class="text-success">{{ $category->products_count ?? 0 }}</h5>
                                                        <small class="text-muted">商品数量</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <h5 class="text-info">{{ $category->children_count ?? 0 }}</h5>
                                                        <small class="text-muted">子分类</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>URL别名:</strong><br>
                                                <span class="text-muted">{{ $category->slug }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>父级分类:</strong><br>
                                                <span class="text-muted">{{ $category->parent->name ?? '顶级分类' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>排序:</strong><br>
                                                <span class="text-muted">{{ $category->sort_order }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>层级:</strong><br>
                                                <span class="text-muted">{{ $category->depth ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO信息 -->
                            @if($category->meta_title || $category->meta_description || $category->meta_keywords)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">SEO信息</h5>
                                </div>
                                <div class="card-body">
                                    @if($category->meta_title)
                                    <div class="mb-2">
                                        <strong>页面标题:</strong><br>
                                        <span class="text-muted">{{ $category->meta_title }}</span>
                                    </div>
                                    @endif

                                    @if($category->meta_description)
                                    <div class="mb-2">
                                        <strong>页面描述:</strong><br>
                                        <span class="text-muted">{{ $category->meta_description }}</span>
                                    </div>
                                    @endif

                                    @if($category->meta_keywords)
                                    <div class="mb-2">
                                        <strong>关键词:</strong><br>
                                        <span class="text-muted">{{ $category->meta_keywords }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- 子分类 -->
                            @if($category->children && $category->children->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">子分类</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($category->children as $child)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $child->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $child->products_count ?? 0 }} 个商品</small>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- 分类商品 -->
                            @if($category->products && $category->products->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">分类商品</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>商品名称</th>
                                                    <th>SKU</th>
                                                    <th>价格</th>
                                                    <th>库存</th>
                                                    <th>状态</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($category->products->take(10) as $product)
                                                    <tr>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->sku }}</td>
                                                        <td>RM {{ number_format($product->price, 2) }}</td>
                                                        <td>{{ $product->stock }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $product->status == 'active' ? 'success' : ($product->status == 'inactive' ? 'danger' : 'warning') }}">
                                                                {{ $product->status == 'active' ? '上架' : ($product->status == 'inactive' ? '下架' : '草稿') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($category->products->count() > 10)
                                        <div class="text-center mt-2">
                                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                                                查看所有商品 ({{ $category->products->count() }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- 操作按钮 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">操作</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> 编辑分类
                                        </a>
                                        
                                        <form action="{{ route('admin.categories.update-status', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $category->is_active ? 'warning' : 'success' }} w-100">
                                                <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></i> 
                                                {{ $category->is_active ? '禁用' : '启用' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
                                        <span class="text-muted">{{ $category->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>更新时间:</strong><br>
                                        <span class="text-muted">{{ $category->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 分类路径 -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">分类路径</h5>
                                </div>
                                <div class="card-body">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            @foreach($category->getFullPath() as $pathCategory)
                                                <li class="breadcrumb-item {{ $pathCategory->id == $category->id ? 'active' : '' }}">
                                                    @if($pathCategory->id == $category->id)
                                                        {{ $pathCategory->name }}
                                                    @else
                                                        <a href="{{ route('admin.categories.show', $pathCategory) }}">{{ $pathCategory->name }}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ol>
                                    </nav>
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
