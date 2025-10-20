import axios from 'axios'
import { ElMessage } from 'element-plus'
import { useMerchantStore } from '@/stores/merchant'

// 创建axios实例
const request = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  timeout: 10000
})

// 请求拦截器
request.interceptors.request.use(
  (config) => {
    const merchantStore = useMerchantStore()
    if (merchantStore.token) {
      config.headers.Authorization = `Bearer ${merchantStore.token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 响应拦截器
request.interceptors.response.use(
  (response) => {
    const res = response.data
    
    // 如果返回的状态码不是200，则认为有错误
    if (res.code !== 200 && res.statusCode !== 200) {
      ElMessage.error(res.message || 'Request Error')
      return Promise.reject(new Error(res.message || 'Error'))
    }
    
    return res.data || res
  },
  (error) => {
    console.error('Request error:', error)
    
    if (error.response) {
      switch (error.response.status) {
        case 401:
          ElMessage.error('Unauthorized, please login again')
          const merchantStore = useMerchantStore()
          merchantStore.logout()
          window.location.href = '/login'
          break
        case 403:
          ElMessage.error('Access forbidden')
          break
        case 404:
          ElMessage.error('Resource not found')
          break
        case 500:
          ElMessage.error('Server error')
          break
        default:
          ElMessage.error(error.response.data?.message || 'Network error')
      }
    } else {
      ElMessage.error('Network error, please try again')
    }
    
    return Promise.reject(error)
  }
)

export default request

