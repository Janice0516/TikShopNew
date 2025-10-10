<template>
  <view class="category-page">
    <!-- 搜索栏 -->
    <view class="search-bar">
      <view class="search-input">
        <uni-icons type="search" size="18" color="#999"></uni-icons>
        <input 
          v-model="searchKeyword" 
          :placeholder="$t('category.searchPlaceholder')"
          @input="handleSearch"
        />
      </view>
    </view>

    <!-- 分类列表 -->
    <view class="category-container">
      <!-- 左侧分类导航 -->
      <view class="category-nav">
        <scroll-view scroll-y class="nav-scroll">
          <view 
            v-for="(category, index) in categories" 
            :key="category.id"
            class="nav-item"
            :class="{ active: activeCategoryIndex === index }"
            @click="selectCategory(index)"
          >
            <text class="nav-text">{{ category.name }}</text>
          </view>
        </scroll-view>
      </view>

      <!-- 右侧商品列表 -->
      <view class="product-list">
        <scroll-view scroll-y class="product-scroll">
          <!-- 当前分类商品 -->
          <view v-if="currentCategoryProducts.length > 0" class="product-section">
            <view 
              v-for="product in currentCategoryProducts" 
              :key="product.id"
              class="product-item"
              @click="goToProductDetail(product.id)"
            >
              <image 
                :src="product.image" 
                class="product-image"
                mode="aspectFill"
              />
              <view class="product-info">
                <text class="product-name">{{ product.name }}</text>
                <text class="product-price">${{ product.price }}</text>
              </view>
            </view>
          </view>

          <!-- 空状态 -->
          <view v-else class="empty-state">
            <uni-icons type="shop" size="60" color="#ccc"></uni-icons>
            <text class="empty-text">{{ $t('category.noProducts') }}</text>
          </view>
        </scroll-view>
      </view>
    </view>

    <!-- 语言切换器 -->
    <view class="language-switcher" @click="toggleLanguage">
      <uni-icons type="globe" size="20" color="#fff"></uni-icons>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

// 响应式数据
const searchKeyword = ref('')
const activeCategoryIndex = ref(0)
const categories = ref([
  { id: 1, name: 'Electronics', nameZh: '电子产品', nameMs: 'Elektronik' },
  { id: 2, name: 'Clothing', nameZh: '服装', nameMs: 'Pakaian' },
  { id: 3, name: 'Home & Garden', nameZh: '家居园艺', nameMs: 'Rumah & Taman' },
  { id: 4, name: 'Sports', nameZh: '运动', nameMs: 'Sukan' },
  { id: 5, name: 'Books', nameZh: '图书', nameMs: 'Buku' },
  { id: 6, name: 'Beauty', nameZh: '美妆', nameMs: 'Kecantikan' },
  { id: 7, name: 'Toys', nameZh: '玩具', nameMs: 'Mainan' },
  { id: 8, name: 'Automotive', nameZh: '汽车用品', nameMs: 'Automotif' }
])

const products = ref([
  { id: 1, categoryId: 1, name: 'iPhone 15 Pro', nameZh: 'iPhone 15 Pro', nameMs: 'iPhone 15 Pro', price: 999, image: '/static/images/iphone.jpg' },
  { id: 2, categoryId: 1, name: 'MacBook Air', nameZh: 'MacBook Air', nameMs: 'MacBook Air', price: 1299, image: '/static/images/macbook.jpg' },
  { id: 3, categoryId: 2, name: 'Nike Air Max', nameZh: 'Nike Air Max', nameMs: 'Nike Air Max', price: 120, image: '/static/images/nike.jpg' },
  { id: 4, categoryId: 2, name: 'Adidas T-Shirt', nameZh: 'Adidas T恤', nameMs: 'Adidas T-Shirt', price: 25, image: '/static/images/adidas.jpg' },
  { id: 5, categoryId: 3, name: 'Coffee Maker', nameZh: '咖啡机', nameMs: 'Pembuat Kopi', price: 89, image: '/static/images/coffee.jpg' },
  { id: 6, categoryId: 4, name: 'Yoga Mat', nameZh: '瑜伽垫', nameMs: 'Mat Yoga', price: 35, image: '/static/images/yoga.jpg' }
])

// 计算属性
const currentCategoryProducts = computed(() => {
  const currentCategory = categories.value[activeCategoryIndex.value]
  if (!currentCategory) return []
  
  let filteredProducts = products.value.filter(product => product.categoryId === currentCategory.id)
  
  // 根据搜索关键词过滤
  if (searchKeyword.value) {
    filteredProducts = filteredProducts.filter(product => 
      product.name.toLowerCase().includes(searchKeyword.value.toLowerCase()) ||
      product.nameZh.toLowerCase().includes(searchKeyword.value.toLowerCase()) ||
      product.nameMs.toLowerCase().includes(searchKeyword.value.toLowerCase())
    )
  }
  
  return filteredProducts
})

// 方法
const selectCategory = (index: number) => {
  activeCategoryIndex.value = index
}

const handleSearch = () => {
  // 搜索逻辑已在计算属性中处理
}

const goToProductDetail = (productId: number) => {
  uni.navigateTo({
    url: `/pages/product/detail?id=${productId}`
  })
}

const toggleLanguage = () => {
  const languages = ['en', 'zh', 'ms']
  const currentIndex = languages.indexOf(locale.value)
  const nextIndex = (currentIndex + 1) % languages.length
  locale.value = languages[nextIndex]
  
  // 保存语言偏好
  uni.setStorageSync('language', languages[nextIndex])
}

// 生命周期
onMounted(() => {
  // 加载保存的语言偏好
  const savedLanguage = uni.getStorageSync('language')
  if (savedLanguage) {
    locale.value = savedLanguage
  }
})
</script>

<style scoped>
.category-page {
  height: 100vh;
  background-color: #f5f5f5;
  display: flex;
  flex-direction: column;
}

/* 搜索栏 */
.search-bar {
  padding: 20rpx;
  background-color: #fff;
  border-bottom: 1rpx solid #eee;
}

.search-input {
  display: flex;
  align-items: center;
  background-color: #f5f5f5;
  border-radius: 50rpx;
  padding: 20rpx 30rpx;
}

.search-input input {
  flex: 1;
  margin-left: 20rpx;
  font-size: 28rpx;
  color: #333;
}

/* 分类容器 */
.category-container {
  flex: 1;
  display: flex;
}

/* 左侧导航 */
.category-nav {
  width: 200rpx;
  background-color: #fff;
  border-right: 1rpx solid #eee;
}

.nav-scroll {
  height: 100%;
}

.nav-item {
  padding: 40rpx 20rpx;
  text-align: center;
  border-bottom: 1rpx solid #f5f5f5;
  transition: all 0.3s;
}

.nav-item.active {
  background-color: #007aff;
  color: #fff;
}

.nav-item.active .nav-text {
  color: #fff;
}

.nav-text {
  font-size: 26rpx;
  color: #333;
  line-height: 1.4;
}

/* 右侧商品列表 */
.product-list {
  flex: 1;
  background-color: #fff;
}

.product-scroll {
  height: 100%;
  padding: 20rpx;
}

.product-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20rpx;
}

.product-item {
  background-color: #fff;
  border-radius: 20rpx;
  overflow: hidden;
  box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.product-item:active {
  transform: scale(0.95);
}

.product-image {
  width: 100%;
  height: 200rpx;
  background-color: #f5f5f5;
}

.product-info {
  padding: 20rpx;
}

.product-name {
  font-size: 26rpx;
  color: #333;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  font-size: 32rpx;
  color: #ff6b35;
  font-weight: bold;
  margin-top: 10rpx;
  display: block;
}

/* 空状态 */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 400rpx;
  color: #999;
}

.empty-text {
  font-size: 28rpx;
  margin-top: 20rpx;
}

/* 语言切换器 */
.language-switcher {
  position: fixed;
  right: 30rpx;
  bottom: 100rpx;
  width: 80rpx;
  height: 80rpx;
  background-color: #007aff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4rpx 12rpx rgba(0, 122, 255, 0.3);
  z-index: 999;
}

.language-switcher:active {
  transform: scale(0.9);
}
</style>
