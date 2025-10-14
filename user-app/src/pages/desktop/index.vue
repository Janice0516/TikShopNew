<template>
  <view class="desktop-container">
    <!-- 顶部导航栏 -->
    <view class="top-header">
      <view class="header-content">
        <view class="logo-section">
          <image src="/static/logo.png" class="logo" />
          <text class="logo-text">TikTok Shop</text>
        </view>
        
        <view class="header-actions">
          <view class="get-app-btn">Get app</view>
          <view class="login-btn" @click="goToLogin">Log in</view>
        </view>
      </view>
    </view>

    <!-- 主要内容区域 -->
    <view class="main-content">
      <!-- 左侧导航栏 -->
      <view class="left-sidebar">
        <view class="sidebar-content">
          <!-- 导航链接 -->
          <view class="nav-links">
            <view class="nav-item" @click="goToSell">
              <uni-icons type="shop" size="20" color="#333"></uni-icons>
              <text class="nav-text">Sell</text>
            </view>
            <view class="nav-item">
              <uni-icons type="more" size="20" color="#333"></uni-icons>
              <text class="nav-text">More</text>
            </view>
          </view>

          <!-- 登录按钮 -->
          <view class="sidebar-login-btn" @click="goToLogin">
            Log in
          </view>

          <!-- 底部链接 -->
          <view class="footer-links">
            <text class="footer-link">Start shopping</text>
            <text class="footer-link">Make money with us</text>
            <text class="footer-link">Company info</text>
            <text class="footer-link">Customer support</text>
            <text class="footer-link">Policy and legal</text>
          </view>
        </view>
      </view>

      <!-- 右侧内容区域 -->
      <view class="right-content">
        <!-- 分类区域 -->
        <view class="categories-section">
          <text class="section-title">Categories</text>
          <scroll-view scroll-x="true" class="categories-scroll">
            <view class="categories-list">
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
          </scroll-view>
        </view>

        <!-- 优惠商品区域 -->
        <view class="savings-section">
          <text class="section-title">Savings for you</text>
          <scroll-view scroll-x="true" class="products-scroll">
            <view class="products-list">
              <view 
                v-for="product in savingsProducts" 
                :key="product.id"
                class="product-card"
                @click="goToProduct(product)"
              >
                <image :src="product.image" class="product-image" />
                <view class="product-info">
                  <text class="product-title">{{ product.name }}</text>
                  <view class="product-rating">
                    <uni-icons type="star-filled" size="12" color="#ffa500"></uni-icons>
                    <text class="rating-text">{{ product.rating }}</text>
                    <text class="sales-text">{{ product.sales }} sold</text>
                  </view>
                  <view class="product-price">
                    <text class="current-price">RM{{ product.price }}</text>
                    <text class="original-price" v-if="product.originalPrice">RM{{ product.originalPrice }}</text>
                  </view>
                </view>
              </view>
            </view>
          </scroll-view>
        </view>

        <!-- 热销商品区域 -->
        <view class="hot-products-section">
          <text class="section-title">Hot Products</text>
          <scroll-view scroll-x="true" class="products-scroll">
            <view class="products-list">
              <view 
                v-for="product in hotProducts" 
                :key="product.id"
                class="product-card"
                @click="goToProduct(product)"
              >
                <image :src="product.image" class="product-image" />
                <view class="product-info">
                  <text class="product-title">{{ product.name }}</text>
                  <view class="product-rating">
                    <uni-icons type="star-filled" size="12" color="#ffa500"></uni-icons>
                    <text class="rating-text">{{ product.rating }}</text>
                    <text class="sales-text">{{ product.sales }} sold</text>
                  </view>
                  <view class="product-price">
                    <text class="current-price">RM{{ product.price }}</text>
                    <text class="original-price" v-if="product.originalPrice">RM{{ product.originalPrice }}</text>
                  </view>
                </view>
              </view>
            </view>
          </scroll-view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { shouldShowMobile } from '@/utils/deviceDetection'

// 响应式数据
const categories = ref([
  { id: 1, name: 'Womenswear & Underwear', icon: '/static/categories/womenswear.png' },
  { id: 2, name: 'Phones & Electronics', icon: '/static/categories/electronics.png' },
  { id: 3, name: 'Fashion Accessories', icon: '/static/categories/accessories.png' },
  { id: 4, name: 'Menswear & Underwear', icon: '/static/categories/menswear.png' },
  { id: 5, name: 'Home Supplies', icon: '/static/categories/home.png' },
  { id: 6, name: 'Beauty & Personal Care', icon: '/static/categories/beauty.png' },
  { id: 7, name: 'Shoes', icon: '/static/categories/shoes.png' },
  { id: 8, name: 'Sports & Outdoor', icon: '/static/categories/sports.png' },
  { id: 9, name: 'Luggage & Bags', icon: '/static/categories/luggage.png' }
])

const savingsProducts = ref([
  {
    id: 1,
    name: 'New Arrival | Bawal Nura- Premium Tudung Bawal Cotton Diamond | 45 Exquisite Designs',
    image: '/static/products/hijab.jpg',
    rating: 4.7,
    sales: '2.2K',
    price: 10.50,
    originalPrice: 20.00
  },
  {
    id: 2,
    name: 'Balincer Vitamin C, 1000 mg, Suplemen Kesihatan, Menyokong Imuniti',
    image: '/static/products/vitamin.jpg',
    rating: 4.7,
    sales: '4.8K',
    price: 17.49,
    originalPrice: 31.80
  },
  {
    id: 3,
    name: 'Pembersih Buih Pelbagai Fungsi, Kathwi, 500ml, untuk Pembersihan Isi Rumah',
    image: '/static/products/cleaner.jpg',
    rating: 4.5,
    sales: '31.6K',
    price: 19.50,
    originalPrice: 48.75
  },
  {
    id: 4,
    name: 'ZEKE B0037 Lelaki Gemuk Seluar Panjang Cargo Pants Men Straight Leg',
    image: '/static/products/cargo.jpg',
    rating: 4.7,
    sales: '37.8K',
    price: 19.90,
    originalPrice: 39.90
  },
  {
    id: 5,
    name: 'Penbose Seluar Lampin Bayi Saiz S-XXXL 50 Keping Super Serap',
    image: '/static/products/diapers.jpg',
    rating: 4.8,
    sales: '242.6K',
    price: 10.01,
    originalPrice: 18.00
  }
])

const hotProducts = ref([])

// 方法
const goToLogin = () => {
  uni.navigateTo({
    url: '/pages/login/login'
  })
}

const goToSell = () => {
  uni.showToast({
    title: 'Sell feature coming soon',
    icon: 'none'
  })
}

const goToCategory = (category: any) => {
  uni.navigateTo({
    url: `/pages/category/category?id=${category.id}&name=${category.name}`
  })
}

const goToProduct = (product: any) => {
  uni.navigateTo({
    url: `/pages/product/detail?id=${product.id}`
  })
}

// 加载数据
const loadData = async () => {
  try {
    // 加载分类数据
    const { getCategories } = await import('@/api/index')
    const categoriesRes = await getCategories()
    if (categoriesRes.data?.data) {
      categories.value = categoriesRes.data.data.slice(0, 9)
    }

    // 加载热销商品
    const { getHotProducts } = await import('@/api/index')
    const hotProductsRes = await getHotProducts()
    if (hotProductsRes.data?.list) {
      hotProducts.value = hotProductsRes.data.list.slice(0, 5)
    }

  } catch (error) {
    console.error('Failed to load desktop data:', error)
  }
}

onMounted(() => {
  // 检查设备类型，如果是移动设备则跳转回移动端
  checkDeviceAndRedirect()
  // 加载数据
  loadData()
})

// 检查设备类型并自动跳转
const checkDeviceAndRedirect = () => {
  console.log('桌面端页面开始设备检测...')
  
  try {
    // 获取系统信息
    const systemInfo = uni.getSystemInfoSync()
    console.log('桌面端系统信息:', systemInfo)
    
    // 简单的屏幕宽度检测
    const screenWidth = systemInfo.screenWidth || 0
    const platform = systemInfo.platform || ''
    
    console.log('桌面端屏幕宽度:', screenWidth, '平台:', platform)
    
    // 判断是否为移动设备
    const isMobile = screenWidth < 1024 && 
                    (platform === 'ios' || platform === 'android')
    
    console.log('是否为移动设备:', isMobile)
    
    if (isMobile) {
      console.log('检测到移动设备，准备跳转到移动端子域名')
      
      // 显示提示
      uni.showToast({
        title: '检测到移动设备，跳转到移动端',
        icon: 'none',
        duration: 1000
      })
      
      // 跳转到移动端子域名
      uni.nextTick(() => {
        console.log('执行跳转到移动端子域名')
        const currentUrl = window.location.href
        const mobileUrl = currentUrl.replace('tikshop-user.onrender.com', 'm.tikshop-user.onrender.com')
        console.log('跳转URL:', mobileUrl)
        window.location.href = mobileUrl
      })
    } else {
      console.log('保持桌面端界面')
    }
  } catch (error) {
    console.error('桌面端设备检测失败:', error)
  }
}
</script>

<style scoped>
.desktop-container {
  min-height: 100vh;
  background-color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 顶部导航栏 */
.top-header {
  background-color: #fff;
  border-bottom: 1px solid #e5e5e5;
  padding: 0 20px;
  height: 60px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo {
  width: 32px;
  height: 32px;
}

.logo-text {
  font-size: 20px;
  font-weight: bold;
  color: #000;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.get-app-btn {
  padding: 8px 16px;
  border: 1px solid #e5e5e5;
  border-radius: 6px;
  font-size: 14px;
  color: #333;
  cursor: pointer;
}

.login-btn {
  padding: 8px 16px;
  background-color: #ff0050;
  color: #fff;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

/* 主要内容区域 */
.main-content {
  display: flex;
  min-height: calc(100vh - 60px);
}

/* 左侧导航栏 */
.left-sidebar {
  width: 200px;
  background-color: #fff;
  border-right: 1px solid #e5e5e5;
  padding: 20px 0;
}

.sidebar-content {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 0 20px;
}

.nav-links {
  margin-bottom: 30px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 0;
  cursor: pointer;
  border-radius: 6px;
  transition: background-color 0.2s;
}

.nav-item:hover {
  background-color: #f5f5f5;
}

.nav-text {
  font-size: 14px;
  color: #333;
}

.sidebar-login-btn {
  background-color: #ff0050;
  color: #fff;
  padding: 12px 20px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  cursor: pointer;
  margin-bottom: 30px;
}

.footer-links {
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.footer-link {
  font-size: 12px;
  color: #999;
  cursor: pointer;
}

.footer-link:hover {
  color: #333;
}

/* 右侧内容区域 */
.right-content {
  flex: 1;
  padding: 20px;
  background-color: #fafafa;
}

.section-title {
  font-size: 24px;
  font-weight: bold;
  color: #000;
  margin-bottom: 20px;
  display: block;
}

/* 分类区域 */
.categories-section {
  margin-bottom: 40px;
}

.categories-scroll {
  white-space: nowrap;
}

.categories-list {
  display: flex;
  gap: 20px;
  padding: 10px 0;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 100px;
  cursor: pointer;
  transition: transform 0.2s;
}

.category-item:hover {
  transform: translateY(-2px);
}

.category-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-bottom: 8px;
  object-fit: cover;
}

.category-name {
  font-size: 12px;
  color: #333;
  text-align: center;
  line-height: 1.3;
}

/* 商品区域 */
.savings-section,
.hot-products-section {
  margin-bottom: 40px;
}

.products-scroll {
  white-space: nowrap;
}

.products-list {
  display: flex;
  gap: 20px;
  padding: 10px 0;
}

.product-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  min-width: 200px;
  cursor: pointer;
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.product-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px 8px 0 0;
}

.product-info {
  padding: 15px;
}

.product-title {
  font-size: 14px;
  color: #333;
  line-height: 1.4;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 8px;
}

.rating-text {
  font-size: 12px;
  color: #333;
}

.sales-text {
  font-size: 12px;
  color: #999;
}

.product-price {
  display: flex;
  align-items: center;
  gap: 8px;
}

.current-price {
  font-size: 16px;
  font-weight: bold;
  color: #ff0050;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .main-content {
    flex-direction: column;
  }
  
  .left-sidebar {
    width: 100%;
    height: auto;
  }
  
  .sidebar-content {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  
  .nav-links {
    display: flex;
    gap: 20px;
    margin-bottom: 0;
  }
  
  .footer-links {
    display: none;
  }
}
</style>
