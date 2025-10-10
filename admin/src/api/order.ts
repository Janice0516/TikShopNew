import request from '@/utils/request'

// 获取订单列表
export function getOrderList(params: any) {
  return request({
    url: '/orders',
    method: 'get',
    params
  })
}

// 获取订单详情
export function getOrderDetail(id: number) {
  return request({
    url: `/orders/${id}`,
    method: 'get'
  })
}

// 订单统计
export function getOrderCount() {
  return request({
    url: '/orders/count',
    method: 'get'
  })
}

