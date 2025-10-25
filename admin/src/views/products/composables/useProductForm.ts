import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getCategoryList, getProductDetail } from '@/api/product'
import type { ProductForm, CategoryOption, ProductVariant } from '../types/product'

export function useProductForm() {
  const router = useRouter()
  const route = useRoute()
  
  const isEdit = ref(false)
  const productId = ref<number>()
  const loading = ref(false)
  const categories = ref<CategoryOption[]>([])

  const form = reactive<ProductForm>({
    name: '',
    categoryId: null,
    brand: '',
    mainImage: '',
    images: '',
    video: '',
    costPrice: 0,
    suggestPrice: 0,
    stock: 0,
    description: '',
    status: 1,
    variants: [] as ProductVariant[]
  })

  // 加载分类数据
  const loadCategories = async () => {
    try {
      console.log('开始加载分类数据...')
      const response = await getCategoryList()
      console.log('分类API响应:', response)
      console.log('响应数据结构:', {
        hasResponse: !!response,
        hasData: !!(response && response.data),
        hasDataData: !!(response && response.data && response.data.data),
        isArray: Array.isArray(response?.data?.data),
        dataLength: response?.data?.data?.length
      })
      
      // 处理axios响应结构：response.data.data 才是真正的数据
      if (response && response.data && response.data.data && Array.isArray(response.data.data)) {
        categories.value = response.data.data
        console.log('成功加载分类:', categories.value.length, '个')
        console.log('分类数据示例:', categories.value.slice(0, 3))
      } else {
        console.warn('分类数据格式不正确:', response)
        console.warn('尝试使用备用数据格式...')
        
        // 尝试其他可能的数据格式
        if (response && response.data && Array.isArray(response.data)) {
          categories.value = response.data
          console.log('使用备用格式加载分类:', categories.value.length, '个')
        } else {
          categories.value = []
          console.error('无法解析分类数据')
        }
      }
    } catch (error) {
      console.error('加载分类失败:', error)
      console.error('错误详情:', error.response?.data || error.message)
      categories.value = []
    }
  }

  // 加载产品详情（编辑模式）
  const loadProductDetail = async (id: number) => {
    try {
      loading.value = true
      const response = await getProductDetail(id)
      const product = response.data
      
      if (product) {
        form.name = product.name || ''
        form.categoryId = product.categoryId || null
        form.brand = product.brand || ''
        form.mainImage = product.mainImage || ''
        form.images = product.images || ''
        form.video = product.video || ''
        form.costPrice = product.costPrice || 0
        form.suggestPrice = product.suggestPrice || 0
        form.stock = product.stock || 0
        form.description = product.description || ''
        form.status = product.status || 1
        
        // 处理款式和规格数据
        if (product.variants) {
          try {
            form.variants = JSON.parse(product.variants)
          } catch (error) {
            console.error('解析款式数据失败:', error)
          }
        }
      }
    } catch (error) {
      console.error('加载产品详情失败:', error)
    } finally {
      loading.value = false
    }
  }

  // 初始化
  onMounted(async () => {
    await loadCategories()
    
    if (route.params.id) {
      isEdit.value = true
      productId.value = Number(route.params.id)
      await loadProductDetail(productId.value)
    }
  })

  // 返回页面
  const goBack = () => {
    router.go(-1)
  }

  return {
    form,
    loading,
    isEdit,
    productId,
    categories,
    goBack,
    loadProductDetail
  }
}
