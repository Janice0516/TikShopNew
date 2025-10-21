<template>
  <div class="category-page">
    <div class="container">
      <!-- 分类信息 -->
      <div class="category-header">
        <h1>{{ category.name }}</h1>
        <p>{{ category.description }}</p>
      </div>
      
      <!-- 筛选和排序 -->
      <div class="filter-section">
        <div class="filter-left">
          <el-select v-model="filters.category" :placeholder="t('category.subCategory')" clearable>
            <el-option
              v-for="subCategory in subCategories"
              :key="subCategory.id"
              :label="subCategory.name"
              :value="subCategory.id"
            />
          </el-select>
          
          <el-select v-model="filters.priceRange" :placeholder="t('category.priceRange')" clearable>
            <el-option label="0-100" value="0-100" />
            <el-option label="100-500" value="100-500" />
            <el-option label="500-1000" value="500-1000" />
            <el-option label="1000+" value="1000+" />
          </el-select>
        </div>
        
        <div class="filter-right">
          <el-select v-model="sortBy" :placeholder="t('category.sortBy')">
            <el-option :label="t('category.defaultSort')" value="default" />
            <el-option :label="t('category.priceAsc')" value="price_asc" />
            <el-option :label="t('category.priceDesc')" value="price_desc" />
            <el-option :label="t('category.salesSort')" value="sales" />
            <el-option :label="t('category.ratingSort')" value="rating" />
          </el-select>
        </div>
      </div>
      
      <!-- 商品列表 -->
      <div class="products-section">
        <div class="products-header">
          <h2>{{ t('category.productList') }}</h2>
          <span class="product-count">{{ t('category.totalProducts', { count: totalProducts }) }}</span>
        </div>
        
        <div class="product-grid" v-loading="loading">
          <ProductCard 
            v-for="product in products" 
            :key="product.id"
            :product="product"
          />
        </div>
        
        <!-- 分页 -->
        <div class="pagination" v-if="totalPages > 1">
          <el-pagination
            v-model:current-page="currentPage"
            :page-size="pageSize"
            :total="totalProducts"
            layout="prev, pager, next, jumper"
            @current-change="handlePageChange"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { categoryApi, productApi } from '@/api'
import ProductCard from '@/components/ProductCard.vue'
import { ElMessage } from 'element-plus'

const route = useRoute()
const { t } = useI18n()

const category = ref<any>({})
const subCategories = ref<any[]>([])
const products = ref<any[]>([])
const loading = ref(false)
const currentPage = ref(1)
const pageSize = ref(12)
const totalProducts = ref(0)
const totalPages = ref(0)
const sortBy = ref('default')

const filters = reactive({
  category: '',
  priceRange: ''
})

// 加载分类信息
const loadCategory = async () => {
  try {
    const categoryId = route.params.id as string
    const response = await categoryApi.getCategories()
    const foundCategory = response.find((cat: any) => cat.id === categoryId)
    
    if (foundCategory) {
      category.value = foundCategory
    } else {
      // 使用默认分类
      category.value = {
        id: categoryId,
        name: '商品分类',
        description: '精选好物，品质保证'
      }
    }
  } catch (error) {
    console.error('加载分类失败:', error)
    category.value = {
      id: route.params.id,
      name: '商品分类',
      description: '精选好物，品质保证'
    }
  }
}

// 加载子分类
const loadSubCategories = async () => {
  try {
    const response = await categoryApi.getCategories()
    subCategories.value = response.filter((cat: any) => cat.parentId === route.params.id)
  } catch (error) {
    console.error('加载子分类失败:', error)
    subCategories.value = []
  }
}

// 加载商品
const loadProducts = async () => {
  try {
    loading.value = true
    const categoryId = route.params.id as string
    
    const params = {
      page: currentPage.value,
      limit: pageSize.value,
      categoryId,
      ...filters,
      sort: sortBy.value
    }
    
    const response = await productApi.getProducts(params)
    products.value = response.products || []
    totalProducts.value = response.total || 0
    totalPages.value = Math.ceil(totalProducts.value / pageSize.value)
  } catch (error) {
    console.error('加载商品失败:', error)
    ElMessage.error('加载商品失败')
    
    // 使用默认商品数据
    products.value = [
      {
        id: '1',
        name: '商品1',
        description: '商品描述',
        price: 99.99,
        originalPrice: 129.99,
        image: 'https://via.placeholder.com/300x200/409EFF/ffffff?text=商品1',
        badge: '热销',
        rating: 4.5,
        reviewCount: 128
      },
      {
        id: '2',
        name: '商品2',
        description: '商品描述',
        price: 199.99,
        image: 'https://via.placeholder.com/300x200/67C23A/ffffff?text=商品2',
        rating: 4.3,
        reviewCount: 89
      }
    ]
    totalProducts.value = products.value.length
    totalPages.value = 1
  } finally {
    loading.value = false
  }
}

// 处理分页变化
const handlePageChange = (page: number) => {
  currentPage.value = page
  loadProducts()
}

// 监听筛选条件变化
watch([() => filters.category, () => filters.priceRange, sortBy], () => {
  currentPage.value = 1
  loadProducts()
})

onMounted(async () => {
  await Promise.all([
    loadCategory(),
    loadSubCategories(),
    loadProducts()
  ])
})
</script>

<style scoped lang="scss">
.category-page {
  padding: 20px 0;
  background: $background-base;
  min-height: 100vh;
}

.category-header {
  background: #fff;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  text-align: center;
  
  h1 {
    font-size: 28px;
    font-weight: bold;
    color: $text-primary;
    margin-bottom: 10px;
  }
  
  p {
    font-size: 16px;
    color: $text-secondary;
    margin: 0;
  }
}

.filter-section {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  
  .filter-left {
    display: flex;
    gap: 15px;
  }
  
  .filter-right {
    display: flex;
    gap: 15px;
  }
}

.products-section {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
}

.products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  
  h2 {
    font-size: 20px;
    font-weight: bold;
    color: $text-primary;
    margin: 0;
  }
  
  .product-count {
    color: $text-secondary;
  }
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.pagination {
  display: flex;
  justify-content: center;
}

@media (max-width: 768px) {
  .filter-section {
    flex-direction: column;
    align-items: stretch;
    
    .filter-left,
    .filter-right {
      justify-content: center;
    }
  }
  
  .category-header {
    padding: 20px;
    
    h1 {
      font-size: 24px;
    }
  }
  
  .product-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
  }
}
</style>
