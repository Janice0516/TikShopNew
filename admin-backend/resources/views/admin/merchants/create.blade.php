@extends('admin.layouts.app')

@section('title', '添加商家')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">仪表盘</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.merchants.index') }}">商家管理</a></li>
                        <li class="breadcrumb-item active">添加商家</li>
                    </ol>
                </div>
                <h4 class="page-title">添加商家</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.merchants.store') }}" method="POST">
                        @csrf
                        
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
                                                    <label for="name" class="form-label">商家名称 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">邮箱地址 <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" name="email" value="{{ old('email') }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">密码 <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                           id="password" name="password" required>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">确认密码 <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control" 
                                                           id="password_confirmation" name="password_confirmation" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">联系电话</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" name="phone" value="{{ old('phone') }}">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="company_name" class="form-label">公司名称</label>
                                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                           id="company_name" name="company_name" value="{{ old('company_name') }}">
                                                    @error('company_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">地址</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- 商家信息 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">商家信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="business_license" class="form-label">营业执照号</label>
                                                    <input type="text" class="form-control @error('business_license') is-invalid @enderror" 
                                                           id="business_license" name="business_license" value="{{ old('business_license') }}">
                                                    @error('business_license')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_number" class="form-label">税务登记号</label>
                                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                                           id="tax_number" name="tax_number" value="{{ old('tax_number') }}">
                                                    @error('tax_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="contact_person" class="form-label">联系人</label>
                                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                                           id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                                                    @error('contact_person')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="contact_phone" class="form-label">联系人电话</label>
                                                    <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                                           id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                                                    @error('contact_phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- 商家状态 -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">商家状态</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">状态 <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>正常</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>禁用</option>
                                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>暂停</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_verified" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_verified">
                                                    已认证
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 财务信息 -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">财务信息</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="balance" class="form-label">初始余额 (RM)</label>
                                            <input type="number" step="0.01" class="form-control @error('balance') is-invalid @enderror" 
                                                   id="balance" name="balance" value="{{ old('balance', 0) }}">
                                            @error('balance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="frozen_amount" class="form-label">冻结金额 (RM)</label>
                                            <input type="number" step="0.01" class="form-control @error('frozen_amount') is-invalid @enderror" 
                                                   id="frozen_amount" name="frozen_amount" value="{{ old('frozen_amount', 0) }}">
                                            @error('frozen_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- 操作按钮 -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 保存商家
                                            </button>
                                            <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary">
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
