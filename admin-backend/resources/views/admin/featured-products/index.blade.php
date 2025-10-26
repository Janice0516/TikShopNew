@extends('admin.layouts.app')

@section('title', '特色商品管理')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">特色商品管理</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                            <i class="fas fa-plus"></i> 添加商品
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Savings for you Section -->
                    <div class="mb-5">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-tags"></i> Savings for you
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">排序</th>
                                        <th width="10%">商品图片</th>
                                        <th width="20%">商品名称</th>
                                        <th width="10%">价格</th>
                                        <th width="8%">评分</th>
                                        <th width="10%">销量</th>
                                        <th width="8%">状态</th>
                                        <th width="20%">操作</th>
                                    </tr>
                                </thead>
                                <tbody id="savings-tbody">
                                    @forelse($savingsProducts as $item)
                                        <tr data-id="{{ $item->id }}">
                                            <td>
                                                <input type="number" class="form-control form-control-sm sort-order" 
                                                       value="{{ $item->sort_order }}" min="0" style="width: 60px;">
                                            </td>
                                            <td>
                                                @if($item->product->images && count($item->product->images) > 0)
                                                    <img src="{{ $item->product->images[0] }}" alt="{{ $item->product->name }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $item->display_title }}</div>
                                                <small class="text-muted">{{ $item->product->name }}</small>
                                            </td>
                                            <td>
                                                <div class="text-danger font-weight-bold">RM {{ number_format($item->display_price, 2) }}</div>
                                                @if($item->display_original_price)
                                                    <small class="text-muted text-decoration-line-through">RM {{ number_format($item->display_original_price, 2) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-warning">{{ $item->display_rating }}</span>
                                                    <i class="fas fa-star text-warning ml-1"></i>
                                                </div>
                                            </td>
                                            <td>{{ number_format($item->display_sales_count / 1000, 1) }}K</td>
                                            <td>
                                                <span class="badge badge-{{ $item->status_color }}">
                                                    {{ $item->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info btn-sm" 
                                                            onclick="editProduct({{ $item->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-{{ $item->is_active ? 'warning' : 'success' }} btn-sm" 
                                                            onclick="toggleStatus({{ $item->id }})">
                                                        <i class="fas fa-{{ $item->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deleteProduct({{ $item->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">暂无商品</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top deals for you Section -->
                    <div>
                        <h4 class="text-success mb-3">
                            <i class="fas fa-fire"></i> Top deals for you
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">排序</th>
                                        <th width="10%">商品图片</th>
                                        <th width="20%">商品名称</th>
                                        <th width="10%">价格</th>
                                        <th width="8%">评分</th>
                                        <th width="10%">销量</th>
                                        <th width="8%">状态</th>
                                        <th width="20%">操作</th>
                                    </tr>
                                </thead>
                                <tbody id="top-deals-tbody">
                                    @forelse($topDealsProducts as $item)
                                        <tr data-id="{{ $item->id }}">
                                            <td>
                                                <input type="number" class="form-control form-control-sm sort-order" 
                                                       value="{{ $item->sort_order }}" min="0" style="width: 60px;">
                                            </td>
                                            <td>
                                                @if($item->product->images && count($item->product->images) > 0)
                                                    <img src="{{ $item->product->images[0] }}" alt="{{ $item->product->name }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $item->display_title }}</div>
                                                <small class="text-muted">{{ $item->product->name }}</small>
                                            </td>
                                            <td>
                                                <div class="text-danger font-weight-bold">RM {{ number_format($item->display_price, 2) }}</div>
                                                @if($item->display_original_price)
                                                    <small class="text-muted text-decoration-line-through">RM {{ number_format($item->display_original_price, 2) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-warning">{{ $item->display_rating }}</span>
                                                    <i class="fas fa-star text-warning ml-1"></i>
                                                </div>
                                            </td>
                                            <td>{{ number_format($item->display_sales_count / 1000, 1) }}K</td>
                                            <td>
                                                <span class="badge badge-{{ $item->status_color }}">
                                                    {{ $item->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info btn-sm" 
                                                            onclick="editProduct({{ $item->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-{{ $item->is_active ? 'warning' : 'success' }} btn-sm" 
                                                            onclick="toggleStatus({{ $item->id }})">
                                                        <i class="fas fa-{{ $item->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deleteProduct({{ $item->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">暂无商品</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加特色商品</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.featured-products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">特色区域</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">请选择</option>
                                    <option value="savings">Savings for you</option>
                                    <option value="top_deals">Top deals for you</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_id">选择商品</label>
                                <select name="product_id" id="product_id" class="form-control" required>
                                    <option value="">请选择商品</option>
                                    @foreach($allProducts as $product)
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->price }}"
                                                data-original-price="{{ $product->original_price }}"
                                                data-rating="{{ $product->rating }}"
                                                data-sales-count="{{ $product->sales_count }}">
                                            {{ $product->name }} - RM {{ number_format($product->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sort_order">排序</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="custom_title">自定义标题</label>
                                <input type="text" name="custom_title" id="custom_title" class="form-control" placeholder="留空使用原商品名称">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="custom_price">自定义价格</label>
                                <input type="number" name="custom_price" id="custom_price" class="form-control" step="0.01" min="0" placeholder="留空使用原价格">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="custom_original_price">自定义原价</label>
                                <input type="number" name="custom_original_price" id="custom_original_price" class="form-control" step="0.01" min="0" placeholder="留空使用原原价">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="custom_rating">自定义评分</label>
                                <input type="number" name="custom_rating" id="custom_rating" class="form-control" step="0.1" min="0" max="5" placeholder="留空使用原评分">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="custom_sales_count">自定义销量</label>
                                <input type="number" name="custom_sales_count" id="custom_sales_count" class="form-control" min="0" placeholder="留空使用原销量">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">编辑特色商品</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sort_order">排序</label>
                                <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_custom_title">自定义标题</label>
                                <input type="text" name="custom_title" id="edit_custom_title" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_custom_price">自定义价格</label>
                                <input type="number" name="custom_price" id="edit_custom_price" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_custom_original_price">自定义原价</label>
                                <input type="number" name="custom_original_price" id="edit_custom_original_price" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_custom_rating">自定义评分</label>
                                <input type="number" name="custom_rating" id="edit_custom_rating" class="form-control" step="0.1" min="0" max="5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_custom_sales_count">自定义销量</label>
                                <input type="number" name="custom_sales_count" id="edit_custom_sales_count" class="form-control" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="edit_is_active" class="form-check-input" value="1">
                            <label for="edit_is_active" class="form-check-label">启用</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 商品选择变化时自动填充数据
    $('#product_id').change(function() {
        const option = $(this).find('option:selected');
        if (option.val()) {
            $('#custom_price').attr('placeholder', '原价: RM ' + parseFloat(option.data('price')).toFixed(2));
            $('#custom_original_price').attr('placeholder', '原原价: RM ' + parseFloat(option.data('original-price')).toFixed(2));
            $('#custom_rating').attr('placeholder', '原评分: ' + parseFloat(option.data('rating')).toFixed(1));
            $('#custom_sales_count').attr('placeholder', '原销量: ' + parseInt(option.data('sales-count')).toLocaleString());
        }
    });

    // 排序更新
    $('.sort-order').on('change', function() {
        const id = $(this).closest('tr').data('id');
        const sortOrder = $(this).val();
        
        $.ajax({
            url: '{{ route("admin.featured-products.update-sort") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: [{ id: id, sort_order: sortOrder }]
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('排序已更新');
                }
            },
            error: function() {
                toastr.error('更新失败');
            }
        });
    });
});

function editProduct(id) {
    // 这里应该通过AJAX获取商品数据并填充表单
    $('#editForm').attr('action', '{{ route("admin.featured-products.update", ":id") }}'.replace(':id', id));
    $('#editProductModal').modal('show');
}

function toggleStatus(id) {
    if (confirm('确定要切换状态吗？')) {
        window.location.href = '{{ route("admin.featured-products.toggle-status", ":id") }}'.replace(':id', id);
    }
}

function deleteProduct(id) {
    if (confirm('确定要删除这个特色商品吗？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.featured-products.destroy", ":id") }}'.replace(':id', id);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
