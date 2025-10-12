<template>
  <view class="cart">
    <!-- 购物车为空 -->
    <view class="empty-cart" v-if="cartList.length === 0">
      <image src="/static/empty-cart.png" class="empty-image" />
      <text class="empty-text">{{ $t('cart.empty') }}</text>
      <view class="empty-btn" @click="goShopping">
        {{ $t('cart.goShopping') }}
      </view>
    </view>

    <!-- 购物车有商品 -->
    <view class="cart-content" v-else>
      <!-- 全选栏 -->
      <view class="select-all-bar">
        <view class="select-all" @click="toggleSelectAll">
          <uni-icons 
            :type="isAllSelected ? 'checkbox-filled' : 'checkbox'" 
            :color="isAllSelected ? '#409EFF' : '#999'"
            size="20"
          ></uni-icons>
          <text class="select-text">{{ $t('cart.selectAll') }}</text>
        </view>
        <view class="delete-selected" @click="deleteSelected" v-if="selectedCount > 0">
          <uni-icons type="trash" size="16" color="#f56c6c"></uni-icons>
          <text class="delete-text">{{ $t('common.delete') }}</text>
        </view>
      </view>

      <!-- 商品列表 -->
      <view class="cart-list">
        <view 
          v-for="item in cartList" 
          :key="item.id"
          class="cart-item"
        >
          <view class="item-select" @click="toggleSelect(item)">
            <uni-icons 
              :type="item.selected ? 'checkbox-filled' : 'checkbox'" 
              :color="item.selected ? '#409EFF' : '#999'"
              size="18"
            ></uni-icons>
          </view>

          <image :src="item.image" class="item-image" />

          <view class="item-info">
            <text class="item-name">{{ item.name }}</text>
            
            <view class="item-specs" v-if="item.specs && Object.keys(item.specs).length > 0">
              <text 
                v-for="(value, key) in item.specs" 
                :key="key"
                class="spec-text"
              >
                {{ key }}: {{ value }}
              </text>
            </view>

            <view class="item-bottom">
              <view class="item-price">RM{{ item.price }}</view>
              
              <view class="quantity-control">
                <view 
                  class="quantity-btn" 
                  @click="decreaseQuantity(item)"
                  :class="{ disabled: item.quantity <= 1 }"
                >
                  <text>-</text>
                </view>
                <input 
                  class="quantity-input" 
                  type="number" 
                  v-model="item.quantity" 
                  :min="1" 
                  :max="item.stock"
                  @blur="updateQuantity(item)"
                />
                <view 
                  class="quantity-btn" 
                  @click="increaseQuantity(item)"
                  :class="{ disabled: item.quantity >= item.stock }"
                >
                  <text>+</text>
                </view>
              </view>
            </view>
          </view>

          <view class="item-actions">
            <view class="delete-btn" @click="deleteItem(item)">
              <uni-icons type="trash" size="16" color="#f56c6c"></uni-icons>
            </view>
          </view>
        </view>
      </view>

      <!-- 底部结算栏 -->
      <view class="checkout-bar">
        <view class="total-info">
          <text class="total-label">{{ $t('cart.total') }}:</text>
          <text class="total-price">RM{{ totalAmount }}</text>
        </view>
        
        <view class="checkout-btn" @click="checkout" :class="{ disabled: selectedCount === 0 }">
          {{ $t('cart.checkout') }} ({{ selectedCount }})
        </view>
      </view>
    </view>

    <!-- 推荐商品 -->
    <view class="recommend-section" v-if="cartList.length > 0">
      <view class="section-title">{{ $t('cart.recommendProducts') }}</view>
      <scroll-view scroll-x="true" class="recommend-scroll">
        <view class="recommend-list">
          <view 
            v-for="product in recommendProducts" 
            :key="product.id"
            class="recommend-item"
            @click="goToProduct(product)"
          >
            <image :src="product.image" class="recommend-image" />
            <view class="recommend-info">
              <text class="recommend-name">{{ product.name }}</text>
              <text class="recommend-price">RM{{ product.price }}</text>
            </view>
          </view>
        </view>
      </scroll-view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const cartList = ref<any[]>([])
const recommendProducts = ref<any[]>([])

// 计算属性
const isAllSelected = computed(() => {
  return cartList.value.length > 0 && cartList.value.every(item => item.selected)
})

const selectedCount = computed(() => {
  return cartList.value.filter(item => item.selected).length
})

const totalAmount = computed(() => {
  return cartList.value
    .filter(item => item.selected)
    .reduce((total, item) => total + (item.price * item.quantity), 0)
    .toFixed(2)
})

// 模拟购物车数据
const mockCartData = [
  {
    id: 1,
    productId: 1,
    name: 'Premium Wireless Headphones',
    image: '/static/products/headphones.jpg',
    price: 89.99,
    quantity: 2,
    stock: 150,
    selected: true,
    specs: {
      'Color': 'Black',
      'Size': 'Large'
    }
  },
  {
    id: 2,
    productId: 2,
    name: 'Smart Watch Series 5',
    image: '/static/products/smartwatch.jpg',
    price: 219.99,
    quantity: 1,
    stock: 80,
    selected: true,
    specs: {
      'Color': 'Silver',
      'Band': 'Sport'
    }
  },
  {
    id: 3,
    productId: 3,
    name: 'Bluetooth Speaker Pro',
    image: '/static/products/speaker.jpg',
    price: 69.99,
    quantity: 1,
    stock: 200,
    selected: false,
    specs: {}
  }
]

// 模拟推荐商品
const mockRecommendProducts = [
  {
    id: 4,
    name: 'Wireless Charger',
    image: '/static/products/charger.jpg',
    price: 29.99
  },
  {
    id: 5,
    name: 'Phone Case',
    image: '/static/products/case.jpg',
    price: 19.99
  },
  {
    id: 6,
    name: 'Screen Protector',
    image: '/static/products/protector.jpg',
    price: 9.99
  }
]

// 加载购物车数据
const loadCartData = async () => {
  try {
    // 模拟数据
    cartList.value = mockCartData
    recommendProducts.value = mockRecommendProducts

    // 实际API调用
    // const res = await getCartList()
    // cartList.value = res.list
    // recommendProducts.value = res.recommend
    
  } catch (error) {
    console.error('Failed to load cart data:', error)
  }
}

// 全选/取消全选
const toggleSelectAll = () => {
  const newSelectState = !isAllSelected.value
  cartList.value.forEach(item => {
    item.selected = newSelectState
  })
}

// 选择/取消选择商品
const toggleSelect = (item: any) => {
  item.selected = !item.selected
}

// 增加数量
const increaseQuantity = (item: any) => {
  if (item.quantity < item.stock) {
    item.quantity++
    updateCartItem(item)
  }
}

// 减少数量
const decreaseQuantity = (item: any) => {
  if (item.quantity > 1) {
    item.quantity--
    updateCartItem(item)
  }
}

// 更新数量
const updateQuantity = (item: any) => {
  if (item.quantity < 1) {
    item.quantity = 1
  } else if (item.quantity > item.stock) {
    item.quantity = item.stock
  }
  updateCartItem(item)
}

// 更新购物车商品
const updateCartItem = (item: any) => {
  // 模拟API调用
  console.log('Updating cart item:', item)
  
  // 实际API调用
  // await updateCartItemAPI(item)
}

// 删除商品
const deleteItem = (item: any) => {
  uni.showModal({
    title: t('common.warning'),
    content: t('message.confirmDelete'),
    success: (res) => {
      if (res.confirm) {
        cartList.value = cartList.value.filter(cartItem => cartItem.id !== item.id)
        deleteCartItemAPI(item.id)
        
        uni.showToast({
          title: t('message.deleteSuccess'),
          icon: 'success'
        })
      }
    }
  })
}

// 删除选中商品
const deleteSelected = () => {
  uni.showModal({
    title: t('common.warning'),
    content: t('message.confirmDelete'),
    success: (res) => {
      if (res.confirm) {
        const selectedIds = cartList.value
          .filter(item => item.selected)
          .map(item => item.id)
        
        cartList.value = cartList.value.filter(item => !item.selected)
        deleteSelectedCartItemsAPI(selectedIds)
        
        uni.showToast({
          title: t('message.deleteSuccess'),
          icon: 'success'
        })
      }
    }
  })
}

// 结算
const checkout = () => {
  if (selectedCount.value === 0) {
    uni.showToast({
      title: t('cart.selectProducts'),
      icon: 'none'
    })
    return
  }

  const selectedItems = cartList.value.filter(item => item.selected)
  
  // 跳转到订单确认页
  uni.navigateTo({
    url: `/pages/order/confirm?items=${encodeURIComponent(JSON.stringify(selectedItems))}`
  })
}

// 去购物
const goShopping = () => {
  uni.switchTab({
    url: '/pages/index/index'
  })
}

// 跳转到商品详情
const goToProduct = (product: any) => {
  uni.navigateTo({
    url: `/pages/product/detail?id=${product.id}`
  })
}

// API调用
const deleteCartItemAPI = (itemId: number) => {
  // 模拟API调用
  console.log('Deleting cart item:', itemId)
  
  // 实际API调用
  // await deleteCartItem(itemId)
}

const deleteSelectedCartItemsAPI = (itemIds: number[]) => {
  // 模拟API调用
  console.log('Deleting selected cart items:', itemIds)
  
  // 实际API调用
  // await deleteSelectedCartItems(itemIds)
}

onMounted(() => {
  loadCartData()
})
</script>

<style scoped>
.cart {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

/* 空购物车 */
.empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100px 20px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  margin: 20px;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.empty-image {
  width: 150px;
  height: 150px;
  margin-bottom: 30px;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.empty-text {
  font-size: 18px;
  color: #6c757d;
  margin-bottom: 40px;
  font-weight: 500;
}

.empty-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  padding: 15px 40px;
  border-radius: 25px;
  font-size: 16px;
  font-weight: 600;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
}

.empty-btn:active {
  transform: scale(0.95);
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.6);
}

/* 购物车内容 */
.cart-content {
  padding-bottom: 100px;
}

/* 全选栏 */
.select-all-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 20px;
  margin: 20px 20px 10px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.select-all {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  border-radius: 10px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  transition: all 0.3s ease;
}

.select-all:active {
  transform: scale(0.98);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.select-all:active .select-text {
  color: #fff;
}

.select-text {
  font-size: 16px;
  color: #2c3e50;
  font-weight: 600;
  transition: color 0.3s ease;
}

.delete-selected {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 10px;
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  transition: all 0.3s ease;
}

.delete-selected:active {
  transform: scale(0.95);
  box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
}

.delete-text {
  font-size: 14px;
  color: #fff;
  font-weight: 600;
}

/* 商品列表 */
.cart-list {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  margin: 10px 20px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.cart-item:last-child {
  border-bottom: none;
}

.cart-item:active {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.item-select {
  margin-right: 15px;
  padding: 5px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.item-select:active {
  background: rgba(102, 126, 234, 0.1);
}

.item-image {
  width: 90px;
  height: 90px;
  border-radius: 12px;
  margin-right: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.cart-item:active .item-image {
  transform: scale(1.05);
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 16px;
  color: #2c3e50;
  font-weight: 600;
  margin-bottom: 10px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}

.item-specs {
  margin-bottom: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.spec-text {
  font-size: 11px;
  color: #6c757d;
  background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
  padding: 4px 8px;
  border-radius: 8px;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.item-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.item-price {
  font-size: 20px;
  font-weight: 700;
  color: #e74c3c;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px;
  padding: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.quantity-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 600;
  color: #495057;
  background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.quantity-btn:active {
  transform: scale(0.95);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}

.quantity-btn.disabled {
  opacity: 0.4;
  background: #e9ecef;
}

.quantity-input {
  width: 50px;
  height: 36px;
  border: none;
  border-radius: 8px;
  text-align: center;
  font-size: 14px;
  font-weight: 600;
  color: #2c3e50;
  background: transparent;
}

.item-actions {
  margin-left: 15px;
}

.delete-btn {
  padding: 10px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
  transition: all 0.3s ease;
}

.delete-btn:active {
  transform: scale(0.9);
  box-shadow: 0 2px 8px rgba(255, 107, 107, 0.5);
}

/* 底部结算栏 */
.checkout-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
}

.total-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.total-label {
  font-size: 16px;
  color: #2c3e50;
  font-weight: 600;
}

.total-price {
  font-size: 24px;
  font-weight: 700;
  color: #e74c3c;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.checkout-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  padding: 15px 30px;
  border-radius: 25px;
  font-size: 16px;
  font-weight: 700;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
}

.checkout-btn:active {
  transform: scale(0.95);
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.6);
}

.checkout-btn.disabled {
  background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
  box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
}

/* 推荐商品 */
.recommend-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  margin: 20px;
  padding: 25px;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 20px;
  text-align: center;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 3px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 2px;
}

.recommend-scroll {
  white-space: nowrap;
  padding: 5px 0;
}

.recommend-list {
  display: flex;
  gap: 20px;
  padding: 5px 0;
}

.recommend-item {
  flex-shrink: 0;
  width: 140px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.recommend-item:active {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.recommend-image {
  width: 100%;
  height: 100px;
  transition: transform 0.3s ease;
}

.recommend-item:active .recommend-image {
  transform: scale(1.1);
}

.recommend-info {
  padding: 15px;
}

.recommend-name {
  font-size: 13px;
  color: #2c3e50;
  font-weight: 600;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 8px;
  line-height: 1.4;
}

.recommend-price {
  font-size: 16px;
  font-weight: 700;
  color: #e74c3c;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>
