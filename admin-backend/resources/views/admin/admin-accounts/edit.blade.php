@extends('admin.layouts.app')

@section('title', '编辑管理员')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.admin-accounts.index') }}">管理员账户</a></li>
                        <li class="breadcrumb-item active">编辑管理员</li>
                    </ol>
                </div>
                <h4 class="page-title">编辑管理员</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.admin-accounts.update', $admin) }}" method="POST">
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
                                                    <label for="name" class="form-label">姓名 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">邮箱 <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">手机号</label>
                                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" name="phone" value="{{ old('phone', $admin->phone) }}">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="avatar" class="form-label">头像</label>
                                                    <input type="url" class="form-control @error('avatar') is-invalid @enderror" 
                                                           id="avatar" name="avatar" value="{{ old('avatar', $admin->avatar) }}">
                                                    @error('avatar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">新密码</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">留空表示不修改密码</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">确认新密码</label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>

                                <!-- 角色权限 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">角色权限</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="role_id" class="form-label">角色 <span class="text-danger">*</span></label>
                                            <select class="form-select @error('role_id') is-invalid @enderror" 
                                                    id="role_id" name="role_id" required>
                                                <option value="">请选择角色</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" 
                                                            {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                        @if($role->description)
                                                            - {{ $role->description }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- 状态设置 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">状态设置</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    启用账户
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin', $admin->is_super_admin) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_super_admin">
                                                    超级管理员
                                                </label>
                                            </div>
                                            <div class="form-text">超级管理员拥有所有权限</div>
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
                                                    <h4 class="text-primary">{{ $admin->login_count ?? 0 }}</h4>
                                                    <small class="text-muted">登录次数</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <h4 class="text-success">{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : '从未登录' }}</h4>
                                                    <small class="text-muted">最后登录</small>
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
                                            <a href="{{ route('admin.admin-accounts.index') }}" class="btn btn-secondary">
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
    // 密码确认验证
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        
        if (password && confirmation && password !== confirmation) {
            this.setCustomValidity('密码不匹配');
        } else {
            this.setCustomValidity('');
        }
    });

    document.getElementById('password').addEventListener('input', function() {
        const confirmation = document.getElementById('password_confirmation');
        if (confirmation.value) {
            confirmation.dispatchEvent(new Event('input'));
        }
    });
});
</script>
@endpush
