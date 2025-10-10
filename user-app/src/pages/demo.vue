<template>
  <view class="demo-page">
    <view class="header">
      <text class="title">商城前端虚拟数据演示</text>
      <text class="subtitle">可以直接查看排版和交互效果</text>
    </view>

    <!-- 轮播图 -->
    <view class="banner-section">
      <text class="section-title">轮播图</text>
      <swiper class="banner-swiper" autoplay circular>
        <swiper-item v-for="banner in banners" :key="banner.id">
          <image :src="banner.image" class="banner-image" />
          <view class="banner-content">
            <text class="banner-title">{{ banner.title }}</text>
          </view>
        </swiper-item>
      </swiper>
    </view>

    <!-- 分类导航 -->
    <view class="category-section">
      <text class="section-title">商品分类</text>
      <view class="category-grid">
        <view 
          v-for="category in categories" 
          :key="category.id" 
          class="category-item"
          @click="selectCategory(category)"
        >
          <text class="category-icon">{{ category.icon }}</text>
          <text class="category-name">{{ category.name }}</text>
        </view>
      </view>
    </view>

    <!-- 热门商品 -->
    <view class="products-section">
      <view class="section-header">
        <text class="section-title">热门商品</text>
        <text class="more-btn" @click="viewMore">更多 ></text>
      </view>
      <scroll-view scroll-x class="products-scroll">
        <view class="products-list">
          <view 
            v-for="product in hotProducts" 
            :key="product.id" 
            class="product-card"
            @click="viewProduct(product)"
          >
            <image :src="product.image" class="product-image" />
            <view class="product-info">
              <text class="product-name">{{ product.name }}</text>
              <view class="product-price">
                <text class="current-price">¥{{ product.price }}</text>
                <text class="original-price" v-if="product.originalPrice">¥{{ product.originalPrice }}</text>
              </view>
              <view class="product-meta">
                <text class="sales">已售{{ product.sales }}</text>
                <text class="rating">⭐ {{ product.rating }}</text>
              </view>
            </view>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 商品列表 -->
    <view class="products-section">
      <view class="section-header">
        <text class="section-title">商品列表</text>
        <view class="filter-btns">
          <text 
            v-for="sort in sortOptions" 
            :key="sort.value"
            class="filter-btn"
            :class="{ active: currentSort === sort.value }"
            @click="changeSort(sort.value)"
          >
            {{ sort.label }}
          </text>
        </view>
      </view>
      <view class="products-grid">
        <view 
          v-for="product in products" 
          :key="product.id" 
          class="product-item"
          @click="viewProduct(product)"
        >
          <image :src="product.image" class="product-image" />
          <view class="product-info">
            <text class="product-name">{{ product.name }}</text>
            <view class="product-price">
              <text class="current-price">¥{{ product.price }}</text>
              <text class="original-price" v-if="product.originalPrice">¥{{ product.originalPrice }}</text>
            </view>
            <view class="product-tags">
              <text 
                v-for="tag in product.tags" 
                :key="tag" 
                class="product-tag"
              >
                {{ tag }}
              </text>
            </view>
          </view>
        </view>
      </view>
    </view>

    <!-- 购物车 -->
    <view class="cart-section">
      <text class="section-title">购物车</text>
      <view class="cart-items">
        <view 
          v-for="item in cartItems" 
          :key="item.id" 
          class="cart-item"
        >
          <image :src="item.product.image" class="cart-image" />
          <view class="cart-info">
            <text class="cart-name">{{ item.product.name }}</text>
            <view class="cart-price">¥{{ item.product.price }}</view>
            <view class="cart-controls">
              <text class="quantity-btn" @click="decreaseQuantity(item)">-</text>
              <text class="quantity">{{ item.quantity }}</text>
              <text class="quantity-btn" @click="increaseQuantity(item)">+</text>
            </view>
          </view>
        </view>
      </view>
    </view>

    <!-- 订单列表 -->
    <view class="orders-section">
      <text class="section-title">订单列表</text>
      <view class="orders-list">
        <view 
          v-for="order in orders" 
          :key="order.id" 
          class="order-item"
          @click="viewOrder(order)"
        >
          <view class="order-header">
            <text class="order-no">{{ order.orderNo }}</text>
            <text class="order-status" :class="order.status">{{ getStatusText(order.status) }}</text>
          </view>
          <view class="order-items">
            <view 
              v-for="item in order.items" 
              :key="item.id" 
              class="order-item-info"
            >
              <image :src="item.product.image" class="order-item-image" />
              <view class="order-item-details">
                <text class="order-item-name">{{ item.product.name }}</text>
                <text class="order-item-price">¥{{ item.price }} x {{ item.quantity }}</text>
              </view>
            </view>
          </view>
          <view class="order-footer">
            <text class="order-total">总计: ¥{{ order.totalAmount }}</text>
            <view class="order-actions">
              <text class="action-btn" v-if="order.status === 'pending'" @click="payOrder(order)">立即支付</text>
              <text class="action-btn" v-if="order.status === 'paid'" @click="cancelOrder(order)">取消订单</text>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { 
  getBanners, 
  getCategories, 
  getHotProducts, 
  getProducts, 
  getCart, 
  getOrders,
  type Product,
  type Category,
  type CartItem,
  type Order
} from '@/api/index'

// 响应式数据
const banners = ref([])
const categories = ref([])
const hotProducts = ref([])
const products = ref([])
const cartItems = ref([])
const orders = ref([])
const currentSort = ref('default')

// 排序选项
const sortOptions = [
  { label: '默认', value: 'default' },
  { label: '价格', value: 'price' },
  { label: '销量', value: 'sales' },
  { label: '评分', value: 'rating' }
]

// 加载数据
const loadData = async () => {
  try {
    const [bannersRes, categoriesRes, hotProductsRes, productsRes, cartRes, ordersRes] = await Promise.all([
      getBanners(),
      getCategories(),
      getHotProducts(),
      getProducts({ page: 1, pageSize: 12 }),
      getCart(),
      getOrders({ page: 1, pageSize: 5 })
    ])

    banners.value = bannersRes.data || []
    categories.value = categoriesRes.data || []
    hotProducts.value = hotProductsRes.data || []
    products.value = productsRes.data?.list || []
    cartItems.value = cartRes.data || []
    orders.value = ordersRes.data?.list || []
  } catch (error) {
    console.error('加载数据失败:', error)
  }
}

// 选择分类
const selectCategory = (category: Category) => {
  console.log('选择分类:', category)
  // 这里可以跳转到分类页面或筛选商品
}

// 查看商品详情
const viewProduct = (product: Product) => {
  console.log('查看商品:', product)
  // 这里可以跳转到商品详情页
}

// 查看更多
const viewMore = () => {
  console.log('查看更多商品')
  // 这里可以跳转到商品列表页
}

// 改变排序
const changeSort = (sort: string) => {
  currentSort.value = sort
  console.log('改变排序:', sort)
  // 这里可以重新加载商品列表
}

// 购物车操作
const increaseQuantity = (item: CartItem) => {
  item.quantity++
}

const decreaseQuantity = (item: CartItem) => {
  if (item.quantity > 1) {
    item.quantity--
  }
}

// 订单操作
const viewOrder = (order: Order) => {
  console.log('查看订单:', order)
  // 这里可以跳转到订单详情页
}

const payOrder = (order: Order) => {
  console.log('支付订单:', order)
  // 这里可以跳转到支付页面
}

const cancelOrder = (order: Order) => {
  console.log('取消订单:', order)
  // 这里可以取消订单
}

// 获取订单状态文本
const getStatusText = (status: string) => {
  const statusMap = {
    pending: '待支付',
    paid: '已支付',
    shipped: '已发货',
    delivered: '已送达',
    cancelled: '已取消'
  }
  return statusMap[status] || status
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.demo-page {
  padding: 20rpx;
  background-color: #f5f5f5;
}

.header {
  text-align: center;
  margin-bottom: 40rpx;
}

.title {
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
}

.subtitle {
  font-size: 24rpx;
  color: #666;
  margin-top: 10rpx;
}

.section-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 20rpx;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}

.more-btn {
  font-size: 24rpx;
  color: #007aff;
}

/* 轮播图 */
.banner-section {
  margin-bottom: 40rpx;
}

.banner-swiper {
  height: 300rpx;
  border-radius: 20rpx;
  overflow: hidden;
}

.banner-image {
  width: 100%;
  height: 100%;
}

.banner-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.5));
  padding: 40rpx 20rpx 20rpx;
}

.banner-title {
  color: white;
  font-size: 28rpx;
  font-weight: bold;
}

/* 分类导航 */
.category-section {
  margin-bottom: 40rpx;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20rpx;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx;
  background: white;
  border-radius: 20rpx;
}

.category-icon {
  font-size: 40rpx;
  margin-bottom: 10rpx;
}

.category-name {
  font-size: 24rpx;
  color: #333;
}

/* 商品列表 */
.products-section {
  margin-bottom: 40rpx;
}

.products-scroll {
  white-space: nowrap;
}

.products-list {
  display: flex;
  gap: 20rpx;
}

.product-card {
  width: 280rpx;
  background: white;
  border-radius: 20rpx;
  overflow: hidden;
  flex-shrink: 0;
}

.product-image {
  width: 100%;
  height: 200rpx;
}

.product-info {
  padding: 20rpx;
}

.product-name {
  font-size: 24rpx;
  color: #333;
  margin-bottom: 10rpx;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  display: flex;
  align-items: center;
  margin-bottom: 10rpx;
}

.current-price {
  font-size: 28rpx;
  color: #ff4444;
  font-weight: bold;
}

.original-price {
  font-size: 20rpx;
  color: #999;
  text-decoration: line-through;
  margin-left: 10rpx;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  font-size: 20rpx;
  color: #666;
}

/* 商品网格 */
.products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20rpx;
}

.product-item {
  background: white;
  border-radius: 20rpx;
  overflow: hidden;
}

.product-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10rpx;
  margin-top: 10rpx;
}

.product-tag {
  font-size: 20rpx;
  color: #007aff;
  background: #e6f3ff;
  padding: 4rpx 8rpx;
  border-radius: 8rpx;
}

/* 筛选按钮 */
.filter-btns {
  display: flex;
  gap: 20rpx;
}

.filter-btn {
  font-size: 24rpx;
  color: #666;
  padding: 10rpx 20rpx;
  border-radius: 20rpx;
  background: white;
}

.filter-btn.active {
  color: #007aff;
  background: #e6f3ff;
}

/* 购物车 */
.cart-section {
  margin-bottom: 40rpx;
}

.cart-items {
  background: white;
  border-radius: 20rpx;
  padding: 20rpx;
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.cart-item:last-child {
  border-bottom: none;
}

.cart-image {
  width: 120rpx;
  height: 120rpx;
  border-radius: 10rpx;
  margin-right: 20rpx;
}

.cart-info {
  flex: 1;
}

.cart-name {
  font-size: 24rpx;
  color: #333;
  margin-bottom: 10rpx;
}

.cart-price {
  font-size: 28rpx;
  color: #ff4444;
  font-weight: bold;
  margin-bottom: 10rpx;
}

.cart-controls {
  display: flex;
  align-items: center;
  gap: 20rpx;
}

.quantity-btn {
  width: 60rpx;
  height: 60rpx;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24rpx;
  color: #333;
}

.quantity {
  font-size: 24rpx;
  color: #333;
}

/* 订单列表 */
.orders-section {
  margin-bottom: 40rpx;
}

.orders-list {
  background: white;
  border-radius: 20rpx;
  padding: 20rpx;
}

.order-item {
  padding: 20rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.order-item:last-child {
  border-bottom: none;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}

.order-no {
  font-size: 24rpx;
  color: #333;
}

.order-status {
  font-size: 24rpx;
  padding: 8rpx 16rpx;
  border-radius: 20rpx;
}

.order-status.pending {
  color: #ff9500;
  background: #fff3e0;
}

.order-status.paid {
  color: #007aff;
  background: #e6f3ff;
}

.order-status.shipped {
  color: #34c759;
  background: #e8f5e8;
}

.order-status.delivered {
  color: #34c759;
  background: #e8f5e8;
}

.order-status.cancelled {
  color: #ff4444;
  background: #ffe6e6;
}

.order-items {
  margin-bottom: 20rpx;
}

.order-item-info {
  display: flex;
  align-items: center;
  margin-bottom: 10rpx;
}

.order-item-image {
  width: 80rpx;
  height: 80rpx;
  border-radius: 10rpx;
  margin-right: 20rpx;
}

.order-item-details {
  flex: 1;
}

.order-item-name {
  font-size: 24rpx;
  color: #333;
  margin-bottom: 5rpx;
}

.order-item-price {
  font-size: 20rpx;
  color: #666;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-total {
  font-size: 28rpx;
  color: #ff4444;
  font-weight: bold;
}

.order-actions {
  display: flex;
  gap: 20rpx;
}

.action-btn {
  font-size: 24rpx;
  color: #007aff;
  padding: 10rpx 20rpx;
  border: 1rpx solid #007aff;
  border-radius: 20rpx;
}
</style>
