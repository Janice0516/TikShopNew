<template>
  <div class="category-navigation">
    <div class="category-tabs">
      <div 
        v-for="category in mainCategories" 
        :key="category.id"
        class="category-tab"
        :class="{ active: activeCategory === category.id }"
        @click="setActiveCategory(category.id)"
      >
        {{ category.name }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

interface Category {
  id: string
  name: string
}

const router = useRouter()

// 主要分类数据
const mainCategories = ref<Category[]>([
  { id: 'all', name: 'All' },
  { id: 'womenswear', name: 'Womenswear & Underwear' },
  { id: 'phones', name: 'Phones & Electronics' },
  { id: 'fashion', name: 'Fashion Accessories' },
  { id: 'menswear', name: 'Menswear & Underwear' },
  { id: 'home', name: 'Home Supplies' },
  { id: 'beauty', name: 'Beauty & Personal Care' },
  { id: 'shoes', name: 'Shoes' },
  { id: 'sports', name: 'Sports & Outdoor' },
  { id: 'bags', name: 'Luggage & Bags' }
])

const activeCategory = ref('all')

const setActiveCategory = (categoryId: string) => {
  activeCategory.value = categoryId
  // 可以在这里添加分类筛选逻辑
  console.log('切换到分类:', categoryId)
}
</script>

<style scoped lang="scss">
.category-navigation {
  margin-top: 60px; // 为固定头部留空间
  padding: 12px 0;
  background: #000;
  
  .category-tabs {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding: 0 12px;
    
    &::-webkit-scrollbar {
      display: none;
    }
    
    .category-tab {
      white-space: nowrap;
      padding: 6px 12px;
      border-radius: 16px;
      background: #1a1a1a;
      color: #fff;
      font-size: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      flex-shrink: 0;
      
      &.active {
        background: #ff0050;
        color: #fff;
      }
      
      &:hover {
        background: #333;
      }
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .category-navigation {
    padding: 8px 0;
    
    .category-tabs {
      gap: 12px;
      padding: 0 8px;
      
      .category-tab {
        padding: 5px 10px;
        font-size: 11px;
        border-radius: 12px;
      }
    }
  }
}

@media (min-width: 769px) {
  .category-navigation {
    display: none;
  }
}
</style>
