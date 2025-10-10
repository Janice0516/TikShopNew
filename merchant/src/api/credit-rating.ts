import request from '@/utils/request'

// 获取商户当前信用评级
export function getMerchantCurrentRating() {
  return request({
    url: '/credit-rating/merchant/current',
    method: 'get'
  })
}

// 获取商户信用评级历史
export function getMerchantRatingHistory(params: any) {
  return request({
    url: '/credit-rating/merchant/history',
    method: 'get',
    params
  })
}

// 获取信用评级说明
export function getCreditRatingGuide() {
  return request({
    url: '/credit-rating/guide',
    method: 'get'
  })
}
