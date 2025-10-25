<template>
  <div class="mobile-shop-detail">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="header-title">{{ shopInfo.name }}</h1>
      <button class="share-btn" @click="shareShop">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="2"/>
          <circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
          <circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="2"/>
          <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke="currentColor" stroke-width="2"/>
          <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
    </div>

    <!-- Shop Content -->
    <div class="shop-content">
      <!-- Loading State -->
      <MobileLoading 
        :loading="isLoading"
        type="skeleton"
        skeleton-type="card"
        :skeleton-count="3"
        message="加载店铺信息中..."
      />
      
      <!-- Error State -->
      <MobileError 
        :show="hasError"
        type="server"
        title="加载失败"
        message="无法加载店铺信息，请重试"
        @retry="handleRetry"
      />
      
      <!-- Shop Header -->
      <div v-if="!isLoading && !hasError" class="shop-header">
        <div class="shop-avatar">
          <img :src="shopInfo.avatar" :alt="shopInfo.name" />
        </div>
        <div class="shop-info">
          <h2 class="shop-name">{{ shopInfo.name }}</h2>
          <div class="shop-rating">
            <div class="rating-stars">
              <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= shopInfo.rating }">★</span>
            </div>
            <span class="rating-value">{{ shopInfo.rating.toFixed(1) }}</span>
            <span class="rating-count">({{ shopInfo.ratingCount }} {{ t('shop.reviews') }})</span>
          </div>
          <div class="shop-stats">
            <div class="stat-item">
              <span class="stat-number">{{ shopInfo.followers }}</span>
              <span class="stat-label">{{ t('shop.followers') }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ shopInfo.products }}</span>
              <span class="stat-label">{{ t('shop.products') }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ shopInfo.sales }}</span>
              <span class="stat-label">{{ t('shop.sales') }}</span>
            </div>
          </div>
        </div>
        <div class="shop-actions">
          <button class="follow-btn" :class="{ 'following': isFollowing }" @click="toggleFollow">
            {{ isFollowing ? t('shop.following') : t('shop.follow') }}
          </button>
          <button class="contact-btn" @click="contactShop">
            {{ t('shop.contact') }}
          </button>
        </div>
      </div>

      <!-- Shop Description -->
      <div class="shop-description">
        <p>{{ shopInfo.description }}</p>
        <div class="shop-tags">
          <span v-for="tag in shopInfo.tags" :key="tag" class="shop-tag">{{ tag }}</span>
        </div>
      </div>

      <!-- Shop Tabs -->
      <div class="shop-tabs">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          class="tab-btn"
          :class="{ 'active': activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
          <span v-if="tab.count > 0" class="tab-count">{{ tab.count }}</span>
        </button>
      </div>

      <!-- Tab Content -->
      <div v-if="!isLoading && !hasError" class="tab-content">
        <!-- Products Tab -->
        <div v-if="activeTab === 'products'" class="products-tab">
          <!-- Filter Bar -->
          <div class="filter-bar">
            <button class="filter-btn" @click="showProductFilters = true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              {{ t('shop.filters') }}
            </button>
            <div class="sort-dropdown">
              <select v-model="productSortBy" @change="sortProducts">
                <option value="default">{{ t('shop.defaultSort') }}</option>
                <option value="price_asc">{{ t('shop.priceAsc') }}</option>
                <option value="price_desc">{{ t('shop.priceDesc') }}</option>
                <option value="rating">{{ t('shop.ratingSort') }}</option>
                <option value="sales">{{ t('shop.salesSort') }}</option>
              </select>
            </div>
          </div>

          <!-- Products Grid -->
          <div class="products-grid">
            <div 
              v-for="product in shopProducts" 
              :key="product.id"
              class="product-item"
              @click="goToProduct(product)"
            >
              <div class="product-image">
                <img :src="product.image" :alt="product.name" />
                <div v-if="product.discount" class="discount-badge">-{{ product.discount }}%</div>
              </div>
              <div class="product-info">
                <h4 class="product-name">{{ product.name }}</h4>
                <div class="product-rating">
                  <span class="rating-stars">★★★★★</span>
                  <span class="rating-count">({{ product.ratingCount }})</span>
                </div>
                <div class="product-price">
                  <span class="current-price">RM{{ formatPrice(product.price) }}</span>
                  <span v-if="product.originalPrice" class="original-price">RM{{ formatPrice(product.originalPrice) }}</span>
                </div>
                <div class="product-sales">{{ t('shop.sold', { count: product.sales }) }}</div>
              </div>
            </div>
          </div>

          <!-- Load More -->
          <div v-if="hasMoreProducts" class="load-more">
            <button class="load-more-btn" @click="loadMoreProducts" :disabled="loadingProducts">
              <span v-if="loadingProducts">{{ t('shop.loading') }}</span>
              <span v-else>{{ t('shop.loadMore') }}</span>
            </button>
          </div>
        </div>

        <!-- Reviews Tab -->
        <div v-if="activeTab === 'reviews'" class="reviews-tab">
          <!-- Reviews Summary -->
          <div class="reviews-summary">
            <div class="summary-rating">
              <div class="rating-score">{{ shopInfo.rating.toFixed(1) }}</div>
              <div class="rating-stars">
                <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= shopInfo.rating }">★</span>
              </div>
              <div class="rating-text">{{ t('shop.basedOnReviews', { count: shopInfo.ratingCount }) }}</div>
            </div>
            <div class="rating-breakdown">
              <div v-for="i in 5" :key="i" class="rating-bar">
                <span class="rating-label">{{ 6 - i }}★</span>
                <div class="rating-progress">
                  <div class="progress-bar" :style="{ width: `${getRatingPercentage(6 - i)}%` }"></div>
                </div>
                <span class="rating-percentage">{{ getRatingPercentage(6 - i) }}%</span>
              </div>
            </div>
          </div>

          <!-- Reviews List -->
          <div class="reviews-list">
            <div v-for="review in shopReviews" :key="review.id" class="review-item">
              <div class="review-header">
                <div class="reviewer-info">
                  <div class="reviewer-avatar">
                    <img :src="review.userAvatar" :alt="review.userName" />
                  </div>
                  <div class="reviewer-details">
                    <div class="reviewer-name">{{ review.userName }}</div>
                    <div class="review-date">{{ formatDate(review.createdAt) }}</div>
                  </div>
                </div>
                <div class="review-rating">
                  <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= review.rating }">★</span>
                </div>
              </div>
              <div class="review-content">
                <p>{{ review.content }}</p>
                <div v-if="review.images && review.images.length > 0" class="review-images">
                  <img 
                    v-for="(image, index) in review.images" 
                    :key="index"
                    :src="image" 
                    :alt="`Review image ${index + 1}`"
                    class="review-image"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- About Tab -->
        <div v-if="activeTab === 'about'" class="about-tab">
          <div class="about-section">
            <h3>{{ t('shop.aboutShop') }}</h3>
            <p>{{ shopInfo.about }}</p>
          </div>

          <div class="about-section">
            <h3>{{ t('shop.shopInfo') }}</h3>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">{{ t('shop.established') }}</span>
                <span class="info-value">{{ shopInfo.established }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ t('shop.location') }}</span>
                <span class="info-value">{{ shopInfo.location }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ t('shop.responseTime') }}</span>
                <span class="info-value">{{ shopInfo.responseTime }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ t('shop.shippingTime') }}</span>
                <span class="info-value">{{ shopInfo.shippingTime }}</span>
              </div>
            </div>
          </div>

          <div class="about-section">
            <h3>{{ t('shop.policies') }}</h3>
            <div class="policies-list">
              <div class="policy-item">
                <div class="policy-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="policy-content">
                  <h4>{{ t('shop.returnPolicy') }}</h4>
                  <p>{{ shopInfo.returnPolicy }}</p>
                </div>
              </div>
              <div class="policy-item">
                <div class="policy-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="policy-content">
                  <h4>{{ t('shop.shippingPolicy') }}</h4>
                  <p>{{ shopInfo.shippingPolicy }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Filter Modal -->
    <div v-if="showProductFilters" class="filter-modal" @click="showProductFilters = false">
      <div class="filter-content" @click.stop>
        <div class="filter-header">
          <h3>{{ t('shop.filters') }}</h3>
          <button class="close-btn" @click="showProductFilters = false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        
        <div class="filter-options">
          <div class="filter-group">
            <h4>{{ t('shop.priceRange') }}</h4>
            <div class="price-range">
              <input v-model="priceRange.min" type="number" :placeholder="t('shop.minPrice')" class="price-input" />
              <span class="price-separator">-</span>
              <input v-model="priceRange.max" type="number" :placeholder="t('shop.maxPrice')" class="price-input" />
            </div>
          </div>

          <div class="filter-group">
            <h4>{{ t('shop.category') }}</h4>
            <select v-model="selectedCategory" class="category-select">
              <option value="">{{ t('shop.allCategories') }}</option>
              <option v-for="category in productCategories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="filter-group">
            <h4>{{ t('shop.rating') }}</h4>
            <div class="rating-filter">
              <label v-for="rating in [5, 4, 3, 2, 1]" :key="rating" class="rating-option">
                <input 
                  type="radio" 
                  :value="rating" 
                  v-model="selectedRating"
                  name="rating"
                />
                <span class="rating-stars">
                  <span v-for="star in rating" :key="star">⭐</span>
                  <span v-for="star in (5 - rating)" :key="star" class="empty-star">☆</span>
                </span>
                <span class="rating-text">{{ rating }}+ {{ t('shop.stars') }}</span>
              </label>
            </div>
          </div>

          <div class="filter-group">
            <label class="checkbox-option">
              <input type="checkbox" v-model="inStockOnly" />
              <span>{{ t('shop.inStockOnly') }}</span>
            </label>
          </div>
        </div>

        <div class="filter-actions">
          <button class="reset-btn" @click="resetFilters">{{ t('shop.reset') }}</button>
          <button class="apply-btn" @click="applyProductFilters">{{ t('shop.apply') }}</button>
        </div>
      </div>
    </div>
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileLoading from '@/components/common/MobileLoading.vue'
import MobileError from '@/components/common/MobileError.vue'
import { shopApi } from '@/api'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const activeTab = ref('products')
const isFollowing = ref(false)
const showProductFilters = ref(false)
const loadingProducts = ref(false)
const hasMoreProducts = ref(true)
const productSortBy = ref('default')
const isLoading = ref(false)
const hasError = ref(false)
const shopId = computed(() => route.params.id as string)

// 商家信息
const shopInfo = ref({
  id: 1,
  name: 'TechStore Pro',
  avatar: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200&h=200&fit=crop',
  rating: 4.8,
  ratingCount: 1250,
  followers: 12500,
  products: 156,
  sales: 8900,
  description: 'Your trusted source for premium electronics and tech accessories. We provide high-quality products with excellent customer service.',
  tags: ['Electronics', 'Gadgets', 'Accessories', 'Premium'],
  about: 'TechStore Pro has been serving customers for over 5 years, specializing in the latest technology and electronic devices. We are committed to providing authentic products and exceptional customer service.',
  established: '2019',
  location: 'Kuala Lumpur, Malaysia',
  responseTime: '< 2 hours',
  shippingTime: '1-3 business days',
  returnPolicy: '30-day return policy for all products',
  shippingPolicy: 'Free shipping on orders over RM100'
})

// 标签页
const tabs = computed(() => [
  { id: 'products', label: t('shop.products'), count: shopInfo.value.products },
  { id: 'reviews', label: t('shop.reviews'), count: shopInfo.value.ratingCount },
  { id: 'about', label: t('shop.about'), count: 0 }
])

// 商家商品
const shopProducts = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro Max',
    image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=200&h=200&fit=crop',
    price: 4999.99,
    originalPrice: 5299.99,
    discount: 6,
    rating: 4.9,
    ratingCount: 1250,
    sales: 890
  },
  {
    id: 2,
    name: 'MacBook Pro M3',
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=200&h=200&fit=crop',
    price: 8999.99,
    originalPrice: null,
    discount: 0,
    rating: 4.8,
    ratingCount: 856,
    sales: 234
  },
  {
    id: 3,
    name: 'AirPods Pro 2',
    image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=200&h=200&fit=crop',
    price: 899.99,
    originalPrice: 999.99,
    discount: 10,
    rating: 4.7,
    ratingCount: 2100,
    sales: 1567
  }
])

// 商家评价
const shopReviews = ref([
  {
    id: 1,
    userName: 'John Doe',
    userAvatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop',
    rating: 5,
    content: 'Excellent service and fast delivery. The product quality is outstanding!',
    images: ['https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=100&h=100&fit=crop'],
    createdAt: '2024-01-15T10:30:00Z'
  },
  {
    id: 2,
    userName: 'Jane Smith',
    userAvatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop',
    rating: 4,
    content: 'Good product, arrived on time. Customer service was helpful.',
    images: [],
    createdAt: '2024-01-14T15:20:00Z'
  }
])

// 商品分类
const productCategories = ref([
  { id: 1, name: 'Smartphones' },
  { id: 2, name: 'Laptops' },
  { id: 3, name: 'Accessories' },
  { id: 4, name: 'Audio' }
])

// 商品过滤器
const productFilters = ref({
  minPrice: '',
  maxPrice: '',
  category: ''
})

// 筛选相关变量
const allProducts = ref([...products.value]) // 保存所有商品用于筛选
const priceRange = ref({
  min: '',
  max: ''
})
const selectedCategory = ref('')
const selectedRating = ref(0)
const inStockOnly = ref(false)

// 方法
const goBack = () => {
  router.back()
}

const shareShop = async () => {
  try {
    const shopUrl = `${window.location.origin}/mobile/shop/${route.params.id}`
    const shareData = {
      title: shopInfo.value.name,
      text: `Check out this amazing shop: ${shopInfo.value.name}`,
      url: shopUrl
    }

    // 检查是否支持Web Share API
    if (navigator.share) {
      await navigator.share(shareData)
      ElMessage.success(t('shop.shareSuccess'))
    } else {
      // 降级到复制链接
      await navigator.clipboard.writeText(shopUrl)
      ElMessage.success(t('shop.linkCopied'))
    }
  } catch (error: any) {
    if (error.name !== 'AbortError') {
      console.error('分享失败:', error)
      ElMessage.error(t('shop.shareFailed'))
    }
  }
}

const toggleFollow = () => {
  isFollowing.value = !isFollowing.value
  ElMessage.success(isFollowing.value ? t('shop.followed') : t('shop.unfollowed'))
}

const contactShop = () => {
  ElMessage.info('联系商家功能开发中...')
}

const goToProduct = (product: any) => {
  router.push(`/mobile/product/${product.id}`)
}

const sortProducts = () => {
  if (!products.value.length) return
  
  const sortedProducts = [...products.value]
  
  switch (productSortBy.value) {
    case 'price_asc':
      sortedProducts.sort((a, b) => a.price - b.price)
      break
    case 'price_desc':
      sortedProducts.sort((a, b) => b.price - a.price)
      break
    case 'sales_desc':
      sortedProducts.sort((a, b) => (b.sales || 0) - (a.sales || 0))
      break
    case 'rating_desc':
      sortedProducts.sort((a, b) => (b.rating || 0) - (a.rating || 0))
      break
    case 'newest':
      sortedProducts.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
      break
    default:
      // 默认排序（综合排序）
      sortedProducts.sort((a, b) => {
        const scoreA = (a.sales || 0) * 0.4 + (a.rating || 0) * 0.6
        const scoreB = (b.sales || 0) * 0.4 + (b.rating || 0) * 0.6
        return scoreB - scoreA
      })
  }
  
  products.value = sortedProducts
  ElMessage.success(t('shop.sortSuccess'))
}

const loadMoreProducts = () => {
  loadingProducts.value = true
  // 模拟加载更多商品
  setTimeout(() => {
    loadingProducts.value = false
    hasMoreProducts.value = false
  }, 1000)
}

const resetProductFilters = () => {
  productFilters.value = {
    minPrice: '',
    maxPrice: '',
    category: ''
  }
}

const resetFilters = () => {
  priceRange.value = { min: '', max: '' }
  selectedCategory.value = ''
  selectedRating.value = 0
  inStockOnly.value = false
  products.value = [...allProducts.value]
  showProductFilters.value = false
  ElMessage.success(t('shop.filterReset'))
}

const applyProductFilters = () => {
  if (!allProducts.value.length) return
  
  let filteredProducts = [...allProducts.value]
  
  // 价格筛选
  if (priceRange.value.min !== '' || priceRange.value.max !== '') {
    const minPrice = priceRange.value.min ? parseFloat(priceRange.value.min) : 0
    const maxPrice = priceRange.value.max ? parseFloat(priceRange.value.max) : Infinity
    
    filteredProducts = filteredProducts.filter(product => {
      const price = product.price || 0
      return price >= minPrice && price <= maxPrice
    })
  }
  
  // 分类筛选
  if (selectedCategory.value) {
    filteredProducts = filteredProducts.filter(product => 
      product.categoryId === selectedCategory.value
    )
  }
  
  // 评分筛选
  if (selectedRating.value) {
    filteredProducts = filteredProducts.filter(product => 
      (product.rating || 0) >= selectedRating.value
    )
  }
  
  // 库存筛选
  if (inStockOnly.value) {
    filteredProducts = filteredProducts.filter(product => 
      (product.stock || 0) > 0
    )
  }
  
  products.value = filteredProducts
  showProductFilters.value = false
  ElMessage.success(t('shop.filterSuccess'))
}

const getRatingPercentage = (rating: number) => {
  // 模拟评分分布
  const distribution = { 5: 70, 4: 20, 3: 7, 2: 2, 1: 1 }
  return distribution[rating as keyof typeof distribution] || 0
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('zh-CN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const loadShopData = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    
    // 获取店铺数据
    const shopResponse = await shopApi.getShopDetail(shopId.value)
    shopInfo.value = { ...shopInfo.value, ...shopResponse.data }
    
    // 获取店铺商品
    const productsResponse = await shopApi.getShopProducts(shopId.value, {
      page: 1,
      limit: 20
    })
    shopProducts.value = productsResponse.data || []
    
    console.log('店铺数据加载完成:', {
      shop: shopInfo.value?.name,
      products: shopProducts.value.length
    })
  } catch (error: any) {
    console.error('加载店铺数据失败:', error)
    hasError.value = true
    ElMessage.error('加载店铺信息失败')
  } finally {
    isLoading.value = false
  }
}

const handleRetry = () => {
  hasError.value = false
  loadShopData()
}

onMounted(() => {
  console.log('Mobile shop detail page mounted')
  loadShopData()
})
</script>

<style scoped lang="scss">
.mobile-shop-detail {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 80px; // 为底部导航栏留出空间
}

.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #000;
  border-bottom: 1px solid #333;
  position: sticky;
  top: 0;
  z-index: 100;

  .back-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }

  .header-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    flex: 1;
    text-align: center;
    padding: 0 20px;
  }

  .share-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }
}

.shop-content {
  padding: 20px;
}

.shop-header {
  background: #1a1a1a;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 16px;

  .shop-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  .shop-info {
    flex: 1;

    .shop-name {
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 8px 0;
      color: #fff;
    }

    .shop-rating {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;

      .rating-stars {
        .star {
          color: #666;
          font-size: 14px;

          &.filled {
            color: #ffc107;
          }
        }
      }

      .rating-value {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
      }

      .rating-count {
        font-size: 12px;
        color: #ccc;
      }
    }

    .shop-stats {
      display: flex;
      gap: 16px;

      .stat-item {
        text-align: center;

        .stat-number {
          display: block;
          font-size: 16px;
          font-weight: 600;
          color: #fff;
        }

        .stat-label {
          font-size: 12px;
          color: #ccc;
        }
      }
    }
  }

  .shop-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;

    .follow-btn {
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;

      &.following {
        background: #333;
        color: #ccc;
      }

      &:hover:not(.following) {
        background: #e6004a;
      }
    }

    .contact-btn {
      background: #1a1a1a;
      color: #fff;
      border: 1px solid #333;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;

      &:hover {
        background: #333;
      }
    }
  }
}

.shop-description {
  background: #1a1a1a;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;

  p {
    font-size: 14px;
    color: #ccc;
    margin: 0 0 12px 0;
    line-height: 1.5;
  }

  .shop-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;

    .shop-tag {
      background: #333;
      color: #fff;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
    }
  }
}

.shop-tabs {
  display: flex;
  background: #1a1a1a;
  border-radius: 12px;
  padding: 4px;
  margin-bottom: 20px;

  .tab-btn {
    flex: 1;
    background: none;
    border: none;
    color: #ccc;
    padding: 12px 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;

    &.active {
      background: #ff0050;
      color: #fff;
    }

    .tab-count {
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 10px;
      min-width: 18px;
      text-align: center;
    }
  }
}

.tab-content {
  .products-tab {
    .filter-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;

      .filter-btn {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;

        &:hover {
          background: #333;
        }
      }

      .sort-dropdown {
        select {
          background: #1a1a1a;
          border: 1px solid #333;
          border-radius: 8px;
          padding: 8px 12px;
          color: #fff;
          font-size: 14px;
          cursor: pointer;

          &:focus {
            outline: none;
            border-color: #ff0050;
          }

          option {
            background: #1a1a1a;
            color: #fff;
          }
        }
      }
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
      margin-bottom: 20px;

      .product-item {
        background: #1a1a1a;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;

        &:hover {
          transform: translateY(-2px);
        }

        .product-image {
          position: relative;
          width: 100%;
          height: 120px;

          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }

          .discount-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ff0050;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
          }
        }

        .product-info {
          padding: 12px;

          .product-name {
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            margin: 0 0 4px 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
          }

          .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;

            .rating-stars {
              color: #ffc107;
              font-size: 12px;
            }

            .rating-count {
              font-size: 10px;
              color: #ccc;
            }
          }

          .product-price {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;

            .current-price {
              font-size: 14px;
              font-weight: 600;
              color: #ff0050;
            }

            .original-price {
              font-size: 12px;
              color: #ccc;
              text-decoration: line-through;
            }
          }

          .product-sales {
            font-size: 10px;
            color: #ccc;
          }
        }
      }
    }

    .load-more {
      text-align: center;

      .load-more-btn {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;

        &:hover:not(:disabled) {
          background: #333;
        }

        &:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }
      }
    }
  }

  .reviews-tab {
    .reviews-summary {
      background: #1a1a1a;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;

      .summary-rating {
        text-align: center;
        margin-bottom: 20px;

        .rating-score {
          font-size: 32px;
          font-weight: 600;
          color: #fff;
          margin-bottom: 8px;
        }

        .rating-stars {
          margin-bottom: 8px;

          .star {
            color: #666;
            font-size: 20px;

            &.filled {
              color: #ffc107;
            }
          }
        }

        .rating-text {
          font-size: 14px;
          color: #ccc;
        }
      }

      .rating-breakdown {
        .rating-bar {
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 8px;

          .rating-label {
            font-size: 12px;
            color: #ccc;
            min-width: 30px;
          }

          .rating-progress {
            flex: 1;
            height: 8px;
            background: #333;
            border-radius: 4px;
            overflow: hidden;

            .progress-bar {
              height: 100%;
              background: #ffc107;
              transition: width 0.3s;
            }
          }

          .rating-percentage {
            font-size: 12px;
            color: #ccc;
            min-width: 30px;
            text-align: right;
          }
        }
      }
    }

    .reviews-list {
      .review-item {
        background: #1a1a1a;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;

        .review-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 12px;

          .reviewer-info {
            display: flex;
            align-items: center;
            gap: 12px;

            .reviewer-avatar {
              width: 40px;
              height: 40px;
              border-radius: 50%;
              overflow: hidden;

              img {
                width: 100%;
                height: 100%;
                object-fit: cover;
              }
            }

            .reviewer-details {
              .reviewer-name {
                font-size: 14px;
                font-weight: 500;
                color: #fff;
                margin-bottom: 2px;
              }

              .review-date {
                font-size: 12px;
                color: #ccc;
              }
            }
          }

          .review-rating {
            .star {
              color: #666;
              font-size: 14px;

              &.filled {
                color: #ffc107;
              }
            }
          }
        }

        .review-content {
          p {
            font-size: 14px;
            color: #ccc;
            margin: 0 0 12px 0;
            line-height: 1.5;
          }

          .review-images {
            display: flex;
            gap: 8px;

            .review-image {
              width: 60px;
              height: 60px;
              border-radius: 8px;
              object-fit: cover;
            }
          }
        }
      }
    }
  }

  .about-tab {
    .about-section {
      background: #1a1a1a;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 16px;

      h3 {
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        margin: 0 0 12px 0;
      }

      p {
        font-size: 14px;
        color: #ccc;
        margin: 0;
        line-height: 1.5;
      }

      .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;

        .info-item {
          display: flex;
          flex-direction: column;
          gap: 4px;

          .info-label {
            font-size: 12px;
            color: #ccc;
          }

          .info-value {
            font-size: 14px;
            color: #fff;
            font-weight: 500;
          }
        }
      }

      .policies-list {
        .policy-item {
          display: flex;
          gap: 12px;
          margin-bottom: 16px;

          &:last-child {
            margin-bottom: 0;
          }

          .policy-icon {
            color: #ff0050;
            margin-top: 2px;
          }

          .policy-content {
            h4 {
              font-size: 14px;
              font-weight: 500;
              color: #fff;
              margin: 0 0 4px 0;
            }

            p {
              font-size: 12px;
              color: #ccc;
              margin: 0;
            }
          }
        }
      }
    }
  }
}

.filter-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  z-index: 1000;
  display: flex;
  align-items: flex-end;

  .filter-content {
    background: #1a1a1a;
    border-radius: 16px 16px 0 0;
    width: 100%;
    max-height: 60vh;
    overflow: hidden;

    .filter-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 1px solid #333;

      h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: #fff;
      }

      .close-btn {
        background: none;
        border: none;
        color: #fff;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;

        &:hover {
          background: #333;
        }
      }
    }

    .filter-options {
      padding: 20px;

      .filter-group {
        margin-bottom: 20px;

        h4 {
          font-size: 16px;
          font-weight: 500;
          color: #fff;
          margin: 0 0 12px 0;
        }

        .price-range {
          display: flex;
          align-items: center;
          gap: 12px;

          .price-input {
            flex: 1;
            background: #333;
            border: 1px solid #555;
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 14px;

            &::placeholder {
              color: #999;
            }

            &:focus {
              outline: none;
              border-color: #ff0050;
            }
          }

          .price-separator {
            color: #ccc;
            font-size: 16px;
          }
        }

        .category-select {
          width: 100%;
          background: #333;
          border: 1px solid #555;
          border-radius: 8px;
          padding: 12px;
          color: #fff;
          font-size: 14px;
          cursor: pointer;

          &:focus {
            outline: none;
            border-color: #ff0050;
          }

          option {
            background: #333;
            color: #fff;
          }
        }

        .rating-filter {
          .rating-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            cursor: pointer;

            input[type="radio"] {
              margin: 0;
            }

            .rating-stars {
              display: flex;
              gap: 2px;

              .empty-star {
                opacity: 0.3;
              }
            }

            .rating-text {
              font-size: 14px;
              color: #ccc;
            }
          }
        }

        .checkbox-option {
          display: flex;
          align-items: center;
          gap: 8px;
          cursor: pointer;

          input[type="checkbox"] {
            margin: 0;
          }

          span {
            font-size: 14px;
            color: #ccc;
          }
        }
      }
    }

    .filter-actions {
      display: flex;
      gap: 12px;
      padding: 20px;
      border-top: 1px solid #333;

      .reset-btn {
        flex: 1;
        background: #333;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;

        &:hover {
          background: #555;
        }
      }

      .apply-btn {
        flex: 1;
        background: #ff0050;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;

        &:hover {
          background: #e6004a;
        }
      }
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .shop-content {
    padding: 16px;
  }

  .shop-header {
    flex-direction: column;
    text-align: center;
    gap: 12px;

    .shop-actions {
      flex-direction: row;
      width: 100%;
    }
  }

  .products-tab .products-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .about-tab .about-section .info-grid {
    grid-template-columns: 1fr;
  }
}

// 全局移动端样式
:global(body) {
  background: #000;
  color: #fff;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}
</style>
