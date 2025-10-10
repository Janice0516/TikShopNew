import request from '@/utils/request'

// 获取提现列表
export function getWithdrawalList(params: any) {
  return request({
    url: '/withdrawal/list',
    method: 'get',
    params
  })
}

// 获取提现详情
export function getWithdrawalDetail(id: number) {
  return request({
    url: `/withdrawal/${id}`,
    method: 'get'
  })
}

// 更新提现状态
export function updateWithdrawalStatus(id: number, data: any) {
  return request({
    url: `/withdrawal/${id}/status`,
    method: 'put',
    data
  })
}

// 创建提现申请
export function createWithdrawal(data: any) {
  return request({
    url: '/withdrawal',
    method: 'post',
    data
  })
}

// 获取商户提现记录
export function getMerchantWithdrawals(params: any) {
  return request({
    url: '/withdrawal/merchant/list',
    method: 'get',
    params
  })
}
