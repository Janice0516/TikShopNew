<template>
  <div class="tiktok-shop">
    <!-- 顶部导航栏 -->
    <AppHeader />

    <div class="main-layout">
      <!-- 左侧边栏 -->
      <AppSidebar />

      <!-- 主内容区域 -->
      <main class="main-content">
        <!-- 面包屑导航 -->
        <div class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">{{ $t('navigation.home') }}</router-link>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">{{ $t('navigation.categories') }}</span>
        </div>

        <!-- 页面标题 -->
        <div class="page-header">
          <h1>{{ $t('navigation.categories') }}</h1>
        </div>
        
        <!-- 分类网格 -->
        <section class="categories-section">
          <div class="categories-grid" v-loading="loading">
            <div 
              v-for="category in categories" 
              :key="category.id"
              class="category-card"
              @click="handleCategoryClick(category)"
            >
              <div class="category-icon">
                <img 
                  :src="getCategoryIcon(category)" 
                  :alt="category.name" 
                  @error="handleImageError"
                  @load="handleImageLoad"
                />
              </div>
              <div class="category-info">
                <h3 class="category-name">{{ category.name }}</h3>
                <p class="category-description">{{ getCategoryDescription(category.name) }}</p>
              </div>
              <div class="category-arrow">
                <span>→</span>
              </div>
            </div>
          </div>
          
          <!-- 空状态 -->
          <div v-if="!loading && categories.length === 0" class="empty-state">
            <div class="empty-icon">📂</div>
            <h3>{{ $t('categories.emptyTitle') }}</h3>
            <p>{{ $t('categories.emptyDescription') }}</p>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAllCategories } from '@/composables/useAllCategories'
import AppHeader from '@/components/layout/AppHeader.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'

const router = useRouter()
const { categories, loading } = useAllCategories()

// 获取分类图标
const getCategoryIcon = (category: any) => {
  // 优先使用 imageUrl，其次使用 icon
  if (category.imageUrl) {
    return category.imageUrl;
  }
  if (category.icon) {
    return category.icon;
  }
  // 默认图标
  return 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80';
}

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

// 处理分类点击
const handleCategoryClick = (category: any) => {
  router.push(`/category/${category.id}`)
}

// 图片错误处理
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.warn('分类图标加载失败:', img.src)
  // 设置默认图标
  img.src = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
}

// 图片加载成功处理
const handleImageLoad = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.log('分类图标加载成功:', img.src)
}
</script>

<style scoped lang="scss">
@import "@/styles/variables.scss";

.tiktok-shop {
  min-height: 100vh;
  background: $background-base;
}

.main-layout {
  display: flex;
  min-height: calc(100vh - 80px); // Adjust for header height
}

.main-content {
  flex: 1;
  padding: 20px;
  margin-left: 250px; // Adjust for sidebar width
  background: $background-base;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  font-size: 14px;
  color: $text-secondary;

  .breadcrumb-link {
    color: $text-secondary;
    text-decoration: none;
    
    &:hover {
      color: $primary-color;
    }
  }

  .breadcrumb-separator {
    margin: 0 8px;
    color: $text-placeholder;
  }

  .breadcrumb-current {
    color: $text-primary;
    font-weight: 500;
  }
}

.page-header {
  background: #fff;
  padding: 40px;
  border-radius: 12px;
  margin-bottom: 30px;
  text-align: center;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  
  h1 {
    font-size: 32px;
    font-weight: bold;
    color: $text-primary;
    margin-bottom: 10px;
  }
  
  p {
    font-size: 16px;
    color: $text-secondary;
    margin: 0;
  }
}

.categories-section {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.category-card {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  border: 1px solid #f0f0f0;
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: $primary-color;
  }
}

.category-icon {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.category-info {
  flex: 1;
  
  .category-name {
    font-size: 18px;
    font-weight: 600;
    color: $text-primary;
    margin: 0 0 8px 0;
    line-height: 1.3;
  }
  
  .category-description {
    font-size: 14px;
    color: $text-secondary;
    margin: 0;
    line-height: 1.4;
  }
}

.category-arrow {
  color: $text-secondary;
  font-size: 20px;
  font-weight: bold;
  transition: all 0.3s ease;
  
  .category-card:hover & {
    color: $primary-color;
    transform: translateX(4px);
  }
}

.empty-state {
  background: #fff;
  padding: 60px 40px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  
  .empty-icon {
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
    margin: 0;
  }
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 15px;
  }
  
  .page-header {
    padding: 30px 20px;
    
    h1 {
      font-size: 28px;
    }
  }
  
  .categories-section {
    padding: 20px;
  }
  
  .categories-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .category-card {
    padding: 20px;
    gap: 15px;
  }
  
  .category-icon {
    width: 60px;
    height: 60px;
  }
  
  .category-info {
    .category-name {
      font-size: 16px;
    }
    
    .category-description {
      font-size: 13px;
    }
  }
  
  .empty-state {
    padding: 40px 20px;
    
    .empty-icon {
      font-size: 48px;
    }
    
    h3 {
      font-size: 18px;
    }
    
    p {
      font-size: 14px;
    }
  }
}
</style>
