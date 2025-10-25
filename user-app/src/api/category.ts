import request from './request';

// 获取分类列表
export const getCategories = () => {
  return request({
    url: '/public-categories',
    method: 'GET'
  });
};

// 获取分类详情
export const getCategoryDetail = (id: string) => {
  return request({
    url: `/public-categories/${id}`,
    method: 'GET'
  });
};

// 获取分类下的商品
export const getCategoryProducts = (id: string, params?: any) => {
  return request({
    url: `/public-categories/${id}/products`,
    method: 'GET',
    params
  });
};
