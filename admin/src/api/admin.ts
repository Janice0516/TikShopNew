import request from '@/utils/request'

// 获取仪表盘统计数据
export const getDashboardStats = () => {
  return request({
    url: '/admin/dashboard/stats',
    method: 'GET'
  })
}

// 管理员登录
export const adminLogin = (data: any) => {
  return request({
    url: '/admin/login',
    method: 'POST',
    data
  })
}

// 获取管理员信息
export const getAdminProfile = () => {
  return request({
    url: '/admin/profile',
    method: 'GET'
  })
}
