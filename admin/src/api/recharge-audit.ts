import request from '@/utils/request'

// 获取充值审核列表
export function getRechargeAuditList(params: any) {
  return request({
    url: '/admin/recharge-audit',
    method: 'get',
    params
  })
}

// 获取充值审核详情
export function getRechargeAuditDetail(id: number) {
  return request({
    url: `/admin/recharge-audit/${id}`,
    method: 'get'
  })
}

// 充值审核统计
export function getRechargeAuditCount() {
  return request({
    url: '/admin/recharge-audit/count',
    method: 'get'
  })
}

// 审核通过
export function approveRecharge(id: number, adminRemark: string) {
  return request({
    url: `/admin/recharge-audit/${id}/approve`,
    method: 'patch',
    data: { adminRemark }
  })
}

// 审核拒绝
export function rejectRecharge(id: number, adminRemark: string) {
  return request({
    url: `/admin/recharge-audit/${id}/reject`,
    method: 'patch',
    data: { adminRemark }
  })
}
