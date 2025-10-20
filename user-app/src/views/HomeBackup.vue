<template>
  <div class="home" :class="{ 'dark-theme': isDarkTheme }">
    <!-- TikTok风格顶部导航 -->
    <div class="tiktok-header">
      <div class="header-content">
        <div class="logo-section">
          <div class="tiktok-logo">♪</div>
          <span class="brand-name">TikTok Shop</span>
        </div>
        
        <div class="search-section">
          <div class="search-container">
            <el-input
              v-model="searchQuery"
              placeholder="Search"
              @keyup.enter="handleSearch"
              class="tiktok-search"
            >
              <template #prefix>
                <el-icon><Search /></el-icon>
              </template>
            </el-input>
            <el-button type="primary" @click="handleSearch" class="search-btn">
              Search
            </el-button>
          </div>
        </div>
        
        <div class="user-section">
          <el-button circle class="user-btn">
            <el-icon><User /></el-icon>
          </el-button>
        </div>
      </div>
      
      <!-- 分类导航标签 -->
      <div class="category-tabs">
        <div class="tab-item active">All</div>
        <div class="tab-item">Womenswear & Underwear</div>
        <div class="tab-item">Phones & Electronics</div>
        <div class="tab-item">Fashion Accessories</div>
        <div class="tab-item">Menswear & Underwear</div>
      </div>
    </div>

    <!-- 主要内容区域 -->
    <div class="main-content">
      <!-- 分类区域 -->
      <section class="categories-section">
        <h2 class="section-title">Categories</h2>
        <div class="categories-grid">
          <div 
            v-for="category in categories" 
            :key="category.id"
            class="category-card"
            @click="goToCategory(category)"
          >
            <div class="category-image">
              <img :src="category.icon" :alt="category.name" />
            </div>
            <span class="category-label">{{ category.name }}</span>
          </div>
        </div>
      </section>

      <!-- 优惠商品区域 -->
      <section class="savings-section">
        <h2 class="section-title">Savings for you</h2>
        <div class="products-grid" v-loading="loadingProducts">
          <ProductCard 
            v-for="product in products" 
            :key="product.id"
            :product="product"
            :is-dark="isDarkTheme"
          />
        </div>
        
        <div class="load-more" v-if="hasMoreProducts">
          <el-button @click="loadMoreProducts" :loading="loadingMore" class="load-more-btn">
            Load More
          </el-button>
        </div>
      </section>
    </div>
    
    <!-- 底部应用推广 -->
    <div class="app-promotion" v-if="showAppPromotion">
      <div class="promotion-content">
        <div class="promotion-left">
          <div class="tiktok-logo-small">♪</div>
          <div class="promotion-text">
            <div class="app-name">TikTok</div>
            <div class="app-desc">Discovery e-commerce</div>
          </div>
        </div>
        <div class="promotion-right">
          <el-button type="primary" class="open-app-btn">Open app</el-button>
          <el-button circle class="close-btn" @click="showAppPromotion = false">
            <el-icon><Close /></el-icon>
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { bannerApi, categoryApi, productApi } from '@/api'
import ProductCard from '@/components/ProductCard.vue'
import { Search, User, Close } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'

const router = useRouter()

// 数据状态
const banners = ref<any[]>([])
const categories = ref<any[]>([])
const products = ref<any[]>([])
const loadingProducts = ref(false)
const loadingMore = ref(false)
const hasMoreProducts = ref(true)
const currentPage = ref(1)
const pageSize = ref(12)
const searchQuery = ref('')
const isDarkTheme = ref(true) // 默认深色主题
const showAppPromotion = ref(true)

// 加载轮播图
const loadBanners = async () => {
  try {
    const response = await bannerApi.getBanners()
    banners.value = response || []
  } catch (error) {
    console.error('加载轮播图失败:', error)
    // 使用默认轮播图
    banners.value = [
      {
        id: 1,
        title: '新品上市',
        description: '精选好物，限时优惠',
        image: 'https://via.placeholder.com/800x300/409EFF/ffffff?text=新品上市',
        link: '/products?sort=newest'
      },
      {
        id: 2,
        title: '热销推荐',
        description: '人气商品，品质保证',
        image: 'https://via.placeholder.com/800x300/67C23A/ffffff?text=热销推荐',
        link: '/products?sort=popular'
      },
      {
        id: 3,
        title: '特价促销',
        description: '限时特价，错过再等一年',
        image: 'https://via.placeholder.com/800x300/E6A23C/ffffff?text=特价促销',
        link: '/products?sort=discount'
      }
    ]
  }
}

// 加载分类
const loadCategories = async () => {
  try {
    const response = await categoryApi.getCategories()
    categories.value = response || []
  } catch (error) {
    console.error('加载分类失败:', error)
    // 使用默认分类
    categories.value = [
      { 
        id: 1, 
        name: '电子产品', 
        icon: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 2, 
        name: '服装鞋帽', 
        icon: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 3, 
        name: '家居生活', 
        icon: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 4, 
        name: '美妆护肤', 
        icon: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 5, 
        name: '食品饮料', 
        icon: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 6, 
        name: '运动户外', 
        icon: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 7, 
        name: '图书文具', 
        icon: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      },
      { 
        id: 8, 
        name: '母婴用品', 
        icon: 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=120&h=120&fit=crop&crop=center&auto=format&q=80' 
      }
    ]
  }
}

// 加载商品
const loadProducts = async (page = 1, append = false) => {
  try {
    if (page === 1) {
      loadingProducts.value = true
    } else {
      loadingMore.value = true
    }

    const response = await productApi.getProducts({
      page,
      limit: pageSize.value,
      sort: 'popular'
    })

    const newProducts = response.products || []
    
    if (append) {
      products.value = [...products.value, ...newProducts]
    } else {
      products.value = newProducts
    }

    hasMoreProducts.value = newProducts.length === pageSize.value
    currentPage.value = page
  } catch (error) {
    console.error('加载商品失败:', error)
    ElMessage.error('加载商品失败')
    
    // 使用默认商品数据
    if (!append) {
      products.value = [
        {
          id: '1',
          name: 'iPhone 15 Pro Max',
          description: '全新A17 Pro芯片，钛金属设计',
          price: 4999,
          originalPrice: 5299,
          image: 'https://via.placeholder.com/300x200/409EFF/ffffff?text=iPhone+15+Pro+Max',
          badge: '热销',
          rating: 4.8,
          reviewCount: 1250
        },
        {
          id: '2',
          name: 'MacBook Pro 16',
          description: 'M3 Max芯片，专业级性能',
          price: 19999,
          originalPrice: 21999,
          image: 'https://via.placeholder.com/300x200/67C23A/ffffff?text=MacBook+Pro+16',
          badge: '新品',
          rating: 4.9,
          reviewCount: 856
        },
        {
          id: '3',
          name: 'AirPods Pro 2',
          description: '主动降噪，空间音频',
          price: 1899,
          originalPrice: 1999,
          image: 'https://via.placeholder.com/300x200/E6A23C/ffffff?text=AirPods+Pro+2',
          rating: 4.7,
          reviewCount: 2340
        },
        {
          id: '4',
          name: 'iPad Air 5',
          description: 'M1芯片，10.9英寸屏幕',
          price: 4399,
          originalPrice: 4599,
          image: 'https://via.placeholder.com/300x200/F56C6C/ffffff?text=iPad+Air+5',
          badge: '特价',
          rating: 4.6,
          reviewCount: 1890
        }
      ]
    }
  } finally {
    loadingProducts.value = false
    loadingMore.value = false
  }
}

// 加载更多商品
const loadMoreProducts = () => {
  loadProducts(currentPage.value + 1, true)
}

// 跳转到分类页面
const goToCategory = (category: any) => {
  router.push(`/category/${category.id}`)
}

// 跳转到所有商品页面
const goToAllProducts = () => {
  router.push('/search')
}

// 跳转到轮播图链接
const goToBannerLink = (banner: any) => {
  if (banner.link) {
    router.push(banner.link)
  }
}

// 页面加载时获取数据
onMounted(async () => {
  try {
    await Promise.all([
      loadBanners(),
      loadCategories(),
      loadProducts()
    ])
  } catch (error) {
    console.error('页面初始化失败:', error)
    // 确保至少有默认数据
    if (products.value.length === 0) {
      products.value = [
        {
          id: '1',
          name: 'iPhone 15 Pro Max',
          description: '全新A17 Pro芯片，钛金属设计',
          price: 4999,
          originalPrice: 5299,
          image: 'https://via.placeholder.com/300x200/409EFF/ffffff?text=iPhone+15+Pro+Max',
          badge: '热销',
          rating: 4.8,
          reviewCount: 1250
        },
        {
          id: '2',
          name: 'MacBook Pro 16',
          description: 'M3 Max芯片，专业级性能',
          price: 19999,
          originalPrice: 21999,
          image: 'https://via.placeholder.com/300x200/67C23A/ffffff?text=MacBook+Pro+16',
          badge: '新品',
          rating: 4.9,
          reviewCount: 856
        },
        {
          id: '3',
          name: 'AirPods Pro 2',
          description: '主动降噪，空间音频',
          price: 1899,
          originalPrice: 1999,
          image: 'https://via.placeholder.com/300x200/E6A23C/ffffff?text=AirPods+Pro+2',
          rating: 4.7,
          reviewCount: 2340
        },
        {
          id: '4',
          name: 'iPad Air 5',
          description: 'M1芯片，10.9英寸屏幕',
          price: 4399,
          originalPrice: 4599,
          image: 'https://via.placeholder.com/300x200/F56C6C/ffffff?text=iPad+Air+5',
          badge: '特价',
          rating: 4.6,
          reviewCount: 1890
        }
      ]
    }
  }
})
</script>

<style scoped lang="scss">
.home {
  min-height: 100vh;
  background: #000;
  color: #fff;
  
  &.dark-theme {
    background: #000;
    color: #fff;
  }
}

.tiktok-header {
  background: #000;
  border-bottom: 1px solid #333;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 10px;
  
  .tiktok-logo {
    font-size: 24px;
    color: #ff0050;
    font-weight: bold;
  }
  
  .brand-name {
    font-size: 18px;
    font-weight: bold;
    color: #fff;
  }
}

.search-section {
  flex: 1;
  max-width: 500px;
  margin: 0 20px;
}

.search-container {
  display: flex;
  border: 2px solid #ff0050;
  border-radius: 8px;
  overflow: hidden;
  
  .tiktok-search {
    flex: 1;
    
    :deep(.el-input__wrapper) {
      background: transparent;
      border: none;
      box-shadow: none;
      
      .el-input__inner {
        color: #fff;
        background: transparent;
        
        &::placeholder {
          color: #999;
        }
      }
    }
  }
  
  .search-btn {
    background: #ff0050;
    border: none;
    border-radius: 0;
    padding: 0 20px;
    
    &:hover {
      background: #e6004a;
    }
  }
}

.user-section {
  .user-btn {
    background: #333;
    border: 1px solid #555;
    color: #fff;
    
    &:hover {
      background: #444;
    }
  }
}

.category-tabs {
  display: flex;
  padding: 0 20px;
  max-width: 1200px;
  margin: 0 auto;
  border-top: 1px solid #333;
  
  .tab-item {
    padding: 15px 20px;
    color: #999;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
    
    &:hover {
      color: #fff;
    }
    
    &.active {
      color: #fff;
      border-bottom-color: #fff;
    }
  }
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.categories-section {
  margin-bottom: 40px;
  
  .section-title {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 20px;
  }
  
  .categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 20px;
  }
  
  .category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #111;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:hover {
      background: #222;
      transform: translateY(-2px);
    }
    
    .category-image {
      width: 60px;
      height: 60px;
      margin-bottom: 10px;
      border-radius: 50%;
      overflow: hidden;
      background: #333;
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    
    .category-label {
      font-size: 12px;
      color: #fff;
      text-align: center;
      line-height: 1.3;
    }
  }
}

.savings-section {
  .section-title {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 20px;
  }
  
  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .load-more {
    text-align: center;
    
    .load-more-btn {
      background: #ff0050;
      border: none;
      color: #fff;
      padding: 12px 30px;
      
      &:hover {
        background: #e6004a;
      }
    }
  }
}

.app-promotion {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #111;
  border-top: 1px solid #333;
  z-index: 1000;
  
  .promotion-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .promotion-left {
    display: flex;
    align-items: center;
    gap: 15px;
    
    .tiktok-logo-small {
      font-size: 20px;
      color: #ff0050;
      font-weight: bold;
    }
    
    .promotion-text {
      .app-name {
        font-size: 16px;
        font-weight: bold;
        color: #fff;
      }
      
      .app-desc {
        font-size: 12px;
        color: #999;
      }
    }
  }
  
  .promotion-right {
    display: flex;
    align-items: center;
    gap: 10px;
    
    .open-app-btn {
      background: #ff0050;
      border: none;
      color: #fff;
      
      &:hover {
        background: #e6004a;
      }
    }
    
    .close-btn {
      background: #333;
      border: 1px solid #555;
      color: #fff;
      
      &:hover {
        background: #444;
      }
    }
  }
}

@media (max-width: 768px) {
  .header-content {
    padding: 10px 15px;
  }
  
  .search-section {
    margin: 0 10px;
  }
  
  .category-tabs {
    padding: 0 15px;
    overflow-x: auto;
    
    .tab-item {
      padding: 12px 15px;
      white-space: nowrap;
    }
  }
  
  .main-content {
    padding: 15px;
  }
  
  .categories-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
  }
  
  .category-card {
    padding: 15px;
    
    .category-image {
      width: 50px;
      height: 50px;
    }
    
    .category-label {
      font-size: 11px;
    }
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
  }
  
  .app-promotion {
    .promotion-content {
      padding: 12px 15px;
    }
  }
}
</style>
