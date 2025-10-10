import request from '@/utils/request'

// 获取店铺信息
export const getShopInfo = () => {
  return request({
    url: '/merchant/shop',
    method: 'GET'
  })
}

// 更新店铺信息
export const updateShopInfo = (data: any) => {
  return request({
    url: '/merchant/shop',
    method: 'PATCH',
    data
  })
}

// 获取店铺统计数据
export const getShopStats = () => {
  return request({
    url: '/merchant/shop/stats',
    method: 'GET'
  })
}

// 更新店铺公告
export const updateShopAnnouncement = (data: any) => {
  return request({
    url: '/merchant/shop/announcement',
    method: 'PATCH',
    data
  })
}

// 上传店铺图片
export const uploadShopImage = (file: File, type: 'logo' | 'banner' | 'welcome' | 'category') => {
  const formData = new FormData()
  formData.append('file', file)
  formData.append('type', type)
  
  return request({
    url: '/merchant/shop/upload',
    method: 'POST',
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}
