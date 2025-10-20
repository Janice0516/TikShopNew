import request from '@/utils/request'

export interface RoleForm {
  code: string
  name: string
  description?: string
  status?: number
  permissionIds?: string[]
}

export interface PermissionForm {
  code: string
  name: string
  description?: string
  group: string
}

export interface AdminForm {
  username: string
  password: string
  nickname?: string
  position?: string
  phone?: string
  email?: string
  roleId?: string
  remark?: string
}

export interface SalespersonForm {
  username: string
  password: string
  nickname: string
  phone: string
  email?: string
  remark?: string
}

export interface RoleQuery {
  page?: number
  limit?: number
  code?: string
  name?: string
  status?: number
}

// ========== 角色管理 API ==========

// 获取角色列表
export function getRoleList(query: RoleQuery) {
  return request({
    url: '/role-permission/role/list',
    method: 'get',
    params: query
  })
}

// 获取所有角色（下拉选择）
export function getAllRoles() {
  return request({
    url: '/role-permission/role/all',
    method: 'get'
  })
}

// 创建角色
export function createRole(data: RoleForm) {
  return request({
    url: '/role-permission/role/create',
    method: 'post',
    data
  })
}

// 更新角色
export function updateRole(id: string, data: Partial<RoleForm>) {
  return request({
    url: `/role-permission/role/${id}`,
    method: 'patch',
    data
  })
}

// 删除角色
export function deleteRole(id: string) {
  return request({
    url: `/role-permission/role/${id}`,
    method: 'delete'
  })
}

// ========== 权限管理 API ==========

// 获取权限列表
export function getPermissionList() {
  return request({
    url: '/role-permission/permission/list',
    method: 'get'
  })
}

// 按分组获取权限
export function getPermissionsByGroup() {
  return request({
    url: '/role-permission/permission/grouped',
    method: 'get'
  })
}

// 创建权限
export function createPermission(data: PermissionForm) {
  return request({
    url: '/role-permission/permission/create',
    method: 'post',
    data
  })
}

// 更新权限
export function updatePermission(id: string, data: Partial<PermissionForm>) {
  return request({
    url: `/role-permission/permission/${id}`,
    method: 'patch',
    data
  })
}

// 删除权限
export function deletePermission(id: string) {
  return request({
    url: `/role-permission/permission/${id}`,
    method: 'delete'
  })
}

// ========== 管理员账户管理 API ==========

// 获取管理员列表
export function getAdminList(query: { page?: number; pageSize?: number }) {
  return request({
    url: '/admin-accounts/list',
    method: 'get',
    params: query
  })
}

// 获取管理员详情
export function getAdminDetail(id: string) {
  return request({
    url: `/admin-accounts/${id}`,
    method: 'get'
  })
}

// 创建管理员账户
export function createAdmin(data: AdminForm) {
  return request({
    url: '/admin-accounts/create',
    method: 'post',
    data
  })
}

// 更新管理员信息
export function updateAdmin(id: string, data: Partial<AdminForm>) {
  return request({
    url: `/admin-accounts/${id}`,
    method: 'patch',
    data
  })
}

// 重置管理员密码
export function resetAdminPassword(id: string, password: string) {
  return request({
    url: `/admin-accounts/${id}/reset-password`,
    method: 'patch',
    data: { password }
  })
}

// 删除管理员
export function deleteAdmin(id: string) {
  return request({
    url: `/admin-accounts/${id}`,
    method: 'delete'
  })
}

// 批量创建业务员账户
export function createSalespersonAccounts(data: SalespersonForm[]) {
  return request({
    url: '/admin-accounts/batch-create-salesperson',
    method: 'post',
    data
  })
}

// ========== 系统初始化 API ==========

// 初始化系统角色和权限
export function initSystemData() {
  return request({
    url: '/role-permission/init-system',
    method: 'post'
  })
}
