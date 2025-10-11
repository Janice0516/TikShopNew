import request from '@/utils/request'
import { mockApi } from '@/utils/mockApi'

// 是否使用虚拟数据
const USE_MOCK_DATA = false

export interface LoginForm {
  phone: string
  password: string
}

export interface RegisterForm {
  phone: string
  password: string
  nickname?: string
}

export interface Product {
  id: number
  name: string
  price: number
  originalPrice?: number
  image: string
  description?: string
  category: string
  brand?: string
  stock: number
  sales: number
  rating: number
  reviews: number
  tags?: string[]
  images?: string[]
  specifications?: Record<string, string>
}

export interface Category {
  id: number
  name: string
  icon?: string
  children?: Category[]
}

export interface CartItem {
  id: number
  product: Product
  quantity: number
  selected: boolean
}

export interface Order {
  id: number
  orderNo: string
  status: 'pending' | 'paid' | 'shipped' | 'delivered' | 'cancelled'
  totalAmount: number
  items: OrderItem[]
  address: Address
  createTime: string
  payTime?: string
  shipTime?: string
  deliverTime?: string
}

export interface OrderItem {
  id: number
  product: Product
  quantity: number
  price: number
}

export interface Address {
  id: number
  name: string
  phone: string
  province: string
  city: string
  district: string
  detail: string
  isDefault: boolean
}

// 用户相关API
export function login(data: LoginForm) {
  if (USE_MOCK_DATA) {
    return mockApi.login(data)
  }
  return request({
    url: '/user/login',
    method: 'post',
    data
  })
}

export function register(data: RegisterForm) {
  if (USE_MOCK_DATA) {
    return mockApi.register(data)
  }
  return request({
    url: '/user/register',
    method: 'post',
    data
  })
}

export function getUserInfo() {
  if (USE_MOCK_DATA) {
    return mockApi.getUserInfo()
  }
  return request({
    url: '/user/profile',
    method: 'get'
  })
}

export function sendCode(phone: string) {
  if (USE_MOCK_DATA) {
    return mockApi.sendCode(phone)
  }
  return request({
    url: '/user/send-code',
    method: 'post',
    data: { phone }
  })
}

// 商品相关API
export function getProducts(params?: {
  page?: number
  pageSize?: number
  category?: number
  keyword?: string
  sort?: string
}) {
  if (USE_MOCK_DATA) {
    return mockApi.getProducts(params)
  }
  return request({
    url: '/products',
    method: 'get',
    params
  })
}

export function getProductDetail(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.getProductDetail(id)
  }
  return request({
    url: `/products/${id}`,
    method: 'get'
  })
}

export function getCategories() {
  if (USE_MOCK_DATA) {
    return mockApi.getCategories()
  }
  return request({
    url: '/categories',
    method: 'get'
  })
}

export function getHotProducts() {
  if (USE_MOCK_DATA) {
    return mockApi.getHotProducts()
  }
  return request({
    url: '/products/hot',
    method: 'get'
  })
}

export function getRecommendProducts() {
  if (USE_MOCK_DATA) {
    return mockApi.getRecommendProducts()
  }
  return request({
    url: '/products/recommend',
    method: 'get'
  })
}

// 购物车相关API
export function getCart() {
  if (USE_MOCK_DATA) {
    return mockApi.getCart()
  }
  return request({
    url: '/cart',
    method: 'get'
  })
}

export function addToCart(data: { productId: number; quantity: number }) {
  if (USE_MOCK_DATA) {
    return mockApi.addToCart(data)
  }
  return request({
    url: '/cart',
    method: 'post',
    data
  })
}

export function updateCartItem(id: number, data: { quantity?: number; selected?: boolean }) {
  if (USE_MOCK_DATA) {
    return mockApi.updateCartItem(id, data)
  }
  return request({
    url: `/cart/${id}`,
    method: 'patch',
    data
  })
}

export function removeCartItem(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.removeCartItem(id)
  }
  return request({
    url: `/cart/${id}`,
    method: 'delete'
  })
}

export function clearCart() {
  if (USE_MOCK_DATA) {
    return mockApi.clearCart()
  }
  return request({
    url: '/cart',
    method: 'delete'
  })
}

// 订单相关API
export function createOrder(data: {
  items: Array<{ productId: number; quantity: number }>
  addressId: number
  remark?: string
}) {
  if (USE_MOCK_DATA) {
    return mockApi.createOrder(data)
  }
  return request({
    url: '/orders',
    method: 'post',
    data
  })
}

export function getOrders(params?: {
  page?: number
  pageSize?: number
  status?: string
}) {
  if (USE_MOCK_DATA) {
    return mockApi.getOrders(params)
  }
  return request({
    url: '/orders',
    method: 'get',
    params
  })
}

export function getOrderDetail(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.getOrderDetail(id)
  }
  return request({
    url: `/orders/${id}`,
    method: 'get'
  })
}

export function cancelOrder(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.cancelOrder(id)
  }
  return request({
    url: `/orders/${id}/cancel`,
    method: 'patch'
  })
}

export function payOrder(id: number, data: { paymentMethod: string }) {
  if (USE_MOCK_DATA) {
    return mockApi.payOrder(id, data)
  }
  return request({
    url: `/orders/${id}/pay`,
    method: 'post',
    data
  })
}

// 地址相关API
export function getAddresses() {
  if (USE_MOCK_DATA) {
    return mockApi.getAddresses()
  }
  return request({
    url: '/addresses',
    method: 'get'
  })
}

export function createAddress(data: Omit<Address, 'id'>) {
  if (USE_MOCK_DATA) {
    return mockApi.createAddress(data)
  }
  return request({
    url: '/addresses',
    method: 'post',
    data
  })
}

export function updateAddress(id: number, data: Partial<Omit<Address, 'id'>>) {
  if (USE_MOCK_DATA) {
    return mockApi.updateAddress(id, data)
  }
  return request({
    url: `/addresses/${id}`,
    method: 'put',
    data
  })
}

export function deleteAddress(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.deleteAddress(id)
  }
  return request({
    url: `/addresses/${id}`,
    method: 'delete'
  })
}

export function setDefaultAddress(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.setDefaultAddress(id)
  }
  return request({
    url: `/addresses/${id}/default`,
    method: 'patch'
  })
}

// 商店相关API
export function getStores(params?: {
  page?: number
  pageSize?: number
  type?: string
  keyword?: string
}) {
  if (USE_MOCK_DATA) {
    return mockApi.getStores(params)
  }
  return request({
    url: '/stores',
    method: 'get',
    params
  })
}

export function getStoreDetail(id: number) {
  if (USE_MOCK_DATA) {
    return mockApi.getStoreDetail(id)
  }
  return request({
    url: `/stores/${id}`,
    method: 'get'
  })
}

export function getStoreProducts(storeId: number, params?: {
  page?: number
  pageSize?: number
}) {
  if (USE_MOCK_DATA) {
    return mockApi.getStoreProducts(storeId, params)
  }
  return request({
    url: `/stores/${storeId}/products`,
    method: 'get',
    params
  })
}

export function getTrendingStores() {
  if (USE_MOCK_DATA) {
    return mockApi.getTrendingStores()
  }
  return request({
    url: '/stores/trending',
    method: 'get'
  })
}

export function getLiveStores() {
  if (USE_MOCK_DATA) {
    return mockApi.getLiveStores()
  }
  return request({
    url: '/stores/live',
    method: 'get'
  })
}

// 其他API
export function getBanners() {
  if (USE_MOCK_DATA) {
    return mockApi.getBanners()
  }
  return request({
    url: '/banners',
    method: 'get'
  })
}
