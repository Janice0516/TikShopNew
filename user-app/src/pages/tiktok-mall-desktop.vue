<template>
  <view class="tiktok-mall-desktop">
    <!-- TikTok风格顶部导航栏 -->
    <view class="desktop-header">
      <view class="header-content">
        <view class="logo-section">
          <text class="logo-icon">🎵</text>
          <text class="logo-text">TikTok Mall</text>
        </view>
        
        <view class="search-section">
          <view class="search-bar" @click="goToSearch">
            <text class="search-placeholder">Search stores & products</text>
            <view class="search-icon">
              <text class="icon">🔍</text>
            </view>
          </view>
        </view>
        
        <view class="header-actions">
          <view class="action-btn" @click="goToCart">
            <text class="icon">🛒</text>
            <text class="badge" v-if="cartCount > 0">{{ cartCount }}</text>
          </view>
          <view class="action-btn" @click="goToProfile">
            <text class="icon">👤</text>
          </view>
          <view class="action-btn" @click="showNotifications">
            <text class="icon">🔔</text>
            <text class="badge" v-if="notificationCount > 0">{{ notificationCount }}</text>
          </view>
        </view>
      </view>
    </view>

    <!-- 主要内容区域 -->
    <view class="main-content">
      <!-- 左侧边栏 -->
      <view class="sidebar">
        <view class="sidebar-section">
          <text class="section-title">Categories</text>
          <view class="category-list">
            <view 
              v-for="category in categories" 
              :key="category.id" 
              class="category-item"
              :class="{ active: selectedCategory === category.name }"
              @click="selectCategory(category.name)"
            >
              <text class="category-icon">{{ category.icon }}</text>
              <text class="category-name">{{ category.name }}</text>
            </view>
          </view>
        </view>
        
        <view class="sidebar-section">
          <text class="section-title">Live Stores</text>
          <view class="live-stores-list">
            <view 
              v-for="store in liveStores" 
              :key="store.id" 
              class="live-store-item"
              @click="goToStore(store.id)"
            >
              <image :src="store.avatar" class="store-avatar" />
              <view class="store-info">
                <text class="store-name">{{ store.name }}</text>
                <text class="live-status">LIVE • {{ store.viewers }}</text>
              </view>
            </view>
          </view>
        </view>
      </view>

      <!-- 右侧主内容区 -->
      <view class="content-area">
        <!-- 轮播图区域 -->
        <view class="banner-section">
          <swiper class="banner-swiper" autoplay circular indicator-dots>
            <swiper-item>
              <image src="https://picsum.photos/800/300?random=1" class="banner-image" />
              <view class="banner-content">
                <text class="banner-title">Summer Sale 2024</text>
                <text class="banner-subtitle">Up to 70% OFF!</text>
                <view class="banner-btn" @click="goToSale">Shop Now</view>
              </view>
            </swiper-item>
            <swiper-item>
              <image src="https://picsum.photos/800/300?random=2" class="banner-image" />
              <view class="banner-content">
                <text class="banner-title">New Arrivals</text>
                <text class="banner-subtitle">Fresh styles every day</text>
                <view class="banner-btn" @click="goToNew">Explore</view>
              </view>
            </swiper-item>
            <swiper-item>
              <image src="https://picsum.photos/800/300?random=3" class="banner-image" />
              <view class="banner-content">
                <text class="banner-title">Live Shopping</text>
                <text class="banner-subtitle">Watch & Shop Now</text>
                <view class="banner-btn" @click="goToLive">Join Live</view>
              </view>
            </swiper-item>
          </swiper>
        </view>

        <!-- 热门商店网格 -->
        <view class="trending-stores-section">
          <view class="section-header">
            <text class="section-title">Trending Stores</text>
            <text class="view-all" @click="goToStores">View All →</text>
          </view>
          <view class="stores-grid">
            <view 
              v-for="store in trendingStores" 
              :key="store.id" 
              class="store-card"
              @click="goToStore(store.id)"
            >
              <view class="store-header">
                <image :src="store.avatar" class="store-avatar" />
                <view class="store-info">
                  <text class="store-name">{{ store.name }}</text>
                  <text class="store-location">{{ store.location }}</text>
                </view>
                <view class="store-stats">
                  <text class="followers">{{ formatNumber(store.followers) }}</text>
                  <text class="rating">⭐ {{ store.rating }}</text>
                </view>
              </view>
              <view class="store-tags">
                <text 
                  v-for="tag in store.tags" 
                  :key="tag" 
                  class="store-tag"
                >
                  {{ tag }}
                </text>
              </view>
              <view class="store-description">
                <text class="description-text">{{ store.description }}</text>
              </view>
            </view>
          </view>
        </view>

        <!-- 热门商品网格 -->
        <view class="trending-products-section">
          <view class="section-header">
            <text class="section-title">Trending Products</text>
            <text class="view-all" @click="goToProducts">View All →</text>
          </view>
          <view class="products-grid">
            <view 
              v-for="product in trendingProducts" 
              :key="product.id" 
              class="product-card"
              @click="goToProduct(product.id)"
            >
              <view class="product-image-container">
                <image :src="product.image" class="product-image" />
                <view class="product-badges">
                  <text v-if="product.isLive" class="live-badge">LIVE</text>
                  <text v-if="product.isTrending" class="trending-badge">HOT</text>
                  <text v-if="product.discount" class="discount-badge">-{{ product.discount }}%</text>
                </view>
                <view class="product-overlay">
                  <view class="product-actions">
                    <view class="action-btn" @click.stop="addToCart(product.id)">
                      <text class="icon">🛒</text>
                    </view>
                    <view class="action-btn" @click.stop="toggleFavorite(product.id)">
                      <text class="icon">❤️</text>
                    </view>
                  </view>
                </view>
              </view>
              <view class="product-info">
                <text class="product-name">{{ product.name }}</text>
                <view class="product-price">
                  <text class="current-price">RM{{ product.price }}</text>
                  <text class="original-price" v-if="product.originalPrice">RM{{ product.originalPrice }}</text>
                </view>
                <view class="product-meta">
                  <text class="store-name">{{ product.storeName }}</text>
                  <text class="rating">⭐ {{ product.rating }}</text>
                </view>
                <view class="product-tags">
                  <text 
                    v-for="tag in product.tags" 
                    :key="tag" 
                    class="product-tag"
                  >
                    {{ tag }}
                  </text>
                </view>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// 响应式数据
const selectedCategory = ref('All')
const cartCount = ref(3)
const notificationCount = ref(5)

// 分类数据
const categories = ref([
  { id: 1, name: 'All', icon: '🏠' },
  { id: 2, name: 'Electronics', icon: '📱' },
  { id: 3, name: 'Fashion', icon: '👗' },
  { id: 4, name: 'Beauty', icon: '💄' },
  { id: 5, name: 'Home', icon: '🏠' },
  { id: 6, name: 'Sports', icon: '⚽' },
  { id: 7, name: 'Books', icon: '📚' },
  { id: 8, name: 'Toys', icon: '🧸' },
  { id: 9, name: 'Food', icon: '🍎' }
])

// 直播商店数据
const liveStores = ref([
  { id: 1, name: 'TechHub Pro', avatar: 'https://picsum.photos/40/40?random=avatar1', viewers: '1.2K' },
  { id: 2, name: 'Fashion Forward', avatar: 'https://picsum.photos/40/40?random=avatar2', viewers: '856' },
  { id: 3, name: 'Beauty Paradise', avatar: 'https://picsum.photos/40/40?random=avatar3', viewers: '2.1K' }
])

// 热门商店数据
const trendingStores = ref([
  {
    id: 1,
    name: 'Home Sweet Home',
    location: 'New York',
    avatar: 'https://picsum.photos/60/60?random=avatar4',
    followers: 25300,
    rating: 4.8,
    tags: ['Hot', 'New'],
    description: 'Welcome to Home Sweet Home! We offer the best home products with fast shipping and excellent customer service.'
  },
  {
    id: 2,
    name: 'Sports Zone',
    location: 'Los Angeles',
    avatar: 'https://picsum.photos/60/60?random=avatar5',
    followers: 18700,
    rating: 4.6,
    tags: ['Sale', 'Trending'],
    description: 'Your one-stop shop for all sports equipment and athletic gear. Quality guaranteed!'
  },
  {
    id: 3,
    name: 'Book World',
    location: 'Chicago',
    avatar: 'https://picsum.photos/60/60?random=avatar6',
    followers: 12400,
    rating: 4.9,
    tags: ['Verified', 'Hot'],
    description: 'Discover amazing books from bestsellers to classics. Free shipping on orders over $25!'
  },
  {
    id: 4,
    name: 'Digital Dreams',
    location: 'Houston',
    avatar: 'https://picsum.photos/60/60?random=avatar7',
    followers: 32100,
    rating: 4.7,
    tags: ['Hot', 'New'],
    description: 'Cutting-edge technology and gadgets for the modern lifestyle. Innovation meets affordability.'
  }
])

// 热门商品数据
const trendingProducts = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro Max',
    price: 999,
    originalPrice: 1299,
    image: 'https://picsum.photos/300/300?random=product1',
    storeName: 'TechHub Pro',
    rating: 4.8,
    isLive: true,
    isTrending: true,
    discount: 30,
    tags: ['Hot', 'Free Shipping']
  },
  {
    id: 2,
    name: 'Nike Air Max 270',
    price: 120,
    originalPrice: 150,
    image: 'https://picsum.photos/300/300?random=product2',
    storeName: 'Sports Zone',
    rating: 4.6,
    isLive: false,
    isTrending: true,
    discount: 20,
    tags: ['New', 'Verified']
  },
  {
    id: 3,
    name: 'Zara Summer Dress',
    price: 45,
    originalPrice: 60,
    image: 'https://picsum.photos/300/300?random=product3',
    storeName: 'Fashion Forward',
    rating: 4.7,
    isLive: true,
    isTrending: false,
    discount: 15,
    tags: ['Sale', '7-Day Return']
  },
  {
    id: 4,
    name: 'Skincare Set',
    price: 89,
    originalPrice: 120,
    image: 'https://picsum.photos/300/300?random=product4',
    storeName: 'Beauty Paradise',
    rating: 4.9,
    isLive: false,
    isTrending: true,
    discount: 25,
    tags: ['Hot', 'Free Shipping']
  },
  {
    id: 5,
    name: 'MacBook Pro M3',
    price: 1999,
    originalPrice: 2299,
    image: 'https://picsum.photos/300/300?random=product5',
    storeName: 'Digital Dreams',
    rating: 4.9,
    isLive: false,
    isTrending: true,
    discount: 13,
    tags: ['Hot', 'Verified']
  },
  {
    id: 6,
    name: 'AirPods Pro 2',
    price: 249,
    originalPrice: 299,
    image: 'https://picsum.photos/300/300?random=product6',
    storeName: 'TechHub Pro',
    rating: 4.7,
    isLive: true,
    isTrending: false,
    discount: 17,
    tags: ['Sale', 'Free Shipping']
  }
])

// 工具函数
const formatNumber = (num: number) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}

// 导航功能
const goToSearch = () => {
  uni.showToast({
    title: 'Search coming soon!',
    icon: 'none'
  })
}

const goToCart = () => {
  uni.showToast({
    title: 'Cart coming soon!',
    icon: 'none'
  })
}

const goToProfile = () => {
  uni.showToast({
    title: 'Profile coming soon!',
    icon: 'none'
  })
}

const showNotifications = () => {
  uni.showToast({
    title: 'Notifications coming soon!',
    icon: 'none'
  })
}

const goToSale = () => {
  uni.showToast({
    title: 'Sale page coming soon!',
    icon: 'none'
  })
}

const goToNew = () => {
  uni.showToast({
    title: 'New arrivals coming soon!',
    icon: 'none'
  })
}

const goToLive = () => {
  uni.showToast({
    title: 'Live shopping coming soon!',
    icon: 'none'
  })
}

const goToStores = () => {
  uni.showToast({
    title: 'Stores list coming soon!',
    icon: 'none'
  })
}

const goToProducts = () => {
  uni.showToast({
    title: 'Products list coming soon!',
    icon: 'none'
  })
}

const goToStore = (storeId: number) => {
  uni.showToast({
    title: `Store ${storeId} coming soon!`,
    icon: 'none'
  })
}

const goToProduct = (productId: number) => {
  uni.showToast({
    title: `Product ${productId} coming soon!`,
    icon: 'none'
  })
}

// 交互功能
const selectCategory = (category: string) => {
  selectedCategory.value = category
  uni.showToast({
    title: `${category} category selected!`,
    icon: 'none'
  })
}

const addToCart = (productId: number) => {
  cartCount.value++
  uni.showToast({
    title: `Added to cart!`,
    icon: 'success'
  })
}

const toggleFavorite = (productId: number) => {
  uni.showToast({
    title: `Added to favorites!`,
    icon: 'success'
  })
}
</script>

<style scoped>
.tiktok-mall-desktop {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
}

/* 桌面版顶部导航栏 */
.desktop-header {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  padding: 15px 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1400px;
  margin: 0 auto;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo-icon {
  font-size: 24px;
}

.logo-text {
  font-size: 20px;
  font-weight: bold;
  color: white;
}

.search-section {
  flex: 1;
  max-width: 500px;
  margin: 0 30px;
}

.search-bar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 25px;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  cursor: pointer;
}

.search-bar:hover {
  background: rgba(255, 255, 255, 1);
  transform: translateY(-2px);
}

.search-placeholder {
  color: #999;
  font-size: 16px;
  font-weight: 500;
}

.search-icon {
  width: 30px;
  height: 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.search-icon .icon {
  font-size: 14px;
  color: white;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}

.action-btn {
  position: relative;
  width: 45px;
  height: 45px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.action-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.action-btn .icon {
  font-size: 18px;
  color: white;
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff4444;
  color: white;
  font-size: 12px;
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* 主要内容区域 */
.main-content {
  display: flex;
  flex: 1;
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  gap: 20px;
}

/* 左侧边栏 */
.sidebar {
  width: 280px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 25px;
  height: fit-content;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.sidebar-section {
  margin-bottom: 30px;
}

.sidebar-section:last-child {
  margin-bottom: 0;
}

.section-title {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 15px;
  display: block;
}

.category-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.category-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 15px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.category-item:hover {
  background: rgba(102, 126, 234, 0.1);
}

.category-item.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.category-icon {
  font-size: 20px;
}

.category-name {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.category-item.active .category-name {
  color: white;
}

.live-stores-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.live-store-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: rgba(255, 68, 68, 0.05);
  border: 1px solid rgba(255, 68, 68, 0.1);
}

.live-store-item:hover {
  background: rgba(255, 68, 68, 0.1);
  transform: translateX(5px);
}

.store-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #ff4444;
}

.store-info {
  flex: 1;
}

.store-name {
  font-size: 14px;
  font-weight: bold;
  color: #333;
  margin-bottom: 2px;
}

.live-status {
  font-size: 12px;
  color: #ff4444;
  font-weight: bold;
}

/* 右侧主内容区 */
.content-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* 轮播图区域 */
.banner-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.banner-swiper {
  height: 300px;
}

.banner-image {
  width: 100%;
  height: 100%;
}

.banner-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  padding: 40px 30px 30px;
  color: white;
}

.banner-title {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 8px;
}

.banner-subtitle {
  font-size: 16px;
  opacity: 0.9;
  margin-bottom: 20px;
}

.banner-btn {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  color: white;
  padding: 12px 24px;
  border-radius: 25px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-block;
}

.banner-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
}

/* 热门商店区域 */
.trending-stores-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 22px;
  font-weight: bold;
  color: #333;
}

.view-all {
  font-size: 14px;
  color: #667eea;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-all:hover {
  color: #764ba2;
  transform: translateX(5px);
}

.stores-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.store-card {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 15px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.store-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  background: rgba(255, 255, 255, 1);
}

.store-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
}

.store-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.store-info {
  flex: 1;
}

.store-name {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 5px;
}

.store-location {
  font-size: 12px;
  color: #666;
}

.store-stats {
  text-align: right;
}

.followers {
  font-size: 14px;
  font-weight: bold;
  color: #333;
  display: block;
  margin-bottom: 2px;
}

.rating {
  font-size: 12px;
  color: #666;
}

.store-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 15px;
}

.store-tag {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 4px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: bold;
}

.store-description {
  margin-top: 10px;
}

.description-text {
  font-size: 12px;
  color: #666;
  line-height: 1.4;
}

/* 热门商品区域 */
.trending-products-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.product-card {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 15px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  background: rgba(255, 255, 255, 1);
}

.product-image-container {
  position: relative;
  height: 200px;
}

.product-image {
  width: 100%;
  height: 100%;
}

.product-badges {
  position: absolute;
  top: 10px;
  left: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.live-badge, .trending-badge, .discount-badge {
  padding: 4px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: bold;
  color: white;
}

.live-badge {
  background: #ff4444;
}

.trending-badge {
  background: #ff6b6b;
}

.discount-badge {
  background: #67C23A;
}

.product-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
  opacity: 1;
}

.product-actions {
  display: flex;
  gap: 10px;
}

.action-btn {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: scale(1.1);
  background: rgba(255, 255, 255, 1);
}

.action-btn .icon {
  font-size: 16px;
}

.product-info {
  padding: 15px;
}

.product-name {
  font-size: 14px;
  font-weight: bold;
  color: #333;
  margin-bottom: 10px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.current-price {
  font-size: 16px;
  font-weight: bold;
  color: #ff4444;
}

.original-price {
  font-size: 12px;
  color: #999;
  text-decoration: line-through;
  margin-left: 8px;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.product-meta .store-name {
  font-size: 12px;
  color: #666;
}

.product-meta .rating {
  font-size: 12px;
  color: #666;
}

.product-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.product-tag {
  background: #f0f0f0;
  color: #666;
  padding: 2px 6px;
  border-radius: 8px;
  font-size: 10px;
}

/* 响应式设计 */
@media (max-width: 1200px) {
  .main-content {
    flex-direction: column;
  }
  
  .sidebar {
    width: 100%;
    order: 2;
  }
  
  .content-area {
    order: 1;
  }
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 15px;
  }
  
  .search-section {
    margin: 0;
    max-width: 100%;
  }
  
  .stores-grid {
    grid-template-columns: 1fr;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

@media (max-width: 480px) {
  .desktop-header {
    padding: 15px 20px;
  }
  
  .main-content {
    padding: 15px;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
}
</style>

