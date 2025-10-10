import request from '@/utils/request'

// 管理员登录
export const login = (data: any) => {
  return request({
    url: '/admin/login',
    method: 'POST',
    data
  })
}

// 获取管理员信息
export const getUserInfo = () => {
  return request({
    url: '/admin/profile',
    method: 'GET'
  })
}

// 获取用户列表
export const getUserList = (params: any) => {
  return request({
    url: '/admin/users',
    method: 'GET',
    params
  })
}

// 获取用户详情
export const getUserDetail = (id: number) => {
  return request({
    url: `/admin/users/${id}`,
    method: 'GET'
  })
}

// 更新用户状态
export const updateUserStatus = (id: number, data: any) => {
  return request({
    url: `/admin/users/${id}/status`,
    method: 'PATCH',
    data
  })
}

// 批量更新用户状态
export const batchUpdateUserStatus = (data: any) => {
  return request({
    url: '/admin/users/batch-status',
    method: 'PATCH',
    data
  })
}

// 登录表单类型
export interface LoginForm {
  username: string
  password: string
}