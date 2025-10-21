<template>
  <div class="shop-detail">
    <div class="container">
      <!-- 店铺头部信息 -->
      <div class="shop-header">
        <div class="shop-banner" v-if="shopInfo.banner">
          <img :src="shopInfo.banner" :alt="shopInfo.name" />
        </div>
        
        <div class="shop-info">
          <div class="shop-logo">
            <img 
              :src="shopInfo.logo || '/default-shop-logo.png'" 
              :alt="shopInfo.name"
              class="logo-image"
            />
          </div>
          
          <div class="shop-details">
            <h1 class="shop-name">{{ shopInfo.name }}</h1>
            <p class="shop-description" v-if="shopInfo.description">
              {{ shopInfo.description }}
            </p>
            
            <div class="shop-stats">
              <div class="stat-item">
                <span class="stat-value">{{ stats.totalProducts }}</span>
                <span class="stat-label">{{ t('shopDetail.products') }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-value">{{ stats.totalSales }}</span>
                <span class="stat-label">{{ t('shopDetail.sales') }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-value">{{ stats.averageRating }}</span>
                <span class="stat-label">{{ t('shopDetail.rating') }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-value">{{ stats.totalCustomers }}</span>
                <span class="stat-label">{{ t('shopDetail.customers') }}</span>
              </div>
            </div>
            
            <div class="shop-rating" v-if="shopInfo.rating">
              <el-rate 
                v-model="shopInfo.rating" 
                disabled 
                show-score 
                text-color="#ff9900"
                score-template="{value}"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- 筛选和搜索 -->
      <div class="shop-filters">
        <div class="filter-left">
          <el-input
            v-model="searchKeyword"
            :placeholder="t('shopDetail.searchProducts')"
            @keyup.enter="handleSearch"
            clearable
          >
            <template #append>
              <el-button @click="handleSearch" :icon="Search">{{ t('common.search') }}</el-button>
            </template>
          </el-input>
        </div>
        
        <div class="filter-right">
          <el-select 
            v-model="selectedCategory" 
            :placeholder="t('shopDetail.selectCategory')" 
            clearable
            @change="handleCategoryChange"
          >
            <el-option :label="t('shopDetail.allCategories')" value="" />
            <el-option 
              v-for="category in categories" 
              :key="category.id"
              :label="category.name" 
              :value="category.id" 
            />
          </el-select>
          
          <el-select v-model="sortBy" @change="handleSortChange">
            <el-option :label="t('shopDetail.defaultSort')" value="default" />
            <el-option :label="t('shopDetail.sortBySales')" value="sales" />
            <el-option :label="t('shopDetail.sortByPrice')" value="price" />
            <el-option :label="t('shopDetail.sortByNewest')" value="newest" />
          </el-select>
        </div>
      </div>

      <!-- 商品列表 -->
      <div class="products-section">
        <div class="section-header">
          <h2>{{ t('shopDetail.shopProducts') }}</h2>
          <span class="product-count">{{ t('shopDetail.totalProducts', { count: pagination.total }) }}</span>
        </div>
        
        <div class="products-grid" v-loading="loading">
          <div 
            v-for="product in products" 
            :key="product.id"
            class="product-card"
            @click="goToProduct(product)"
          >
            <div class="product-image-container">
              <img :src="product.mainImage" :alt="product.name" class="product-image" />
              <div class="product-badge" v-if="product.isTopDeal">Top Deal</div>
            </div>
            
            <div class="product-info">
              <h3 class="product-name">{{ product.name }}</h3>
              <p class="product-brand" v-if="product.brand">{{ product.brand }}</p>
              
              <div class="product-rating" v-if="product.rating">
                <el-rate v-model="product.rating" disabled size="small" />
                <span class="rating-count">({{ product.sales }} sold)</span>
              </div>
              
              <div class="product-price-section">
                <div class="current-price">RM{{ product.salePrice }}</div>
                <div class="original-price" v-if="product.suggestPrice && product.suggestPrice > product.salePrice">
                  RM{{ product.suggestPrice }}
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 分页 -->
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[12, 24, 48, 96]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handlePageChange"
          @current-change="handlePageChange"
          class="pagination"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { shopApi } from '@/api'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

// 店铺信息
const shopInfo = ref<any>({})
const stats = ref<any>({})
const loading = ref(false)

// 商品列表
const products = ref<any[]>([])
const categories = ref<any[]>([])

// 筛选条件
const searchKeyword = ref('')
const selectedCategory = ref('')
const sortBy = ref('default')

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 24,
  total: 0
})

// 加载店铺信息
const loadShopInfo = async () => {
  try {
    const merchantId = route.params.id as string
    const response = await shopApi.getMerchantShop(merchantId)
    
    if (response.code === 200) {
      shopInfo.value = response.data.shopInfo
      stats.value = response.data.stats
    } else {
      ElMessage.error(response.message || '获取店铺信息失败')
    }
  } catch (error: any) {
    console.error('加载店铺信息失败:', error)
    ElMessage.error('加载店铺信息失败')
  }
}

// 加载商品列表
const loadProducts = async () => {
  try {
    loading.value = true
    const merchantId = route.params.id as string
    
    const response = await shopApi.getMerchantProducts(merchantId, {
      page: pagination.page,
      pageSize: pagination.pageSize,
      categoryId: selectedCategory.value,
      keyword: searchKeyword.value
    })
    
    if (response.code === 200) {
      products.value = response.data.list
      pagination.total = response.data.total
    } else {
      ElMessage.error(response.message || '获取商品列表失败')
    }
  } catch (error: any) {
    console.error('加载商品列表失败:', error)
    ElMessage.error('加载商品列表失败')
  } finally {
    loading.value = false
  }
}

// 加载分类列表
const loadCategories = async () => {
  try {
    const response = await shopApi.getCategories()
    if (response.code === 200) {
      categories.value = response.data
    }
  } catch (error) {
    console.error('加载分类列表失败:', error)
  }
}

// 跳转到商品详情
const goToProduct = (product: any) => {
  router.push(`/product/${product.id}`)
}

// 搜索处理
const handleSearch = () => {
  pagination.page = 1
  loadProducts()
}

// 分类筛选
const handleCategoryChange = () => {
  pagination.page = 1
  loadProducts()
}

// 排序处理
const handleSortChange = () => {
  pagination.page = 1
  loadProducts()
}

// 分页处理
const handlePageChange = () => {
  loadProducts()
}

// 页面加载
onMounted(() => {
  loadShopInfo()
  loadProducts()
  loadCategories()
})
</script>

<style scoped lang="scss">
.shop-detail {
  padding: 20px 0;
  background: #f5f5f5;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.shop-header {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.shop-banner {
  height: 200px;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.shop-info {
  padding: 30px;
  display: flex;
  align-items: flex-start;
  gap: 30px;
}

.shop-logo {
  flex-shrink: 0;
  
  .logo-image {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
}

.shop-details {
  flex: 1;
}

.shop-name {
  font-size: 28px;
  font-weight: bold;
  color: #333;
  margin: 0 0 10px 0;
}

.shop-description {
  font-size: 16px;
  color: #666;
  margin: 0 0 20px 0;
  line-height: 1.5;
}

.shop-stats {
  display: flex;
  gap: 40px;
  margin-bottom: 20px;
}

.stat-item {
  text-align: center;
  
  .stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #ff0050;
    margin-bottom: 5px;
  }
  
  .stat-label {
    font-size: 14px;
    color: #666;
  }
}

.shop-rating {
  display: flex;
  align-items: center;
  gap: 10px;
}

.shop-filters {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-left {
  flex: 1;
  max-width: 400px;
}

.filter-right {
  display: flex;
  gap: 15px;
}

.products-section {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  
  h2 {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin: 0;
  }
  
  .product-count {
    color: #666;
    font-size: 14px;
  }
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.product-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #f0f0f0;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }
}

.product-image-container {
  position: relative;
  height: 200px;
  overflow: hidden;
  
  .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  
  .product-card:hover .product-image {
    transform: scale(1.05);
  }
  
  .product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff0050;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
  }
}

.product-info {
  padding: 15px;
}

.product-name {
  font-size: 16px;
  font-weight: 500;
  color: #333;
  margin: 0 0 8px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-brand {
  font-size: 14px;
  color: #666;
  margin: 0 0 10px 0;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  
  .rating-count {
    font-size: 12px;
    color: #666;
  }
}

.product-price-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.current-price {
  font-size: 18px;
  font-weight: bold;
  color: #ff0050;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 30px;
}

@media (max-width: 768px) {
  .shop-info {
    flex-direction: column;
    text-align: center;
    gap: 20px;
  }
  
  .shop-stats {
    justify-content: center;
    gap: 20px;
  }
  
  .shop-filters {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-right {
    justify-content: center;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 15px;
  }
}
</style>
