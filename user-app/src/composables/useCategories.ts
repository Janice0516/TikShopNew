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
    'Home & Living': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Electronics & Appliances': 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Fashion & Bags': 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Beauty & Personal Care': 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Food & Fresh': 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Sports & Outdoor': 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Books & Stationery': 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Baby & Kids': 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Womenswear & Underwear': 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Phones & Electronics': 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Fashion Accessories': 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Menswear & Underwear': 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Home Supplies': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Shoes': 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Luggage & Bags': 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
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
          name: 'Womenswear & Underwear', 
          icon: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '2', 
          name: 'Phones & Electronics', 
          icon: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '3', 
          name: 'Fashion Accessories', 
          icon: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '4', 
          name: 'Menswear & Underwear', 
          icon: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '5', 
          name: 'Home Supplies', 
          icon: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '6', 
          name: 'Beauty & Personal Care', 
          icon: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '7', 
          name: 'Shoes', 
          icon: 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '8', 
          name: 'Sports & Outdoor', 
          icon: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '9', 
          name: 'Luggage & Bags', 
          icon: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
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
