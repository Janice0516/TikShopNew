import { ref, onMounted } from 'vue'
import { useProducts } from './useProducts'

export interface TopDealProduct {
  id: string | number
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
  badge?: string
  discount?: number
}

export function useTopDeals() {
  const topDeals = ref<TopDealProduct[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // 使用基础商品 composable
  const { loadProducts } = useProducts()

  const loadTopDeals = async () => {
    loading.value = true
    error.value = null
    
    try {
      // 获取 Top Deals 数据
      const response = await fetch('/api/shop/top-deals?limit=10')
      const data = await response.json()
      
      const apiProducts = data?.list || []
      
      // 格式化 Top Deals 商品
      topDeals.value = apiProducts.map((product: any) => {
        const salePrice = parseFloat(product.salePrice) || 0
        const discountPrice = parseFloat(product.discountPrice) || 0
        const isDiscountActive = product.isDiscountActive && 
          product.discountStartTime && product.discountEndTime &&
          new Date() >= new Date(product.discountStartTime) &&
          new Date() <= new Date(product.discountEndTime)
        
        // 如果折扣有效，显示折扣价；否则显示售价
        const currentPrice = isDiscountActive && discountPrice > 0 ? discountPrice : salePrice
        const originalPrice = isDiscountActive && discountPrice > 0 ? salePrice : null
        const discount = originalPrice ? Math.round(((originalPrice - currentPrice) / originalPrice) * 100) : 0
        
        return {
          id: product.id,
          name: product.name,
          description: product.description,
          price: currentPrice, // 当前价格（折扣价或售价）
          originalPrice: originalPrice, // 原价（仅在有折扣时显示）
          image: product.mainImage || `https://via.placeholder.com/200x200/ff0050/ffffff?text=${encodeURIComponent(product.name)}`,
          rating: 4.0 + Math.random() * 1, // 4.0-5.0 评分
          sales: product.sales || 0,
          stock: product.stock || 0,
          brand: product.brand || '',
          merchantName: product.merchantName || '',
          badge: discount > 0 ? `${discount}% OFF` : product.recommendReason || null,
          discount: discount
        }
      })
    } catch (err: any) {
      console.error('加载 Top Deals 失败:', err)
      error.value = err.message || '加载 Top Deals 失败'
      
      // 使用模拟数据作为备用
      topDeals.value = [
        {
          id: '1',
          name: 'BABYLOUS Kids Girl Plain Blouse e Baju Budak Perempuan Leng an Panjang Baju Budak Outin...',
          price: 8.49,
          originalPrice: 16.98,
          image: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          rating: 4.9,
          sales: 11400,
          stock: 50,
          brand: 'babylous',
          badge: '50% OFF'
        },
        {
          id: '2',
          name: 'Soft Chiffon Shawl by Almayla',
          price: 7.90,
          originalPrice: 14.90,
          image: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          rating: 4.9,
          sales: 164100,
          stock: 30,
          brand: 'Almayla',
          badge: '47% OFF'
        },
        {
          id: '3',
          name: 'BBLAN SKINCARE SET TONER ORIGINAL HQ',
          price: 51.50,
          originalPrice: 80.00,
          image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          rating: 4.8,
          sales: 14300,
          stock: 25,
          brand: 'BBLAN',
          badge: '36% OFF'
        },
        {
          id: '4',
          name: 'Alat Berguna, Simen Pengedap, untuk Membetulkan Lubang Penyaman Udara dan Pembet...',
          price: 12.00,
          originalPrice: 35.13,
          image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          rating: 4.5,
          sales: 13500,
          stock: 100,
          brand: 'HomeFix',
          badge: '66% OFF'
        },
        {
          id: '5',
          name: 'YOURFUSTAN RARA DRESS DA ILYWEAR',
          price: 29.90,
          originalPrice: 100.00,
          image: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=200&h=200&fit=crop&crop=center&auto=format&q=80',
          rating: 4.8,
          sales: 4200,
          stock: 15,
          brand: 'YOURFUSTAN',
          badge: '70% OFF'
        }
      ]
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    loadTopDeals()
  })

  return {
    topDeals,
    loading,
    error,
    loadTopDeals
  }
}
