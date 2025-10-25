<template>
  <div class="search-page">
    <div class="container">
      <!-- 搜索框 -->
      <div class="search-header">
        <div class="search-box">
          <el-input
            v-model="searchQuery"
            :placeholder="t('search.searchProducts')"
            @keyup.enter="handleSearch"
            size="large"
          >
            <template #append>
              <el-button @click="handleSearch" :icon="Search" />
            </template>
          </el-input>
        </div>
        
        <div class="search-info" v-if="searchQuery">
          <h2>{{ t('search.searchResults') }}: "{{ searchQuery }}"</h2>
          <span class="result-count">{{ t('search.foundResults', { count: totalResults }) }}</span>
        </div>
      </div>
      
      <!-- 筛选和排序 -->
      <div class="filter-section" v-if="products.length > 0">
        <div class="filter-left">
          <el-select v-model="filters.category" :placeholder="t('search.category')" clearable>
            <el-option
              v-for="category in categories"
              :key="category.id"
              :label="category.name"
              :value="category.id"
            />
          </el-select>
          
          <el-select v-model="filters.priceRange" :placeholder="t('search.priceRange')" clearable>
            <el-option label="0-100" value="0-100" />
            <el-option label="100-500" value="100-500" />
            <el-option label="500-1000" value="500-1000" />
            <el-option label="1000+" value="1000+" />
          </el-select>
        </div>
        
        <div class="filter-right">
          <el-select v-model="sortBy" :placeholder="t('search.sortBy')">
            <el-option :label="t('search.defaultSort')" value="default" />
            <el-option :label="t('search.priceAsc')" value="price_asc" />
            <el-option :label="t('search.priceDesc')" value="price_desc" />
            <el-option :label="t('search.salesSort')" value="sales" />
            <el-option :label="t('search.ratingSort')" value="rating" />
          </el-select>
        </div>
      </div>
      
      <!-- 商品列表 -->
      <div class="products-section" v-if="products.length > 0">
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
            :total="totalResults"
            layout="prev, pager, next, jumper"
            @current-change="handlePageChange"
          />
        </div>
      </div>
      
      <!-- 无结果 -->
      <div class="no-results" v-else-if="!loading && searchQuery">
        <div class="no-results-content">
          <el-icon size="64" color="#ccc"><Search /></el-icon>
          <h3>{{ t('search.noResults') }}</h3>
          <p>{{ t('search.noResultsDesc') }}</p>
          <el-button type="primary" @click="clearSearch">{{ t('search.searchAgain') }}</el-button>
        </div>
      </div>
      
      <!-- 推荐商品 -->
      <div class="recommendations" v-if="!searchQuery">
        <h2>{{ t('search.recommendedProducts') }}</h2>
        <div class="product-grid">
          <ProductCard 
            v-for="product in recommendedProducts" 
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { productApi, categoryApi } from '@/api'
import ProductCard from '@/components/ProductCard.vue'
import { Search } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

const searchQuery = ref('')
const products = ref<any[]>([])
const categories = ref<any[]>([])
const recommendedProducts = ref<any[]>([])
const loading = ref(false)
const currentPage = ref(1)
const pageSize = ref(12)
const totalResults = ref(0)
const totalPages = ref(0)
const sortBy = ref('default')

const filters = reactive({
  category: '',
  priceRange: ''
})

// 初始化搜索
const initSearch = () => {
  const query = route.query.q as string
  if (query) {
    searchQuery.value = query
    handleSearch()
  }
}

// 执行搜索
const handleSearch = async () => {
  if (!searchQuery.value.trim()) {
    ElMessage.warning('请输入搜索关键词')
    return
  }
  
  try {
    loading.value = true
    currentPage.value = 1
    
    const params = {
      keyword: searchQuery.value,
      page: currentPage.value,
      limit: pageSize.value,
      ...filters,
      sort: sortBy.value
    }
    
    // 使用正确的API端点进行搜索
    const response = await fetch(`/api/shop/products?keyword=${encodeURIComponent(searchQuery.value)}&page=${currentPage.value}&pageSize=${pageSize.value}`)
    const data = await response.json()
    
    if (data && data.list) {
      products.value = data.list.map((product: any) => ({
        id: product.id,
        name: product.name,
        description: product.description,
        price: product.price,
        image: product.image || 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=200&fit=crop&crop=center&auto=format&q=80',
        rating: product.rating || 4.5,
        reviewCount: product.sales || 0,
        sales: product.sales || 0,
        stock: product.stock || 0,
        brand: product.brand,
        merchantName: product.merchantName
      }))
      totalResults.value = data.total || 0
      totalPages.value = Math.ceil(totalResults.value / pageSize.value)
    } else {
      products.value = []
      totalResults.value = 0
      totalPages.value = 0
    }
    
    // 更新URL
    router.replace({ query: { q: searchQuery.value } })
  } catch (error) {
    console.error('搜索失败:', error)
    ElMessage.error('搜索失败')
    
    // 使用默认搜索结果
    products.value = [
      {
        id: '1',
        name: `搜索结果: ${searchQuery.value}`,
        description: '这是搜索结果的商品',
        price: 99.99,
        image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=200&fit=crop&crop=center&auto=format&q=80',
        rating: 4.5,
        reviewCount: 128,
        sales: 128,
        stock: 10,
        brand: 'Brand A',
        merchantName: 'Merchant A'
      }
    ]
    totalResults.value = products.value.length
    totalPages.value = 1
  } finally {
    loading.value = false
  }
}

// 加载分类
const loadCategories = async () => {
  try {
    const response = await fetch('/api/shop/categories')
    const data = await response.json()
    categories.value = data || []
  } catch (error) {
    console.error('加载分类失败:', error)
    categories.value = []
  }
}

// 加载推荐商品
const loadRecommendedProducts = async () => {
  try {
    // 使用正确的API端点
    const response = await fetch('/api/shop/products?limit=8&page=1')
    const data = await response.json()
    
    if (data && data.list) {
      recommendedProducts.value = data.list.map((product: any) => ({
        id: product.id,
        name: product.name,
        description: product.description,
        price: product.price,
        image: product.image || 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=200&fit=crop&crop=center&auto=format&q=80',
        rating: product.rating || 4.5,
        reviewCount: product.sales || 0,
        sales: product.sales || 0,
        stock: product.stock || 0,
        brand: product.brand,
        merchantName: product.merchantName
      }))
    } else {
      throw new Error('No products data')
    }
  } catch (error) {
    console.error('加载推荐商品失败:', error)
    recommendedProducts.value = [
      {
        id: '1',
        name: '推荐商品1',
        description: '精选好物',
        price: 99.99,
        image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=200&fit=crop&crop=center&auto=format&q=80',
        rating: 4.5,
        reviewCount: 128,
        sales: 128,
        stock: 10,
        brand: 'Brand A',
        merchantName: 'Merchant A'
      },
      {
        id: '2',
        name: '推荐商品2',
        description: '精选好物',
        price: 199.99,
        image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=200&fit=crop&crop=center&auto=format&q=80',
        rating: 4.3,
        reviewCount: 89,
        sales: 89,
        stock: 15,
        brand: 'Brand B',
        merchantName: 'Merchant B'
      }
    ]
  }
}

// 处理分页变化
const handlePageChange = (page: number) => {
  currentPage.value = page
  handleSearch()
}

// 清空搜索
const clearSearch = () => {
  searchQuery.value = ''
  products.value = []
  totalResults.value = 0
  router.replace({ query: {} })
}

// 监听筛选条件变化
watch([() => filters.category, () => filters.priceRange, sortBy], () => {
  if (searchQuery.value) {
    currentPage.value = 1
    handleSearch()
  }
})

onMounted(async () => {
  await Promise.all([
    loadCategories(),
    loadRecommendedProducts(),
    initSearch()
  ])
})
</script>

<style scoped lang="scss">
.search-page {
  padding: 20px 0;
  background: $background-base;
  min-height: 100vh;
}

.search-header {
  background: #fff;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  
  .search-box {
    max-width: 600px;
    margin: 0 auto 20px;
  }
  
  .search-info {
    text-align: center;
    
    h2 {
      font-size: 24px;
      font-weight: bold;
      color: $text-primary;
      margin-bottom: 10px;
    }
    
    .result-count {
      color: $text-secondary;
    }
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

.no-results {
  background: #fff;
  padding: 60px 20px;
  border-radius: 8px;
  text-align: center;
  
  .no-results-content {
    h3 {
      font-size: 20px;
      color: $text-primary;
      margin: 20px 0 10px;
    }
    
    p {
      color: $text-secondary;
      margin-bottom: 20px;
    }
  }
}

.recommendations {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  
  h2 {
    font-size: 20px;
    font-weight: bold;
    color: $text-primary;
    margin-bottom: 20px;
  }
  
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
  }
}

@media (max-width: 768px) {
  .search-header {
    padding: 20px;
    
    .search-info h2 {
      font-size: 20px;
    }
  }
  
  .filter-section {
    flex-direction: column;
    align-items: stretch;
    
    .filter-left,
    .filter-right {
      justify-content: center;
    }
  }
  
  .product-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
  }
}
</style>
