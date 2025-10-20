import request from '@/utils/request'

export interface LoginForm {
  username: string
  password: string
}

export interface RegisterForm {
  username: string
  password: string
  merchantName: string
  contactName: string
  contactPhone: string
  shopName?: string
  inviteCode?: string
}

// 商家登录
export function login(data: LoginForm) {
  return request({
    url: '/merchant/login',
    method: 'post',
    data
  })
}

// 商家注册
export function register(data: RegisterForm) {
  return request({
    url: '/merchant/register',
    method: 'post',
    data
  })
}

// 获取商家信息
export function getMerchantInfo() {
  return request({
    url: '/merchant/profile',
    method: 'get'
  })
}

// 更新商家信息
export function updateMerchantInfo(data: any) {
  return request({
    url: '/merchant/shop',
    method: 'patch',
    data
  })
}
