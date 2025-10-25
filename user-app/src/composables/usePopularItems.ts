import { ref, onMounted } from 'vue'

export interface PopularItem {
  id: string | number
  name: string
  description?: string
  price?: number
  originalPrice?: number
  image: string
  rating?: number
  sales?: number
  stock?: number
  brand?: string
  merchantName?: string
  badge?: string
  category?: string
}

export function usePopularItems() {
  const popularItems = ref<PopularItem[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const loadPopularItems = async () => {
    loading.value = true
    error.value = null
    
    try {
      // 获取 Popular Items 数据
      const response = await fetch('/api/shop/popular-items?limit=8')
      const data = await response.json()
      
      const apiProducts = data?.list || []
      
      // 格式化 Popular Items 商品
      popularItems.value = apiProducts.map((product: any) => {
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
          name: product.name,
          description: product.description,
          price: currentPrice, // 当前价格（折扣价或售价）
          originalPrice: originalPrice, // 原价（仅在有折扣时显示）
          image: product.mainImage || `https://via.placeholder.com/200x200/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
          rating: 4.0 + Math.random() * 1,
          sales: product.sales || 0,
          stock: product.stock || 0,
          brand: product.brand || '',
          merchantName: product.merchantName || '',
          category: product.categoryName || '',
          badge: product.recommendReason || null
        }
      })
    } catch (err: any) {
      console.error('加载 Popular Items 失败:', err)
      error.value = err.message || '加载 Popular Items 失败'
      
      // 使用模拟数据作为备用
      popularItems.value = [
        {
          id: '1',
          name: 'PINKFLASH OFFICIAL STORE',
          image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Beauty & Personal Care',
          brand: 'PINKFLASH'
        },
        {
          id: '2',
          name: 'Lock Live Payment',
          image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Electronics',
          brand: 'SecurePay',
          badge: 'LIMITED TIME ONLY'
        },
        {
          id: '3',
          name: 'Jermanpine',
          image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Sports & Outdoor',
          brand: 'Jermanpine'
        },
        {
          id: '4',
          name: 'Baby Rompers',
          image: 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Baby & Kids',
          brand: 'BabyStyle'
        },
        {
          id: '5',
          name: 'Marble Wallpaper SIZE 30x60CM',
          image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Home & Living',
          brand: 'MarbleCo',
          badge: 'READY STOCK'
        },
        {
          id: '6',
          name: 'Fashion Accessories Collection',
          image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Fashion Accessories',
          brand: 'StyleHub'
        },
        {
          id: '7',
          name: 'Tech Gadgets Bundle',
          image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Electronics',
          brand: 'TechStore'
        },
        {
          id: '8',
          name: 'Kitchen Essentials',
          image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          category: 'Home & Living',
          brand: 'KitchenPro'
        }
      ]
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    loadPopularItems()
  })

  return {
    popularItems,
    loading,
    error,
    loadPopularItems
  }
}
