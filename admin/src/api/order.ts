import request from '@/utils/request'

// 获取订单列表
export function getOrderList(params: any) {
  return request({
    url: '/admin/orders',
    method: 'get',
    params
  })
}

// 获取订单详情
export function getOrderDetail(id: number) {
  return request({
    url: `/admin/orders/${id}`,
    method: 'get'
  })
}

// 订单统计
export function getOrderCount() {
  return request({
    url: '/admin/orders/count',
    method: 'get'
  })
}

// 取消订单
export function cancelOrder(id: number, reason: string) {
  return request({
    url: `/admin/orders/${id}/cancel`,
    method: 'patch',
    data: { reason }
  })
}

// 发货
export function shipOrder(id: number, logisticsCompany: string, trackingNumber: string) {
  return request({
    url: `/admin/orders/${id}/ship`,
    method: 'patch',
    data: { logisticsCompany, trackingNumber }
  })
}

