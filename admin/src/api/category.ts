import request from '@/utils/request'

// 获取分类列表（对齐后端 /products/categories）
export function getCategoryList(params?: any) {
  return request({
    url: '/products/categories',
    method: 'get',
    params
  })
}

// 获取分类树形结构（同上返回树形）
export function getCategoryTree() {
  return request({
    url: '/products/categories',
    method: 'get'
  })
}

// 获取分类详情（后端未提供对应详情端点，暂保留为分类ID查询商品分类详情不可用）
export function getCategoryDetail(id: number) {
  return request({
    url: `/category/${id}`,
    method: 'get'
  })
}

// 创建分类（后端未开放 /products/categories 的写操作，保留原管理端分类写操作路径）
export function createCategory(data: any) {
  return request({
    url: '/category',
    method: 'post',
    data
  })
}

// 更新分类
export function updateCategory(id: number, data: any) {
  return request({
    url: `/category/${id}`,
    method: 'put',
    data
  })
}

// 删除分类
export function deleteCategory(id: number) {
  return request({
    url: `/category/${id}`,
    method: 'delete'
  })
}

// 更新分类状态
export function updateCategoryStatus(id: number, status: number) {
  return request({
    url: `/category/${id}/status`,
    method: 'put',
    data: { status }
  })
}

// 更新分类排序
export function updateCategorySort(id: number, sort: number) {
  return request({
    url: `/category/${id}/sort`,
    method: 'put',
    data: { sort }
  })
}
