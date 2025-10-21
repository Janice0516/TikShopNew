import { ref, onMounted } from 'vue'
import { categoryApi } from '@/api'

export interface Category {
  id: string | number
  name: string
  icon: string
}

export function useCategories() {
  const categories = ref<Category[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // 分类图标映射
  const categoryIcons = {
    // 英文分类名称（API实际返回的）
    'Home & Living': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Beauty & Personal Care': 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Bags & Luggage': 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Food & Fresh': 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Sports Shoes': 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Home Appliances': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Beverages': 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Electronics & Appliances': 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Women\'s Clothing': 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
            'Computers': 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
            'Fruits': 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Cosmetics': 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
            'Kitchenware': 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
            'Fashion & Bags': 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Men\'s Clothing': 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Mobile Phones': 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Snacks': 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Skincare': 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Home Textiles': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    
    // 中文分类名称（备用）
    '服装鞋包': 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '数码家电': 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '食品生鲜': 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '美妆个护': 'https://images.unsplash.com/photo-1596462502278-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '家居生活': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '运动户外': 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '图书文具': 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '母婴用品': 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '汽车用品': 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    '宠物用品': 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
  }

  const loadCategories = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await categoryApi.getCategories()
      const apiCategories = response.list || []
      
      // 为分类添加图标
      categories.value = apiCategories.map((category: any) => ({
        ...category,
        icon: categoryIcons[category.name as keyof typeof categoryIcons] || 
              'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
      }))
    } catch (err: any) {
      console.error('加载分类失败:', err)
      error.value = err.message || '加载分类失败'
      
      // 使用备用分类数据
      categories.value = [
        { 
          id: '1', 
          name: 'Fashion & Bags', 
          icon: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '2', 
          name: 'Electronics & Appliances', 
          icon: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '3', 
          name: 'Food & Fresh', 
          icon: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '4', 
          name: 'Beauty & Personal Care', 
          icon: 'https://images.unsplash.com/photo-1596462502278-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '5', 
          name: 'Home & Living', 
          icon: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '6', 
          name: 'Sports Shoes', 
          icon: 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '7', 
          name: 'Computers', 
          icon: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '8', 
          name: 'Mobile Phones', 
          icon: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '9', 
          name: 'Beverages', 
          icon: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        }
      ]
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    loadCategories()
  })

  return {
    categories,
    loading,
    error,
    loadCategories
  }
}
