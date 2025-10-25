<template>
  <div class="tiktok-shop">
    <!-- Mobile Layout -->
    <template v-if="isMobile">
      <MobileHeader />
      <CategoryNavigation :mainCategories="subCategories" @select-category="handleSubCategoryChange" />
      
      <div class="mobile-content-wrapper">
        <!-- Mobile Category Header (simplified) -->
        <section class="mobile-category-header" v-if="categoryInfo">
          <h1 class="category-title">{{ categoryName }}</h1>
          <p class="category-description">{{ getCategoryDescription(categoryName) }}</p>
        </section>

        <!-- Mobile Filter/Sort (simplified) -->
        <section class="mobile-filter-sort">
          <div class="sort-dropdown">
            <label>{{ $t('category.sortBy') }}:</label>
            <select v-model="sortBy" @change="handleSortChange">
              <option value="default">{{ $t('category.defaultSort') }}</option>
              <option value="price_asc">{{ $t('category.priceAsc') }}</option>
              <option value="price_desc">{{ $t('category.priceDesc') }}</option>
              <option value="sales">{{ $t('category.salesSort') }}</option>
              <option value="rating">{{ $t('category.ratingSort') }}</option>
            </select>
          </div>
        </section>

        <!-- Mobile Product Grid -->
        <section class="mobile-products-section">
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>{{ $t('common.loading') }}</p>
          </div>
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <h3>{{ $t('common.error') }}</h3>
            <p>{{ error }}</p>
            <button @click="loadProducts" class="retry-btn">{{ $t('common.retry') }}</button>
          </div>
          <div v-else-if="products.length === 0" class="empty-state">
            <div class="empty-icon">📦</div>
            <h3>{{ $t('category.noProducts') }}</h3>
            <p>{{ $t('category.noProductsDesc') }}</p>
            <router-link to="/" class="back-btn">{{ $t('category.backToCategories') }}</router-link>
          </div>
          <div v-else class="mobile-products-grid">
            <MobileProductCard
              v-for="product in products"
              :key="product.id"
              :product="formatProductForCard(product)"
              @click="handleProductClick"
            />
          </div>
          <div v-if="totalPages > 1" class="pagination">
            <button 
              :disabled="currentPage === 1" 
              @click="goToPage(currentPage - 1)"
              class="pagination-btn"
            >
              {{ $t('common.previous') }}
            </button>
            <div class="pagination-info">
              {{ $t('common.page') }} {{ currentPage }} {{ $t('common.of') }} {{ totalPages }}
            </div>
            <button 
              :disabled="currentPage === totalPages" 
              @click="goToPage(currentPage + 1)"
              class="pagination-btn"
            >
              {{ $t('common.next') }}
            </button>
          </div>
        </section>
      </div>
      <AppPromotion />
    </template>

    <!-- Desktop Layout -->
    <template v-else>
      <AppHeader />
      <div class="main-layout">
        <AppSidebar />
        <main class="main-content">
        <!-- 面包屑导航 -->
        <div class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">{{ $t('navigation.home') }}</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link to="/categories" class="breadcrumb-link">{{ $t('navigation.categories') }}</router-link>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">{{ categoryName }}</span>
        </div>

        <!-- 分类头部信息 -->
        <section class="category-header-section" v-if="categoryInfo">
          <div class="category-header">
            <div class="category-icon">
              <img :src="categoryInfo.icon" :alt="categoryInfo.name" />
            </div>
            <div class="category-info">
              <h1 class="category-title">{{ categoryInfo.name }}</h1>
              <p class="category-description">{{ getCategoryDescription(categoryInfo.name) }}</p>
              <div class="category-stats">
                <span class="product-count">{{ $t('category.totalProducts', { count: totalProducts }) }}</span>
              </div>
            </div>
          </div>
        </section>

        <!-- 筛选和排序 -->
        <section class="filter-section">
          <div class="filter-left">
            <div class="sort-dropdown">
              <label>{{ $t('category.sortBy') }}:</label>
              <select v-model="sortBy" @change="handleSortChange">
                <option value="default">{{ $t('category.defaultSort') }}</option>
                <option value="price_asc">{{ $t('category.priceAsc') }}</option>
                <option value="price_desc">{{ $t('category.priceDesc') }}</option>
                <option value="sales">{{ $t('category.salesSort') }}</option>
                <option value="rating">{{ $t('category.ratingSort') }}</option>
              </select>
            </div>
          </div>
          <div class="filter-right">
            <div class="view-toggle">
              <button 
                :class="{ active: viewMode === 'grid' }" 
                @click="viewMode = 'grid'"
                title="网格视图"
              >
                <span>⊞</span>
              </button>
              <button 
                :class="{ active: viewMode === 'list' }" 
                @click="viewMode = 'list'"
                title="列表视图"
              >
                <span>☰</span>
              </button>
            </div>
          </div>
        </section>

        <!-- 商品展示区域 -->
        <section class="products-section">
          <div class="section-header">
            <h2 class="section-title">{{ categoryName }} {{ $t('category.productList') }}</h2>
          </div>
          
          <div class="products-container">
            <div v-if="loading" class="loading-state">
              <div class="loading-spinner"></div>
              <p>{{ $t('common.loading') }}</p>
            </div>

            <div v-else-if="error" class="error-state">
              <div class="error-icon">⚠️</div>
              <h3>{{ $t('common.error') }}</h3>
              <p>{{ error }}</p>
              <button @click="loadProducts" class="retry-btn">{{ $t('common.retry') }}</button>
            </div>

            <div v-else-if="products.length === 0" class="empty-state">
              <div class="empty-icon">📦</div>
              <h3>{{ $t('category.noProducts') }}</h3>
              <p>{{ $t('category.noProductsDesc') }}</p>
              <router-link to="/" class="back-btn">{{ $t('category.backToCategories') }}</router-link>
            </div>

            <!-- 网格视图 -->
            <div v-else-if="viewMode === 'grid'" class="products-grid">
              <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="formatProductForCard(product)"
                @product-click="handleProductClick"
              />
            </div>

            <!-- 列表视图 -->
            <div v-else class="products-list">
              <div 
                v-for="product in products" 
                :key="product.id"
                class="product-item"
                @click="handleProductClick(product)"
              >
                <div class="product-image">
                  <img :src="product.mainImage" :alt="getTranslatedProduct(product).name" />
                  <div class="product-badge" v-if="product.isRecommended">推荐</div>
                </div>
                <div class="product-details">
                  <h3 class="product-name">{{ getTranslatedProduct(product).name }}</h3>
                  <p class="product-description">{{ getTranslatedProduct(product).description }}</p>
                  <div class="product-rating" v-if="product.rating > 0">
                    <span class="rating-stars">★★★★★</span>
                    <span class="rating-value">{{ product.rating.toFixed(1) }}</span>
                    <span class="rating-count">({{ product.reviewCount }})</span>
                  </div>
                  <div class="product-merchant">
                    <span class="merchant-name">{{ product.merchantName }}</span>
                  </div>
                </div>
                <div class="product-price-section">
                  <div class="product-price">
                    <span class="current-price">RM {{ formatPrice(product.price) }}</span>
                    <span v-if="product.originalPrice && product.originalPrice > product.price" 
                          class="original-price">RM {{ formatPrice(product.originalPrice) }}</span>
                  </div>
                  <button class="add-to-cart-btn" @click.stop="handleAddToCart(product)">
                    {{ $t('product.addToCart') }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- 分页 -->
          <div v-if="totalPages > 1" class="pagination">
            <button 
              :disabled="currentPage === 1" 
              @click="goToPage(currentPage - 1)"
              class="pagination-btn"
            >
              {{ $t('common.previous') }}
            </button>
            
            <div class="pagination-info">
              {{ $t('common.page') }} {{ currentPage }} {{ $t('common.of') }} {{ totalPages }}
            </div>
            
            <button 
              :disabled="currentPage === totalPages" 
              @click="goToPage(currentPage + 1)"
              class="pagination-btn"
            >
              {{ $t('common.next') }}
            </button>
          </div>
        </section>
        </main>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { productApi } from '@/api'
import { useProductTranslations } from '@/utils/productTranslations'
import { useUserStore } from '@/stores/user'
import { useCartStore } from '@/stores/cart'
import AppHeader from '@/components/layout/AppHeader.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import ProductCard from '@/components/products/ProductCard.vue'
import MobileHeader from '@/components/layout/MobileHeader.vue'
import CategoryNavigation from '@/components/layout/CategoryNavigation.vue'
import MobileProductCard from '@/components/products/MobileProductCard.vue'
import AppPromotion from '@/components/layout/AppPromotion.vue'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const userStore = useUserStore()
const cartStore = useCartStore()

// 使用商品翻译功能
const { getTranslatedProduct } = useProductTranslations()

// 响应式数据
const isMobile = ref(window.innerWidth <= 768)
const categoryInfo = ref<any>(null)
const subCategories = ref<any[]>([]) // For horizontal subcategory navigation
const products = ref<any[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const sortBy = ref('default')
const viewMode = ref<'grid' | 'list'>('grid')
const currentPage = ref(1)
const pageSize = ref(12)
const totalProducts = ref(0)

// 计算属性
const categoryId = computed(() => route.params.id as string)
const categoryName = computed(() => categoryInfo.value?.name || '分类')

const totalPages = computed(() => Math.ceil(totalProducts.value / pageSize.value))

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, start + 4)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// 获取分类描述
const getCategoryDescription = (categoryName: string) => {
  const descriptions: Record<string, string> = {
    'Womenswear & Underwear': '时尚女装与内衣',
    'Phones & Electronics': '手机数码与电子产品',
    'Fashion Accessories': '时尚配饰与珠宝',
    'Menswear & Underwear': '男装与内衣',
    'Home Supplies': '家居用品与装饰',
    'Beauty & Personal Care': '美妆个护与健康',
    'Shoes': '鞋类与运动鞋',
    'Sports & Outdoor': '运动户外与健身',
    'Luggage & Bags': '箱包与旅行用品',
    'Toys & Hobbies': '玩具与兴趣爱好',
    'Automotive & Motorcycle': '汽车摩托车用品',
    'Kids Fashion': '儿童时尚与服装',
    'Kitchenware': '厨具与厨房用品',
    'Computers & Office Equipment': '电脑办公设备',
    'Baby & Maternity': '婴儿母婴用品',
    'Tools & Hardware': '工具五金与维修',
    'Textiles & Soft Furnishings': '纺织品与软装',
    'Pet Supplies': '宠物用品与护理',
    'Home Improvement': '家装建材与工具',
    'Food & Beverages': '食品饮料与生鲜',
    'Muslim Fashion': '穆斯林时尚服装',
    'Books, Magazines & Audio': '图书杂志与音频',
    'Household Appliances': '家用电器与设备',
    'Health': '健康医疗与保健',
    'Furniture': '家具与家居装饰',
    'Jewelry Accessories & Derivatives': '珠宝配饰与衍生品',
    'Collectibles': '收藏品与艺术品',
    'Pre-Owned': '二手商品与闲置'
  }
  
  return descriptions[categoryName] || '精选好物，品质保证'
}

// 格式化价格
const formatPrice = (price: number) => {
  return price.toFixed(2)
}

// 格式化商品数据以适配ProductCard组件
const formatProductForCard = (product: any) => {
  const translatedProduct = getTranslatedProduct(product)
  return {
    id: product.id || '',
    name: translatedProduct.name || 'Unknown Product',
    description: translatedProduct.description || '',
    price: Number(product.salePrice) || 0,
    originalPrice: product.discountPrice ? Number(product.discountPrice) : undefined,
    image: product.mainImage || '/placeholder-image.jpg',
    rating: 4.5, // 默认评分，因为数据库中没有rating字段
    sales: Number(product.sales) || 0,
    stock: Number(product.stock) || 0,
    brand: product.brand || '',
    merchantName: product.merchantName || '', // 使用真实的商家名称
    merchantId: product.merchantId || '',
    badge: undefined // 暂时不显示推荐标签
  }
}

// 加载分类信息
const loadCategoryInfo = async () => {
  try {
    const response = await productApi.getCategories()
    const allCategories = response.data || []
    const currentCategory = allCategories.find((cat: any) => String(cat.id) === categoryId.value)
    categoryInfo.value = currentCategory

    // Load subcategories if available
    if (currentCategory && currentCategory.children && currentCategory.children.length > 0) {
      subCategories.value = currentCategory.children.map((child: any) => ({
        id: String(child.id),
        name: child.name,
        icon: child.icon // Assuming subcategories also have icons
      }))
    } else {
      subCategories.value = []
    }
  } catch (err) {
    console.error('加载分类信息失败:', err)
  }
}

// 加载商品
const loadProducts = async () => {
  loading.value = true
  error.value = null
  
  try {
    // 使用商城真实数据接口
    const response = await fetch(`/api/shop/products?categoryId=${encodeURIComponent(categoryId.value)}&page=${currentPage.value}&pageSize=${pageSize.value}`)
    const data = await response.json()
    
    products.value = data?.list || []
    totalProducts.value = data?.total || products.value.length || 0
  } catch (err: any) {
    console.error('加载商品失败:', err)
    error.value = err.message || '加载商品失败'
  } finally {
    loading.value = false
  }
}

// 处理排序变化
const handleSortChange = () => {
  currentPage.value = 1
  loadProducts()
}

// 处理子分类变化
const handleSubCategoryChange = (subCategoryId: string) => {
  // Implement filtering by subcategory if needed
  console.log('Selected subcategory:', subCategoryId)
  // For now, just reload products for the main category
  currentPage.value = 1
  loadProducts()
}

// 移动端检测
const handleResize = () => {
  isMobile.value = window.innerWidth <= 768
}

// 处理商品点击
const handleProductClick = (product: any) => {
  router.push(`/product/${product.id}`)
}

// 处理加入购物车
const handleAddToCart = async (product: any) => {
  try {
    console.log('加入购物车:', product)
    
    // 检查用户是否登录
    if (!userStore.isLoggedIn) {
      ElMessage.warning('请先登录')
      router.push('/login')
      return
    }
    
    // 调用购物车API
    await cartStore.addToCart(String(product.id), 1)
    
    ElMessage.success('商品已加入购物车')
    
    // 更新购物车数量显示
    await cartStore.fetchCart()
    
  } catch (error: any) {
    console.error('加入购物车失败:', error)
    ElMessage.error(error.message || '加入购物车失败，请重试')
  }
}

// 分页处理
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    loadProducts()
  }
}

// 监听分类ID变化
watch(categoryId, () => {
  if (categoryId.value) {
    loadCategoryInfo()
    loadProducts()
  }
}, { immediate: true })

// 组件挂载
onMounted(() => {
  window.addEventListener('resize', handleResize)
  handleResize() // Set initial mobile state
  if (categoryId.value) {
    loadCategoryInfo()
    loadProducts()
  }
})
</script>

<style scoped lang="scss">
@import "@/styles/variables.scss";

.tiktok-shop {
  min-height: 100vh;
  background: #000; // Default to black for mobile
}

.main-layout {
  display: flex;
  min-height: calc(100vh - 80px);
}

.main-content {
  flex: 1;
  padding: 20px;
  margin-left: 250px;
  background: $background-base;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  font-size: 14px;
  
  .breadcrumb-link {
    color: $primary-color;
    text-decoration: none;
    
    &:hover {
      text-decoration: underline;
    }
  }
  
  .breadcrumb-separator {
    margin: 0 8px;
    color: $text-secondary;
  }
  
  .breadcrumb-current {
    color: $text-primary;
    font-weight: 500;
  }
}

.category-header-section {
  margin-bottom: 30px;
}

.category-header {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 30px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  
  .category-icon {
    width: 120px;
    height: 120px;
    border-radius: 16px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  
  .category-info {
    flex: 1;
    
    .category-title {
      font-size: 28px;
      font-weight: bold;
      color: $text-primary;
      margin: 0 0 10px 0;
    }
    
    .category-description {
      font-size: 16px;
      color: $text-secondary;
      margin: 0 0 15px 0;
    }
    
    .category-stats {
      .product-count {
        background: $primary-color;
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
      }
    }
  }
}

.filter-section {
  background: #fff;
  padding: 20px 30px;
  border-radius: 12px;
  margin-bottom: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  
  .filter-left {
    display: flex;
    align-items: center;
    gap: 20px;
    
    .sort-dropdown {
      display: flex;
      align-items: center;
      gap: 10px;
      
      label {
        font-size: 14px;
        color: $text-primary;
        font-weight: 500;
      }
      
      select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
        cursor: pointer;
        
        &:focus {
          outline: none;
          border-color: $primary-color;
        }
      }
    }
  }
  
  .filter-right {
    .view-toggle {
      display: flex;
      gap: 5px;
      
      button {
        padding: 8px 12px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        
        &:first-child {
          border-radius: 6px 0 0 6px;
        }
        
        &:last-child {
          border-radius: 0 6px 6px 0;
        }
        
        &.active {
          background: $primary-color;
          color: #fff;
          border-color: $primary-color;
        }
        
        &:hover:not(.active) {
          background: #f5f5f5;
        }
        
        span {
          font-size: 16px;
        }
      }
    }
  }
}

.products-section {
  margin-bottom: 30px;
  
  .section-header {
    margin-bottom: 20px;
    
    .section-title {
      font-size: 24px;
      font-weight: bold;
      color: $text-primary;
      margin: 0;
    }
  }
  
  .products-container {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  }
  
  .loading-state, .error-state, .empty-state {
    text-align: center;
    padding: 60px 40px;
    
    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid $primary-color;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    
    .error-icon, .empty-icon {
      font-size: 64px;
      margin-bottom: 20px;
      opacity: 0.5;
    }
    
    h3 {
      font-size: 20px;
      font-weight: 600;
      color: $text-primary;
      margin-bottom: 10px;
    }
    
    p {
      font-size: 16px;
      color: $text-secondary;
      margin-bottom: 20px;
    }
    
    .retry-btn, .back-btn {
      background: $primary-color;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      
      &:hover {
        background: darken($primary-color, 10%);
      }
    }
  }
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.product-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }
  
  .product-image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .product-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: $primary-color;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
    }
  }
  
  .product-info {
    padding: 20px;
    
    .product-name {
      font-size: 16px;
      font-weight: 600;
      color: $text-primary;
      margin: 0 0 10px 0;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-price {
      margin-bottom: 10px;
      
      .current-price {
        font-size: 18px;
        font-weight: bold;
        color: $primary-color;
        margin-right: 8px;
      }
      
      .original-price {
        font-size: 14px;
        color: $text-secondary;
        text-decoration: line-through;
      }
    }
    
    .product-rating {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 10px;
      
      .rating-stars {
        color: #ffc107;
        font-size: 14px;
      }
      
      .rating-value {
        font-size: 14px;
        color: $text-primary;
        font-weight: 500;
      }
      
      .rating-count {
        font-size: 12px;
        color: $text-secondary;
      }
    }
    
    .product-merchant {
      .merchant-name {
        font-size: 12px;
        color: $text-secondary;
      }
    }
  }
}

.products-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 30px;
}

.product-item {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  gap: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }
  
  .product-image {
    position: relative;
    width: 120px;
    height: 120px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .product-badge {
      position: absolute;
      top: 5px;
      left: 5px;
      background: $primary-color;
      color: #fff;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: 500;
    }
  }
  
  .product-details {
    flex: 1;
    
    .product-name {
      font-size: 18px;
      font-weight: 600;
      color: $text-primary;
      margin: 0 0 8px 0;
    }
    
    .product-description {
      font-size: 14px;
      color: $text-secondary;
      margin: 0 0 10px 0;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-rating {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 10px;
      
      .rating-stars {
        color: #ffc107;
        font-size: 14px;
      }
      
      .rating-value {
        font-size: 14px;
        color: $text-primary;
        font-weight: 500;
      }
      
      .rating-count {
        font-size: 12px;
        color: $text-secondary;
      }
    }
    
    .product-merchant {
      .merchant-name {
        font-size: 12px;
        color: $text-secondary;
      }
    }
  }
  
  .product-price-section {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-end;
    min-width: 150px;
    
    .product-price {
      margin-bottom: 15px;
      
      .current-price {
        font-size: 20px;
        font-weight: bold;
        color: $primary-color;
        display: block;
        margin-bottom: 5px;
      }
      
      .original-price {
        font-size: 14px;
        color: $text-secondary;
        text-decoration: line-through;
      }
    }
    
    .add-to-cart-btn {
      background: $primary-color;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
      
      &:hover {
        background: darken($primary-color, 10%);
      }
    }
  }
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  margin-top: 30px;
  
  .pagination-btn {
    padding: 10px 20px;
    border: 1px solid #ddd;
    background: #fff;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 14px;
    
    &:hover:not(:disabled) {
      background: #f5f5f5;
      border-color: $primary-color;
    }
    
    &:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
  }
  
  .pagination-info {
    font-size: 14px;
    color: $text-secondary;
    font-weight: 500;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 15px;
  }
  
  .category-header {
    flex-direction: column;
    text-align: center;
    gap: 20px;
    padding: 20px;
    
    .category-icon {
      width: 100px;
      height: 100px;
    }
    
    .category-info {
      .category-title {
        font-size: 24px;
      }
    }
  }
  
  .filter-section {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
    padding: 15px;
    
    .filter-left {
      justify-content: center;
    }
    
    .filter-right {
      display: flex;
      justify-content: center;
    }
  }
  
  .products-section {
    .products-container {
      padding: 15px;
    }
    
    .products-grid {
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 15px;
    }
  }
  
  .product-item {
    flex-direction: column;
    text-align: center;
    
    .product-image {
      width: 100%;
      height: 200px;
    }
    
    .product-price-section {
      align-items: center;
      min-width: auto;
    }
  }
}

// Mobile-specific styles
.mobile-content-wrapper {
  padding-top: 120px; // Space for fixed MobileHeader (60px) + CategoryNavigation (approx 60px)
  padding-bottom: 80px; // Space for AppPromotion
  background: #000;
  min-height: calc(100vh - 120px - 80px);
}

.mobile-category-header {
  padding: 16px 12px;
  background: #000;
  color: #fff;
  text-align: center;

  .category-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 4px;
  }

  .category-description {
    font-size: 12px;
    color: #ccc;
  }
}

.mobile-filter-sort {
  display: flex;
  justify-content: flex-end; // Align to right
  padding: 8px 12px;
  background: #000;
  border-bottom: 1px solid #333; // Subtle separator

  .sort-dropdown {
    display: flex;
    align-items: center;
    gap: 8px;

    label {
      font-size: 12px;
      color: #ccc;
    }

    select {
      padding: 4px 8px;
      border: 1px solid #333;
      border-radius: 4px;
      background: #1a1a1a;
      color: #fff;
      font-size: 12px;
      cursor: pointer;
      -webkit-appearance: none; // Remove default arrow
      -moz-appearance: none;
      appearance: none;
      background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-6.5%200-12.3%203.2-16.1%208.1-3.8%204.9-4.9%2011-3.1%2017.1l133.9%20163.1c4.6%205.6%2011.9%208.6%2019.2%208.6s14.6-3%2019.2-8.6L287%2086.5c3.1-6.1%202-12.2-3.1-17.1z%22%2F%3E%3C%2Fsvg%3E');
      background-repeat: no-repeat;
      background-position: right 8px center;
      background-size: 10px;

      &:focus {
        outline: none;
        border-color: #ff0050;
      }
    }
  }
}

.mobile-products-section {
  padding: 12px;
  background: #000;

  .mobile-products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); // Two columns
    gap: 8px; // Smaller gap for mobile
  }
}

// Hide desktop components on mobile
@media (max-width: 768px) {
  .main-layout, .breadcrumb, .category-header-section, .filter-section, .products-section {
    display: none;
  }
}

// Hide mobile components on desktop
@media (min-width: 769px) {
  .mobile-header, .category-navigation, .mobile-content-wrapper, .app-promotion {
    display: none;
  }
  .tiktok-shop {
    background: $background-base; // Desktop uses light background
  }
}
</style>
