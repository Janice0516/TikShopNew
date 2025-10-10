import { ref } from 'vue'
import { login as loginApi, getUserInfo, register as registerApi } from '@/api/user'
import type { LoginForm, RegisterForm } from '@/api/user'

export const useUserStore = () => {
  const token = ref<string>(uni.getStorageSync('token') || '')
  const userInfo = ref<any>(null)

  // 登录
  const login = async (loginForm: LoginForm) => {
    try {
      const res: any = await loginApi(loginForm)
      token.value = res.data.token
      userInfo.value = res.data.userInfo
      uni.setStorageSync('token', res.data.token)
      return Promise.resolve(res)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  // 注册
  const register = async (registerForm: RegisterForm) => {
    try {
      const res: any = await registerApi(registerForm)
      return Promise.resolve(res)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  // 获取用户信息
  const getInfo = async () => {
    try {
      const res: any = await getUserInfo()
      userInfo.value = res.data
      return Promise.resolve(res)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  // 登出
  const logout = () => {
    token.value = ''
    userInfo.value = null
    uni.removeStorageSync('token')
  }

  return {
    token,
    userInfo,
    login,
    register,
    getInfo,
    logout
  }
}
