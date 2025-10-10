import request from '@/utils/request'

// 获取商户余额信息
export function getMerchantBalance() {
  return request({
    url: '/withdrawal/balance',
    method: 'get'
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

// 获取提现详情
export function getWithdrawalDetail(id: number) {
  return request({
    url: `/withdrawal/${id}`,
    method: 'get'
  })
}
