import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { userApi } from '@/api'

export const useUserStore = defineStore('user', () => {
  // 状态
  const userInfo = ref<any>(null)
  const token = ref<string>(localStorage.getItem('token') || '')
  const isLoggedIn = computed(() => !!token.value && !!userInfo.value)

  // 登录
  const login = async (phone: string, password: string) => {
    try {
      const response = await userApi.login({ phone, password })
      token.value = response.token
      userInfo.value = response.user
      localStorage.setItem('token', response.token)
      return response
    } catch (error) {
      throw error
    }
  }

  // 注册
  const register = async (phone: string, password: string, verifyCode: string) => {
    try {
      const response = await userApi.register({ phone, password, verifyCode })
      token.value = response.token
      userInfo.value = response.user
      localStorage.setItem('token', response.token)
      return response
    } catch (error) {
      throw error
    }
  }

  // 获取用户信息
  const fetchUserInfo = async () => {
    try {
      const response = await userApi.getUserInfo()
      userInfo.value = response
      return response
    } catch (error) {
      throw error
    }
  }

  // 更新用户信息
  const updateUserInfo = async (data: any) => {
    try {
      const response = await userApi.updateUserInfo(data)
      userInfo.value = { ...userInfo.value, ...response }
      return response
    } catch (error) {
      throw error
    }
  }

  // 登出
  const logout = () => {
    token.value = ''
    userInfo.value = null
    localStorage.removeItem('token')
  }

  // 初始化用户信息
  const initUserInfo = async () => {
    if (token.value && !userInfo.value) {
      try {
        await fetchUserInfo()
      } catch (error) {
        logout()
      }
    }
  }

  return {
    userInfo,
    token,
    isLoggedIn,
    login,
    register,
    fetchUserInfo,
    updateUserInfo,
    logout,
    initUserInfo
  }
})
