<template>
  <div class="product-variants" v-if="variants && variants.length > 0">
    <!-- 变体类型标题 -->
    <div class="variant-type-label">
      {{ variantTypeLabel }}: {{ selectedVariant?.name || variants[0]?.name }}
    </div>
    
    <!-- 变体选择区域 -->
    <div class="variant-options">
      <div 
        v-for="(variant, index) in variants" 
        :key="variant.id || index"
        class="variant-option"
        :class="{ 
          active: selectedIndex === index,
          disabled: variant.stock === 0
        }"
        @click="selectVariant(index)"
      >
        <!-- 变体图片 -->
        <div class="variant-image" v-if="variant.image">
          <img 
            :src="variant.image" 
            :alt="variant.name"
            @error="handleImageError"
          />
        </div>
        
        <!-- 变体颜色块 (如果没有图片) -->
        <div 
          v-else-if="variant.color" 
          class="variant-color"
          :style="{ backgroundColor: variant.color }"
        ></div>
        
        <!-- 变体名称 -->
        <div class="variant-name">{{ variant.name }}</div>
        
        <!-- 变体价格 (如果有不同价格) -->
        <div class="variant-price" v-if="variant.price && variant.price !== basePrice">
          RM{{ formatPrice(variant.price) }}
        </div>
        
        <!-- 库存状态 -->
        <div class="variant-stock" v-if="variant.stock !== undefined">
          <span v-if="variant.stock === 0" class="out-of-stock">缺货</span>
          <span v-else-if="variant.stock < 10" class="low-stock">仅剩{{ variant.stock }}件</span>
        </div>
      </div>
    </div>
    
    <!-- 变体详细信息 -->
    <div class="variant-details" v-if="selectedVariant">
      <div class="variant-info">
        <div class="info-row" v-if="selectedVariant.size">
          <span class="info-label">尺寸:</span>
          <span class="info-value">{{ selectedVariant.size }}</span>
        </div>
        <div class="info-row" v-if="selectedVariant.material">
          <span class="info-label">材质:</span>
          <span class="info-value">{{ selectedVariant.material }}</span>
        </div>
        <div class="info-row" v-if="selectedVariant.weight">
          <span class="info-label">重量:</span>
          <span class="info-value">{{ selectedVariant.weight }}</span>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 无变体状态 -->
  <div v-else class="no-variants">
    <div class="no-variants-text">此商品暂无变体选项</div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

interface ProductVariant {
  id?: string | number
  name: string
  image?: string
  color?: string
  price?: number
  stock?: number
  size?: string
  material?: string
  weight?: string
  sku?: string
}

interface Props {
  variants: ProductVariant[]
  variantType?: string
  basePrice?: number
  initialSelection?: number
}

const props = withDefaults(defineProps<Props>(), {
  variantType: '颜色',
  basePrice: 0,
  initialSelection: 0
})

const emit = defineEmits<{
  variantChange: [variant: ProductVariant, index: number]
  variantSelect: [index: number]
}>()

const selectedIndex = ref(props.initialSelection)

// 计算属性
const selectedVariant = computed(() => {
  return props.variants[selectedIndex.value]
})

const variantTypeLabel = computed(() => {
  return props.variantType || '颜色'
})

// 方法
const selectVariant = (index: number) => {
  const variant = props.variants[index]
  
  // 检查是否缺货
  if (variant.stock === 0) {
    return
  }
  
  selectedIndex.value = index
  emit('variantChange', variant, index)
  emit('variantSelect', index)
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.src = '/placeholder-variant.jpg'
}

// 监听props变化
watch(() => props.initialSelection, (newIndex) => {
  selectedIndex.value = newIndex
})

// 监听变体数据变化
watch(() => props.variants, (newVariants) => {
  if (newVariants && newVariants.length > 0) {
    // 确保选中的索引在有效范围内
    if (selectedIndex.value >= newVariants.length) {
      selectedIndex.value = 0
    }
  }
}, { immediate: true })
</script>

<style scoped lang="scss">
.product-variants {
  margin-bottom: 24px;
  
  .variant-type-label {
    font-size: 14px;
    font-weight: 500;
    color: #000;
    margin-bottom: 12px;
  }
  
  .variant-options {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
    
    .variant-option {
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
      padding: 8px;
      border-radius: 8px;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      min-width: 80px;
      
      &:hover:not(.disabled) {
        border-color: #e5e5e5;
        background: #f8f8f8;
      }
      
      &.active {
        border-color: #000;
        background: #f0f0f0;
      }
      
      &.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        
        .variant-name {
          color: #999;
        }
      }
      
      .variant-image {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 8px;
        
        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }
      
      .variant-color {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        margin-bottom: 8px;
        border: 1px solid #e5e5e5;
      }
      
      .variant-name {
        font-size: 12px;
        font-weight: 500;
        color: #000;
        text-align: center;
        margin-bottom: 4px;
      }
      
      .variant-price {
        font-size: 11px;
        color: #ff0050;
        font-weight: 600;
      }
      
      .variant-stock {
        font-size: 10px;
        margin-top: 4px;
        
        .out-of-stock {
          color: #ff0050;
        }
        
        .low-stock {
          color: #ff6b35;
        }
      }
    }
  }
  
  .variant-details {
    background: #f8f8f8;
    border-radius: 8px;
    padding: 12px;
    
    .variant-info {
      .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        
        &:last-child {
          margin-bottom: 0;
        }
        
        .info-label {
          font-size: 12px;
          color: #666;
        }
        
        .info-value {
          font-size: 12px;
          color: #000;
          font-weight: 500;
        }
      }
    }
  }
}

.no-variants {
  margin-bottom: 24px;
  
  .no-variants-text {
    font-size: 14px;
    color: #666;
    text-align: center;
    padding: 20px;
    background: #f8f8f8;
    border-radius: 8px;
  }
}

// 移动端优化
@media (max-width: 768px) {
  .product-variants {
    .variant-options {
      gap: 8px;
      
      .variant-option {
        min-width: 70px;
        padding: 6px;
        
        .variant-image,
        .variant-color {
          width: 50px;
          height: 50px;
        }
        
        .variant-name {
          font-size: 11px;
        }
      }
    }
  }
}
</style>


