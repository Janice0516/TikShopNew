import { mockData } from '@/utils/mockData'

// 模拟API延迟
const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms))

// 模拟API响应
const mockResponse = (data: any, success: boolean = true) => ({
  code: success ? 200 : 400,
  message: success ? 'success' : 'error',
  data: data,
  timestamp: Date.now()
})

// 模拟API服务
export const mockApi = {
  // 用户相关
  async login(data: { phone: string; password: string }) {
    await delay(500)
    if (data.phone === '13888888888' && data.password === '123456') {
      return mockResponse({
        token: 'mock-jwt-token-' + Date.now(),
        user: mockData.generateUser()
      })
    }
    return mockResponse(null, false)
  },

  async register(data: { phone: string; password: string; nickname?: string }) {
    await delay(500)
    return mockResponse({
      token: 'mock-jwt-token-' + Date.now(),
      user: mockData.generateUser()
    })
  },

  async getUserInfo() {
    await delay(300)
    return mockResponse(mockData.generateUser())
  },

  async sendCode(phone: string) {
    await delay(200)
    return mockResponse({ message: '验证码已发送' })
  },

  // 商品相关
  async getProducts(params?: {
    page?: number
    pageSize?: number
    category?: number
    keyword?: string
    sort?: string
  }) {
    await delay(400)
    const products = mockData.generateProducts(20)
    const page = params?.page || 1
    const pageSize = params?.pageSize || 10
    const start = (page - 1) * pageSize
    const end = start + pageSize
    
    return mockResponse({
      list: products.slice(start, end),
      total: products.length,
      page: page,
      pageSize: pageSize
    })
  },

  async getProductDetail(id: number) {
    await delay(300)
    const products = mockData.generateProducts(1)
    const product = products[0]
    product.id = id
    return mockResponse(product)
  },

  async getCategories() {
    await delay(200)
    return mockResponse(mockData.generateCategories())
  },

  async getHotProducts() {
    await delay(300)
    const products = mockData.generateProducts(8)
    return mockResponse(products)
  },

  async getRecommendProducts() {
    await delay(300)
    const products = mockData.generateProducts(6)
    return mockResponse(products)
  },

  // 购物车相关
  async getCart() {
    await delay(300)
    const products = mockData.generateProducts(20)
    const cartItems = mockData.generateCartItems(products)
    return mockResponse(cartItems)
  },

  async addToCart(data: { productId: number; quantity: number }) {
    await delay(200)
    return mockResponse({ message: '已添加到购物车' })
  },

  async updateCartItem(id: number, data: { quantity?: number; selected?: boolean }) {
    await delay(200)
    return mockResponse({ message: '购物车已更新' })
  },

  async removeCartItem(id: number) {
    await delay(200)
    return mockResponse({ message: '商品已移除' })
  },

  async clearCart() {
    await delay(200)
    return mockResponse({ message: '购物车已清空' })
  },

  // 订单相关
  async createOrder(data: {
    items: Array<{ productId: number; quantity: number }>
    addressId: number
    remark?: string
  }) {
    await delay(500)
    return mockResponse({
      orderId: Math.floor(Math.random() * 10000) + 1000,
      orderNo: 'ORD' + Date.now(),
      message: '订单创建成功'
    })
  },

  async getOrders(params?: {
    page?: number
    pageSize?: number
    status?: string
  }) {
    await delay(400)
    const orders = mockData.generateOrders(15)
    const page = params?.page || 1
    const pageSize = params?.pageSize || 10
    const start = (page - 1) * pageSize
    const end = start + pageSize
    
    return mockResponse({
      list: orders.slice(start, end),
      total: orders.length,
      page: page,
      pageSize: pageSize
    })
  },

  async getOrderDetail(id: number) {
    await delay(300)
    const orders = mockData.generateOrders(1)
    const order = orders[0]
    order.id = id
    return mockResponse(order)
  },

  async cancelOrder(id: number) {
    await delay(300)
    return mockResponse({ message: '订单已取消' })
  },

  async payOrder(id: number, data: { paymentMethod: string }) {
    await delay(500)
    return mockResponse({ message: '支付成功' })
  },

  // 地址相关
  async getAddresses() {
    await delay(300)
    return mockResponse(mockData.generateAddresses())
  },

  async createAddress(data: any) {
    await delay(300)
    return mockResponse({ message: '地址添加成功' })
  },

  async updateAddress(id: number, data: any) {
    await delay(300)
    return mockResponse({ message: '地址更新成功' })
  },

  async deleteAddress(id: number) {
    await delay(300)
    return mockResponse({ message: '地址删除成功' })
  },

  async setDefaultAddress(id: number) {
    await delay(300)
    return mockResponse({ message: '默认地址设置成功' })
  },

  // 商店相关API
  async getStores(params?: {
    page?: number
    pageSize?: number
    type?: string
    keyword?: string
  }) {
    await delay(400)
    const stores = mockData.generateStores(20)
    const page = params?.page || 1
    const pageSize = params?.pageSize || 10
    const start = (page - 1) * pageSize
    const end = start + pageSize
    
    return mockResponse({
      list: stores.slice(start, end),
      total: stores.length,
      page: page,
      pageSize: pageSize
    })
  },

  async getStoreDetail(id: number) {
    await delay(300)
    const stores = mockData.generateStores(1)
    const store = stores[0]
    store.id = id
    return mockResponse(store)
  },

  async getStoreProducts(storeId: number, params?: {
    page?: number
    pageSize?: number
  }) {
    await delay(400)
    const products = mockData.generateProducts(20).filter(p => p.storeId === storeId)
    const page = params?.page || 1
    const pageSize = params?.pageSize || 10
    const start = (page - 1) * pageSize
    const end = start + pageSize
    
    return mockResponse({
      list: products.slice(start, end),
      total: products.length,
      page: page,
      pageSize: pageSize
    })
  },

  async getTrendingStores() {
    await delay(300)
    const stores = mockData.generateStores(8)
    return mockResponse(stores)
  },

  async getLiveStores() {
    await delay(300)
    const stores = mockData.generateStores(20).filter(s => s.isLive)
    return mockResponse(stores.slice(0, 6))
  },

  // 其他
  async getBanners() {
    await delay(200)
    return mockResponse(mockData.generateBanners())
  }
}
