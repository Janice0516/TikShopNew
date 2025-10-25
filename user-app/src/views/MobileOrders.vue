<template>
  <div class="mobile-orders">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="header-title">{{ t('navigation.orders') }}</h1>
      <button class="filter-btn" @click="showFilterModal = true" v-if="orders.length > 0">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <!-- Status Tabs -->
    <div class="status-tabs" v-if="orders.length > 0">
      <div class="tabs-container">
        <button 
          v-for="status in statusOptions" 
          :key="status.value"
          class="tab-btn"
          :class="{ 'active': activeStatus === status.value }"
          @click="changeStatus(status.value)"
        >
          {{ status.label }}
          <span v-if="status.count > 0" class="tab-count">{{ status.count }}</span>
        </button>
      </div>
    </div>

    <!-- Orders Content -->
    <div class="orders-content">
      <!-- Loading State -->
      <MobileLoading 
        :loading="isLoading"
        type="skeleton"
        skeleton-type="list"
        :skeleton-count="3"
        message="加载订单中..."
      />
      
      <!-- Error State -->
      <MobileError 
        :show="hasError"
        type="server"
        title="加载失败"
        message="无法加载订单信息，请重试"
        @retry="handleRetry"
      />

      <!-- Empty State -->
      <div v-if="!isLoading && !hasError && filteredOrders.length === 0" class="empty-orders">
        <div class="empty-icon">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
            <path d="M16 11V7A4 4 0 0 0 8 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3 class="empty-title">{{ getEmptyTitle() }}</h3>
        <p class="empty-description">{{ getEmptyDescription() }}</p>
        <button class="shop-btn" @click="goShopping">
          {{ t('orders.startShopping') }}
        </button>
      </div>

      <!-- Orders List -->
      <div v-else-if="!isLoading && !hasError" class="orders-list">
        <div 
          v-for="order in filteredOrders" 
          :key="order.id" 
          class="order-item"
          @click="goToOrderDetail(order)"
        >
          <!-- Order Header -->
          <div class="order-header">
            <div class="order-info">
              <div class="order-number">{{ t('orders.orderNumber') }}: {{ order.orderNumber }}</div>
              <div class="order-time">{{ formatDate(order.createdAt) }}</div>
            </div>
            <div class="order-status">
              <span class="status-badge" :class="getStatusClass(order.status)">
                {{ getStatusText(order.status) }}
              </span>
            </div>
          </div>

          <!-- Order Items -->
          <div class="order-items">
            <div 
              v-for="(item, index) in order.items.slice(0, 3)" 
              :key="index"
              class="order-item-product"
            >
              <div class="product-image">
                <img :src="item.product.image" :alt="item.product.name" />
              </div>
              <div class="product-info">
                <h4 class="product-name">{{ item.product.name }}</h4>
                <div class="product-specs">
                  <span class="product-quantity">{{ t('orders.quantity') }}: {{ item.quantity }}</span>
                  <span class="product-price">RM{{ formatPrice(item.product.price) }}</span>
                </div>
              </div>
            </div>
            
            <!-- More Items Indicator -->
            <div v-if="order.items.length > 3" class="more-items">
              <span>{{ t('orders.moreItems', { count: order.items.length - 3 }) }}</span>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="order-summary">
            <div class="summary-info">
              <div class="total-items">{{ t('orders.totalItems', { count: order.items.length }) }}</div>
              <div class="total-amount">{{ t('common.total') }}: <span class="amount">RM{{ formatPrice(order.totalAmount) }}</span></div>
            </div>
            <div class="order-actions">
              <button 
                v-if="order.status === 'pending'"
                class="action-btn cancel-btn"
                @click.stop="cancelOrder(order)"
              >
                {{ t('orders.cancel') }}
              </button>
              <button 
                v-if="order.status === 'shipped'"
                class="action-btn confirm-btn"
                @click.stop="confirmReceipt(order)"
              >
                {{ t('orders.confirmReceipt') }}
              </button>
              <button 
                v-if="order.status === 'completed'"
                class="action-btn review-btn"
                @click.stop="writeReview(order)"
              >
                {{ t('orders.writeReview') }}
              </button>
              <button 
                v-if="order.status === 'completed'"
                class="action-btn reorder-btn"
                @click.stop="reorder(order)"
              >
                {{ t('orders.reorder') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Modal -->
    <div v-if="showFilterModal" class="filter-modal" @click="showFilterModal = false">
      <div class="filter-content" @click.stop>
        <div class="filter-header">
          <h3>{{ t('orders.filterBy') }}</h3>
          <button class="close-btn" @click="showFilterModal = false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        
        <div class="filter-options">
          <div 
            v-for="status in statusOptions" 
            :key="status.value"
            class="filter-option"
            @click="changeStatus(status.value); showFilterModal = false"
          >
            <div class="option-content">
              <span class="option-label">{{ status.label }}</span>
              <span v-if="status.count > 0" class="option-count">{{ status.count }}</span>
            </div>
            <div class="option-check" v-if="activeStatus === status.value">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileLoading from '@/components/common/MobileLoading.vue'
import MobileError from '@/components/common/MobileError.vue'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

// 状态管理
const loading = ref(false)
const isLoading = ref(false)
const hasError = ref(false)
const showFilterModal = ref(false)
const activeStatus = ref('all')

// 模拟订单数据
const orders = ref([
  {
    id: 1,
    orderNumber: 'ORD202401001',
    status: 'pending',
    createdAt: '2024-01-15T10:30:00Z',
    totalAmount: 299.99,
    items: [
      {
        product: {
          id: 1,
          name: 'iPhone 15 Pro',
          image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=200&h=200&fit=crop',
          price: 299.99
        },
        quantity: 1
      }
    ]
  },
  {
    id: 2,
    orderNumber: 'ORD202401002',
    status: 'shipped',
    createdAt: '2024-01-14T15:20:00Z',
    totalAmount: 89.50,
    items: [
      {
        product: {
          id: 2,
          name: 'AirPods Pro',
          image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=200&h=200&fit=crop',
          price: 89.50
        },
        quantity: 1
      }
    ]
  },
  {
    id: 3,
    orderNumber: 'ORD202401003',
    status: 'completed',
    createdAt: '2024-01-10T09:15:00Z',
    totalAmount: 45.00,
    items: [
      {
        product: {
          id: 3,
          name: 'Wireless Charger',
          image: 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=200&h=200&fit=crop',
          price: 45.00
        },
        quantity: 1
      }
    ]
  }
])

// 状态选项
const statusOptions = computed(() => [
  { value: 'all', label: t('orders.all'), count: orders.value.length },
  { value: 'pending', label: t('orders.pending'), count: orders.value.filter(o => o.status === 'pending').length },
  { value: 'shipped', label: t('orders.shipped'), count: orders.value.filter(o => o.status === 'shipped').length },
  { value: 'completed', label: t('orders.completed'), count: orders.value.filter(o => o.status === 'completed').length },
  { value: 'cancelled', label: t('orders.cancelled'), count: orders.value.filter(o => o.status === 'cancelled').length }
])

// 过滤后的订单
const filteredOrders = computed(() => {
  if (activeStatus.value === 'all') {
    return orders.value
  }
  return orders.value.filter(order => order.status === activeStatus.value)
})

// 方法
const goBack = () => {
  router.back()
}

const changeStatus = (status: string) => {
  activeStatus.value = status
}

const goToOrderDetail = (order: any) => {
  router.push(`/mobile/order-detail/${order.id}`)
}

const cancelOrder = async (order: any) => {
  try {
    await ElMessageBox.confirm(
      t('orders.confirmCancel', { orderNumber: order.orderNumber }),
      t('common.confirm'),
      {
        confirmButtonText: t('orders.cancel'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    order.status = 'cancelled'
    ElMessage.success(t('orders.cancelSuccess'))
  } catch {
    // 用户取消操作
  }
}

const confirmReceipt = async (order: any) => {
  try {
    await ElMessageBox.confirm(
      t('orders.confirmReceipt', { orderNumber: order.orderNumber }),
      t('common.confirm'),
      {
        confirmButtonText: t('orders.confirmReceipt'),
        cancelButtonText: t('common.cancel'),
        type: 'info'
      }
    )
    
    order.status = 'completed'
    ElMessage.success(t('orders.receiptConfirmed'))
  } catch {
    // 用户取消操作
  }
}

const writeReview = (order: any) => {
  router.push(`/mobile/order/review/${order.id}`)
}

const reorder = (order: any) => {
  ElMessage.info('重新下单功能开发中...')
}

const goShopping = () => {
  router.push('/mobile')
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('zh-CN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const getStatusText = (status: string) => {
  const statusMap: Record<string, string> = {
    pending: t('orders.pending'),
    shipped: t('orders.shipped'),
    completed: t('orders.completed'),
    cancelled: t('orders.cancelled')
  }
  return statusMap[status] || status
}

const getStatusClass = (status: string) => {
  const classMap: Record<string, string> = {
    pending: 'status-pending',
    shipped: 'status-shipped',
    completed: 'status-completed',
    cancelled: 'status-cancelled'
  }
  return classMap[status] || ''
}

const getEmptyTitle = () => {
  if (activeStatus.value === 'all') {
    return t('orders.noOrders')
  }
  return t('orders.noOrdersInStatus', { status: getStatusText(activeStatus.value) })
}

const getEmptyDescription = () => {
  if (activeStatus.value === 'all') {
    return t('orders.noOrdersDescription')
  }
  return t('orders.noOrdersInStatusDescription')
}

// 加载订单数据
const loadOrders = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    
    // 获取用户订单数据
    const response = await orderApi.getUserOrders()
    
    // 更新订单数据
    orders.value = response.data || []
    
    console.log('订单数据加载完成:', orders.value.length)
  } catch (error: any) {
    console.error('加载订单失败:', error)
    hasError.value = true
    ElMessage.error('加载订单失败')
  } finally {
    isLoading.value = false
  }
}

// 重试加载
const handleRetry = () => {
  hasError.value = false
  loadOrders()
}

onMounted(() => {
  // 根据路由参数设置状态筛选
  if (route.query.status) {
    activeStatus.value = route.query.status as string
  }
  
  loadOrders()
})

// 从URL参数获取初始状态
onMounted(() => {
  const statusParam = route.query.status as string
  if (statusParam && statusOptions.value.some(option => option.value === statusParam)) {
    activeStatus.value = statusParam
  }
})
</script>

<style scoped lang="scss">
.mobile-orders {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 80px; // 为底部导航栏留出空间
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

  .filter-btn {
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
}

.status-tabs {
  background: #000;
  border-bottom: 1px solid #333;
  padding: 0 20px;
  position: sticky;
  top: 60px;
  z-index: 99;

  .tabs-container {
    display: flex;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;

    &::-webkit-scrollbar {
      display: none;
    }

    .tab-btn {
      background: none;
      border: none;
      color: #ccc;
      padding: 12px 16px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      white-space: nowrap;
      display: flex;
      align-items: center;
      gap: 6px;
      border-bottom: 2px solid transparent;

      &.active {
        color: #ff0050;
        border-bottom-color: #ff0050;
      }

      &:hover {
        color: #fff;
      }

      .tab-count {
        background: #ff0050;
        color: #fff;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
      }
    }
  }
}

.orders-content {
  padding: 20px;
}

.loading-container {
  text-align: center;
  padding: 60px 20px;

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #333;
    border-top: 3px solid #ff0050;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
  }

  p {
    color: #ccc;
    margin: 0;
  }
}

.empty-orders {
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

.orders-list {
  .order-item {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #222;
      transform: translateY(-2px);
    }

    &:active {
      transform: translateY(0);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;

      .order-info {
        .order-number {
          font-size: 14px;
          font-weight: 500;
          color: #fff;
          margin-bottom: 4px;
        }

        .order-time {
          font-size: 12px;
          color: #ccc;
        }
      }

      .order-status {
        .status-badge {
          padding: 4px 8px;
          border-radius: 6px;
          font-size: 12px;
          font-weight: 500;

          &.status-pending {
            background: #fff3cd;
            color: #856404;
          }

          &.status-shipped {
            background: #d1ecf1;
            color: #0c5460;
          }

          &.status-completed {
            background: #d4edda;
            color: #155724;
          }

          &.status-cancelled {
            background: #f8d7da;
            color: #721c24;
          }
        }
      }
    }

    .order-items {
      margin-bottom: 12px;

      .order-item-product {
        display: flex;
        align-items: center;
        margin-bottom: 8px;

        &:last-child {
          margin-bottom: 0;
        }

        .product-image {
          width: 50px;
          height: 50px;
          border-radius: 6px;
          overflow: hidden;
          margin-right: 12px;

          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
        }

        .product-info {
          flex: 1;

          .product-name {
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            margin: 0 0 4px 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
          }

          .product-specs {
            display: flex;
            justify-content: space-between;
            align-items: center;

            .product-quantity {
              font-size: 12px;
              color: #ccc;
            }

            .product-price {
              font-size: 14px;
              font-weight: 600;
              color: #ff0050;
            }
          }
        }
      }

      .more-items {
        text-align: center;
        padding: 8px;
        font-size: 12px;
        color: #666;
        border-top: 1px solid #333;
        margin-top: 8px;
      }
    }

    .order-summary {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 12px;
      border-top: 1px solid #333;

      .summary-info {
        .total-items {
          font-size: 12px;
          color: #ccc;
          margin-bottom: 2px;
        }

        .total-amount {
          font-size: 16px;
          font-weight: 600;
          color: #fff;

          .amount {
            color: #ff0050;
          }
        }
      }

      .order-actions {
        display: flex;
        gap: 8px;

        .action-btn {
          padding: 6px 12px;
          border-radius: 6px;
          font-size: 12px;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s;
          border: none;

          &.cancel-btn {
            background: #dc3545;
            color: #fff;

            &:hover {
              background: #c82333;
            }
          }

          &.confirm-btn {
            background: #28a745;
            color: #fff;

            &:hover {
              background: #218838;
            }
          }

          &.review-btn {
            background: #ffc107;
            color: #000;

            &:hover {
              background: #e0a800;
            }
          }

          &.reorder-btn {
            background: #ff0050;
            color: #fff;

            &:hover {
              background: #e6004a;
            }
          }
        }
      }
    }
  }
}

.filter-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  z-index: 1000;
  display: flex;
  align-items: flex-end;

  .filter-content {
    background: #1a1a1a;
    border-radius: 16px 16px 0 0;
    width: 100%;
    max-height: 60vh;
    overflow: hidden;

    .filter-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 1px solid #333;

      h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: #fff;
      }

      .close-btn {
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
    }

    .filter-options {
      padding: 0 20px 20px;

      .filter-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        cursor: pointer;
        border-bottom: 1px solid #222;

        &:last-child {
          border-bottom: none;
        }

        &:hover {
          background: #222;
          margin: 0 -20px;
          padding: 16px 20px;
        }

        .option-content {
          display: flex;
          align-items: center;
          gap: 8px;

          .option-label {
            font-size: 16px;
            color: #fff;
          }

          .option-count {
            background: #ff0050;
            color: #fff;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
          }
        }

        .option-check {
          color: #ff0050;
        }
      }
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// 响应式设计
@media (max-width: 480px) {
  .orders-content {
    padding: 16px;
  }

  .orders-list .order-item {
    padding: 14px;

    .order-items .order-item-product {
      .product-image {
        width: 45px;
        height: 45px;
      }

      .product-info .product-name {
        font-size: 13px;
      }
    }

    .order-summary {
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;

      .order-actions {
        width: 100%;
        justify-content: flex-end;
      }
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
