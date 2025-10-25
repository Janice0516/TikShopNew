<template>
  <div class="mobile-cart">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="header-title">{{ t('navigation.cart') }}</h1>
      <button class="edit-btn" @click="toggleEditMode" v-if="cartStore.cartItems.length > 0">
        {{ isEditMode ? t('common.done') : t('common.edit') }}
      </button>
    </div>

    <!-- Cart Content -->
    <div class="cart-content">
      <!-- Loading State -->
      <MobileLoading 
        :loading="isLoading"
        type="skeleton"
        skeleton-type="list"
        :skeleton-count="3"
        message="加载购物车中..."
      />
      
      <!-- Error State -->
      <MobileError 
        :show="hasError"
        type="network"
        title="加载失败"
        message="无法加载购物车，请检查网络连接"
        @retry="handleRetry"
      />
      
      <!-- Empty Cart -->
      <div v-if="!isLoading && !hasError && cartStore.cartItems.length === 0" class="empty-cart">
        <div class="empty-icon">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
            <circle cx="9" cy="21" r="1" fill="currentColor"/>
            <circle cx="20" cy="21" r="1" fill="currentColor"/>
            <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="empty-title">{{ t('cart.emptyTitle') }}</h3>
        <p class="empty-description">{{ t('cart.emptyDescription') }}</p>
        <button class="shop-btn" @click="goShopping">
          {{ t('cart.startShopping') }}
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else-if="!isLoading && !hasError" class="cart-items">
        <!-- Select All -->
        <div class="select-all-section">
          <div class="select-all-item" @click="toggleSelectAll">
            <div class="checkbox" :class="{ 'checked': selectAll }">
              <svg v-if="selectAll" width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span class="select-all-text">{{ t('cart.selectAll') }}</span>
          </div>
          <button v-if="isEditMode" class="delete-selected-btn" @click="deleteSelected" :disabled="selectedItems.length === 0">
            {{ t('cart.deleteSelected') }}
          </button>
        </div>

        <!-- Cart Item List -->
        <div class="item-list">
          <div 
            v-for="item in cartStore.cartItems" 
            :key="item.id" 
            class="cart-item"
            :class="{ 'editing': isEditMode }"
          >
            <!-- Item Selection -->
            <div class="item-selection" @click="toggleItemSelection(item)">
              <div class="checkbox" :class="{ 'checked': item.selected }">
                <svg v-if="item.selected" width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>

            <!-- Item Image -->
            <div class="item-image" @click="goToProduct(item.product)">
              <img :src="item.product.image" :alt="item.product.name" />
            </div>

            <!-- Item Info -->
            <div class="item-info">
              <h3 class="item-name" @click="goToProduct(item.product)">{{ item.product.name }}</h3>
              <p class="item-description">{{ item.product.description }}</p>
              <div class="item-price">RM{{ formatPrice(item.product.price) }}</div>
            </div>

            <!-- Quantity Controls -->
            <div class="quantity-controls">
              <button 
                class="quantity-btn minus" 
                @click="decreaseQuantity(item)"
                :disabled="item.quantity <= 1"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
              <span class="quantity-value">{{ item.quantity }}</span>
              <button 
                class="quantity-btn plus" 
                @click="increaseQuantity(item)"
                :disabled="item.quantity >= (item.product.stock || 99)"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"/>
                  <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
            </div>

            <!-- Item Total -->
            <div class="item-total">
              <div class="total-price">RM{{ formatPrice(item.product.price * item.quantity) }}</div>
            </div>

            <!-- Delete Button (Edit Mode) -->
            <button v-if="isEditMode" class="delete-item-btn" @click="removeItem(item)">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2"/>
                <path d="M19,6V20A2,2 0 0,1 17,22H7A2,2 0 0,1 5,20V6M8,6V4A2,2 0 0,1 10,2H14A2,2 0 0,1 16,4V6" stroke="currentColor" stroke-width="2"/>
                <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2"/>
                <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Summary -->
    <div v-if="cartStore.cartItems.length > 0" class="bottom-summary">
      <div class="summary-content">
        <div class="summary-info">
          <div class="selected-count">
            {{ t('cart.selectedItems', { count: selectedItems.length }) }}
          </div>
          <div class="total-amount">
            {{ t('common.total') }}: <span class="amount">RM{{ formatPrice(totalAmount) }}</span>
          </div>
        </div>
        <button 
          class="checkout-btn"
          @click="goToCheckout"
          :disabled="selectedItems.length === 0"
        >
          {{ t('cart.checkout', { count: selectedItems.length }) }}
        </button>
      </div>
    </div>
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useCartStore } from '@/stores/cart'
import { ElMessage, ElMessageBox } from 'element-plus'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileLoading from '@/components/common/MobileLoading.vue'
import MobileError from '@/components/common/MobileError.vue'

const router = useRouter()
const { t } = useI18n()
const cartStore = useCartStore()

// 状态管理
const isEditMode = ref(false)
const isLoading = ref(false)
const hasError = ref(false)

// 计算属性
const selectAll = computed({
  get: () => cartStore.cartItems.length > 0 && cartStore.cartItems.every(item => item.selected),
  set: (value) => {
    cartStore.cartItems.forEach(item => {
      item.selected = value
    })
  }
})

const selectedItems = computed(() => 
  cartStore.cartItems.filter(item => item.selected)
)

const totalAmount = computed(() => 
  selectedItems.value.reduce((total, item) => 
    total + (item.product.price * item.quantity), 0
  )
)

// 方法
const goBack = () => {
  router.back()
}

const toggleEditMode = () => {
  isEditMode.value = !isEditMode.value
}

const toggleSelectAll = () => {
  selectAll.value = !selectAll.value
}

const toggleItemSelection = (item: any) => {
  item.selected = !item.selected
}

const increaseQuantity = (item: any) => {
  if (item.quantity < (item.product.stock || 99)) {
    item.quantity++
    cartStore.updateQuantity(item.id, item.quantity)
  }
}

const decreaseQuantity = (item: any) => {
  if (item.quantity > 1) {
    item.quantity--
    cartStore.updateQuantity(item.id, item.quantity)
  }
}

const removeItem = async (item: any) => {
  try {
    await ElMessageBox.confirm(
      t('cart.confirmDelete', { name: item.product.name }),
      t('common.confirm'),
      {
        confirmButtonText: t('common.delete'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    cartStore.removeItem(item.id)
    ElMessage.success(t('cart.itemDeleted'))
  } catch {
    // 用户取消删除
  }
}

const deleteSelected = async () => {
  if (selectedItems.value.length === 0) return
  
  try {
    await ElMessageBox.confirm(
      t('cart.confirmDeleteSelected', { count: selectedItems.value.length }),
      t('common.confirm'),
      {
        confirmButtonText: t('common.delete'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    selectedItems.value.forEach(item => {
      cartStore.removeItem(item.id)
    })
    
    ElMessage.success(t('cart.selectedDeleted'))
  } catch {
    // 用户取消删除
  }
}

const goToProduct = (product: any) => {
  router.push(`/mobile/product/${product.id}`)
}

const goShopping = () => {
  router.push('/mobile')
}

const goToCheckout = () => {
  if (selectedItems.value.length === 0) {
    ElMessage.warning(t('cart.selectItemsFirst'))
    return
  }
  router.push('/mobile/order')
}

// 加载购物车数据
const loadCartData = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    
    // 获取购物车数据
    await cartStore.fetchCartItems()
    
    console.log('购物车数据加载完成:', cartStore.cartItems.length)
  } catch (error: any) {
    console.error('加载购物车失败:', error)
    hasError.value = true
    ElMessage.error('加载购物车失败')
  } finally {
    isLoading.value = false
  }
}

// 重试加载
const handleRetry = () => {
  hasError.value = false
  loadCartData()
}

// 页面加载时获取数据
onMounted(() => {
  loadCartData()
})

const formatPrice = (price: number) => {
  return price.toFixed(2)
}
</script>

<style scoped lang="scss">
.mobile-cart {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 160px; // 为底部汇总和导航栏留出空间
}

.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #000;
  border-bottom: 1px solid #333;
  position: sticky;
  top: 0;
  z-index: 100;

  .back-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }

  .header-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }

  .edit-btn {
    background: none;
    border: none;
    color: #ff0050;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }
}

.cart-content {
  padding: 20px;
}

.empty-cart {
  text-align: center;
  padding: 60px 20px;

  .empty-icon {
    color: #666;
    margin-bottom: 24px;
  }

  .empty-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 12px 0;
    color: #fff;
  }

  .empty-description {
    font-size: 14px;
    color: #ccc;
    margin: 0 0 32px 0;
    line-height: 1.5;
  }

  .shop-btn {
    background: #ff0050;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 16px 32px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #e6004a;
      transform: translateY(-1px);
    }
  }
}

.cart-items {
  .select-all-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 0;
    border-bottom: 1px solid #333;
    margin-bottom: 16px;

    .select-all-item {
      display: flex;
      align-items: center;
      cursor: pointer;

      .checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #666;
        border-radius: 4px;
        margin-right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;

        &.checked {
          background: #ff0050;
          border-color: #ff0050;
          color: #fff;
        }
      }

      .select-all-text {
        font-size: 16px;
        font-weight: 500;
      }
    }

    .delete-selected-btn {
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s;

      &:disabled {
        background: #333;
        color: #666;
        cursor: not-allowed;
      }

      &:not(:disabled):hover {
        background: #e6004a;
      }
    }
  }

  .item-list {
    .cart-item {
      display: flex;
      align-items: center;
      padding: 16px 0;
      border-bottom: 1px solid #222;
      transition: all 0.2s;

      &:last-child {
        border-bottom: none;
      }

      &.editing {
        .item-selection {
          opacity: 1;
        }
      }

      .item-selection {
        margin-right: 12px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;

        .checkbox {
          width: 20px;
          height: 20px;
          border: 2px solid #666;
          border-radius: 4px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s;

          &.checked {
            background: #ff0050;
            border-color: #ff0050;
            color: #fff;
          }
        }
      }

      .item-image {
        width: 80px;
        height: 80px;
        margin-right: 16px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }

      .item-info {
        flex: 1;
        margin-right: 12px;

        .item-name {
          font-size: 16px;
          font-weight: 500;
          margin: 0 0 4px 0;
          color: #fff;
          cursor: pointer;
          line-height: 1.3;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .item-description {
          font-size: 12px;
          color: #ccc;
          margin: 0 0 8px 0;
          line-height: 1.3;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .item-price {
          font-size: 16px;
          font-weight: 600;
          color: #ff0050;
        }
      }

      .quantity-controls {
        display: flex;
        align-items: center;
        margin-right: 12px;

        .quantity-btn {
          width: 32px;
          height: 32px;
          border: 1px solid #333;
          background: #1a1a1a;
          color: #fff;
          border-radius: 6px;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s;

          &:hover:not(:disabled) {
            background: #333;
            border-color: #555;
          }

          &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
          }
        }

        .quantity-value {
          min-width: 40px;
          text-align: center;
          font-size: 16px;
          font-weight: 500;
          margin: 0 8px;
        }
      }

      .item-total {
        margin-right: 12px;

        .total-price {
          font-size: 16px;
          font-weight: 600;
          color: #fff;
        }
      }

      .delete-item-btn {
        background: #ff0050;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px;
        cursor: pointer;
        transition: all 0.2s;

        &:hover {
          background: #e6004a;
        }
      }
    }
  }
}

.bottom-summary {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #000;
  border-top: 1px solid #333;
  padding: 16px 20px;
  z-index: 100;

  .summary-content {
    display: flex;
    align-items: center;
    justify-content: space-between;

    .summary-info {
      .selected-count {
        font-size: 14px;
        color: #ccc;
        margin-bottom: 4px;
      }

      .total-amount {
        font-size: 18px;
        font-weight: 600;
        color: #fff;

        .amount {
          color: #ff0050;
        }
      }
    }

    .checkout-btn {
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 16px 24px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      min-width: 120px;

      &:hover:not(:disabled) {
        background: #e6004a;
        transform: translateY(-1px);
      }

      &:disabled {
        background: #333;
        color: #666;
        cursor: not-allowed;
        transform: none;
      }
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .cart-content {
    padding: 16px;
  }

  .cart-items .item-list .cart-item {
    .item-image {
      width: 70px;
      height: 70px;
    }

    .item-info .item-name {
      font-size: 15px;
    }

    .quantity-controls .quantity-btn {
      width: 28px;
      height: 28px;
    }
  }

  .bottom-summary {
    padding: 12px 16px;

    .summary-content .checkout-btn {
      padding: 14px 20px;
      font-size: 15px;
      min-width: 100px;
    }
  }
}

// 全局移动端样式
:global(body) {
  background: #000;
  color: #fff;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}
</style>
