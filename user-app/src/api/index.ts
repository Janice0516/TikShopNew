import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://tiktokbusines.store/api'

const request = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
})

// 请求拦截器
request.interceptors.request.use(
  (config) => {
    // 可以在这里添加token等认证信息
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 响应拦截器
request.interceptors.response.use(
  (response) => {
    return response.data
  },
  (error) => {
    console.error('API请求错误:', error)
    return Promise.reject(error)
  }
)

// 用户API
export const userApi = {
  // 用户登录
  login: (data: any) => {
    return request.post('/user/login', data)
  },
  
  // 用户注册
  register: (data: any) => {
    return request.post('/user/register', data)
  },
  
  // 获取用户信息
  getUserInfo: () => {
    return request.get('/user/profile')
  },
  
  // 更新用户信息
  updateUserInfo: (data: any) => {
    return request.put('/user/profile', data)
  }
}

// 商品API
export const productApi = {
  // 获取商品详情
  getProductDetail: (id: string) => {
    return request.get(`/products/shop/${id}`)
  },
  
  // 获取商品列表
  getProductList: (params: any) => {
    return request.get('/products', { params })
  },
  
  // 获取分类商品
  getCategoryProducts: (categoryId: string, params: any) => {
    return request.get(`/products/category/${categoryId}`, { params })
  }
}

// 分类API
export const categoryApi = {
  // 获取分类列表
  getCategories: () => {
    return request.get('/categories')
  },
  
  // 获取分类详情
  getCategoryDetail: (id: string) => {
    return request.get(`/categories/${id}`)
  }
}

// 购物车API
export const cartApi = {
  // 获取购物车
  getCart: () => {
    return request.get('/cart')
  },
  
  // 添加商品到购物车
  addToCart: (data: any) => {
    return request.post('/cart/add', data)
  },
  
  // 更新购物车商品数量
  updateCartItem: (id: string, data: any) => {
    return request.put(`/cart/${id}`, data)
  },
  
  // 删除购物车商品
  removeCartItem: (id: string) => {
    return request.delete(`/cart/${id}`)
  },
  
  // 清空购物车
  clearCart: () => {
    return request.delete('/cart/clear')
  }
}

// 订单API
export const orderApi = {
  // 创建订单
  createOrder: (data: any) => {
    return request.post('/orders', data)
  },
  
  // 获取订单列表
  getOrderList: (params: any) => {
    return request.get('/orders', { params })
  },
  
  // 获取订单详情
  getOrderDetail: (id: string) => {
    return request.get(`/orders/${id}`)
  },
  
  // 取消订单
  cancelOrder: (id: string) => {
    return request.put(`/orders/${id}/cancel`)
  }
}

// 地址API
export const addressApi = {
  // 获取地址列表
  getAddressList: () => {
    return request.get('/addresses')
  },
  
  // 创建地址
  createAddress: (data: any) => {
    return request.post('/addresses', data)
  },
  
  // 更新地址
  updateAddress: (id: string, data: any) => {
    return request.put(`/addresses/${id}`, data)
  },
  
  // 删除地址
  deleteAddress: (id: string) => {
    return request.delete(`/addresses/${id}`)
  },
  
  // 设置默认地址
  setDefaultAddress: (id: string) => {
    return request.put(`/addresses/${id}/default`)
  }
}

// 商家API
export const shopApi = {
  // 获取商家店铺信息
  getMerchantShop: (id: string) => {
    return request.get(`/merchant/shop/${id}`)
  }
}

export default request

// 评价API
export const reviewApi = {
  // 获取商品评价
  getProductReviews: (productId: string, params: any) => {
    return request.get(`/reviews/product/${productId}`, { params })
  },
  
  // 创建评价
  createReview: (data: any) => {
    return request.post('/reviews', data)
  },
  
  // 更新评价
  updateReview: (id: string, data: any) => {
    return request.put(`/reviews/${id}`, data)
  },
  
  // 删除评价
  deleteReview: (id: string) => {
    return request.delete(`/reviews/${id}`)
  }
}

// 收藏API
export const favoritesApi = {
  // 获取收藏列表
  getFavorites: () => {
    return request.get('/favorites')
  },
  
  // 添加收藏
  addToFavorites: (productId: string) => {
    return request.post('/favorites', { productId })
  },
  
  // 移除收藏
  removeFromFavorites: (productId: string) => {
    return request.delete(`/favorites/${productId}`)
  },
  
  // 检查是否已收藏
  checkFavorite: (productId: string) => {
    return request.get(`/favorites/check/${productId}`)
  }
}
