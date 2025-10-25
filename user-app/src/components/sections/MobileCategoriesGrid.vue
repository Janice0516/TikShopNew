<template>
  <div class="categories-grid-section">
    <h2 class="section-title">Categories</h2>
    
    <div class="categories-grid">
      <div 
        v-for="category in categories" 
        :key="category.id"
        class="category-item"
        @click="goToCategory(category)"
      >
        <div class="category-icon">
          <img 
            :src="getCategoryIcon(category)" 
            :alt="category.name" 
            @error="handleImageError"
          />
        </div>
        <span class="category-name">{{ category.name }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

interface Category {
  id: string
  name: string
  icon: string
}

const props = defineProps<{
  categories: Category[]
}>()

const router = useRouter()

const goToCategory = (category: Category) => {
  router.push(`/category/${category.id}`)
}

// 获取分类图标
const getCategoryIcon = (category: Category) => {
  // 优先使用 imageUrl，其次使用 icon
  if ((category as any).imageUrl) {
    return (category as any).imageUrl;
  }
  if (category.icon) {
    return category.icon;
  }
  // 默认图标
  return 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80';
}

// 图片错误处理
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.warn('分类图标加载失败:', img.src)
  // 设置默认图标
  img.src = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
}
</script>

<style scoped lang="scss">
.categories-grid-section {
  background: #000;
  padding: 16px 0;
  
  .section-title {
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 12px;
    padding: 0 12px;
  }
  
  .categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    padding: 0 12px;
    
    .category-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
      transition: transform 0.3s ease;
      
      &:hover {
        transform: translateY(-2px);
      }
      
      .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 6px;
        transition: transform 0.3s ease;
        
        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }
      
      .category-name {
        font-size: 11px;
        color: #fff;
        text-align: center;
        line-height: 1.2;
        max-width: 70px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      
      &:hover .category-icon {
        transform: scale(1.05);
      }
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .categories-grid-section {
    .categories-grid {
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      
      .category-item {
        .category-icon {
          width: 50px;
          height: 50px;
        }
        
        .category-name {
          font-size: 11px;
          max-width: 70px;
        }
      }
    }
  }
}

@media (min-width: 769px) {
  .categories-grid-section {
    display: none;
  }
}
</style>
