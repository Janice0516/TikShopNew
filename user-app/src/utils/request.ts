import { ref } from 'vue'

// 创建请求实例
const baseURL = 'https://tiktokshop-api.loca.lt/api'

// 请求拦截器
const request = (options: any) => {
  return new Promise((resolve, reject) => {
    // 获取token
    const token = uni.getStorageSync('token') || ''
    
    // 设置请求头
    const header = {
      'Content-Type': 'application/json',
      ...options.header
    }
    
    if (token) {
      header['Authorization'] = `Bearer ${token}`
    }
    
    // 发起请求
    uni.request({
      url: baseURL + options.url,
      method: options.method || 'GET',
      data: options.data || {},
      header,
      success: (res: any) => {
        if (res.statusCode === 200) {
          if (res.data.code === 200) {
            resolve(res.data)
          } else {
            uni.showToast({
              title: res.data.message || '请求失败',
              icon: 'none'
            })
            reject(res.data)
          }
        } else if (res.statusCode === 401) {
          uni.showToast({
            title: '未登录或登录已过期',
            icon: 'none'
          })
          // 清除token并跳转到登录页
          uni.removeStorageSync('token')
          uni.navigateTo({
            url: '/pages/login/login'
          })
          reject(res)
        } else {
          uni.showToast({
            title: '请求失败',
            icon: 'none'
          })
          reject(res)
        }
      },
      fail: (err: any) => {
        uni.showToast({
          title: '网络错误',
          icon: 'none'
        })
        reject(err)
      }
    })
  })
}

export default request
