<template>
  <view class="home">
    <!-- 用户状态栏 -->
    <view class="user-status-bar">
      <view class="user-info" v-if="userInfo">
        <image :src="userInfo.avatar || '/static/default-avatar.png'" class="user-avatar" />
        <view class="user-details">
          <text class="user-name">{{ userInfo.nickname || userInfo.name || '用户' }}</text>
          <text class="user-phone">{{ userInfo.phone || '' }}</text>
        </view>
        <view class="logout-btn" @click="handleLogout">
          <uni-icons type="logout" size="16" color="#999"></uni-icons>
        </view>
      </view>
      <view class="login-prompt" v-else>
        <view class="welcome-text">
          <text class="welcome-title">{{ $t('home.welcome') }}</text>
          <text class="welcome-subtitle">{{ $t('home.welcomeSubtitle') }}</text>
        </view>
        <view class="auth-buttons">
          <view class="login-btn" @click="goToLogin">{{ $t('home.login') }}</view>
          <view class="register-btn" @click="goToRegister">{{ $t('home.register') }}</view>
        </view>
      </view>
    </view>

    <!-- 顶部搜索栏 -->
    <view class="search-bar">
      <view class="search-input" @click="goToSearch">
        <text class="search-placeholder">{{ $t('home.search') }}</text>
        <uni-icons type="search" size="18" color="#999"></uni-icons>
      </view>
    </view>

    <!-- 轮播图 -->
    <view class="banner-section">
      <swiper class="banner-swiper" :indicator-dots="true" :autoplay="true" :interval="3000" :duration="500">
        <swiper-item v-for="(banner, index) in banners" :key="index">
          <image :src="banner.image" class="banner-image" mode="aspectFill" @click="goToBanner(banner)" />
        </swiper-item>
      </swiper>
    </view>

    <!-- 分类导航 -->
    <view class="category-section">
      <view class="section-title">{{ $t('home.categories') }}</view>
      <view class="category-grid">
        <view 
          v-for="category in categories" 
          :key="category.id" 
          class="category-item"
          @click="goToCategory(category)"
        >
          <image :src="category.icon" class="category-icon" />
          <text class="category-name">{{ category.name }}</text>
        </view>
      </view>
    </view>

    <!-- 热销商品 -->
    <view class="hot-products-section">
      <view class="section-header">
        <text class="section-title">{{ $t('home.hotProducts') }}</text>
        <text class="view-all" @click="goToAllProducts">{{ $t('home.viewAll') }}</text>
      </view>
      
      <scroll-view scroll-x="true" class="product-scroll">
        <view class="product-list">
          <view 
            v-for="product in hotProducts" 
            :key="product.id" 
            class="product-item"
            @click="goToProduct(product)"
          >
            <image :src="product.image" class="product-image" mode="aspectFill" />
            <view class="product-info">
              <text class="product-name">{{ product.name }}</text>
              <view class="product-price">
                <text class="current-price">RM{{ product.price }}</text>
                <text class="original-price" v-if="product.originalPrice">RM{{ product.originalPrice }}</text>
              </view>
              <view class="product-sales">
                <text class="sales-text">{{ $t('product.sales') }}: {{ product.sales }}</text>
              </view>
            </view>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 新品推荐 -->
    <view class="new-products-section">
      <view class="section-header">
        <text class="section-title">{{ $t('home.newArrivals') }}</text>
        <text class="view-all" @click="goToAllProducts">{{ $t('home.viewAll') }}</text>
      </view>
      
      <view class="product-grid">
        <view 
          v-for="product in newProducts" 
          :key="product.id" 
          class="product-card"
          @click="goToProduct(product)"
        >
          <image :src="product.image" class="product-image" mode="aspectFill" />
          <view class="product-info">
            <text class="product-name">{{ product.name }}</text>
            <view class="product-price">
              <text class="current-price">RM{{ product.price }}</text>
              <text class="original-price" v-if="product.originalPrice">RM{{ product.originalPrice }}</text>
            </view>
            <view class="product-tags">
              <text class="tag new-tag" v-if="product.isNew">{{ $t('product.new') }}</text>
              <text class="tag hot-tag" v-if="product.isHot">{{ $t('product.hot') }}</text>
            </view>
          </view>
        </view>
      </view>
    </view>

    <!-- TikTok商城入口 -->
    <view class="tiktok-entry">
      <view class="tiktok-btn" @click="goToTikTokMall">
        <text class="tiktok-icon">🎵</text>
        <text class="tiktok-text">TikTok Mall</text>
      </view>
    </view>

    <!-- TikTok测试入口 -->
    <view class="test-entry">
      <view class="test-btn" @click="goToTest">
        <text class="test-icon">🧪</text>
        <text class="test-text">TikTok Test</text>
      </view>
    </view>

    <!-- 演示页面入口 -->
    <view class="demo-entry">
      <view class="demo-btn" @click="goToDemo">
        <uni-icons type="eye" size="16" color="#fff"></uni-icons>
        <text class="demo-text">虚拟数据演示</text>
      </view>
    </view>

    <!-- 语言切换器 -->
    <view class="language-switcher">
      <view class="switcher-item" @click="showLanguageModal = true">
        <uni-icons type="globe" size="16" color="#409EFF"></uni-icons>
        <text class="switcher-text">{{ currentLanguage.name }}</text>
      </view>
    </view>

    <!-- 语言选择弹窗 -->
    <uni-popup ref="languagePopup" type="bottom">
      <view class="language-modal">
        <view class="modal-header">
          <text class="modal-title">{{ $t('settings.language') }}</text>
          <uni-icons type="close" size="20" @click="showLanguageModal = false"></uni-icons>
        </view>
        <view class="language-list">
          <view 
            v-for="lang in languages" 
            :key="lang.code"
            class="language-item"
            :class="{ active: lang.code === currentLanguage.code }"
            @click="changeLanguage(lang.code)"
          >
            <text class="language-flag">{{ lang.flag }}</text>
            <text class="language-name">{{ lang.name }}</text>
            <uni-icons v-if="lang.code === currentLanguage.code" type="checkmarkempty" size="16" color="#409EFF"></uni-icons>
          </view>
    </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { languages, setLanguage } from '@/locale'
import { shouldShowDesktop } from '@/utils/deviceDetection'

const { t, locale } = useI18n()

const showLanguageModal = ref(false)
const userInfo = ref<any>(null)

// 轮播图数据
const banners = ref([
  {
    id: 1,
    image: '/static/banner1.jpg',
    title: 'New Year Sale',
    link: '/category/electronics'
  },
  {
    id: 2,
    image: '/static/banner2.jpg',
    title: 'Free Shipping',
    link: '/category/fashion'
  },
  {
    id: 3,
    image: '/static/banner3.jpg',
    title: 'Best Deals',
    link: '/category/home'
  }
])

// 分类数据
const categories = ref([
  { id: 1, name: 'Electronics', icon: '/static/category/electronics.png' },
  { id: 2, name: 'Fashion', icon: '/static/category/fashion.png' },
  { id: 3, name: 'Home & Garden', icon: '/static/category/home.png' },
  { id: 4, name: 'Sports', icon: '/static/category/sports.png' },
  { id: 5, name: 'Books', icon: '/static/category/books.png' },
  { id: 6, name: 'Beauty', icon: '/static/category/beauty.png' },
  { id: 7, name: 'Toys', icon: '/static/category/toys.png' },
  { id: 8, name: 'More', icon: '/static/category/more.png' }
])

// 热销商品
const hotProducts = ref([])

// 新品推荐
const newProducts = ref([])

// 当前语言
const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === locale.value) || languages[0]
})

// 跳转到登录页
const goToLogin = () => {
  uni.navigateTo({
    url: '/pages/login/login'
  })
}

// 跳转到注册页
const goToRegister = () => {
  uni.navigateTo({
    url: '/pages/register/register'
  })
}

// 退出登录
const handleLogout = () => {
  uni.showModal({
    title: t('common.warning'),
    content: t('profile.confirmLogout'),
    success: (res) => {
      if (res.confirm) {
        uni.removeStorageSync('token')
        uni.removeStorageSync('userInfo')
        userInfo.value = null
        uni.showToast({
          title: t('message.logoutSuccess'),
          icon: 'success'
        })
      }
    }
  })
}

// 跳转到搜索页
const goToSearch = () => {
  uni.navigateTo({
    url: '/pages/search/search'
  })
}

// 跳转到分类页
const goToCategory = (category: any) => {
  uni.navigateTo({
    url: `/pages/category/category?id=${category.id}&name=${category.name}`
  })
}

// 跳转到商品详情
const goToProduct = (product: any) => {
  uni.navigateTo({
    url: `/pages/product/detail?id=${product.id}`
  })
}

// 跳转到所有商品
const goToAllProducts = () => {
  uni.switchTab({
    url: '/pages/category/category'
  })
}

// 轮播图点击
const goToBanner = (banner: any) => {
  uni.navigateTo({
    url: banner.link
  })
}

// 跳转到TikTok商城
const goToTikTokMall = () => {
  uni.navigateTo({
    url: '/pages/tiktok-mall'
  })
}

// 跳转到测试页面
const goToTest = () => {
  uni.navigateTo({
    url: '/pages/test-tiktok'
  })
}

// 跳转到演示页面
const goToDemo = () => {
  uni.navigateTo({
    url: '/pages/demo'
  })
}

// 切换语言
const changeLanguage = (langCode: string) => {
  setLanguage(langCode)
  showLanguageModal.value = false
  uni.showToast({
    title: t('message.operationSuccess'),
    icon: 'success'
  })
}

onMounted(() => {
  // 立即执行设备检测
  console.log('页面加载完成，开始设备检测')
  
  // 检查设备类型并自动跳转
  checkDeviceAndRedirect()
  // 检查用户登录状态
  checkUserStatus()
  // 加载数据
  loadData()
})

// 检查设备类型并自动跳转
const checkDeviceAndRedirect = () => {
  console.log('开始设备检测...')
  
  try {
    // 获取系统信息
    const systemInfo = uni.getSystemInfoSync()
    console.log('系统信息:', systemInfo)
    
    // 简单的屏幕宽度检测
    const screenWidth = systemInfo.screenWidth || 0
    const platform = systemInfo.platform || ''
    
    console.log('屏幕宽度:', screenWidth, '平台:', platform)
    
    // 判断是否为桌面设备
    const isDesktop = screenWidth >= 1024 || 
                     platform === 'windows' || 
                     platform === 'mac' || 
                     platform === 'linux'
    
    console.log('是否为桌面设备:', isDesktop)
    
    if (isDesktop) {
      console.log('检测到桌面设备，准备跳转到桌面端')
      
      // 显示提示
      uni.showToast({
        title: '检测到桌面设备，跳转到桌面端',
        icon: 'none',
        duration: 1000
      })
      
      // 使用nextTick而不是setTimeout
      uni.nextTick(() => {
        console.log('执行跳转到桌面端')
        uni.redirectTo({
          url: '/pages/desktop/index'
        })
      })
    } else {
      console.log('保持移动端界面')
    }
  } catch (error) {
    console.error('设备检测失败:', error)
    
    // 如果检测失败，使用简单的屏幕宽度判断
    const screenWidth = window.innerWidth || 0
    console.log('备用检测 - 屏幕宽度:', screenWidth)
    
    if (screenWidth >= 1024) {
      console.log('备用检测 - 跳转到桌面端')
      uni.redirectTo({
        url: '/pages/desktop/index'
      })
    }
  }
}

// 检查用户登录状态
const checkUserStatus = () => {
  const storedUserInfo = uni.getStorageSync('userInfo')
  if (storedUserInfo) {
    userInfo.value = storedUserInfo
  }
}

const loadData = async () => {
  try {
    console.log('Loading home data...')
    
    // 使用真实API调用
    const [bannersRes, categoriesRes, hotProductsRes, newProductsRes] = await Promise.all([
      getBanners(),
      getCategories(),
      getHotProducts(),
      getNewProducts()
    ])
    
    // 更新数据
    banners.value = bannersRes.data || []
    categories.value = categoriesRes.data?.data || []
    hotProducts.value = hotProductsRes.data?.list || []
    newProducts.value = newProductsRes.data?.list || []
    
    console.log('Home data loaded successfully')
    
  } catch (error) {
    console.error('Failed to load home data:', error)
    // 如果API失败，使用默认数据
    console.log('Using default data due to API error')
  }
}
</script>

<style scoped>
.home {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
  position: relative;
}

.home::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 200px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  z-index: -1;
}

/* 用户状态栏 */
.user-status-bar {
  padding: 20px 20px 10px;
  background: transparent;
  position: relative;
  z-index: 10;
}

.user-info {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  padding: 15px;
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 20px;
  margin-right: 12px;
}

.user-details {
  flex: 1;
}

.user-name {
  display: block;
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 2px;
}

.user-phone {
  display: block;
  font-size: 12px;
  color: #666;
}

.logout-btn {
  padding: 8px;
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.05);
}

.login-prompt {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  padding: 15px;
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.welcome-text {
  flex: 1;
}

.welcome-title {
  display: block;
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 2px;
}

.welcome-subtitle {
  display: block;
  font-size: 12px;
  color: #666;
}

.auth-buttons {
  display: flex;
  gap: 8px;
}

.login-btn {
  background: #409EFF;
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
}

.register-btn {
  background: #67C23A;
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
}

/* 搜索栏 */
.search-bar {
  padding: 10px 20px 15px;
  background: transparent;
  position: relative;
  z-index: 10;
}

.search-input {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 30px;
  padding: 15px 25px;
  height: 50px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-input:active {
  transform: scale(0.98);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  background: rgba(255, 255, 255, 1);
}

.search-placeholder {
  color: #6c757d;
  font-size: 16px;
  font-weight: 500;
  letter-spacing: 0.5px;
}

/* 轮播图 */
.banner-section {
  margin: 25px 20px 20px;
  position: relative;
  z-index: 5;
}

.banner-swiper {
  height: 220px;
  border-radius: 25px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
  position: relative;
}

.banner-swiper::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  z-index: 1;
  pointer-events: none;
}

.banner-image {
  width: 100%;
  height: 100%;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  filter: brightness(1.05) contrast(1.1);
}

.banner-image:active {
  transform: scale(1.02);
}

/* 分类导航 */
.category-section {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  margin: 20px 20px;
  border-radius: 25px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.3);
  position: relative;
  overflow: hidden;
}

.category-section::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.05) 0%, transparent 70%);
  pointer-events: none;
}

.section-title {
  font-size: 20px;
  font-weight: 800;
  color: #2c3e50;
  margin-bottom: 25px;
  text-align: center;
  position: relative;
  letter-spacing: 0.5px;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 50px;
  height: 4px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 3px;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  position: relative;
  z-index: 1;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 15px;
  border-radius: 20px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.5);
  position: relative;
  overflow: hidden;
}

.category-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.category-item:active {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.category-item:active::before {
  opacity: 1;
}

.category-item:active .category-name {
  color: #fff;
  transform: scale(1.05);
}

.category-icon {
  width: 50px;
  height: 50px;
  margin-bottom: 12px;
  border-radius: 50%;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
}

.category-item:active .category-icon {
  transform: scale(1.1);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.category-name {
  font-size: 13px;
  color: #495057;
  text-align: center;
  font-weight: 700;
  transition: all 0.3s ease;
  letter-spacing: 0.3px;
  position: relative;
  z-index: 1;
}

/* 热销商品 */
.hot-products-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  margin: 15px 20px;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 30px;
  height: 3px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 2px;
}

.view-all {
  font-size: 14px;
  color: #667eea;
  font-weight: 600;
  padding: 5px 10px;
  border-radius: 15px;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  transition: all 0.3s ease;
}

.view-all:active {
  transform: scale(0.95);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}

.product-scroll {
  white-space: nowrap;
  padding: 5px 0;
}

.product-list {
  display: flex;
  gap: 20px;
  padding: 5px 0;
}

.product-item {
  flex-shrink: 0;
  width: 170px;
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.5);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.product-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.product-item:active {
  transform: translateY(-5px) scale(1.03);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.product-item:active::before {
  opacity: 1;
}

.product-image {
  width: 100%;
  height: 130px;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  filter: brightness(1.02) contrast(1.05);
  position: relative;
  z-index: 2;
}

.product-item:active .product-image {
  transform: scale(1.05);
}

.product-info {
  padding: 18px;
  position: relative;
  z-index: 2;
}

.product-name {
  font-size: 14px;
  color: #2c3e50;
  font-weight: 700;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 12px;
  line-height: 1.4;
  letter-spacing: 0.3px;
}

.product-price {
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}

.current-price {
  font-size: 17px;
  font-weight: 800;
  color: #e74c3c;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 1px 2px rgba(231, 76, 60, 0.2);
}

.original-price {
  font-size: 12px;
  color: #95a5a6;
  text-decoration: line-through;
  margin-left: 8px;
  font-weight: 600;
}

.sales-text {
  font-size: 11px;
  color: #7f8c8d;
  font-weight: 600;
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(41, 128, 185, 0.1) 100%);
  padding: 4px 10px;
  border-radius: 12px;
  display: inline-block;
  border: 1px solid rgba(52, 152, 219, 0.2);
  letter-spacing: 0.3px;
}

/* 新品推荐 */
.new-products-section {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  margin: 20px 20px 30px;
  border-radius: 25px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.3);
  position: relative;
  overflow: hidden;
}

.new-products-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(118, 75, 162, 0.05) 0%, transparent 70%);
  pointer-events: none;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  position: relative;
  z-index: 1;
}

.product-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.5);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.product-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.product-card:active {
  transform: translateY(-5px) scale(1.03);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.product-card:active::before {
  opacity: 1;
}

.product-card .product-image {
  width: 100%;
  height: 150px;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  filter: brightness(1.02) contrast(1.05);
  position: relative;
  z-index: 2;
}

.product-card:active .product-image {
  transform: scale(1.05);
}

.product-card .product-info {
  padding: 18px;
  position: relative;
  z-index: 2;
}

.product-card .product-name {
  font-size: 15px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 14px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  letter-spacing: 0.3px;
}

.product-card .product-price {
  margin-bottom: 12px;
}

.product-card .current-price {
  font-size: 17px;
  font-weight: 800;
  color: #e74c3c;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 1px 2px rgba(231, 76, 60, 0.2);
}

.product-card .original-price {
  font-size: 12px;
  color: #95a5a6;
  text-decoration: line-through;
  margin-left: 8px;
  font-weight: 600;
}

.product-tags {
  margin-top: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.tag {
  font-size: 10px;
  font-weight: 700;
  padding: 5px 10px;
  border-radius: 15px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 2;
}

.new-tag {
  background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
  color: #fff;
  box-shadow: 0 3px 10px rgba(39, 174, 96, 0.3);
}

.hot-tag {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  color: #fff;
  box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
}

/* TikTok商城入口 */
.tiktok-entry {
  position: fixed;
  bottom: 360px;
  right: 20px;
  z-index: 999;
}

.tiktok-btn {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #ff0050 0%, #ff4081 100%);
  color: #fff;
  padding: 14px 18px;
  border-radius: 30px;
  font-size: 14px;
  font-weight: 700;
  box-shadow: 0 10px 30px rgba(255, 0, 80, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.tiktok-btn:active {
  transform: scale(0.95);
  box-shadow: 0 6px 20px rgba(255, 0, 80, 0.6);
}

.tiktok-icon {
  font-size: 16px;
  margin-right: 8px;
}

.tiktok-text {
  letter-spacing: 0.3px;
}

/* TikTok测试入口 */
.test-entry {
  position: fixed;
  bottom: 280px;
  right: 20px;
  z-index: 999;
}

.test-btn {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  color: #fff;
  padding: 14px 18px;
  border-radius: 30px;
  font-size: 14px;
  font-weight: 700;
  box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.test-btn:active {
  transform: scale(0.95);
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.6);
}

.test-icon {
  font-size: 16px;
  margin-right: 8px;
}

.test-text {
  letter-spacing: 0.3px;
}

/* 演示页面入口 */
.demo-entry {
  position: fixed;
  bottom: 200px;
  right: 20px;
  z-index: 999;
}

.demo-btn {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  color: #fff;
  padding: 14px 18px;
  border-radius: 30px;
  font-size: 14px;
  font-weight: 700;
  box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.demo-btn:active {
  transform: scale(0.95);
  box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
}

.demo-text {
  margin-left: 8px;
  letter-spacing: 0.3px;
}

/* 语言切换器 */
.language-switcher {
  position: fixed;
  bottom: 120px;
  right: 20px;
  z-index: 999;
}

.switcher-item {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  padding: 14px 18px;
  border-radius: 30px;
  font-size: 14px;
  font-weight: 700;
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.switcher-item:active {
  transform: scale(0.95);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.switcher-text {
  margin-left: 8px;
  letter-spacing: 0.3px;
}

/* 语言选择弹窗 */
.language-modal {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 30px 30px 0 0;
  padding: 30px;
  box-shadow: 0 -15px 40px rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid rgba(102, 126, 234, 0.1);
}

.modal-title {
  font-size: 22px;
  font-weight: 800;
  color: #2c3e50;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: 0.5px;
}

.language-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.language-item {
  display: flex;
  align-items: center;
  padding: 20px 25px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.language-item:active {
  transform: scale(0.98);
}

.language-item.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.language-item.active .language-name {
  color: #fff;
  font-weight: 800;
}

.language-flag {
  font-size: 28px;
  margin-right: 18px;
  filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.1));
}

.language-name {
  flex: 1;
  font-size: 17px;
  color: #2c3e50;
  font-weight: 700;
  transition: all 0.3s ease;
  letter-spacing: 0.3px;
}
</style>