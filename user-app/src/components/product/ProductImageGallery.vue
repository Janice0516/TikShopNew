<template>
  <div class="product-image-gallery">
    <div class="main-image-container">
      <img 
        :src="currentImage" 
        :alt="productName" 
        class="main-image"
        @error="handleImageError"
      />
      <div class="image-navigation" v-if="allImages.length > 1">
        <button 
          class="nav-arrow prev" 
          @click="previousImage" 
          :disabled="currentImageIndex === 0"
          :aria-label="$t('product.previousImage')"
        >
          ‹
        </button>
        <button 
          class="nav-arrow next" 
          @click="nextImage" 
          :disabled="currentImageIndex === allImages.length - 1"
          :aria-label="$t('product.nextImage')"
        >
          ›
        </button>
      </div>
      
      <!-- 图片指示器 -->
      <div class="image-indicators" v-if="allImages.length > 1">
        <span 
          v-for="(image, index) in allImages" 
          :key="index"
          class="indicator"
          :class="{ active: currentImageIndex === index }"
          @click="selectImage(index)"
        ></span>
      </div>
    </div>
    
    <!-- 缩略图导航 -->
    <div class="thumbnail-navigation" v-if="allImages.length > 1">
      <div 
        v-for="(image, index) in allImages" 
        :key="index"
        class="thumbnail"
        :class="{ active: currentImageIndex === index }"
        @click="selectImage(index)"
      >
        <img :src="image" :alt="`${productName} ${index + 1}`" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

interface Props {
  images: string[]
  productName: string
  initialIndex?: number
}

const props = withDefaults(defineProps<Props>(), {
  initialIndex: 0
})

const emit = defineEmits<{
  imageChange: [index: number]
}>()

const currentImageIndex = ref(props.initialIndex)

const allImages = computed(() => props.images || [])
const currentImage = computed(() => {
  if (allImages.value.length === 0) {
    return ''
  }
  const image = allImages.value[currentImageIndex.value]
  return image || allImages.value[0] || ''
})

const selectImage = (index: number) => {
  currentImageIndex.value = index
  emit('imageChange', index)
}

const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
    emit('imageChange', currentImageIndex.value)
  }
}

const nextImage = () => {
  if (currentImageIndex.value < allImages.value.length - 1) {
    currentImageIndex.value++
    emit('imageChange', currentImageIndex.value)
  }
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.warn('Image failed to load:', img.src)
  console.warn('Image element:', img)
  console.warn('All images:', allImages.value)
  // 不设置占位图，保持原样
}

// 监听props变化
watch(() => props.initialIndex, (newIndex) => {
  currentImageIndex.value = newIndex
})

// 键盘导航
const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'ArrowLeft') {
    previousImage()
  } else if (event.key === 'ArrowRight') {
    nextImage()
  }
}

// 添加键盘事件监听
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped lang="scss">
.product-image-gallery {
  flex: 1;
  max-width: 500px;
  
  .main-image-container {
    position: relative;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
    
    .main-image {
      width: 100%;
      height: 500px;
      object-fit: cover;
      transition: opacity 0.3s ease;
    }
    
    .image-navigation {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 100%;
      display: flex;
      justify-content: space-between;
      padding: 0 16px;
      
      .nav-arrow {
        background: rgba(255, 255, 255, 0.8);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        
        &:hover {
          background: rgba(255, 255, 255, 1);
          transform: scale(1.1);
        }
        
        &:disabled {
          opacity: 0.5;
          cursor: not-allowed;
          transform: none;
        }
      }
    }
    
    .image-indicators {
      position: absolute;
      bottom: 16px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
      
      .indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: background-color 0.3s ease;
        
        &.active {
          background: rgba(255, 255, 255, 1);
        }
        
        &:hover {
          background: rgba(255, 255, 255, 0.8);
        }
      }
    }
  }
  
  .thumbnail-navigation {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 4px 0;
    
    .thumbnail {
      flex-shrink: 0;
      width: 60px;
      height: 60px;
      border-radius: 6px;
      overflow: hidden;
      cursor: pointer;
      border: 2px solid transparent;
      transition: border-color 0.3s ease;
      
      &.active {
        border-color: #000;
      }
      
      &:hover {
        border-color: #666;
      }
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
  }
}

// 移动端优化
@media (max-width: 768px) {
  .product-image-gallery {
    max-width: 100%;
    
    .main-image-container {
      .main-image {
        height: 300px;
      }
      
      .image-navigation {
        .nav-arrow {
          width: 36px;
          height: 36px;
          font-size: 18px;
        }
      }
    }
    
    .thumbnail-navigation {
      .thumbnail {
        width: 50px;
        height: 50px;
      }
    }
  }
}
</style>
