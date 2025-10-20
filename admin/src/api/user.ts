import request from '@/utils/request'

// 管理员登录
export function login(data: any) {
  return request({
    url: '/admin/login',
    method: 'post',
    data
  })
}

// 管理员登录（别名）
export function loginApi(data: any) {
  return request({
    url: '/admin/login',
    method: 'post',
    data
  })
}

// 获取管理员信息
export function getUserInfo() {
  return request({
    url: '/admin/profile',
    method: 'get'
  })
}

// 测试连接
export function testConnection() {
  return request({
    url: '/health',
    method: 'get'
  })
}

// 获取用户列表
export function getUserList(params: any) {
  return request({
    url: '/admin/users',
    method: 'get',
    params
  })
}

// 获取用户详情
export function getUserDetail(id: number) {
  return request({
    url: `/admin/users/${id}`,
    method: 'get'
  })
}

// 用户统计
export function getUserCount() {
  return request({
    url: '/admin/users/count',
    method: 'get'
  })
}

// 更新用户状态
export function updateUserStatus(id: number, status: number) {
  return request({
    url: `/admin/users/${id}/status`,
    method: 'patch',
    data: { status }
  })
}

// 重置用户密码
export function resetUserPassword(id: number, newPassword: string) {
  return request({
    url: `/admin/users/${id}/reset-password`,
    method: 'patch',
    data: { newPassword }
  })
}