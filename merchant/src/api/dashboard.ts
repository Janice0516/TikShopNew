import request from '@/utils/request'

// 获取仪表板统计
export const getDashboardStats = () => {
  return request({
    url: '/merchant/dashboard/stats',
    method: 'GET'
  })
}
