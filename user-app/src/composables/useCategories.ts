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

  // 主要分类列表（首页显示）
  const mainCategories = [
    'Womenswear & Underwear',
    'Phones & Electronics', 
    'Fashion Accessories',
    'Beauty & Personal Care',
    'Home Supplies',
    'Sports & Outdoor',
    'Health',
    'Kids Fashion',
    'Food & Beverages',
    'Automotive & Motorcycle',
    'Books, Magazines & Audio',
    'Pet Supplies'
  ]

  // 分类图标映射
  const categoryIcons = {
    // TikTok Shop 官方分类
    'Womenswear & Underwear': 'https://thesvaya.com/cdn/shop/files/green-asymmetrical-velvet-kurta-1.webp?v=1751101578&width=1080',
    'Phones & Electronics': 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Fashion Accessories': 'https://tampamagazines.com/wp-content/uploads/2024/03/7201-scaled.jpg',
    'Menswear & Underwear': 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Home Supplies': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Beauty & Personal Care': 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Shoes': 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Sports & Outdoor': 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Luggage & Bags': 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Toys & Hobbies': 'https://cdn.firstcry.com/education/2022/11/06094158/Toy-Names-For-Kids.jpg',
    'Automotive & Motorcycle': 'https://www.marketresearchintellect.com/images/blogs/gearing-up-for-the-future-how-technology-is-transforming-automotive-components.webp',
    'Kids Fashion': 'https://i.pinimg.com/564x/7e/38/10/7e381083ba67cbf758d5ef343a7b5ac9.jpg',
    'Kitchenware': 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Computers & Office Equipment': 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Baby & Maternity': 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Tools & Hardware': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTgI3vtsrLU8JLBxa6ygpkNA620rTd0XHj1AQ&s',
    'Textiles & Soft Furnishings': 'https://cityfurnish.com/blog/wp-content/uploads/2023/11/28375-min.jpg',
    'Pet Supplies': 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Home Improvement': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLIfK_Ybnvi2D9YCZwH3XzatG4SWpSTADi6Q&s',
    'Food & Beverages': 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Muslim Fashion': 'https://static.independent.co.uk/s3fs-public/thumbnails/image/2017/10/16/17/modest-clothing.jpg?width=1200&height=1200&fit=crop',
    'Books, Magazines & Audio': 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Household Appliances': 'https://cdn.firstcry.com/education/2023/01/13101355/Names-Of-Household-Appliances-In-English.jpg',
    'Health': 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Furniture': 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Jewelry Accessories & Derivatives': 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=120&h=120&fit=crop&crop=center&auto=format&q=80',
    'Collectibles': 'https://www.shutterstock.com/image-photo/motril-spain-09242022-collectible-figurines-600nw-2248181147.jpg',
    'Pre-Owned': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7EWLEMbHjK-i-t_4MOiMjiWAWLKQ-EW3iMQ&s'
  }

  const loadCategories = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await categoryApi.getCategories()
      const apiCategories = response.list || []
      
      // 只显示主要的一级分类（parentId为'0'），添加图标
      const allCategories = apiCategories
        .filter((category: any) => category.parentId === '0' || category.parentId === 0)
        .map((category: any) => ({
          id: category.id,
          name: category.name,
          icon: category.imageUrl || category.icon || categoryIcons[category.name as keyof typeof categoryIcons] || 
                'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
        }))
      
      // 只显示主要分类（首页显示）
      categories.value = allCategories.filter(category => 
        mainCategories.includes(category.name)
      )
    } catch (err: any) {
      console.error('加载分类失败:', err)
      error.value = err.message || '加载分类失败'
      
      // 使用备用主要分类数据
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
          name: 'Beauty & Personal Care', 
          icon: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '5', 
          name: 'Home Supplies', 
          icon: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '6', 
          name: 'Sports & Outdoor', 
          icon: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '7', 
          name: 'Health', 
          icon: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '8', 
          name: 'Kids Fashion', 
          icon: 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '9', 
          name: 'Food & Beverages', 
          icon: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '10', 
          name: 'Automotive & Motorcycle', 
          icon: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '11', 
          name: 'Books, Magazines & Audio', 
          icon: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
        },
        { 
          id: '12', 
          name: 'Pet Supplies', 
          icon: 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
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