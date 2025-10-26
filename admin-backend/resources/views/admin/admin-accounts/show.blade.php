@extends('admin.layouts.app')

@section('title', '管理员详情')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.admin-accounts.index') }}">管理员账户</a></li>
                        <li class="breadcrumb-item active">管理员详情</li>
                    </ol>
                </div>
                <h4 class="page-title">管理员详情</h4>
            </div>
        </div>
    </div>

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
                                <label class="form-label fw-bold">姓名</label>
                                <p class="form-control-plaintext">{{ $admin->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">邮箱</label>
                                <p class="form-control-plaintext">{{ $admin->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">手机号</label>
                                <p class="form-control-plaintext">{{ $admin->phone ?: '未设置' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">头像</label>
                                <p class="form-control-plaintext">
                                    @if($admin->avatar)
                                        <img src="{{ $admin->avatar }}" alt="头像" class="rounded-circle" width="40" height="40">
                                    @else
                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">状态</label>
                                <p class="form-control-plaintext">
                                    @if($admin->is_active)
                                        <span class="badge bg-success">启用</span>
                                    @else
                                        <span class="badge bg-danger">禁用</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">管理员类型</label>
                                <p class="form-control-plaintext">
                                    @if($admin->is_super_admin)
                                        <span class="badge bg-warning">超级管理员</span>
                                    @else
                                        <span class="badge bg-primary">普通管理员</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">创建时间</label>
                                <p class="form-control-plaintext">{{ $admin->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">更新时间</label>
                                <p class="form-control-plaintext">{{ $admin->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 角色信息 -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">角色信息</h5>
                </div>
                <div class="card-body">
                    @if($admin->role)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">角色名称</label>
                                    <p class="form-control-plaintext">{{ $admin->role->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">角色标识</label>
                                    <p class="form-control-plaintext">
                                        <code>{{ $admin->role->slug }}</code>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">角色描述</label>
                            <p class="form-control-plaintext">{{ $admin->role->description ?: '暂无描述' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">角色权限</label>
                            <div class="row">
                                @foreach($admin->role->permissions->groupBy('group') as $group => $permissions)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">{{ $group }}</h6>
                                            </div>
                                            <div class="card-body">
                                                @foreach($permissions as $permission)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>{{ $permission->name }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                            <p class="text-muted">该管理员暂无角色</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 登录记录 -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">登录记录</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">登录次数</label>
                                <p class="form-control-plaintext">{{ $admin->login_count ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">最后登录时间</label>
                                <p class="form-control-plaintext">
                                    {{ $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i:s') : '从未登录' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">最后登录IP</label>
                                <p class="form-control-plaintext">{{ $admin->last_login_ip ?: '未知' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">最后登录地点</label>
                                <p class="form-control-plaintext">{{ $admin->last_login_location ?: '未知' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <h3 class="text-primary">{{ $admin->login_count ?? 0 }}</h3>
                                <small class="text-muted">登录次数</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <h3 class="text-success">{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : '从未登录' }}</h3>
                                <small class="text-muted">最后登录</small>
                            </div>
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
                        <a href="{{ route('admin.admin-accounts.edit', $admin) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> 编辑管理员
                        </a>
                        <a href="{{ route('admin.admin-accounts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 返回列表
                        </a>
                        @if(!$admin->is_super_admin)
                            <button type="button" class="btn btn-danger" onclick="deleteAdmin({{ $admin->id }})">
                                <i class="fas fa-trash"></i> 删除管理员
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-danger" disabled>
                                <i class="fas fa-lock"></i> 超级管理员不可删除
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 管理员属性 -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">管理员属性</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>超级管理员</span>
                            @if($admin->is_super_admin)
                                <span class="badge bg-warning">是</span>
                            @else
                                <span class="badge bg-secondary">否</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>状态</span>
                            @if($admin->is_active)
                                <span class="badge bg-success">启用</span>
                            @else
                                <span class="badge bg-danger">禁用</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>邮箱验证</span>
                            @if($admin->email_verified_at)
                                <span class="badge bg-success">已验证</span>
                            @else
                                <span class="badge bg-warning">未验证</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 删除确认模态框 -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">确认删除</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>确定要删除管理员 "<span id="adminName"></span>" 吗？</p>
                <p class="text-danger">此操作不可撤销！</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">确认删除</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteAdmin(adminId) {
    const adminName = '{{ $admin->name }}';
    document.getElementById('adminName').textContent = adminName;
    document.getElementById('deleteForm').action = '{{ route("admin.admin-accounts.destroy", $admin) }}';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
