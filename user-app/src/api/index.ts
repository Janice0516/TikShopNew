import api from './request'

// 用户相关API
export const userApi = {
  // 用户登录
  login: (data: { phone: string; password: string }) => {
    return api.post('/auth/login', data)
  },
  
  // 用户注册
  register: (data: { phone: string; password: string; verifyCode: string }) => {
    return api.post('/auth/register', data)
  },
  
  // 获取用户信息
  getUserInfo: () => {
    return api.get('/user/profile')
  },
  
  // 更新用户信息
  updateUserInfo: (data: any) => {
    return api.put('/user/profile', data)
  }
}

// 商品相关API
export const productApi = {
  // 获取商品列表
  getProducts: (params?: any) => {
    return api.get('/products', { params })
  },
  
  // 获取商品详情
  getProductDetail: (id: string) => {
    return api.get(`/products/${id}`)
  },
  
  // 搜索商品
  searchProducts: (keyword: string, params?: any) => {
    return api.get('/products/search', { 
      params: { keyword, ...params } 
    })
  }
}

// 分类相关API
export const categoryApi = {
  // 获取分类列表
  getCategories: () => {
    return api.get('/shop/categories')
  },
  
  // 获取分类商品
  getCategoryProducts: (categoryId: string, params?: any) => {
    return api.get(`/shop/categories/${categoryId}/products`, { params })
  }
}

// 购物车相关API
export const cartApi = {
  // 获取购物车
  getCart: () => {
    return api.get('/cart')
  },
  
  // 添加商品到购物车
  addToCart: (data: { productId: string; quantity: number }) => {
    return api.post('/cart/add', data)
  },
  
  // 更新购物车商品数量
  updateCartItem: (itemId: string, quantity: number) => {
    return api.put(`/cart/items/${itemId}`, { quantity })
  },
  
  // 删除购物车商品
  removeCartItem: (itemId: string) => {
    return api.delete(`/cart/items/${itemId}`)
  },
  
  // 清空购物车
  clearCart: () => {
    return api.delete('/cart/clear')
  }
}

// 订单相关API
export const orderApi = {
  // 创建订单
  createOrder: (data: any) => {
    return api.post('/orders', data)
  },
  
  // 获取订单列表
  getOrders: (params?: any) => {
    return api.get('/orders', { params })
  },
  
  // 获取订单详情
  getOrderDetail: (orderId: string) => {
    return api.get(`/orders/${orderId}`)
  },
  
  // 取消订单
  cancelOrder: (orderId: string) => {
    return api.put(`/orders/${orderId}/cancel`)
  }
}

// 轮播图相关API
export const bannerApi = {
  // 获取轮播图
  getBanners: () => {
    return api.get('/banners')
  }
}
