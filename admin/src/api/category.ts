import request from '@/utils/request'

// 获取分类列表
export function getCategoryList(params?: any) {
  return request({
    url: '/categories',
    method: 'get',
    params
  })
}

// 获取分类树形结构
export function getCategoryTree() {
  return request({
    url: '/categories/tree',
    method: 'get'
  })
}

// 获取分类详情
export function getCategoryDetail(id: number) {
  return request({
    url: `/categories/${id}`,
    method: 'get'
  })
}

// 创建分类
export function createCategory(data: any) {
  return request({
    url: '/categories',
    method: 'post',
    data
  })
}

// 更新分类
export function updateCategory(id: number, data: any) {
  return request({
    url: `/categories/${id}`,
    method: 'put',
    data
  })
}

// 删除分类
export function deleteCategory(id: number) {
  return request({
    url: `/categories/${id}`,
    method: 'delete'
  })
}

// 更新分类状态
export function updateCategoryStatus(id: number, status: number) {
  return request({
    url: `/categories/${id}/status`,
    method: 'put',
    data: { status }
  })
}

// 更新分类排序
export function updateCategorySort(id: number, sort: number) {
  return request({
    url: `/categories/${id}/sort`,
    method: 'put',
    data: { sort }
  })
}
