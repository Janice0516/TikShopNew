import request from '@/utils/request';

// 增加商户资金
export function increaseFund(data: any) {
  return request({
    url: '/admin/fund-management/increase',
    method: 'post',
    data,
  });
}

// 冻结商户资金
export function freezeFund(data: any) {
  return request({
    url: '/admin/fund-management/freeze',
    method: 'post',
    data,
  });
}

// 解冻商户资金
export function unfreezeFund(data: any) {
  return request({
    url: '/admin/fund-management/unfreeze',
    method: 'post',
    data,
  });
}

// 扣除商户资金
export function deductFund(data: any) {
  return request({
    url: '/admin/fund-management/deduct',
    method: 'post',
    data,
  });
}

// 退还商户资金
export function refundFund(data: any) {
  return request({
    url: '/admin/fund-management/refund',
    method: 'post',
    data,
  });
}

// 获取商户资金信息
export function getMerchantFundInfo(merchantId: number) {
  return request({
    url: `/admin/fund-management/merchant/${merchantId}/overview`,
    method: 'get',
  });
}

// 获取资金操作记录
export function getFundOperationList(params: any) {
  return request({
    url: '/admin/fund-management/operations',
    method: 'get',
    params,
  });
}

// 获取操作类型列表
export function getOperationTypes() {
  return request({
    url: '/admin/fund-management/operation-types',
    method: 'get',
  });
}
