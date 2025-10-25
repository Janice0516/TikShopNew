import { defineStore } from 'pinia'
import { ref } from 'vue'
import { login as loginApi, getUserInfo } from '@/api/user'
import type { LoginForm } from '@/api/user'

export const useUserStore = defineStore('user', () => {
  const token = ref<string>(localStorage.getItem('token') || '')
  const userInfo = ref<any>(null)

  // 登录
  const login = async (loginForm: LoginForm) => {
    try {
      const res = await loginApi(loginForm)
      // 修复：直接访问 res.data，因为API返回的是 {token, userInfo}
      const data = res.data
      token.value = data.token
      userInfo.value = data.userInfo
      localStorage.setItem('token', data.token)
      return Promise.resolve(res)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  // 获取用户信息
  const getInfo = async () => {
    try {
      const res = await getUserInfo()
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
    localStorage.removeItem('token')
  }

  return {
    token,
    userInfo,
    login,
    getInfo,
    logout
  }
})

