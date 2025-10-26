@extends('admin.layouts.app')

@section('title', '编辑角色')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">角色管理</a></li>
                        <li class="breadcrumb-item active">编辑角色</li>
                    </ol>
                </div>
                <h4 class="page-title">编辑角色</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
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
                                                    <label for="name" class="form-label">角色名称 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="slug" class="form-label">角色标识</label>
                                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                           id="slug" name="slug" value="{{ old('slug', $role->slug) }}">
                                                    @error('slug')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">留空将自动生成</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">角色描述</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4">{{ old('description', $role->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sort_order" class="form-label">排序</label>
                                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $role->sort_order) }}">
                                                    @error('sort_order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="level" class="form-label">角色等级</label>
                                                    <input type="number" class="form-control @error('level') is-invalid @enderror" 
                                                           id="level" name="level" value="{{ old('level', $role->level) }}" min="1" max="10">
                                                    @error('level')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">数字越大权限越高</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 权限配置 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">权限配置</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($permissions as $group => $groupPermissions)
                                                <div class="col-md-6 mb-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">{{ $group }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            @foreach($groupPermissions as $permission)
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input" 
                                                                           id="permission_{{ $permission->id }}" 
                                                                           name="permissions[]" 
                                                                           value="{{ $permission->id }}"
                                                                           {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                        {{ $permission->name }}
                                                                    </label>
                                                                    @if($permission->description)
                                                                        <br><small class="text-muted">{{ $permission->description }}</small>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- 角色状态 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">角色状态</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    启用角色
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_system" name="is_system" value="1" {{ old('is_system', $role->is_system) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_system">
                                                    系统角色
                                                </label>
                                            </div>
                                            <div class="form-text">系统角色不能被删除</div>
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
                                                    <h4 class="text-primary">{{ $role->permissions_count ?? 0 }}</h4>
                                                    <small class="text-muted">权限数量</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <h4 class="text-success">{{ $role->admins_count ?? 0 }}</h4>
                                                    <small class="text-muted">管理员数量</small>
                                                </div>
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
                                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
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
    // 自动生成slug
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        
        if (!document.getElementById('slug').value) {
            document.getElementById('slug').value = slug;
        }
    });

    // 全选/取消全选权限
    document.querySelectorAll('.card-header h6').forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const card = this.closest('.card');
            const checkboxes = card.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
        });
    });
});
</script>
@endpush
