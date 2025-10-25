import request from './request';

// 获取商品列表
export const getProducts = (params?: any) => {
  return request({
    url: '/products',
    method: 'GET',
    params
  });
};

// 获取商品详情
export const getProductDetail = (id: string) => {
  return request({
    url: `/products/shop/${id}`,
    method: 'GET'
  });
};

// 搜索商品
export const searchProducts = (params?: any) => {
  return request({
    url: '/products/search',
    method: 'GET',
    params
  });
};

// 获取热门商品
export const getHotProducts = (params?: any) => {
  return request({
    url: '/products/hot',
    method: 'GET',
    params
  });
};

// 获取特价商品
export const getSaleProducts = (params?: any) => {
  return request({
    url: '/products/sale',
    method: 'GET',
    params
  });
};
