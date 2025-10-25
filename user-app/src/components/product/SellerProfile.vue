<template>
  <div class="seller-profile">
    <div class="seller-avatar">
      <div class="avatar-circle" :style="{ backgroundColor: avatarColor }">
        {{ sellerInitials }}
      </div>
    </div>
    <div class="seller-info">
      <div class="seller-name">{{ props.seller?.name || 'CF Fire jewelry' }}</div>
      <div class="seller-stats">
        <span class="seller-rating">
          <span class="star-icon">⭐</span>
          {{ props.seller?.rating || 3.7 }}
        </span>
        <span class="seller-sales">{{ formatSales(props.seller?.sales || 36800) }} Sold</span>
        <span class="seller-followers">{{ formatSales(props.seller?.followers || 1000) }}+ Followers</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Seller {
  id: string | number
  name: string
  rating: number
  sales: number
  followers: number
}

interface Props {
  seller: Seller
}

const props = defineProps<Props>()

const sellerInitials = computed(() => {
  if (!props.seller?.name) return 'CF'
  return props.seller.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

const avatarColor = computed(() => {
  const colors = ['#ff0050', '#00d4aa', '#ff6b35', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57']
  const index = (props.seller?.id?.toString().length || 1) % colors.length
  return colors[index]
})

const formatSales = (sales: number) => {
  if (sales >= 1000) {
    return (sales / 1000).toFixed(1) + 'K'
  }
  return sales.toString()
}
</script>

<style scoped lang="scss">
.seller-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding: 16px;
  background: #f8f8f8;
  border-radius: 8px;
  border: 1px solid #e5e5e5;
  
  .seller-avatar {
    .avatar-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 600;
      color: #fff;
    }
  }
  
  .seller-info {
    flex: 1;
    
    .seller-name {
      font-size: 14px;
      font-weight: 500;
      color: #000;
      margin-bottom: 4px;
    }
    
    .seller-stats {
      font-size: 12px;
      color: #666;
      
      span {
        margin-right: 12px;
      }
      
      .star-icon {
        margin-right: 2px;
      }
    }
  }
}
</style>
