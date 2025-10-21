<template>
  <div class="cart-page">
    <div class="container">
      <div class="cart-header">
        <h1>{{ t('navigation.cart') }}</h1>
        <span class="cart-count">{{ t('cart.totalItems', { count: cartStore.cartCount }) }}</span>
      </div>
      
      <div class="cart-content" v-if="cartStore.cartItems.length > 0">
        <!-- 购物车商品列表 -->
        <div class="cart-items">
          <div class="cart-item" v-for="item in cartStore.cartItems" :key="item.id">
            <div class="item-checkbox">
              <el-checkbox 
                v-model="item.selected" 
                @change="updateSelection"
              />
            </div>
            
            <div class="item-image">
              <img :src="item.product.image" :alt="item.product.name" />
            </div>
            
            <div class="item-info">
              <h3 class="item-name">{{ item.product.name }}</h3>
              <p class="item-description">{{ item.product.description }}</p>
              <div class="item-price">RM{{ item.product.price }}</div>
            </div>
            
            <div class="item-quantity">
              <el-input-number 
                v-model="item.quantity" 
                :min="1" 
                :max="item.product.stock || 99"
                @change="updateQuantity(item)"
                controls-position="right"
              />
            </div>
            
            <div class="item-total">
              <div class="total-price">RM{{ (item.product.price * item.quantity).toFixed(2) }}</div>
            </div>
            
            <div class="item-actions">
              <el-button 
                type="danger" 
                size="small" 
                @click="removeItem(item)"
                :icon="Delete"
              >
                {{ t('common.delete') }}
              </el-button>
            </div>
          </div>
        </div>
        
        <!-- 购物车汇总 -->
        <div class="cart-summary">
          <div class="summary-header">
            <el-checkbox 
              v-model="selectAll" 
              @change="toggleSelectAll"
            >
              {{ t('cart.selectAll') }}
            </el-checkbox>
            <el-button 
              type="danger" 
              size="small" 
              @click="clearSelected"
              :disabled="selectedItems.length === 0"
            >
              {{ t('cart.deleteSelected') }}
            </el-button>
          </div>
          
          <div class="summary-content">
            <div class="summary-info">
              <div class="selected-count">
                {{ t('cart.selectedItems', { count: selectedItems.length }) }}
              </div>
              <div class="total-amount">
                {{ t('common.total') }}: <span class="amount">RM{{ totalAmount.toFixed(2) }}</span>
              </div>
            </div>
            
            <div class="summary-actions">
              <el-button 
                type="primary" 
                size="large"
                @click="goToCheckout"
                :disabled="selectedItems.length === 0"
              >
                {{ t('cart.checkout', { count: selectedItems.length }) }}
              </el-button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 空购物车 -->
      <div class="empty-cart" v-else>
        <div class="empty-content">
          <el-icon size="64" color="#ccc"><ShoppingCart /></el-icon>
          <h3>{{ t('cart.emptyCart') }}</h3>
          <p>{{ t('cart.emptyCartDesc') }}</p>
          <el-button type="primary" @click="$router.push('/')">{{ t('cart.goShopping') }}</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useCartStore } from '@/stores/cart'
import { Delete, ShoppingCart } from '@element-plus/icons-vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const router = useRouter()
const { t } = useI18n()
const cartStore = useCartStore()

const selectAll = ref(false)

// 计算属性
const selectedItems = computed(() => {
  return cartStore.cartItems.filter(item => item.selected)
})

const totalAmount = computed(() => {
  return selectedItems.value.reduce((total, item) => {
    return total + (item.product.price * item.quantity)
  }, 0)
})

// 更新选择状态
const updateSelection = () => {
  selectAll.value = cartStore.cartItems.length > 0 && 
    cartStore.cartItems.every(item => item.selected)
}

// 全选/取消全选
const toggleSelectAll = (checked: boolean) => {
  cartStore.cartItems.forEach(item => {
    item.selected = checked
  })
}

// 更新商品数量
const updateQuantity = async (item: any) => {
  try {
    await cartStore.updateQuantity(item.id, item.quantity)
    ElMessage.success('数量已更新')
  } catch (error) {
    ElMessage.error('更新失败，请重试')
  }
}

// 删除商品
const removeItem = async (item: any) => {
  try {
    await ElMessageBox.confirm('确定要删除这个商品吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    await cartStore.removeItem(item.id)
    ElMessage.success('已删除')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败，请重试')
    }
  }
}

// 清空选中商品
const clearSelected = async () => {
  try {
    await ElMessageBox.confirm(`确定要删除选中的${selectedItems.value.length}件商品吗？`, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    for (const item of selectedItems.value) {
      await cartStore.removeItem(item.id)
    }
    
    ElMessage.success('已删除选中商品')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败，请重试')
    }
  }
}

// 去结算
const goToCheckout = () => {
  if (selectedItems.value.length === 0) {
    ElMessage.warning('请选择要结算的商品')
    return
  }
  
  // 将选中的商品信息存储到sessionStorage
  sessionStorage.setItem('checkoutItems', JSON.stringify(selectedItems.value))
  router.push('/order')
}

// 加载购物车数据
const loadCart = async () => {
  try {
    await cartStore.fetchCart()
    
    // 初始化选择状态
    cartStore.cartItems.forEach(item => {
      if (item.selected === undefined) {
        item.selected = true
      }
    })
    
    updateSelection()
  } catch (error) {
    console.error('加载购物车失败:', error)
  }
}

onMounted(() => {
  loadCart()
})
</script>

<style scoped lang="scss">
.cart-page {
  padding: 20px 0;
  background: $background-base;
  min-height: 100vh;
}

.cart-header {
  background: #fff;
  padding: 20px 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  
  h1 {
    font-size: 24px;
    font-weight: bold;
    color: $text-primary;
    margin: 0;
  }
  
  .cart-count {
    color: $text-secondary;
  }
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 20px;
}

.cart-items {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
}

.cart-item {
  display: grid;
  grid-template-columns: 40px 100px 1fr 120px 100px 80px;
  gap: 15px;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid $border-lighter;
  
  &:last-child {
    border-bottom: none;
  }
}

.item-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.item-info {
  .item-name {
    font-size: 16px;
    font-weight: 500;
    color: $text-primary;
    margin: 0 0 8px 0;
    line-height: 1.4;
  }
  
  .item-description {
    font-size: 14px;
    color: $text-secondary;
    margin: 0 0 8px 0;
    line-height: 1.4;
  }
  
  .item-price {
    font-size: 16px;
    font-weight: bold;
    color: $danger-color;
  }
}

.item-total {
  text-align: right;
  
  .total-price {
    font-size: 18px;
    font-weight: bold;
    color: $danger-color;
  }
}

.cart-summary {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  height: fit-content;
  position: sticky;
  top: 20px;
}

.summary-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid $border-lighter;
}

.summary-content {
  .summary-info {
    margin-bottom: 20px;
    
    .selected-count {
      font-size: 14px;
      color: $text-secondary;
      margin-bottom: 10px;
    }
    
    .total-amount {
      font-size: 18px;
      font-weight: bold;
      color: $text-primary;
      
      .amount {
        color: $danger-color;
        font-size: 24px;
      }
    }
  }
  
  .summary-actions {
    .el-button {
      width: 100%;
      height: 45px;
      font-size: 16px;
    }
  }
}

.empty-cart {
  background: #fff;
  padding: 60px 20px;
  border-radius: 8px;
  text-align: center;
  
  .empty-content {
    h3 {
      font-size: 20px;
      color: $text-primary;
      margin: 20px 0 10px;
    }
    
    p {
      color: $text-secondary;
      margin-bottom: 20px;
    }
  }
}

@media (max-width: 768px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
  
  .cart-item {
    grid-template-columns: 1fr;
    gap: 10px;
    text-align: center;
  }
  
  .item-image {
    width: 80px;
    height: 80px;
    margin: 0 auto;
  }
  
  .cart-summary {
    position: static;
  }
  
  .cart-header {
    padding: 15px 20px;
    
    h1 {
      font-size: 20px;
    }
  }
}
</style>
