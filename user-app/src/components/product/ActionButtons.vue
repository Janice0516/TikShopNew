<template>
  <div class="action-buttons">
    <button class="add-to-cart-btn" @click="handleAddToCart">
      {{ $t('product.addToCart') }}
    </button>
    <button class="buy-now-btn" @click="handleBuyNow">
      {{ $t('product.buyNow') }}
    </button>
    <button class="share-btn" @click="handleShare">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="2"/>
        <circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
        <circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="2"/>
        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke="currentColor" stroke-width="2"/>
        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke="currentColor" stroke-width="2"/>
      </svg>
      {{ $t('product.share') }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

const { t } = useI18n()

interface Props {
  productId: string | number
  productName?: string
  variantId?: string | number
  quantity?: number
}

const props = withDefaults(defineProps<Props>(), {
  quantity: 1
})

const emit = defineEmits<{
  addToCart: [productId: string | number, variantId?: string | number, quantity?: number]
  buyNow: [productId: string | number, variantId?: string | number, quantity?: number]
  share: [productId: string | number, productName?: string]
}>()

const handleAddToCart = () => {
  emit('addToCart', props.productId, props.variantId, props.quantity)
}

const handleBuyNow = () => {
  emit('buyNow', props.productId, props.variantId, props.quantity)
}

const handleShare = async () => {
  try {
    const productUrl = `${window.location.origin}/product/${props.productId}`
    const shareData = {
      title: props.productName || 'Amazing Product',
      text: `Check out this amazing product: ${props.productName || 'Product'}`,
      url: productUrl
    }

    // 检查是否支持Web Share API
    if (navigator.share) {
      await navigator.share(shareData)
      ElMessage.success(t('product.shareSuccess'))
    } else {
      // 降级到复制链接
      await navigator.clipboard.writeText(productUrl)
      ElMessage.success(t('product.linkCopied'))
    }
    
    emit('share', props.productId, props.productName)
  } catch (error: any) {
    if (error.name !== 'AbortError') {
      console.error('分享失败:', error)
      ElMessage.error(t('product.shareFailed'))
    }
  }
}
</script>

<style scoped lang="scss">
.action-buttons {
  display: flex;
  gap: 12px;
  
  .add-to-cart-btn {
    flex: 1;
    background: #000;
    color: #fff;
    border: none;
    padding: 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    
    &:hover {
      background: #333;
    }
    
    &:active {
      transform: translateY(1px);
    }
  }
  
  .buy-now-btn {
    flex: 1;
    background: #ff0050;
    color: #fff;
    border: none;
    padding: 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    
    &:hover {
      background: #e6004a;
    }
    
    &:active {
      transform: translateY(1px);
    }
  }

  .share-btn {
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
    padding: 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    
    &:hover {
      background: #e0e0e0;
      border-color: #ccc;
    }
    
    &:active {
      transform: translateY(1px);
    }

    svg {
      width: 20px;
      height: 20px;
    }
  }
}

// 移动端优化
@media (max-width: 768px) {
  .action-buttons {
    flex-direction: column;
    gap: 8px;
    
    .add-to-cart-btn,
    .buy-now-btn,
    .share-btn {
      padding: 14px;
      font-size: 15px;
    }
  }
}
</style>


