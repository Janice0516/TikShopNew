import request from '@/utils/request'

// 获取商品列表
export function getProductList(params: any) {
  return request({
    url: '/products',
    method: 'get',
    params
  })
}

// 获取商品详情
export function getProductDetail(id: number) {
  return request({
    url: `/products/${id}`,
    method: 'get'
  })
}

// 创建商品
export function createProduct(data: any) {
  return request({
    url: '/products',
    method: 'post',
    data
  })
}

// 更新商品
export function updateProduct(id: number, data: any) {
  return request({
    url: `/products/${id}`,
    method: 'put',
    data
  })
}

// 删除商品
export function deleteProduct(id: number) {
  return request({
    url: `/products/${id}`,
    method: 'delete'
  })
}

// 更新商品状态
export function updateProductStatus(id: number, status: number) {
  return request({
    url: `/products/${id}/status`,
    method: 'patch',
    data: { status }
  })
}

// 获取分类列表
export function getCategoryList() {
  return request({
    url: '/products/categories',
    method: 'get'
  })
}

