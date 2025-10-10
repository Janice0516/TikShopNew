import request from '@/utils/request'

export interface LoginForm {
  phone: string
  password: string
}

export interface RegisterForm {
  phone: string
  password: string
  nickname?: string
}

// 用户登录
export function login(data: LoginForm) {
  return request({
    url: '/user/login',
    method: 'post',
    data
  })
}

// 用户注册
export function register(data: RegisterForm) {
  return request({
    url: '/user/register',
    method: 'post',
    data
  })
}

// 获取用户信息
export function getUserInfo() {
  return request({
    url: '/user/profile',
    method: 'get'
  })
}

// 发送验证码
export function sendCode(phone: string) {
  return request({
    url: '/user/send-code',
    method: 'post',
    data: { phone }
  })
}
