@extends('admin.layouts.app')

@section('title', '角色详情')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">角色管理</a></li>
                        <li class="breadcrumb-item active">角色详情</li>
                    </ol>
                </div>
                <h4 class="page-title">角色详情</h4>
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
                                <label class="form-label fw-bold">角色名称</label>
                                <p class="form-control-plaintext">{{ $role->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">角色标识</label>
                                <p class="form-control-plaintext">
                                    <code>{{ $role->slug }}</code>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">角色描述</label>
                        <p class="form-control-plaintext">{{ $role->description ?: '暂无描述' }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">排序</label>
                                <p class="form-control-plaintext">{{ $role->sort_order }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">角色等级</label>
                                <p class="form-control-plaintext">{{ $role->level }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">状态</label>
                                <p class="form-control-plaintext">
                                    @if($role->is_active)
                                        <span class="badge bg-success">启用</span>
                                    @else
                                        <span class="badge bg-danger">禁用</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">创建时间</label>
                                <p class="form-control-plaintext">{{ $role->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">更新时间</label>
                                <p class="form-control-plaintext">{{ $role->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 权限列表 -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">权限列表</h5>
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        <div class="row">
                            @foreach($role->permissions->groupBy('group') as $group => $permissions)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">{{ $group }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($permissions as $permission)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <div>
                                                        <div class="fw-medium">{{ $permission->name }}</div>
                                                        @if($permission->description)
                                                            <small class="text-muted">{{ $permission->description }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">该角色暂无权限</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 管理员列表 -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">使用该角色的管理员</h5>
                </div>
                <div class="card-body">
                    @if($role->admins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>姓名</th>
                                        <th>邮箱</th>
                                        <th>状态</th>
                                        <th>最后登录</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->admins as $admin)
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                @if($admin->is_active)
                                                    <span class="badge bg-success">启用</span>
                                                @else
                                                    <span class="badge bg-danger">禁用</span>
                                                @endif
                                            </td>
                                            <td>{{ $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i:s') : '从未登录' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">暂无管理员使用该角色</p>
                        </div>
                    @endif
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
                                <h3 class="text-primary">{{ $role->permissions->count() }}</h3>
                                <small class="text-muted">权限数量</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <h3 class="text-success">{{ $role->admins->count() }}</h3>
                                <small class="text-muted">管理员数量</small>
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
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> 编辑角色
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 返回列表
                        </a>
                        @if(!$role->is_system)
                            <button type="button" class="btn btn-danger" onclick="deleteRole({{ $role->id }})">
                                <i class="fas fa-trash"></i> 删除角色
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-danger" disabled>
                                <i class="fas fa-lock"></i> 系统角色不可删除
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 角色属性 -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">角色属性</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>系统角色</span>
                            @if($role->is_system)
                                <span class="badge bg-warning">是</span>
                            @else
                                <span class="badge bg-secondary">否</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>状态</span>
                            @if($role->is_active)
                                <span class="badge bg-success">启用</span>
                            @else
                                <span class="badge bg-danger">禁用</span>
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
                <p>确定要删除角色 "<span id="roleName"></span>" 吗？</p>
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
function deleteRole(roleId) {
    const roleName = '{{ $role->name }}';
    document.getElementById('roleName').textContent = roleName;
    document.getElementById('deleteForm').action = '{{ route("admin.roles.destroy", $role) }}';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
