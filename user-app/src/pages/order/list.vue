<template>
  <view class="order-list">
    <!-- 订单状态筛选 -->
    <view class="status-filter">
      <scroll-view scroll-x="true" class="filter-scroll">
        <view class="filter-list">
          <view 
            v-for="status in statusList" 
            :key="status.value"
            class="filter-item"
            :class="{ active: currentStatus === status.value }"
            @click="switchStatus(status.value)"
          >
            <text class="filter-text">{{ status.label }}</text>
            <view class="filter-badge" v-if="status.count > 0">{{ status.count }}</view>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 订单列表 -->
    <view class="order-content">
      <view v-if="orderList.length === 0" class="empty-state">
        <image src="/static/empty-order.png" class="empty-image" />
        <text class="empty-text">{{ $t('order.noOrders') }}</text>
        <view class="empty-btn" @click="goShopping">
          {{ $t('order.goShopping') }}
        </view>
      </view>

      <view v-else class="order-list-content">
        <view 
          v-for="order in orderList" 
          :key="order.id"
          class="order-item"
          @click="goToOrderDetail(order)"
        >
          <!-- 订单头部 -->
          <view class="order-header">
            <view class="order-info">
              <text class="order-no">{{ $t('order.orderNo') }}: {{ order.orderNo }}</text>
              <text class="order-time">{{ order.orderTime }}</text>
            </view>
            <view class="order-status">
              <text class="status-text" :class="getStatusClass(order.status)">
                {{ getStatusText(order.status) }}
              </text>
            </view>
          </view>

          <!-- 商品列表 -->
          <view class="order-products">
            <view 
              v-for="item in order.items" 
              :key="item.id"
              class="product-item"
            >
              <image :src="item.image" class="product-image" />
              <view class="product-info">
                <text class="product-name">{{ item.name }}</text>
                <view class="product-specs" v-if="item.specs && Object.keys(item.specs).length > 0">
                  <text 
                    v-for="(value, key) in item.specs" 
                    :key="key"
                    class="spec-text"
                  >
                    {{ key }}: {{ value }}
                  </text>
                </view>
                <view class="product-bottom">
                  <text class="product-price">${{ item.price }}</text>
                  <text class="product-quantity">x{{ item.quantity }}</text>
                </view>
              </view>
            </view>
          </view>

          <!-- 订单金额 -->
          <view class="order-amount">
            <text class="amount-label">{{ $t('order.totalAmount') }}:</text>
            <text class="amount-value">${{ order.totalAmount }}</text>
          </view>

          <!-- 订单操作 -->
          <view class="order-actions">
            <view class="action-btn secondary" @click.stop="viewOrderDetail(order)">
              {{ $t('order.viewDetails') }}
            </view>
            
            <view 
              v-if="order.status === 'pending'"
              class="action-btn danger" 
              @click.stop="cancelOrder(order)"
            >
              {{ $t('order.cancelOrder') }}
            </view>
            
            <view 
              v-if="order.status === 'pending'"
              class="action-btn primary" 
              @click.stop="payOrder(order)"
            >
              {{ $t('order.payNow') }}
            </view>
            
            <view 
              v-if="order.status === 'shipped'"
              class="action-btn success" 
              @click.stop="confirmReceipt(order)"
            >
              {{ $t('order.confirmReceipt') }}
            </view>
            
            <view 
              v-if="order.status === 'completed'"
              class="action-btn secondary" 
              @click.stop="buyAgain(order)"
            >
              {{ $t('order.buyAgain') }}
            </view>
          </view>
        </view>
      </view>
    </view>

    <!-- 加载更多 -->
    <view class="load-more" v-if="orderList.length > 0">
      <view v-if="loading" class="loading">
        <uni-icons type="spinner-cycle" size="16" color="#409EFF"></uni-icons>
        <text class="loading-text">{{ $t('common.loading') }}</text>
      </view>
      <view v-else-if="hasMore" class="load-more-btn" @click="loadMore">
        {{ $t('common.loadMore') }}
      </view>
      <view v-else class="no-more">
        {{ $t('common.noMore') }}
      </view>
    </view>

    <!-- 订单详情弹窗 -->
    <uni-popup ref="detailPopup" type="bottom">
      <view class="order-detail-modal">
        <view class="modal-header">
          <text class="modal-title">{{ $t('order.orderDetail') }}</text>
          <uni-icons type="close" size="20" @click="closeDetailModal"></uni-icons>
        </view>
        
        <view class="detail-content" v-if="selectedOrder">
          <!-- 订单信息 -->
          <view class="detail-section">
            <text class="section-title">{{ $t('order.orderInfo') }}</text>
            <view class="info-item">
              <text class="info-label">{{ $t('order.orderNo') }}:</text>
              <text class="info-value">{{ selectedOrder.orderNo }}</text>
            </view>
            <view class="info-item">
              <text class="info-label">{{ $t('order.orderTime') }}:</text>
              <text class="info-value">{{ selectedOrder.orderTime }}</text>
            </view>
            <view class="info-item">
              <text class="info-label">{{ $t('order.orderStatus') }}:</text>
              <text class="info-value" :class="getStatusClass(selectedOrder.status)">
                {{ getStatusText(selectedOrder.status) }}
              </text>
            </view>
          </view>

          <!-- 收货地址 -->
          <view class="detail-section">
            <text class="section-title">{{ $t('order.shippingAddress') }}</text>
            <view class="address-info">
              <text class="recipient-name">{{ selectedOrder.address.name }}</text>
              <text class="recipient-phone">{{ selectedOrder.address.phone }}</text>
              <text class="address-detail">{{ selectedOrder.address.fullAddress }}</text>
            </view>
          </view>

          <!-- 商品信息 -->
          <view class="detail-section">
            <text class="section-title">{{ $t('order.productInfo') }}</text>
            <view class="product-list">
              <view 
                v-for="item in selectedOrder.items" 
                :key="item.id"
                class="product-item"
              >
                <image :src="item.image" class="product-image" />
                <view class="product-info">
                  <text class="product-name">{{ item.name }}</text>
                  <view class="product-specs" v-if="item.specs && Object.keys(item.specs).length > 0">
                    <text 
                      v-for="(value, key) in item.specs" 
                      :key="key"
                      class="spec-text"
                    >
                      {{ key }}: {{ value }}
                    </text>
                  </view>
                  <view class="product-bottom">
                    <text class="product-price">${{ item.price }}</text>
                    <text class="product-quantity">x{{ item.quantity }}</text>
                  </view>
                </view>
              </view>
            </view>
          </view>

          <!-- 费用明细 -->
          <view class="detail-section">
            <text class="section-title">{{ $t('order.costBreakdown') }}</text>
            <view class="cost-list">
              <view class="cost-item">
                <text class="cost-label">{{ $t('order.subtotal') }}</text>
                <text class="cost-value">${{ selectedOrder.subtotal }}</text>
              </view>
              <view class="cost-item">
                <text class="cost-label">{{ $t('order.shippingFee') }}</text>
                <text class="cost-value">${{ selectedOrder.shippingFee }}</text>
              </view>
              <view class="cost-item total">
                <text class="cost-label">{{ $t('order.totalAmount') }}</text>
                <text class="cost-value">${{ selectedOrder.totalAmount }}</text>
              </view>
            </view>
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { onLoad } from '@dcloudio/uni-app'

const { t } = useI18n()

const loading = ref(false)
const hasMore = ref(true)
const currentStatus = ref('all')
const orderList = ref<any[]>([])
const selectedOrder = ref<any>(null)

// 订单状态列表
const statusList = ref([
  { label: t('order.all'), value: 'all', count: 0 },
  { label: t('order.pending'), value: 'pending', count: 0 },
  { label: t('order.paid'), value: 'paid', count: 0 },
  { label: t('order.shipped'), value: 'shipped', count: 0 },
  { label: t('order.completed'), value: 'completed', count: 0 }
])

// 模拟订单数据
const mockOrders = [
  {
    id: 1,
    orderNo: 'ORD20250104001',
    orderTime: '2025-01-04 14:30:00',
    status: 'pending',
    totalAmount: '89.99',
    subtotal: '79.99',
    shippingFee: '9.99',
    address: {
      name: 'John Smith',
      phone: '+1 234-567-8900',
      fullAddress: '123 Main Street, New York, NY 10001, United States'
    },
    items: [
      {
        id: 1,
        name: 'Premium Wireless Headphones',
        image: '/static/products/headphones.jpg',
        price: '89.99',
        quantity: 1,
        specs: {
          'Color': 'Black',
          'Size': 'Large'
        }
      }
    ]
  },
  {
    id: 2,
    orderNo: 'ORD20250104002',
    orderTime: '2025-01-04 13:15:00',
    status: 'shipped',
    totalAmount: '219.99',
    subtotal: '199.99',
    shippingFee: '19.99',
    address: {
      name: 'Jane Doe',
      phone: '+1 234-567-8901',
      fullAddress: '456 Oak Avenue, Los Angeles, CA 90210, United States'
    },
    items: [
      {
        id: 2,
        name: 'Smart Watch Series 5',
        image: '/static/products/smartwatch.jpg',
        price: '219.99',
        quantity: 1,
        specs: {
          'Color': 'Silver',
          'Band': 'Sport'
        }
      }
    ]
  },
  {
    id: 3,
    orderNo: 'ORD20250104003',
    orderTime: '2025-01-04 12:00:00',
    status: 'completed',
    totalAmount: '69.99',
    subtotal: '59.99',
    shippingFee: '9.99',
    address: {
      name: 'Bob Johnson',
      phone: '+1 234-567-8902',
      fullAddress: '789 Pine Street, Chicago, IL 60601, United States'
    },
    items: [
      {
        id: 3,
        name: 'Bluetooth Speaker Pro',
        image: '/static/products/speaker.jpg',
        price: '69.99',
        quantity: 1,
        specs: {}
      }
    ]
  }
]

// 页面加载
onLoad((options: any) => {
  if (options.status) {
    currentStatus.value = options.status
  }
  loadOrders()
})

// 加载订单列表
const loadOrders = async () => {
  loading.value = true
  try {
    // 模拟数据
    let filteredOrders = mockOrders
    if (currentStatus.value !== 'all') {
      filteredOrders = mockOrders.filter(order => order.status === currentStatus.value)
    }
    
    orderList.value = filteredOrders
    
    // 更新状态计数
    updateStatusCounts()
    
    // 实际API调用
    // const res = await getOrderList({
    //   status: currentStatus.value,
    //   page: 1,
    //   pageSize: 20
    // })
    // orderList.value = res.list
    // hasMore.value = res.hasMore
    
  } catch (error) {
    console.error('Failed to load orders:', error)
  } finally {
    loading.value = false
  }
}

// 更新状态计数
const updateStatusCounts = () => {
  statusList.value.forEach(status => {
    if (status.value === 'all') {
      status.count = mockOrders.length
    } else {
      status.count = mockOrders.filter(order => order.status === status.value).length
    }
  })
}

// 切换状态
const switchStatus = (status: string) => {
  currentStatus.value = status
  loadOrders()
}

// 加载更多
const loadMore = () => {
  if (loading.value || !hasMore.value) return
  loadOrders()
}

// 跳转到订单详情
const goToOrderDetail = (order: any) => {
  uni.navigateTo({
    url: `/pages/order/detail?id=${order.id}`
  })
}

// 查看订单详情
const viewOrderDetail = (order: any) => {
  selectedOrder.value = order
  uni.$refs.detailPopup?.open()
}

// 关闭详情弹窗
const closeDetailModal = () => {
  uni.$refs.detailPopup?.close()
}

// 取消订单
const cancelOrder = (order: any) => {
  uni.showModal({
    title: t('common.warning'),
    content: t('order.confirmCancel'),
    success: (res) => {
      if (res.confirm) {
        // 实际API调用
        // await cancelOrderAPI(order.id)
        
        order.status = 'cancelled'
        uni.showToast({
          title: t('order.cancelSuccess'),
          icon: 'success'
        })
      }
    }
  })
}

// 支付订单
const payOrder = (order: any) => {
  uni.navigateTo({
    url: `/pages/payment/payment?orderId=${order.id}`
  })
}

// 确认收货
const confirmReceipt = (order: any) => {
  uni.showModal({
    title: t('common.warning'),
    content: t('order.confirmReceipt'),
    success: (res) => {
      if (res.confirm) {
        // 实际API调用
        // await confirmReceiptAPI(order.id)
        
        order.status = 'completed'
        uni.showToast({
          title: t('order.receiptSuccess'),
          icon: 'success'
        })
      }
    }
  })
}

// 再次购买
const buyAgain = (order: any) => {
  const items = order.items.map((item: any) => ({
    productId: item.id,
    name: item.name,
    image: item.image,
    price: item.price,
    quantity: item.quantity,
    specs: item.specs
  }))
  
  uni.navigateTo({
    url: `/pages/order/confirm?items=${encodeURIComponent(JSON.stringify(items))}`
  })
}

// 去购物
const goShopping = () => {
  uni.switchTab({
    url: '/pages/index/index'
  })
}

// 获取状态文本
const getStatusText = (status: string) => {
  switch (status) {
    case 'pending': return t('order.pending')
    case 'paid': return t('order.paid')
    case 'shipped': return t('order.shipped')
    case 'completed': return t('order.completed')
    case 'cancelled': return t('order.cancelled')
    default: return status
  }
}

// 获取状态样式
const getStatusClass = (status: string) => {
  switch (status) {
    case 'pending': return 'status-pending'
    case 'paid': return 'status-paid'
    case 'shipped': return 'status-shipped'
    case 'completed': return 'status-completed'
    case 'cancelled': return 'status-cancelled'
    default: return ''
  }
}

onMounted(() => {
  // 初始化
})
</script>

<style scoped>
.order-list {
  background-color: #f5f5f5;
  min-height: 100vh;
}

/* 状态筛选 */
.status-filter {
  background-color: #fff;
  border-bottom: 1px solid #eee;
}

.filter-scroll {
  white-space: nowrap;
}

.filter-list {
  display: flex;
  padding: 15px 20px;
  gap: 20px;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 20px;
  background-color: #f8f9fa;
  position: relative;
}

.filter-item.active {
  background-color: #e6f7ff;
}

.filter-text {
  font-size: 14px;
  color: #666;
}

.filter-item.active .filter-text {
  color: #409EFF;
}

.filter-badge {
  background-color: #f56c6c;
  color: #fff;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 16px;
  text-align: center;
}

/* 订单内容 */
.order-content {
  padding: 10px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100px 20px;
}

.empty-image {
  width: 120px;
  height: 120px;
  margin-bottom: 20px;
}

.empty-text {
  font-size: 16px;
  color: #999;
  margin-bottom: 30px;
}

.empty-btn {
  background-color: #409EFF;
  color: #fff;
  padding: 12px 30px;
  border-radius: 6px;
  font-size: 16px;
}

/* 订单列表 */
.order-list-content {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.order-item {
  background-color: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* 订单头部 */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.order-info {
  flex: 1;
}

.order-no {
  font-size: 14px;
  color: #333;
  margin-bottom: 4px;
}

.order-time {
  font-size: 12px;
  color: #999;
}

.order-status {
  margin-left: 15px;
}

.status-text {
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 4px;
}

.status-pending {
  background-color: #fff2e8;
  color: #fa8c16;
}

.status-paid {
  background-color: #e6f7ff;
  color: #1890ff;
}

.status-shipped {
  background-color: #f6ffed;
  color: #52c41a;
}

.status-completed {
  background-color: #f6ffed;
  color: #52c41a;
}

.status-cancelled {
  background-color: #fff1f0;
  color: #f5222d;
}

/* 商品列表 */
.order-products {
  margin-bottom: 15px;
}

.product-item {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}

.product-item:last-child {
  margin-bottom: 0;
}

.product-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
}

.product-info {
  flex: 1;
}

.product-name {
  font-size: 14px;
  color: #333;
  margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-specs {
  margin-bottom: 4px;
}

.spec-text {
  font-size: 10px;
  color: #666;
  background-color: #f5f5f5;
  padding: 1px 4px;
  border-radius: 2px;
  margin-right: 4px;
}

.product-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.product-price {
  font-size: 14px;
  font-weight: bold;
  color: #f56c6c;
}

.product-quantity {
  font-size: 12px;
  color: #666;
}

/* 订单金额 */
.order-amount {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-top: 15px;
  border-top: 1px solid #f0f0f0;
}

.amount-label {
  font-size: 14px;
  color: #666;
}

.amount-value {
  font-size: 16px;
  font-weight: bold;
  color: #f56c6c;
}

/* 订单操作 */
.order-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.action-btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 12px;
  text-align: center;
  min-width: 60px;
}

.action-btn.primary {
  background-color: #409EFF;
  color: #fff;
}

.action-btn.secondary {
  background-color: #f8f9fa;
  color: #666;
  border: 1px solid #e9ecef;
}

.action-btn.success {
  background-color: #67C23A;
  color: #fff;
}

.action-btn.danger {
  background-color: #F56C6C;
  color: #fff;
}

/* 加载更多 */
.load-more {
  padding: 20px;
  text-align: center;
}

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.loading-text {
  font-size: 14px;
  color: #666;
}

.load-more-btn {
  font-size: 14px;
  color: #409EFF;
}

.no-more {
  font-size: 14px;
  color: #999;
}

/* 订单详情弹窗 */
.order-detail-modal {
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

.detail-content {
  padding: 20px;
  max-height: 60vh;
  overflow-y: auto;
}

.detail-section {
  margin-bottom: 20px;
}

.section-title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 10px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.info-label {
  font-size: 14px;
  color: #666;
}

.info-value {
  font-size: 14px;
  color: #333;
}

.address-info {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}

.recipient-name {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 4px;
}

.recipient-phone {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.address-detail {
  font-size: 14px;
  color: #666;
  line-height: 1.4;
}

.product-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.cost-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cost-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cost-item.total {
  border-top: 1px solid #eee;
  padding-top: 8px;
  font-weight: bold;
}

.cost-label {
  font-size: 14px;
  color: #666;
}

.cost-value {
  font-size: 14px;
  color: #333;
}

.cost-item.total .cost-value {
  font-size: 16px;
  color: #f56c6c;
}
</style>
