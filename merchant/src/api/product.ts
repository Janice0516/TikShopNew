import request from '@/utils/request'

// 获取平台商品列表（用于选品）
export const getPlatformProducts = (params?: any) => {
  return request({
    url: '/products',
    method: 'GET',
    params
  })
}

// 获取商家商品列表
export const getMerchantProducts = (params?: any) => {
  return request({
    url: '/merchant/products',
    method: 'GET',
    params
  })
}

// 商家选品（添加商品到自己店铺）
export const selectProduct = (data: any) => {
  return request({
    url: '/merchant/products',
    method: 'POST',
    data
  })
}

// 更新商品价格
export const updateProductPrice = (id: number, data: any) => {
  return request({
    url: `/merchant/products/${id}/price`,
    method: 'PATCH',
    data
  })
}

// 上下架商品
export const toggleProductStatus = (id: number, status: number) => {
  return request({
    url: `/merchant/products/${id}/status`,
    method: 'PATCH',
    data: { status }
  })
}

// 删除商品
export const deleteProduct = (id: number) => {
  return request({
    url: `/merchant/products/${id}`,
    method: 'DELETE'
  })
}

// 获取商品详情
export const getProductDetail = (id: number) => {
  return request({
    url: `/products/${id}`,
    method: 'GET'
  })
}

// 获取商品分类
export const getCategories = () => {
  return request({
    url: '/public-categories',
    method: 'GET'
  })
}

