import request from '@/utils/request'

// 获取商家列表（修复：使用管理员端接口）
export function getMerchantList(params: any) {
  return request({
    url: '/admin/merchants',
    method: 'get',
    params
  })
}

// 审核商家（保留原路径，若后端提供则可用）
export function auditMerchant(id: number, status: number, rejectReason?: string) {
  return request({
    url: `/merchant/${id}/audit`,
    method: 'patch',
    data: { status, rejectReason }
  })
}

// 更新商家信息（保留原路径，若后端提供则可用）
export function updateMerchant(id: number, data: any) {
  return request({
    url: `/merchant/${id}`,
    method: 'put',
    data
  })
}

// 重置商家密码（保留原路径，若后端提供则可用）
export function resetMerchantPassword(id: number, data: { newPassword: string }) {
  return request({
    url: `/merchant/${id}/reset-password`,
    method: 'patch',
    data
  })
}

