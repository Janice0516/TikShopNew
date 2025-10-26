@extends('admin.layouts.app')

@section('title', '编辑商品')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">商品管理</a></li>
                        <li class="breadcrumb-item active">编辑商品</li>
                    </ol>
                </div>
                <h4 class="page-title">编辑商品</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- 基本信息 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">基本信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">商品名称 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sku" class="form-label">商品SKU <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                           id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                                    @error('sku')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">商品描述</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">商品分类 <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                                            id="category_id" name="category_id" required>
                                                        <option value="">请选择分类</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="merchant_id" class="form-label">所属商家 <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('merchant_id') is-invalid @enderror" 
                                                            id="merchant_id" name="merchant_id" required>
                                                        <option value="">请选择商家</option>
                                                        @foreach($merchants as $merchant)
                                                            <option value="{{ $merchant->id }}" {{ old('merchant_id', $product->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                                                {{ $merchant->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('merchant_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="brand" class="form-label">品牌</label>
                                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                                           id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                                    @error('brand')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 价格和库存 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">价格和库存</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">销售价格 (RM) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                                           id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cost_price" class="form-label">成本价格 (RM)</label>
                                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                                           id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                                                    @error('cost_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="stock" class="form-label">库存数量 <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                                           id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                                    @error('stock')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="weight" class="form-label">重量 (kg)</label>
                                                    <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror" 
                                                           id="weight" name="weight" value="{{ old('weight', $product->weight) }}">
                                                    @error('weight')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sort_order" class="form-label">排序</label>
                                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}">
                                                    @error('sort_order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 商品规格 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">商品规格</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">规格配置</label>
                                            <div id="specifications-container">
                                                @if($product->specifications && count($product->specifications) > 0)
                                                    @foreach($product->specifications as $index => $spec)
                                                        <div class="specification-item mb-2">
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" name="specifications[{{ $index }}][key]" 
                                                                           placeholder="规格名称" value="{{ $spec['key'] ?? '' }}">
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" name="specifications[{{ $index }}][value]" 
                                                                           placeholder="规格值" value="{{ $spec['value'] ?? '' }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-danger btn-sm remove-specification">删除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="specification-item mb-2">
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" name="specifications[0][key]" placeholder="规格名称">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" name="specifications[0][value]" placeholder="规格值">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm remove-specification">删除</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm" id="add-specification">添加规格</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- 商品状态 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">商品状态</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">状态 <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>上架</option>
                                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>下架</option>
                                                <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>草稿</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" 
                                                       {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">
                                                    推荐商品
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 商品图片 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">商品图片</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($product->images && count($product->images) > 0)
                                            <div class="mb-3">
                                                <label class="form-label">当前图片</label>
                                                <div class="row">
                                                    @foreach($product->images as $image)
                                                        <div class="col-6 mb-2">
                                                            <img src="{{ $image }}" alt="商品图片" class="img-fluid rounded">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <label for="images" class="form-label">上传新图片</label>
                                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                                   id="images" name="images[]" multiple accept="image/*">
                                            @error('images')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">支持多张图片上传，建议尺寸：800x800px</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 统计信息 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">统计信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <h4 class="text-primary">{{ $product->views_count ?? 0 }}</h4>
                                                    <small class="text-muted">浏览次数</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <h4 class="text-success">{{ $product->sales_count ?? 0 }}</h4>
                                                    <small class="text-muted">销售数量</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="mb-2">
                                                <h4 class="text-warning">{{ $product->rating ?? 0 }}</h4>
                                                <small class="text-muted">评分</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 操作按钮 -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 保存修改
                                            </button>
                                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> 返回列表
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 添加规格
    document.getElementById('add-specification').addEventListener('click', function() {
        const container = document.getElementById('specifications-container');
        const index = container.children.length;
        
        const div = document.createElement('div');
        div.className = 'specification-item mb-2';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="specifications[${index}][key]" placeholder="规格名称">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="specifications[${index}][value]" placeholder="规格值">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-specification">删除</button>
                </div>
            </div>
        `;
        
        container.appendChild(div);
    });

    // 删除规格
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-specification')) {
            e.target.closest('.specification-item').remove();
        }
    });
});
</script>
@endpush
