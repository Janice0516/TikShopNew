<template>
  <div class="tiktok-shop">
    <!-- 顶部导航栏 -->
    <header class="top-header">
      <div class="header-content">
        <div class="header-left">
          <div class="logo">
            <span class="tiktok-icon">♪</span>
            <span class="logo-text">TikTok Shop</span>
          </div>
        </div>
        
        <div class="header-right">
          <button class="get-app-btn">Get app</button>
          <button class="login-btn">Log in</button>
        </div>
      </div>
    </header>

    <div class="main-layout">
      <!-- 左侧边栏 -->
      <aside class="sidebar">
        <div class="sidebar-content">
          <div class="sidebar-logo">
            <span class="tiktok-icon">♪</span>
            <span class="logo-text">TikTok Shop</span>
          </div>
          
          <nav class="sidebar-nav">
            <div class="nav-item">
              <span class="nav-icon">🛍️</span>
              <span class="nav-text">Sell</span>
            </div>
            <div class="nav-item">
              <span class="nav-icon">⋯</span>
              <span class="nav-text">More</span>
            </div>
          </nav>
          
          <div class="sidebar-login">
            <button class="login-btn-large">Log in</button>
          </div>
          
          <div class="sidebar-footer">
            <a href="#" class="footer-link">Start shopping</a>
            <a href="#" class="footer-link">Make money with us</a>
            <a href="#" class="footer-link">Company info</a>
            <a href="#" class="footer-link">Customer support</a>
            <a href="#" class="footer-link">Policy and legal</a>
          </div>
        </div>
      </aside>

      <!-- 主内容区域 -->
      <main class="main-content">
        <!-- 分类区域 -->
        <section class="categories-section">
          <h2 class="section-title">Categories</h2>
          <div class="categories-container">
            <div class="categories-scroll">
              <div 
                v-for="category in categories" 
                :key="category.id"
                class="category-item"
                @click="goToCategory(category)"
              >
                <div class="category-icon">
                  <img :src="category.icon" :alt="category.name" />
                </div>
                <span class="category-name">{{ category.name }}</span>
              </div>
            </div>
            <button class="scroll-arrow">→</button>
          </div>
        </section>

        <!-- 优惠商品区域 -->
        <section class="savings-section">
          <h2 class="section-title">Savings for you</h2>
          <div class="products-container">
            <div class="products-list">
              <div 
                v-for="product in products" 
                :key="product.id"
                class="product-card"
                @click="goToProduct(product)"
              >
                <!-- 商品横幅 -->
                <div v-if="product.banner" class="product-banner">
                  {{ product.banner }}
                </div>
                
                <!-- 商品图片 -->
                <div class="product-image-container">
                  <img :src="product.image" :alt="product.name" class="product-image" />
                  
                  <!-- 商品徽章 -->
                  <div v-if="product.badge" class="product-badge">
                    <div class="badge-content">
                      <div class="badge-title">{{ product.badge.title }}</div>
                      <div class="badge-price">{{ product.badge.price }}</div>
                      <div class="badge-discount">{{ product.badge.discount }}</div>
                    </div>
                  </div>
                  
                  <!-- 倒计时 -->
                  <div v-if="product.timer" class="product-timer">
                    {{ product.timer }}
                  </div>
                </div>
                
                <!-- 商品信息 -->
                <div class="product-info">
                  <h3 class="product-name">{{ product.name }}</h3>
                  <p class="product-description">{{ product.description }}</p>
                  
                  <!-- 评分和销量 -->
                  <div class="product-stats">
                    <div v-if="product.rating" class="product-rating">
                      <span class="rating-stars">★</span>
                      <span class="rating-score">{{ product.rating }}</span>
                    </div>
                    <div v-if="product.sales" class="product-sales">
                      {{ formatSales(product.sales) }} sold
                    </div>
                  </div>
                  
                  <!-- 价格 -->
                  <div class="product-pricing">
                    <div class="current-price">RM{{ product.price }}</div>
                    <div v-if="product.originalPrice" class="original-price">RM{{ product.originalPrice }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { productApi, categoryApi } from '@/api'
import { ElMessage } from 'element-plus'

const router = useRouter()

// 数据状态
const categories = ref<any[]>([])
const products = ref<any[]>([])
const loading = ref(false)

// 分类数据
const loadCategories = async () => {
  try {
    const response = await categoryApi.getCategories()
    categories.value = response.data || []
  } catch (error) {
    console.error('加载分类失败:', error)
    // 使用默认分类数据
    categories.value = [
      { id: '1', name: 'Womenswear & Underwear', icon: 'https://via.placeholder.com/60x60/F5E6D3/ffffff?text=👗' },
      { id: '2', name: 'Phones & Electronics', icon: 'https://via.placeholder.com/60x60/E8F4FD/ffffff?text=📱' },
      { id: '3', name: 'Fashion Accessories', icon: 'https://via.placeholder.com/60x60/F0E68C/ffffff?text=👒' },
      { id: '4', name: 'Menswear & Underwear', icon: 'https://via.placeholder.com/60x60/D3D3D3/ffffff?text=👕' },
      { id: '5', name: 'Home Supplies', icon: 'https://via.placeholder.com/60x60/ADD8E6/ffffff?text=🧴' },
      { id: '6', name: 'Beauty & Personal Care', icon: 'https://via.placeholder.com/60x60/FFFFFF/ffffff?text=💄' },
      { id: '7', name: 'Shoes', icon: 'https://via.placeholder.com/60x60/FFFFFF/ffffff?text=👟' },
      { id: '8', name: 'Sports & Outdoor', icon: 'https://via.placeholder.com/60x60/90EE90/ffffff?text=🏕️' },
      { id: '9', name: 'Luggage & Bags', icon: 'https://via.placeholder.com/60x60/D3D3D3/ffffff?text=🧳' }
    ]
  }
}

// 商品数据
const loadProducts = async () => {
  loading.value = true
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 10 })
    const apiProducts = response?.data?.list || []
    
    // 转换为TikTok Shop风格的商品数据
    products.value = apiProducts.map((product: any, index: number) => ({
      id: product.id,
      name: product.name,
      description: product.description,
      price: product.suggestPrice || product.costPrice,
      originalPrice: product.suggestPrice ? product.costPrice : null,
      image: `https://via.placeholder.com/300x300/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
      rating: 4.5 + Math.random() * 0.5,
      sales: Math.floor(Math.random() * 10000) + 100,
      banner: index === 0 ? 'GRACE AND GLOW BIG SALE' : null,
      timer: index === 0 ? 'TIME: 10.14-10.17' : null,
      badge: index === 0 ? {
        title: '10.15 BIG SALE!',
        price: 'RM45',
        discount: '31% OFF'
      } : null
    }))
  } catch (error) {
    console.error('加载商品失败:', error)
    ElMessage.error('加载商品失败！')
    
    // 使用默认商品数据
    products.value = [
      {
        id: '1',
        name: 'Grace and Glow New Bundle 2in1 Vanilla Velvet Body Mist + BO Smooth Deodorant Serum',
        description: 'Grace and Glow New Bundle 2in1 Vanilla Velvet Body Mist + BO Smooth Deodorant Serum - Premium Quality',
        price: 30.90,
        originalPrice: 44.80,
        image: 'https://via.placeholder.com/300x300/8A2BE2/ffffff?text=Grace+Glow',
        rating: 4.8,
        sales: 10000,
        banner: 'GRACE AND GLOW BIG SALE',
        timer: 'TIME: 10.14-10.17',
        badge: {
          title: '10.15 BIG SALE!',
          price: 'RM45',
          discount: '31% OFF'
        }
      },
      {
        id: '2',
        name: 'Superstore Premium Quality Item',
        description: '[PENINGKATAN PERCUMA] Naik taraf PERCUMA dari 220g ke 240g | Kualiti Lebih Baik',
        price: 19.00,
        originalPrice: 20.00,
        image: 'https://via.placeholder.com/300x300/FF0000/ffffff?text=Superstore',
        rating: 4.6,
        sales: 5000
      },
      {
        id: '3',
        name: 'Brobaba Fish Grill Pan Japanese Style',
        description: 'Brobaba Pemanggang Ikan PEMBAKAR BBQ GRILL PENGEPIT IKAN AYAM BBQ Fish Grill Pan',
        price: 13.90,
        originalPrice: 49.00,
        image: 'https://via.placeholder.com/300x300/000000/ffffff?text=Grill+Pan',
        rating: 4.8,
        sales: 776
      },
      {
        id: '4',
        name: 'Dell Chromebook Laptop 4GB RAM 16GB SSD',
        description: '(Refurbished) AlaCarte | Dell (3100/3180/5190) Chromebook Laptop (Intel Celeron/4GB RAM)',
        price: 96.00,
        originalPrice: 196.00,
        image: 'https://via.placeholder.com/300x300/000000/ffffff?text=Chromebook',
        rating: 4.6,
        sales: 3300,
        badge: {
          title: 'LIVE SALE',
          price: '',
          discount: ''
        }
      },
      {
        id: '5',
        name: 'MeowMe Unisex Casual Sandals',
        description: 'READY STOCK MeowMe Kasut Unisex Lembut Kasut Lelaki Perempuan Kasual Sandal pantai',
        price: 5.65,
        originalPrice: 20.00,
        image: 'https://via.placeholder.com/300x300/F5DEB3/ffffff?text=Sandals',
        rating: 4.7,
        sales: 67000
      }
    ]
  } finally {
    loading.value = false
  }
}

// 格式化销量
const formatSales = (sales: number) => {
  if (sales >= 1000) {
    return `${(sales / 1000).toFixed(1)}K`
  }
  return sales.toString()
}

// 跳转到分类页面
const goToCategory = (category: any) => {
  router.push(`/category/${category.id}`)
}

// 跳转到商品详情页
const goToProduct = (product: any) => {
  router.push(`/product/${product.id}`)
}

// 页面加载时获取数据
onMounted(async () => {
  await Promise.all([
    loadCategories(),
    loadProducts()
  ])
})
</script>

<style scoped lang="scss">
.tiktok-shop {
  min-height: 100vh;
  background: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

// 顶部导航栏
.top-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #fff;
  border-bottom: 1px solid #e5e5e5;
  z-index: 1000;
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
    padding: 0 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .header-left {
    .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      
      .tiktok-icon {
        font-size: 24px;
        color: #000;
        font-weight: bold;
      }
      
      .logo-text {
        font-size: 18px;
        font-weight: bold;
        color: #000;
      }
    }
  }
  
  .header-right {
    display: flex;
    gap: 12px;
    
    .get-app-btn {
      padding: 8px 16px;
      border: 1px solid #000;
      background: #fff;
      color: #000;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      
      &:hover {
        background: #f5f5f5;
      }
    }
    
    .login-btn {
      padding: 8px 16px;
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      
      &:hover {
        background: #e6004a;
      }
    }
  }
}

// 主布局
.main-layout {
  display: flex;
  margin-top: 60px;
  min-height: calc(100vh - 60px);
}

// 左侧边栏
.sidebar {
  width: 240px;
  background: #fff;
  border-right: 1px solid #e5e5e5;
  position: fixed;
  left: 0;
  top: 60px;
  bottom: 0;
  overflow-y: auto;
  
  .sidebar-content {
    padding: 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  
  .sidebar-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 30px;
    
    .tiktok-icon {
      font-size: 24px;
      color: #000;
      font-weight: bold;
    }
    
    .logo-text {
      font-size: 18px;
      font-weight: bold;
      color: #000;
    }
  }
  
  .sidebar-nav {
    margin-bottom: 30px;
    
    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 0;
      cursor: pointer;
      
      .nav-icon {
        font-size: 18px;
      }
      
      .nav-text {
        font-size: 16px;
        color: #000;
      }
      
      &:hover {
        background: #f5f5f5;
        border-radius: 4px;
        padding-left: 8px;
      }
    }
  }
  
  .sidebar-login {
    margin-bottom: 30px;
    
    .login-btn-large {
      width: 100%;
      padding: 12px;
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      
      &:hover {
        background: #e6004a;
      }
    }
  }
  
  .sidebar-footer {
    margin-top: auto;
    
    .footer-link {
      display: block;
      padding: 8px 0;
      color: #666;
      text-decoration: none;
      font-size: 14px;
      
      &:hover {
        color: #000;
      }
    }
  }
}

// 主内容区域
.main-content {
  flex: 1;
  margin-left: 240px;
  padding: 20px;
  background: #fff;
}

// 分类区域
.categories-section {
  margin-bottom: 40px;
  
  .section-title {
    font-size: 24px;
    font-weight: bold;
    color: #000;
    margin-bottom: 20px;
  }
  
  .categories-container {
    position: relative;
    
    .categories-scroll {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding-bottom: 10px;
      
      &::-webkit-scrollbar {
        height: 4px;
      }
      
      &::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
      }
      
      &::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
      }
    }
    
    .scroll-arrow {
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      background: #fff;
      border: 1px solid #e5e5e5;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      
      &:hover {
        background: #f5f5f5;
      }
    }
  }
  
  .category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 80px;
    cursor: pointer;
    
    .category-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      overflow: hidden;
      margin-bottom: 8px;
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    
    .category-name {
      font-size: 12px;
      color: #000;
      text-align: center;
      line-height: 1.2;
    }
    
    &:hover {
      .category-icon {
        transform: scale(1.05);
      }
    }
  }
}

// 优惠商品区域
.savings-section {
  .section-title {
    font-size: 24px;
    font-weight: bold;
    color: #000;
    margin-bottom: 20px;
  }
  
  .products-container {
    position: relative;
    
    .products-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
      max-height: 600px;
      overflow-y: auto;
      padding-right: 10px;
      
      &::-webkit-scrollbar {
        width: 6px;
      }
      
      &::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
      }
      
      &::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
        
        &:hover {
          background: #a8a8a8;
        }
      }
    }
  }
}

// 商品卡片
.product-card {
  width: 100%;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
  display: flex;
  align-items: center;
  gap: 15px;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  .product-banner {
    background: #ff0050;
    color: #fff;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
  }
  
  .product-image-container {
    position: relative;
    width: 120px;
    height: 120px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 8px;
    
    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .product-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #ff0050;
      color: #fff;
      border-radius: 4px;
      padding: 4px 8px;
      font-size: 10px;
      
      .badge-content {
        text-align: center;
        
        .badge-title {
          font-weight: bold;
        }
        
        .badge-price {
          text-decoration: line-through;
        }
        
        .badge-discount {
          font-weight: bold;
        }
      }
    }
    
    .product-timer {
      position: absolute;
      bottom: 10px;
      left: 10px;
      background: rgba(0, 0, 0, 0.7);
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 10px;
    }
  }
  
  .product-info {
    flex: 1;
    padding: 15px;
    
    .product-name {
      font-size: 16px;
      font-weight: bold;
      color: #000;
      margin-bottom: 8px;
      line-height: 1.3;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-description {
      font-size: 14px;
      color: #666;
      margin-bottom: 10px;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-stats {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 10px;
      
      .product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        
        .rating-stars {
          color: #ffa500;
          font-size: 12px;
        }
        
        .rating-score {
          font-size: 12px;
          color: #666;
        }
      }
      
      .product-sales {
        font-size: 12px;
        color: #666;
      }
    }
    
    .product-pricing {
      display: flex;
      align-items: center;
      gap: 8px;
      
      .current-price {
        font-size: 16px;
        font-weight: bold;
        color: #ff0050;
      }
      
      .original-price {
        font-size: 12px;
        color: #999;
        text-decoration: line-through;
      }
    }
  }
}

// 响应式设计
@media (max-width: 768px) {
  .sidebar {
    display: none;
  }
  
  .main-content {
    margin-left: 0;
    padding: 15px;
  }
  
  .top-header {
    .header-content {
      padding: 0 15px;
    }
  }
  
  .product-card {
    min-width: 250px;
  }
  
  .category-item {
    min-width: 70px;
    
    .category-icon {
      width: 50px;
      height: 50px;
    }
  }
}
</style>
