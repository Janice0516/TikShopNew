import request from '@/utils/request'

// 获取商家列表
export function getMerchantList(params: any) {
  return request({
    url: '/merchant/list',
    method: 'get',
    params
  })
}

// 审核商家
export function auditMerchant(id: number, status: number, rejectReason?: string) {
  return request({
    url: `/merchant/${id}/audit`,
    method: 'patch',
    data: { status, rejectReason }
  })
}

// 更新商家信息
export function updateMerchant(id: number, data: any) {
  return request({
    url: `/merchant/${id}`,
    method: 'put',
    data
  })
}

// 重置商家密码
export function resetMerchantPassword(id: number, data: { newPassword: string }) {
  return request({
    url: `/merchant/${id}/reset-password`,
    method: 'patch',
    data
  })
}

