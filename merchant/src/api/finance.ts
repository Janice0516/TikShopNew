import request from '@/utils/request'

// 获取商家财务统计
export const getFinanceStats = () => {
  return request({
    url: '/merchant/finance/stats',
    method: 'GET'
  })
}

// 获取资金流水
export const getFundFlow = (params?: any) => {
  return request({
    url: '/merchant/finance/fund-flow',
    method: 'GET',
    params
  })
}

// 申请提现
export const applyWithdraw = (data: any) => {
  return request({
    url: '/merchant/finance/withdraw',
    method: 'POST',
    data
  })
}

// 获取提现记录
export const getWithdrawHistory = (params?: any) => {
  return request({
    url: '/merchant/finance/withdraw-history',
    method: 'GET',
    params
  })
}

// 获取收益统计（按时间）
export const getEarningsStats = (params?: any) => {
  return request({
    url: '/merchant/finance/earnings-stats',
    method: 'GET',
    params
  })
}

// 账户充值
export const rechargeAccount = (data: any) => {
  return request({
    url: '/recharge/merchant',
    method: 'POST',
    data
  })
}

// 获取充值记录
export const getRechargeHistory = (params?: any) => {
  return request({
    url: '/recharge/merchant',
    method: 'GET',
    params
  })
}

