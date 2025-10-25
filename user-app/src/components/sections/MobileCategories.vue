<template>
  <section class="mobile-categories">
    <h2 class="section-title">Categories</h2>
    <div class="categories-container">
      <div class="categories-scroll" ref="categoriesScrollRef">
        <div 
          v-for="category in categories" 
          :key="category.id"
          class="category-item"
          @click="handleCategoryClick(category)"
        >
          <div class="category-icon">
            <img 
              :src="category.icon" 
              :alt="category.name" 
              @error="handleImageError"
            />
          </div>
          <span class="category-name">{{ category.name }}</span>
        </div>
      </div>
      
      <!-- 滚动箭头 -->
      <button class="scroll-arrow left" @click="scrollCategories('left')" v-if="showLeftArrow">
        <el-icon><ArrowLeft /></el-icon>
      </button>
      <button class="scroll-arrow right" @click="scrollCategories('right')" v-if="showRightArrow">
        <el-icon><ArrowRight /></el-icon>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, onMounted, onUnmounted } from 'vue'
import { ArrowLeft, ArrowRight } from '@element-plus/icons-vue'
import { useRouter } from 'vue-router'

interface Category {
  id: string | number
  name: string
  icon: string
}

const props = defineProps<{
  categories: Category[]
}>()

const emit = defineEmits<{
  categoryClick: [category: Category]
}>()

const router = useRouter()
const categoriesScrollRef = ref<HTMLElement | null>(null)
const showLeftArrow = ref(false)
const showRightArrow = ref(true)

const handleCategoryClick = (category: Category) => {
  emit('categoryClick', category)
  router.push(`/category/${category.id}`)
}

// 图片错误处理
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.warn('分类图标加载失败:', img.src)
  // 设置默认图标
  img.src = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&h=120&fit=crop&crop=center&auto=format&q=80'
}

const scrollCategories = (direction: 'left' | 'right') => {
  if (categoriesScrollRef.value) {
    const scrollAmount = 200 // 滚动距离
    if (direction === 'left') {
      categoriesScrollRef.value.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
    } else {
      categoriesScrollRef.value.scrollBy({ left: scrollAmount, behavior: 'smooth' })
    }
  }
}

const updateArrowVisibility = () => {
  if (categoriesScrollRef.value) {
    const { scrollLeft, scrollWidth, clientWidth } = categoriesScrollRef.value
    showLeftArrow.value = scrollLeft > 0
    showRightArrow.value = scrollLeft < scrollWidth - clientWidth - 10
  }
}

onMounted(() => {
  if (categoriesScrollRef.value) {
    categoriesScrollRef.value.addEventListener('scroll', updateArrowVisibility)
    updateArrowVisibility()
  }
})

onUnmounted(() => {
  if (categoriesScrollRef.value) {
    categoriesScrollRef.value.removeEventListener('scroll', updateArrowVisibility)
  }
})
</script>

<style scoped lang="scss">
.mobile-categories {
  background: #000;
  color: #fff;
  padding: 20px 0;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #fff;
  margin: 0 0 16px 16px;
}

.categories-container {
  position: relative;
  display: flex;
  align-items: center;
}

.categories-scroll {
  display: flex;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* iOS 平滑滚动 */
  scroll-behavior: smooth;
  padding: 0 16px;
  gap: 16px;
  
  /* 隐藏滚动条 */
  &::-webkit-scrollbar {
    display: none;
  }
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
  
  /* 触屏优化 */
  touch-action: pan-x;
  overscroll-behavior-x: contain;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  transition: transform 0.2s ease;
  flex-shrink: 0; /* 防止收缩 */
  width: 80px; /* 固定宽度 */
  
  /* 触屏优化 */
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
  
  &:hover {
    transform: scale(1.05);
  }
  
  &:active {
    transform: scale(0.95);
    transition: transform 0.1s ease;
  }
  
  .category-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    margin-bottom: 8px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    
    &:hover {
      background: rgba(255, 0, 80, 0.2);
      border-color: #ff0050;
    }
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  
  .category-name {
    font-size: 12px;
    color: #fff;
    text-align: center;
    line-height: 1.2;
    font-weight: 500;
    word-break: break-word;
    max-width: 80px;
  }
}

.scroll-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  
  &:hover {
    background: rgba(255, 0, 80, 0.8);
    transform: translateY(-50%) scale(1.1);
  }
  
  &.left {
    left: 8px;
  }
  
  &.right {
    right: 8px;
  }
  
  .el-icon {
    font-size: 18px;
  }
}

// 响应式设计
@media (max-width: 480px) {
  .mobile-categories {
    padding: 16px 0;
  }
  
  .section-title {
    font-size: 16px;
    margin-bottom: 12px;
    margin-left: 12px;
  }
  
  .categories-scroll {
    padding: 0 12px;
    gap: 12px;
  }
  
  .category-item {
    width: 70px;
    
    .category-icon {
      width: 50px;
      height: 50px;
      margin-bottom: 6px;
    }
    
    .category-name {
      font-size: 11px;
      max-width: 70px;
    }
  }
  
  .scroll-arrow {
    width: 32px;
    height: 32px;
    
    .el-icon {
      font-size: 14px;
    }
    
    &.left {
      left: 6px;
    }
    
    &.right {
      right: 6px;
    }
  }
}

@media (max-width: 360px) {
  .category-item {
    width: 65px;
    
    .category-icon {
      width: 45px;
      height: 45px;
    }
    
    .category-name {
      font-size: 10px;
      max-width: 65px;
    }
  }
}
</style>
