import axios from 'axios'
import type { AxiosError, AxiosResponse, InternalAxiosRequestConfig } from 'axios'
import { ElMessage } from 'element-plus'
import router from '@/router'

// 创建axios实例
const service = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  timeout: 30000  // 增加到30秒
})

// 请求拦截器
service.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    // 从localStorage获取token，避免循环依赖
    const token = localStorage.getItem('token')
    
    // 添加token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    return config
  },
  (error: AxiosError) => {
    console.error('请求错误：', error)
    return Promise.reject(error)
  }
)

// 将可能的数组/对象错误消息安全转字符串
function normalizeErrorMessage(message: any): string {
  if (typeof message === 'string') return message
  try {
    if (Array.isArray(message)) {
      return message.map(m => (typeof m === 'string' ? m : JSON.stringify(m))).join('; ')
    }
    if (message && typeof message === 'object') {
      // 常见 NestJS ValidationPipe 错误结构
      if (message.message && Array.isArray(message.message)) {
        return message.message.join('; ')
      }
      if (message.error) {
        return String(message.error)
      }
      return JSON.stringify(message)
    }
    return '请求失败'
  } catch {
    return '请求失败'
  }
}

// 响应拦截器
service.interceptors.response.use(
  (response) => {
    const data = response.data
    // 检查响应体中的code字段，而不是HTTP状态码
    if (data.code && data.code !== 200) {
      ElMessage.error(normalizeErrorMessage(data.message) || '请求失败')
      return Promise.reject(new Error(normalizeErrorMessage(data.message) || '请求失败'))
    }
    return response
  },
  (error) => {
    const { response } = error
    if (response) {
      const { status, data } = response
      switch (status) {
        case 401:
          ElMessage.error('未登录或登录已过期，请重新登录')
          // 清除token并跳转到登录页
          localStorage.removeItem('token')
          const base = import.meta.env.BASE_URL || '/'
          router.push(`${base}login`)
          break
        case 403:
          ElMessage.error('没有权限访问')
          break
        case 404:
          ElMessage.error('请求的资源不存在')
          break
        case 500:
          ElMessage.error('服务器错误')
          break
        default:
          ElMessage.error(normalizeErrorMessage(data?.message) || '请求失败')
      }
    } else if (error.code === 'ECONNABORTED') {
      ElMessage.error('请求超时，请检查网络连接或稍后重试')
    } else if (error.code === 'NETWORK_ERROR') {
      ElMessage.error('网络连接失败，请检查网络设置')
    } else {
      ElMessage.error('网络错误，请检查网络连接')
    }
    
    return Promise.reject(error)
  }
)

export default service

