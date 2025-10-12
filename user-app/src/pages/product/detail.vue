<template>
  <view class="product-detail">
    <!-- 商品图片轮播 -->
    <view class="product-images">
      <swiper class="image-swiper" :indicator-dots="true" :autoplay="false">
        <swiper-item v-for="(image, index) in productImages" :key="index">
          <image :src="image" class="product-image" mode="aspectFill" />
        </swiper-item>
      </swiper>
    </view>

    <!-- 商品基本信息 -->
    <view class="product-info">
      <view class="product-title">{{ product.name }}</view>
      <view class="product-subtitle">{{ product.brand }}</view>
      
      <view class="price-section">
        <view class="current-price">RM{{ product.price }}</view>
        <view class="original-price" v-if="product.originalPrice">RM{{ product.originalPrice }}</view>
        <view class="discount-tag" v-if="product.originalPrice">
          {{ Math.round((1 - product.price / product.originalPrice) * 100) }}% OFF
        </view>
      </view>

      <view class="product-tags">
        <text class="tag hot-tag" v-if="product.isHot">{{ $t('product.hot') }}</text>
        <text class="tag new-tag" v-if="product.isNew">{{ $t('product.new') }}</text>
        <text class="tag stock-tag" :class="product.stock > 0 ? 'in-stock' : 'out-stock'">
          {{ product.stock > 0 ? $t('product.inStock') : $t('product.outOfStock') }}
        </text>
      </view>
    </view>

    <!-- 商品规格选择 -->
    <view class="spec-section" v-if="product.specs && product.specs.length > 0">
      <view class="section-title">{{ $t('product.selectSpec') }}</view>
      <view class="spec-list">
        <view 
          v-for="spec in product.specs" 
          :key="spec.id"
          class="spec-item"
          :class="{ active: selectedSpecs[spec.name] === spec.value }"
          @click="selectSpec(spec.name, spec.value)"
        >
          {{ spec.value }}
        </view>
      </view>
    </view>

    <!-- 数量选择 -->
    <view class="quantity-section">
      <view class="section-title">{{ $t('product.quantity') }}</view>
      <view class="quantity-control">
        <view class="quantity-btn" @click="decreaseQuantity" :class="{ disabled: quantity <= 1 }">
          <text>-</text>
        </view>
        <input 
          class="quantity-input" 
          type="number" 
          v-model="quantity" 
          :min="1" 
          :max="product.stock"
        />
        <view class="quantity-btn" @click="increaseQuantity" :class="{ disabled: quantity >= product.stock }">
          <text>+</text>
        </view>
      </view>
    </view>

    <!-- 商品详情 -->
    <view class="detail-section">
      <view class="section-title">{{ $t('product.description') }}</view>
      <view class="detail-content">
        <rich-text :nodes="product.description"></rich-text>
      </view>
    </view>

    <!-- 商品参数 -->
    <view class="params-section" v-if="product.params && product.params.length > 0">
      <view class="section-title">{{ $t('product.parameters') }}</view>
      <view class="params-list">
        <view 
          v-for="param in product.params" 
          :key="param.name"
          class="param-item"
        >
          <text class="param-name">{{ param.name }}</text>
          <text class="param-value">{{ param.value }}</text>
        </view>
      </view>
    </view>

    <!-- 底部操作栏 -->
    <view class="bottom-bar">
      <view class="action-buttons">
        <view class="btn-secondary" @click="addToFavorites">
          <uni-icons type="heart" size="20" :color="isFavorite ? '#f56c6c' : '#999'"></uni-icons>
          <text>{{ isFavorite ? $t('product.favorited') : $t('product.favorite') }}</text>
        </view>
        
        <view class="btn-secondary" @click="goToCart">
          <uni-icons type="cart" size="20" color="#999"></uni-icons>
          <text>{{ $t('cart.title') }}</text>
          <view class="cart-badge" v-if="cartCount > 0">{{ cartCount }}</view>
        </view>
      </view>

      <view class="main-buttons">
        <view class="btn-primary" @click="addToCart">
          {{ $t('product.addToCart') }}
        </view>
        <view class="btn-buy" @click="buyNow">
          {{ $t('product.buyNow') }}
        </view>
      </view>
    </view>

    <!-- 规格选择弹窗 -->
    <uni-popup ref="specPopup" type="bottom">
      <view class="spec-modal">
        <view class="modal-header">
          <text class="modal-title">{{ $t('product.selectSpec') }}</text>
          <uni-icons type="close" size="20" @click="closeSpecModal"></uni-icons>
        </view>
        
        <view class="modal-content">
          <view class="product-summary">
            <image :src="product.mainImage" class="summary-image" />
            <view class="summary-info">
              <text class="summary-name">{{ product.name }}</text>
              <text class="summary-price">RM{{ product.price }}</text>
            </view>
          </view>

          <view class="spec-groups" v-for="specGroup in product.specGroups" :key="specGroup.name">
            <view class="spec-group-title">{{ specGroup.name }}</view>
            <view class="spec-group-list">
              <view 
                v-for="spec in specGroup.specs" 
                :key="spec.value"
                class="spec-option"
                :class="{ active: selectedSpecs[specGroup.name] === spec.value }"
                @click="selectSpec(specGroup.name, spec.value)"
              >
                {{ spec.value }}
              </view>
            </view>
          </view>

          <view class="quantity-selector">
            <text class="quantity-label">{{ $t('product.quantity') }}</text>
            <view class="quantity-control">
              <view class="quantity-btn" @click="decreaseQuantity" :class="{ disabled: quantity <= 1 }">
                <text>-</text>
              </view>
              <input 
                class="quantity-input" 
                type="number" 
                v-model="quantity" 
                :min="1" 
                :max="product.stock"
              />
              <view class="quantity-btn" @click="increaseQuantity" :class="{ disabled: quantity >= product.stock }">
                <text>+</text>
              </view>
            </view>
          </view>
        </view>

        <view class="modal-footer">
          <view class="btn-primary" @click="confirmAddToCart">
            {{ $t('product.addToCart') }}
          </view>
          <view class="btn-buy" @click="confirmBuyNow">
            {{ $t('product.buyNow') }}
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { onLoad } from '@dcloudio/uni-app'

const { t } = useI18n()

const productId = ref('')
const product = ref<any>({})
const productImages = ref<string[]>([])
const selectedSpecs = reactive<Record<string, string>>({})
const quantity = ref(1)
const isFavorite = ref(false)
const cartCount = ref(0)

// 模拟商品数据
const mockProduct = {
  id: 1,
  name: 'Premium Wireless Headphones',
  brand: 'TechPro',
  price: 89.99,
  originalPrice: 129.99,
  mainImage: '/static/products/headphones.jpg',
  images: [
    '/static/products/headphones.jpg',
    '/static/products/headphones2.jpg',
    '/static/products/headphones3.jpg'
  ],
  stock: 150,
  sales: 1250,
  isHot: true,
  isNew: false,
  description: '<p>Premium wireless headphones with noise cancellation technology. Perfect for music lovers and professionals.</p><p>Features:</p><ul><li>Active Noise Cancellation</li><li>30-hour battery life</li><li>Quick charge (10 min = 3 hours)</li><li>Premium sound quality</li></ul>',
  specs: [
    { id: 1, name: 'Color', value: 'Black' },
    { id: 2, name: 'Color', value: 'White' },
    { id: 3, name: 'Color', value: 'Blue' }
  ],
  specGroups: [
    {
      name: 'Color',
      specs: [
        { value: 'Black' },
        { value: 'White' },
        { value: 'Blue' }
      ]
    }
  ],
  params: [
    { name: 'Brand', value: 'TechPro' },
    { name: 'Model', value: 'TP-WH-2024' },
    { name: 'Battery Life', value: '30 hours' },
    { name: 'Charging Time', value: '2 hours' },
    { name: 'Weight', value: '250g' },
    { name: 'Warranty', value: '2 years' }
  ]
}

// 页面加载
onLoad((options: any) => {
  productId.value = options.id || '1'
  loadProduct()
})

// 加载商品数据
const loadProduct = async () => {
  try {
    // 模拟数据
    product.value = mockProduct
    productImages.value = mockProduct.images
    
    // 设置默认规格
    if (mockProduct.specs && mockProduct.specs.length > 0) {
      selectedSpecs[mockProduct.specs[0].name] = mockProduct.specs[0].value
    }

    // 实际API调用
    // const res = await getProductDetail(productId.value)
    // product.value = res
    
  } catch (error) {
    console.error('Failed to load product:', error)
    uni.showToast({
      title: t('message.networkError'),
      icon: 'error'
    })
  }
}

// 选择规格
const selectSpec = (specName: string, specValue: string) => {
  selectedSpecs[specName] = specValue
}

// 增加数量
const increaseQuantity = () => {
  if (quantity.value < product.value.stock) {
    quantity.value++
  }
}

// 减少数量
const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

// 添加到收藏
const addToFavorites = () => {
  isFavorite.value = !isFavorite.value
  uni.showToast({
    title: isFavorite.value ? t('product.favorited') : t('product.unfavorited'),
    icon: 'success'
  })
}

// 添加到购物车
const addToCart = () => {
  if (product.value.specs && product.value.specs.length > 0) {
    // 有规格选择，显示弹窗
    uni.$refs.specPopup?.open()
  } else {
    // 直接添加
    confirmAddToCart()
  }
}

// 确认添加到购物车
const confirmAddToCart = () => {
  const cartItem = {
    productId: product.value.id,
    name: product.value.name,
    image: product.value.mainImage,
    price: product.value.price,
    quantity: quantity.value,
    specs: { ...selectedSpecs }
  }
  
  // 添加到购物车
  addToCartAPI(cartItem)
  
  uni.showToast({
    title: t('message.addCartSuccess'),
    icon: 'success'
  })
  
  closeSpecModal()
}

// 立即购买
const buyNow = () => {
  if (product.value.specs && product.value.specs.length > 0) {
    // 有规格选择，显示弹窗
    uni.$refs.specPopup?.open()
  } else {
    // 直接购买
    confirmBuyNow()
  }
}

// 确认立即购买
const confirmBuyNow = () => {
  const orderItem = {
    productId: product.value.id,
    name: product.value.name,
    image: product.value.mainImage,
    price: product.value.price,
    quantity: quantity.value,
    specs: { ...selectedSpecs }
  }
  
  // 跳转到订单确认页
  uni.navigateTo({
    url: `/pages/order/confirm?items=${encodeURIComponent(JSON.stringify([orderItem]))}`
  })
  
  closeSpecModal()
}

// 关闭规格弹窗
const closeSpecModal = () => {
  uni.$refs.specPopup?.close()
}

// 跳转到购物车
const goToCart = () => {
  uni.switchTab({
    url: '/pages/cart/cart'
  })
}

// 添加到购物车API
const addToCartAPI = (item: any) => {
  // 模拟API调用
  console.log('Adding to cart:', item)
  cartCount.value++
  
  // 实际API调用
  // await addToCart(item)
}

onMounted(() => {
  // 获取购物车数量
  getCartCount()
})

const getCartCount = () => {
  // 模拟获取购物车数量
  cartCount.value = 3
  
  // 实际API调用
  // const res = await getCartCount()
  // cartCount.value = res.count
}
</script>

<style scoped>
.product-detail {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
  padding-bottom: 100px;
  position: relative;
}

.product-detail::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 300px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  z-index: -1;
}

/* 商品图片 */
.product-images {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  margin: 20px;
  border-radius: 25px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  position: relative;
  z-index: 5;
}

.image-swiper {
  height: 400px;
}

.product-image {
  width: 100%;
  height: 100%;
  filter: brightness(1.02) contrast(1.05);
}

/* 商品信息 */
.product-info {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  margin: 0 20px 20px;
  padding: 25px;
  border-radius: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.3);
  position: relative;
  z-index: 5;
}

.product-title {
  font-size: 22px;
  font-weight: 800;
  color: #2c3e50;
  margin-bottom: 8px;
  line-height: 1.3;
  letter-spacing: 0.3px;
}

.product-subtitle {
  font-size: 16px;
  color: #7f8c8d;
  margin-bottom: 20px;
  font-weight: 600;
  letter-spacing: 0.3px;
}

.price-section {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  gap: 15px;
}

.current-price {
  font-size: 28px;
  font-weight: 900;
  color: #e74c3c;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 2px 4px rgba(231, 76, 60, 0.2);
}

.original-price {
  font-size: 18px;
  color: #95a5a6;
  text-decoration: line-through;
  font-weight: 600;
}

.discount-tag {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  color: #fff;
  padding: 6px 12px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
}

.product-tags {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.tag {
  font-size: 12px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.hot-tag {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  color: #fff;
  box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
}

.new-tag {
  background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
  color: #fff;
  box-shadow: 0 3px 10px rgba(39, 174, 96, 0.3);
}

.stock-tag.in-stock {
  background: linear-gradient(135deg, rgba(24, 144, 255, 0.1) 0%, rgba(24, 144, 255, 0.2) 100%);
  color: #1890ff;
  border: 1px solid rgba(24, 144, 255, 0.3);
}

.stock-tag.out-stock {
  background: linear-gradient(135deg, rgba(250, 140, 22, 0.1) 0%, rgba(250, 140, 22, 0.2) 100%);
  color: #fa8c16;
  border: 1px solid rgba(250, 140, 22, 0.3);
}

/* 规格选择 */
.spec-section {
  background-color: #fff;
  padding: 20px;
  margin-bottom: 10px;
}

.section-title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 15px;
}

.spec-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.spec-item {
  padding: 8px 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  color: #666;
}

.spec-item.active {
  border-color: #409EFF;
  color: #409EFF;
  background-color: #e6f7ff;
}

/* 数量选择 */
.quantity-section {
  background-color: #fff;
  padding: 20px;
  margin-bottom: 10px;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 10px;
}

.quantity-btn {
  width: 40px;
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: #666;
}

.quantity-btn.disabled {
  opacity: 0.5;
}

.quantity-input {
  width: 80px;
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 4px;
  text-align: center;
  font-size: 16px;
}

/* 商品详情 */
.detail-section {
  background-color: #fff;
  padding: 20px;
  margin-bottom: 10px;
}

.detail-content {
  line-height: 1.6;
  color: #666;
}

/* 商品参数 */
.params-section {
  background-color: #fff;
  padding: 20px;
  margin-bottom: 10px;
}

.params-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.param-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
}

.param-name {
  color: #666;
  font-size: 14px;
}

.param-value {
  color: #333;
  font-size: 14px;
}

/* 底部操作栏 */
.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  border-top: 1px solid #eee;
  padding: 10px 15px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.action-buttons {
  display: flex;
  gap: 15px;
}

.btn-secondary {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 8px;
  position: relative;
}

.btn-secondary text {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

.cart-badge {
  position: absolute;
  top: 0;
  right: 0;
  background-color: #f56c6c;
  color: #fff;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 16px;
  text-align: center;
}

.main-buttons {
  flex: 1;
  display: flex;
  gap: 10px;
}

.btn-primary {
  flex: 1;
  background-color: #409EFF;
  color: #fff;
  padding: 12px;
  border-radius: 6px;
  text-align: center;
  font-size: 16px;
}

.btn-buy {
  flex: 1;
  background-color: #f56c6c;
  color: #fff;
  padding: 12px;
  border-radius: 6px;
  text-align: center;
  font-size: 16px;
}

/* 规格选择弹窗 */
.spec-modal {
  background-color: #fff;
  border-radius: 12px 12px 0 0;
  max-height: 80vh;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-title {
  font-size: 18px;
  font-weight: bold;
  color: #333;
}

.modal-content {
  padding: 20px;
}

.product-summary {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.summary-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
}

.summary-info {
  flex: 1;
}

.summary-name {
  font-size: 16px;
  color: #333;
  margin-bottom: 8px;
}

.summary-price {
  font-size: 18px;
  font-weight: bold;
  color: #f56c6c;
}

.spec-groups {
  margin-bottom: 20px;
}

.spec-group-title {
  font-size: 16px;
  color: #333;
  margin-bottom: 10px;
}

.spec-group-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.spec-option {
  padding: 8px 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  color: #666;
}

.spec-option.active {
  border-color: #409EFF;
  color: #409EFF;
  background-color: #e6f7ff;
}

.quantity-selector {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.quantity-label {
  font-size: 16px;
  color: #333;
}

.modal-footer {
  display: flex;
  gap: 10px;
  padding: 20px;
  border-top: 1px solid #eee;
}
</style>
