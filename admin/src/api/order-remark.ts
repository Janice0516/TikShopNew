import request from '@/utils/request'

export interface OrderRemarkForm {
  adminRemark?: string
  logisticsStatus?: string
  trackingNumber?: string
  logisticsCompany?: string
}

export interface OrderLogisticsForm {
  orderId: string
  logisticsStatus: string
  trackingNumber?: string
  logisticsCompany?: string
  adminRemark?: string
}

export interface BatchLogisticsForm {
  orderId: string
  logisticsStatus: string
  trackingNumber?: string
  logisticsCompany?: string
  adminRemark?: string
}

// 获取订单详情（包含备注和物流信息）
export function getOrderDetail(id: string) {
  return request({
    url: `/admin/order-remark/${id}`,
    method: 'get'
  })
}

// 更新订单备注
export function updateOrderRemark(id: string, data: OrderRemarkForm) {
  return request({
    url: `/admin/order-remark/${id}/remark`,
    method: 'patch',
    data
  })
}

// 更新订单物流状态
export function updateOrderLogistics(data: OrderLogisticsForm) {
  return request({
    url: '/admin/order-remark/logistics',
    method: 'patch',
    data
  })
}

// 批量更新订单物流状态
export function batchUpdateLogistics(orders: BatchLogisticsForm[]) {
  return request({
    url: '/admin/order-remark/batch-logistics',
    method: 'post',
    data: orders
  })
}

// 获取订单物流历史记录
export function getLogisticsHistory(id: string) {
  return request({
    url: `/admin/order-remark/${id}/logistics-history`,
    method: 'get'
  })
}
