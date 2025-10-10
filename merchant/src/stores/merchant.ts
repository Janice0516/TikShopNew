import { defineStore } from 'pinia'
import { ref } from 'vue'
import { login as loginApi, getMerchantInfo as getMerchantInfoApi } from '@/api/merchant'
import type { LoginForm } from '@/api/merchant'

export const useMerchantStore = defineStore('merchant', () => {
  const token = ref<string>(localStorage.getItem('merchant-token') || '')
  const merchantInfo = ref<any>(null)

  // 登录
  const login = async (loginForm: LoginForm) => {
    try {
      const res: any = await loginApi(loginForm)
      // 处理嵌套的data结构
      const actualData = res.data?.data || res.data
      token.value = actualData.token
      merchantInfo.value = actualData.merchantInfo
      localStorage.setItem('merchant-token', actualData.token)
      return Promise.resolve(res)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  // 获取商家信息
  const getMerchantInfo = async () => {
    try {
      const res: any = await getMerchantInfoApi()
      merchantInfo.value = res.data
      return Promise.resolve(res)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  // 登出
  const logout = () => {
    token.value = ''
    merchantInfo.value = null
    localStorage.removeItem('merchant-token')
  }

  return {
    token,
    merchantInfo,
    login,
    getMerchantInfo,
    logout
  }
})

