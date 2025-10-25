import { ref } from 'vue'
import { productApi } from '@/api'

export interface Product {
  id: string | number
  productId?: string | number
  name: string
  description?: string
  price: number
  originalPrice?: number
  image: string
  rating: number
  sales: number
  stock: number
  brand?: string
  merchantName?: string
  categoryId?: string | number
  categoryName?: string
  merchantId?: string | number
  badge?: string
}

export interface Pagination {
  current: number
  pageSize: number
  total: number
  totalPages: number
}

export function useProducts() {
  const products = ref<Product[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<Pagination>({
    current: 1,
    pageSize: 10,
    total: 0,
    totalPages: 0
  })

  const loadProducts = async (page = 1, pageSize = 10) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/shop/products?page=${page}&pageSize=${pageSize}`)
      const data = await response.json()
      
      if (data) {
        pagination.value.total = data.total || data.list?.length || 0
        pagination.value.totalPages = data.totalPages || Math.ceil(pagination.value.total / pageSize)
        pagination.value.current = data.page || page
        pagination.value.pageSize = pageSize
      }
      
      const apiProducts = data?.list || []
      
      products.value = apiProducts.map((product: any) => {
        const salePrice = parseFloat(product.salePrice) || 0
        const discountPrice = parseFloat(product.discountPrice) || 0
        const isDiscountActive = product.isDiscountActive && 
          product.discountStartTime && product.discountEndTime &&
          new Date() >= new Date(product.discountStartTime) &&
          new Date() <= new Date(product.discountEndTime)
        
        // 如果折扣有效，显示折扣价；否则显示售价
        const currentPrice = isDiscountActive && discountPrice > 0 ? discountPrice : salePrice
        const originalPrice = isDiscountActive && discountPrice > 0 ? salePrice : null
        
        return {
          id: product.id,
          productId: product.productId,
          name: product.name,
          description: product.description,
          price: currentPrice, // 当前价格（折扣价或售价）
          originalPrice: originalPrice, // 原价（仅在有折扣时显示）
          image: product.mainImage || `https://via.placeholder.com/300x300/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
          rating: 4.0, // 固定评分
          sales: product.sales || 0,
          stock: product.stock || 0,
          brand: product.brand || '',
          categoryId: product.categoryId || '',
          categoryName: product.categoryName || '',
          merchantId: product.merchantId,
          merchantName: product.merchantName || '',
          badge: null // 移除虚拟促销信息
        }
      })
    } catch (err: any) {
      console.error('加载商品失败:', err)
      error.value = err.message || '加载商品失败'
      products.value = []
      pagination.value.total = 0
      pagination.value.totalPages = 0
    } finally {
      loading.value = false
    }
  }

  const loadProductsByCategory = async (categoryId: string | number, page = 1, pageSize = 10) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/shop/products?categoryId=${categoryId}&page=${page}&pageSize=${pageSize}`)
      const data = await response.json()
      
      if (data) {
        pagination.value.total = data.total || data.list?.length || 0
        pagination.value.totalPages = data.totalPages || Math.ceil(pagination.value.total / pageSize)
        pagination.value.current = data.page || page
        pagination.value.pageSize = pageSize
      }
      
      const apiProducts = data?.list || []
      
      products.value = apiProducts.map((product: any) => {
        const salePrice = parseFloat(product.salePrice) || 0
        const discountPrice = parseFloat(product.discountPrice) || 0
        
        const isDiscountActive = product.isDiscountActive && 
          product.discountStartTime && product.discountEndTime &&
          new Date() >= new Date(product.discountStartTime) &&
          new Date() <= new Date(product.discountEndTime)
        
        const currentPrice = isDiscountActive && discountPrice > 0 ? discountPrice : salePrice
        const originalPrice = isDiscountActive && discountPrice > 0 ? salePrice : null
        
        const shouldShowOriginalPrice = originalPrice && originalPrice !== currentPrice
        
        return {
          id: product.id,
          productId: product.productId,
          name: product.name,
          description: product.description,
          price: currentPrice,
          originalPrice: shouldShowOriginalPrice ? originalPrice : null,
          image: product.mainImage || `https://via.placeholder.com/300x300/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
          rating: 4.0,
          sales: product.sales || 0,
          stock: product.stock || 0,
          brand: product.brand || '',
          categoryId: product.categoryId || '',
          categoryName: product.categoryName || '',
          merchantId: product.merchantId,
          merchantName: product.merchantName || '',
          badge: null
        }
      })
    } catch (err: any) {
      console.error('加载分类商品失败:', err)
      error.value = err.message || '加载分类商品失败'
      products.value = []
      pagination.value.total = 0
      pagination.value.totalPages = 0
    } finally {
      loading.value = false
    }
  }

  const searchProducts = async (keyword: string, page = 1, pageSize = 10) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/shop/products?keyword=${encodeURIComponent(keyword)}&page=${page}&pageSize=${pageSize}`)
      const data = await response.json()
      
      if (data) {
        pagination.value.total = data.total || data.list?.length || 0
        pagination.value.totalPages = data.totalPages || Math.ceil(pagination.value.total / pageSize)
        pagination.value.current = data.page || page
        pagination.value.pageSize = pageSize
      }
      
      const apiProducts = data?.list || []
      
      products.value = apiProducts.map((product: any) => {
        const salePrice = parseFloat(product.salePrice) || 0
        const discountPrice = parseFloat(product.discountPrice) || 0
        
        const isDiscountActive = product.isDiscountActive && 
          product.discountStartTime && product.discountEndTime &&
          new Date() >= new Date(product.discountStartTime) &&
          new Date() <= new Date(product.discountEndTime)
        
        const currentPrice = isDiscountActive && discountPrice > 0 ? discountPrice : salePrice
        const originalPrice = isDiscountActive && discountPrice > 0 ? salePrice : null
        
        const shouldShowOriginalPrice = originalPrice && originalPrice !== currentPrice
        
        return {
          id: product.id,
          productId: product.productId,
          name: product.name,
          description: product.description,
          price: currentPrice,
          originalPrice: shouldShowOriginalPrice ? originalPrice : null,
          image: product.mainImage || `https://via.placeholder.com/300x300/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
          rating: 4.0,
          sales: product.sales || 0,
          stock: product.stock || 0,
          brand: product.brand || '',
          categoryId: product.categoryId || '',
          categoryName: product.categoryName || '',
          merchantId: product.merchantId,
          merchantName: product.merchantName || '',
          badge: null
        }
      })
    } catch (err: any) {
      console.error('搜索商品失败:', err)
      error.value = err.message || '搜索商品失败'
      products.value = []
      pagination.value.total = 0
      pagination.value.totalPages = 0
    } finally {
      loading.value = false
    }
  }

  return {
    products,
    loading,
    error,
    pagination,
    loadProducts,
    loadProductsByCategory,
    searchProducts
  }
}
