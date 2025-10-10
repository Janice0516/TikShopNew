import request from '@/utils/request'

// 获取商家订单列表
export const getMerchantOrders = (params?: any) => {
  return request({
    url: '/merchant/orders',
    method: 'GET',
    params
  })
}

// 获取订单详情
export const getOrderDetail = (id: number) => {
  return request({
    url: `/merchant/orders/${id}`,
    method: 'GET'
  })
}

// 发货
export const shipOrder = (id: number, data: any) => {
  return request({
    url: `/merchant/orders/${id}/ship`,
    method: 'PATCH',
    data
  })
}

// 获取订单统计
export const getOrderStats = () => {
  return request({
    url: '/merchant/orders/stats',
    method: 'GET'
  })
}

