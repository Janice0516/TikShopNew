import request from '@/utils/request'

// 创建信用评级
export function createCreditRating(data: any) {
  return request({
    url: '/credit-rating',
    method: 'post',
    data
  })
}

// 获取信用评级列表
export function getCreditRatingList(params: any) {
  return request({
    url: '/credit-rating',
    method: 'get',
    params
  })
}

// 获取信用评级详情
export function getCreditRatingDetail(id: number) {
  return request({
    url: `/credit-rating/${id}`,
    method: 'get'
  })
}

// 更新信用评级
export function updateCreditRating(id: number, data: any) {
  return request({
    url: `/credit-rating/${id}`,
    method: 'put',
    data
  })
}

// 删除信用评级
export function deleteCreditRating(id: number) {
  return request({
    url: `/credit-rating/${id}`,
    method: 'delete'
  })
}

// 获取商户当前信用评级
export function getMerchantCurrentRating(merchantId: number) {
  return request({
    url: `/credit-rating/merchant/${merchantId}/current`,
    method: 'get'
  })
}

// 获取商户信用评级历史
export function getMerchantRatingHistory(merchantId: number, params: any) {
  return request({
    url: `/credit-rating/merchant/${merchantId}/history`,
    method: 'get',
    params
  })
}

// 根据等级获取分数范围
export function getScoreRangeByLevel(level: string) {
  return request({
    url: `/credit-rating/utils/level/${level}`,
    method: 'get'
  })
}

// 根据分数获取等级
export function getLevelByScore(score: number) {
  return request({
    url: `/credit-rating/utils/score/${score}`,
    method: 'get'
  })
}

// 自动计算商户信用评级
export function calculateMerchantRating(merchantId: number) {
  return request({
    url: `/credit-rating/calculate/${merchantId}`,
    method: 'post'
  })
}

// 获取信用评级统计信息
export function getCreditRatingStats() {
  return request({
    url: '/credit-rating/dashboard-stats',
    method: 'get'
  })
}

// 批量重新计算所有商户信用评级
export function recalculateAllMerchantRatings() {
  return request({
    url: '/credit-rating/recalculate-all',
    method: 'post'
  })
}
