import request from '@/utils/request'

// 获取充值记录列表
export function getRechargeList(params: any) {
  return request({
    url: '/recharge/admin',
    method: 'get',
    params
  })
}

// 获取充值详情
export function getRechargeDetail(id: number) {
  return request({
    url: `/recharge/${id}`,
    method: 'get'
  })
}

// 审核充值
export function auditRecharge(data: any) {
  return request({
    url: '/recharge/audit',
    method: 'post',
    data
  })
}

// 获取充值统计
export function getRechargeStats() {
  return request({
    url: '/recharge/stats/overview',
    method: 'get'
  })
}
