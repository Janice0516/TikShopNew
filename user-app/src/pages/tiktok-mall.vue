<template>
  <view class="tiktok-shop">
    <!-- TikTok Shop头部 -->
    <view class="shop-header">
      <view class="header-content">
        <view class="logo-section">
          <text class="logo-icon">🎵</text>
          <text class="logo-text">TikTok Shop</text>
        </view>
        <view class="header-actions">
          <view class="action-btn" @click="goToSearch">
            <text class="icon">🔍</text>
          </view>
          <view class="action-btn" @click="goToCart">
            <text class="icon">🛒</text>
            <view class="badge" v-if="cartCount > 0">{{ cartCount }}</view>
          </view>
        </view>
      </view>
    </view>

    <!-- TikTok风格轮播图 -->
    <view class="hero-banner">
      <swiper class="hero-swiper" autoplay circular indicator-dots indicator-color="rgba(255,255,255,0.3)" indicator-active-color="#fff">
        <swiper-item>
          <view class="hero-slide" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
            <view class="hero-content">
              <text class="hero-title">Summer Sale</text>
              <text class="hero-subtitle">Up to 70% OFF</text>
              <view class="hero-btn" @click="goToSale">Shop Now</view>
            </view>
            <view class="hero-image">
              <text class="emoji">🛍️</text>
            </view>
          </view>
        </swiper-item>
        <swiper-item>
          <view class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <view class="hero-content">
              <text class="hero-title">New Arrivals</text>
              <text class="hero-subtitle">Fresh Styles</text>
              <view class="hero-btn" @click="goToNew">Explore</view>
            </view>
            <view class="hero-image">
              <text class="emoji">✨</text>
            </view>
          </view>
        </swiper-item>
        <swiper-item>
          <view class="hero-slide" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
            <view class="hero-content">
              <text class="hero-title">Live Shopping</text>
              <text class="hero-subtitle">Watch & Buy</text>
              <view class="hero-btn" @click="goToLive">Join Live</view>
            </view>
            <view class="hero-image">
              <text class="emoji">📺</text>
            </view>
          </view>
        </swiper-item>
      </swiper>
    </view>

    <!-- 快速分类导航 -->
    <view class="quick-categories">
      <scroll-view scroll-x class="categories-scroll">
        <view class="categories-list">
          <view 
            v-for="category in quickCategories" 
            :key="category.id" 
            class="category-item"
            @click="selectCategory(category.name)"
          >
            <view class="category-icon">{{ category.icon }}</view>
            <text class="category-name">{{ category.name }}</text>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 直播商店区域 -->
    <view class="live-section">
      <view class="section-header">
        <view class="section-title">
          <view class="live-indicator">
            <view class="live-dot"></view>
            <text class="live-text">LIVE</text>
          </view>
          <text class="title-text">Live Shopping</text>
        </view>
        <text class="view-all" @click="goToLiveStores">View All</text>
      </view>
      <scroll-view scroll-x class="live-scroll">
        <view class="live-list">
          <view 
            v-for="store in liveStores" 
            :key="store.id" 
            class="live-store-card"
            @click="goToLiveStore(store.id)"
          >
            <view class="store-cover">
              <image :src="store.cover" class="cover-image" />
              <view class="live-overlay">
                <view class="live-badge">
                  <view class="live-dot"></view>
                  <text class="live-text">LIVE</text>
                </view>
                <text class="viewers">{{ store.viewers }} watching</text>
              </view>
            </view>
            <view class="store-info">
              <image :src="store.avatar" class="store-avatar" />
              <view class="store-details">
                <text class="store-name">{{ store.name }}</text>
                <text class="store-type">{{ store.type }}</text>
              </view>
            </view>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 热门商品区域 -->
    <view class="trending-section">
      <view class="section-header">
        <text class="section-title">Trending Products</text>
        <text class="view-all" @click="goToProducts">View All</text>
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

    <!-- 推荐商店区域 -->
    <view class="stores-section">
      <view class="section-header">
        <text class="section-title">Recommended Stores</text>
        <text class="view-all" @click="goToStores">View All</text>
      </view>
      <view class="stores-list">
        <view 
          v-for="store in recommendedStores" 
          :key="store.id" 
          class="store-item"
          @click="goToStore(store.id)"
        >
          <image :src="store.avatar" class="store-avatar" />
          <view class="store-info">
            <text class="store-name">{{ store.name }}</text>
            <text class="store-location">{{ store.location }}</text>
            <view class="store-stats">
              <text class="followers">{{ formatNumber(store.followers) }} followers</text>
              <text class="rating">⭐ {{ store.rating }}</text>
            </view>
          </view>
          <view class="follow-btn" @click.stop="followStore(store.id)">
            <text class="follow-text">Follow</text>
          </view>
        </view>
      </view>
    </view>

    <!-- 底部安全区域 -->
    <view class="safe-area"></view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// 响应式数据
const cartCount = ref(3)

// 快速分类数据
const quickCategories = ref([
  { id: 1, name: 'Electronics', icon: '📱' },
  { id: 2, name: 'Fashion', icon: '👗' },
  { id: 3, name: 'Beauty', icon: '💄' },
  { id: 4, name: 'Home', icon: '🏠' },
  { id: 5, name: 'Sports', icon: '⚽' },
  { id: 6, name: 'Books', icon: '📚' },
  { id: 7, name: 'Toys', icon: '🧸' },
  { id: 8, name: 'Food', icon: '🍎' }
])

// 直播商店数据
const liveStores = ref([
  {
    id: 1,
    name: 'TechHub Pro',
    type: 'Electronics',
    avatar: 'https://picsum.photos/60/60?random=avatar1',
    cover: 'https://picsum.photos/300/150?random=cover1',
    viewers: '1.2K'
  },
  {
    id: 2,
    name: 'Fashion Forward',
    type: 'Fashion',
    avatar: 'https://picsum.photos/60/60?random=avatar2',
    cover: 'https://picsum.photos/300/150?random=cover2',
    viewers: '856'
  },
  {
    id: 3,
    name: 'Beauty Paradise',
    type: 'Beauty',
    avatar: 'https://picsum.photos/60/60?random=avatar3',
    cover: 'https://picsum.photos/300/150?random=cover3',
    viewers: '2.1K'
  },
  {
    id: 4,
    name: 'Home Sweet Home',
    type: 'Home',
    avatar: 'https://picsum.photos/60/60?random=avatar4',
    cover: 'https://picsum.photos/300/150?random=cover4',
    viewers: '945'
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
  }
])

// 推荐商店数据
const recommendedStores = ref([
  {
    id: 1,
    name: 'Home Sweet Home',
    location: 'New York',
    avatar: 'https://picsum.photos/60/60?random=store1',
    followers: 25300,
    rating: 4.8
  },
  {
    id: 2,
    name: 'Sports Zone',
    location: 'Los Angeles',
    avatar: 'https://picsum.photos/60/60?random=store2',
    followers: 18700,
    rating: 4.6
  },
  {
    id: 3,
    name: 'Book World',
    location: 'Chicago',
    avatar: 'https://picsum.photos/60/60?random=store3',
    followers: 12400,
    rating: 4.9
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

const goToLiveStores = () => {
  uni.showToast({
    title: 'Live stores coming soon!',
    icon: 'none'
  })
}

const goToProducts = () => {
  uni.showToast({
    title: 'Products list coming soon!',
    icon: 'none'
  })
}

const goToStores = () => {
  uni.showToast({
    title: 'Stores list coming soon!',
    icon: 'none'
  })
}

const goToLiveStore = (storeId: number) => {
  uni.showToast({
    title: `Live Store ${storeId} coming soon!`,
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
  uni.showToast({
    title: `${category} category selected!`,
    icon: 'none'
  })
}

const followStore = (storeId: number) => {
  uni.showToast({
    title: `Followed Store ${storeId}!`,
    icon: 'success'
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
.tiktok-shop {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* TikTok Shop头部 */
.shop-header {
  background: #000;
  padding: 15px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  position: sticky;
  top: 44px;
  z-index: 999;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.logo-icon {
  font-size: 20px;
}

.logo-text {
  font-size: 18px;
  font-weight: bold;
  color: #fff;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.action-btn {
  position: relative;
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:active {
  transform: scale(0.95);
  background: rgba(255, 255, 255, 0.2);
}

.action-btn .icon {
  font-size: 16px;
  color: #fff;
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff0050;
  color: white;
  font-size: 10px;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 16px;
  text-align: center;
}

/* TikTok风格轮播图 */
.hero-banner {
  margin: 0;
  position: relative;
}

.hero-swiper {
  height: 200px;
}

.hero-slide {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 30px 25px;
  position: relative;
  overflow: hidden;
}

.hero-content {
  flex: 1;
  z-index: 2;
}

.hero-title {
  font-size: 24px;
  font-weight: bold;
  color: #fff;
  margin-bottom: 8px;
  display: block;
}

.hero-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 20px;
  display: block;
}

.hero-btn {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 10px 20px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-block;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.hero-btn:active {
  transform: scale(0.95);
  background: rgba(255, 255, 255, 0.3);
}

.hero-image {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.hero-image .emoji {
  font-size: 80px;
  opacity: 0.3;
}

/* 快速分类导航 */
.quick-categories {
  background: #000;
  padding: 20px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.categories-scroll {
  white-space: nowrap;
}

.categories-list {
  display: flex;
  gap: 20px;
  padding: 0 20px;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 60px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.category-item:active {
  transform: scale(0.95);
}

.category-icon {
  font-size: 24px;
  margin-bottom: 8px;
}

.category-name {
  font-size: 12px;
  color: #fff;
  font-weight: 500;
  text-align: center;
}

/* 直播商店区域 */
.live-section {
  background: #000;
  padding: 25px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 0 20px;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.live-indicator {
  display: flex;
  align-items: center;
  gap: 5px;
}

.live-dot {
  width: 8px;
  height: 8px;
  background: #ff0050;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

.live-text {
  font-size: 12px;
  color: #ff0050;
  font-weight: bold;
}

.title-text {
  font-size: 18px;
  font-weight: bold;
  color: #fff;
}

.view-all {
  font-size: 14px;
  color: #ff0050;
  font-weight: 600;
  cursor: pointer;
}

.live-scroll {
  white-space: nowrap;
}

.live-list {
  display: flex;
  gap: 15px;
  padding: 0 20px;
}

.live-store-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 15px;
  overflow: hidden;
  min-width: 200px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.live-store-card:active {
  transform: scale(0.98);
}

.store-cover {
  position: relative;
  height: 120px;
}

.cover-image {
  width: 100%;
  height: 100%;
}

.live-overlay {
  position: absolute;
  top: 10px;
  left: 10px;
  right: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.live-badge {
  display: flex;
  align-items: center;
  gap: 5px;
  background: rgba(255, 0, 80, 0.9);
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: bold;
}

.viewers {
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
}

.store-info {
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.store-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.store-details {
  flex: 1;
}

.store-name {
  font-size: 14px;
  font-weight: bold;
  color: #fff;
  margin-bottom: 2px;
}

.store-type {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

/* 热门商品区域 */
.trending-section {
  background: #000;
  padding: 25px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-title {
  font-size: 18px;
  font-weight: bold;
  color: #fff;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
  padding: 0 20px;
}

.product-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 15px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.product-card:active {
  transform: scale(0.98);
}

.product-image-container {
  position: relative;
  height: 180px;
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
  background: #ff0050;
}

.trending-badge {
  background: #ff6b6b;
}

.discount-badge {
  background: #00d4aa;
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
  width: 35px;
  height: 35px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:active {
  transform: scale(0.9);
}

.action-btn .icon {
  font-size: 14px;
}

.product-info {
  padding: 15px;
}

.product-name {
  font-size: 14px;
  font-weight: bold;
  color: #fff;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.current-price {
  font-size: 16px;
  font-weight: bold;
  color: #ff0050;
}

.original-price {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  text-decoration: line-through;
  margin-left: 8px;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.product-meta .store-name {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

.product-meta .rating {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

.product-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.product-tag {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.8);
  padding: 2px 6px;
  border-radius: 8px;
  font-size: 10px;
}

/* 推荐商店区域 */
.stores-section {
  background: #000;
  padding: 25px 0;
}

.stores-list {
  padding: 0 20px;
}

.store-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
}

.store-item:last-child {
  border-bottom: none;
}

.store-item:active {
  transform: scale(0.98);
}

.store-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.store-info {
  flex: 1;
}

.store-name {
  font-size: 16px;
  font-weight: bold;
  color: #fff;
  margin-bottom: 4px;
}

.store-location {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 4px;
}

.store-stats {
  display: flex;
  gap: 15px;
}

.followers {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

.rating {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

.follow-btn {
  background: #ff0050;
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}

.follow-btn:active {
  transform: scale(0.95);
  background: #e6004a;
}

/* 底部安全区域 */
.safe-area {
  height: 34px;
  background: #000;
}

/* 动画效果 */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* 响应式设计 */
@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    padding: 0 15px;
  }
  
  .categories-list {
    padding: 0 15px;
    gap: 15px;
  }
  
  .live-list {
    padding: 0 15px;
    gap: 10px;
  }
  
  .stores-list {
    padding: 0 15px;
  }
  
  .section-header {
    padding: 0 15px;
  }
}
</style>
