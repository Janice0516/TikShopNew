<template>
  <div class="orders-page">
    <div class="container">
      <div class="orders-header">
        <h1>我的订单</h1>
      </div>
      
      <!-- 订单状态筛选 -->
      <div class="order-filters">
        <el-tabs v-model="activeStatus" @tab-change="handleStatusChange">
          <el-tab-pane label="全部" name="all" />
          <el-tab-pane label="待付款" name="pending" />
          <el-tab-pane label="待发货" name="paid" />
          <el-tab-pane label="待收货" name="shipped" />
          <el-tab-pane label="已完成" name="completed" />
          <el-tab-pane label="已取消" name="cancelled" />
        </el-tabs>
      </div>
      
      <!-- 订单列表 -->
      <div class="orders-list" v-loading="loading">
        <div class="order-item" v-for="order in orders" :key="order.id">
          <div class="order-header">
            <div class="order-info">
              <span class="order-number">订单号: {{ order.orderNumber }}</span>
              <span class="order-time">{{ formatDate(order.createdAt) }}</span>
            </div>
            <div class="order-status">
              <el-tag :type="getStatusType(order.status)">
                {{ getStatusText(order.status) }}
              </el-tag>
            </div>
          </div>
          
          <div class="order-content">
            <div class="order-products">
              <div 
                class="product-item" 
                v-for="item in order.items" 
                :key="item.id"
                @click="goToProduct(item.product.id)"
              >
                <div class="product-image">
                  <img :src="item.product.image" :alt="item.product.name" />
                </div>
                <div class="product-info">
                  <h3 class="product-name">{{ item.product.name }}</h3>
                  <p class="product-spec">{{ item.product.spec }}</p>
                  <div class="product-price">RM{{ item.product.price }}</div>
                </div>
                <div class="product-quantity">
                  <span>×{{ item.quantity }}</span>
                </div>
              </div>
            </div>
            
            <div class="order-summary">
              <div class="summary-info">
                <div class="total-amount">
                  实付款: <span class="amount">RM{{ order.totalAmount }}</span>
                </div>
                <div class="item-count">共{{ getTotalItems(order.items) }}件商品</div>
              </div>
              
              <div class="order-actions">
                <el-button 
                  v-if="order.status === 'pending'"
                  @click="payOrder(order)"
                  type="primary"
                  size="small"
                >
                  立即付款
                </el-button>
                <el-button 
                  v-if="order.status === 'shipped'"
                  @click="confirmReceipt(order)"
                  type="success"
                  size="small"
                >
                  确认收货
                </el-button>
                <el-button 
                  v-if="['pending', 'paid'].includes(order.status)"
                  @click="cancelOrder(order)"
                  size="small"
                >
                  取消订单
                </el-button>
                <el-button 
                  @click="goToOrderDetail(order)"
                  size="small"
                >
                  查看详情
                </el-button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 分页 -->
        <div class="pagination" v-if="totalPages > 1">
          <el-pagination
            v-model:current-page="currentPage"
            :page-size="pageSize"
            :total="totalOrders"
            layout="prev, pager, next, jumper"
            @current-change="handlePageChange"
          />
        </div>
      </div>
      
      <!-- 空状态 -->
      <div class="empty-orders" v-if="!loading && orders.length === 0">
        <div class="empty-content">
          <el-icon size="64" color="#ccc"><Document /></el-icon>
          <h3>暂无订单</h3>
          <p>您还没有任何订单记录</p>
          <el-button type="primary" @click="$router.push('/')">去购物</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { orderApi } from '@/api'
import { Document } from '@element-plus/icons-vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const router = useRouter()

const orders = ref<any[]>([])
const loading = ref(false)
const activeStatus = ref('all')
const currentPage = ref(1)
const pageSize = ref(10)
const totalOrders = ref(0)
const totalPages = ref(0)

// 订单状态映射
const statusMap = {
  pending: '待付款',
  paid: '待发货',
  shipped: '待收货',
  completed: '已完成',
  cancelled: '已取消'
}

// 获取状态类型
const getStatusType = (status: string) => {
  const typeMap = {
    pending: 'warning',
    paid: 'info',
    shipped: 'primary',
    completed: 'success',
    cancelled: 'danger'
  }
  return typeMap[status] || 'info'
}

// 获取状态文本
const getStatusText = (status: string) => {
  return statusMap[status] || status
}

// 格式化日期
const formatDate = (date: string) => {
  return new Date(date).toLocaleString('zh-CN')
}

// 获取商品总数
const getTotalItems = (items: any[]) => {
  return items.reduce((total, item) => total + item.quantity, 0)
}

// 加载订单列表
const loadOrders = async () => {
  try {
    loading.value = true
    
    const params = {
      page: currentPage.value,
      limit: pageSize.value,
      status: activeStatus.value === 'all' ? undefined : activeStatus.value
    }
    
    const response = await orderApi.getOrders(params)
    orders.value = response.orders || []
    totalOrders.value = response.total || 0
    totalPages.value = Math.ceil(totalOrders.value / pageSize.value)
  } catch (error) {
    console.error('加载订单失败:', error)
    ElMessage.error('加载订单失败')
    
    // 使用默认订单数据
    orders.value = [
      {
        id: '1',
        orderNumber: 'ORD202401010001',
        status: 'pending',
        totalAmount: 199.99,
        createdAt: '2024-01-01T10:00:00Z',
        items: [
          {
            id: '1',
            product: {
              id: '1',
              name: '商品名称',
              image: 'https://via.placeholder.com/80x80/409EFF/ffffff?text=商品',
              price: 99.99,
              spec: '规格: 默认'
            },
            quantity: 2
          }
        ]
      },
      {
        id: '2',
        orderNumber: 'ORD202401010002',
        status: 'completed',
        totalAmount: 299.99,
        createdAt: '2024-01-01T11:00:00Z',
        items: [
          {
            id: '2',
            product: {
              id: '2',
              name: '另一个商品',
              image: 'https://via.placeholder.com/80x80/67C23A/ffffff?text=商品2',
              price: 299.99,
              spec: '规格: 大号'
            },
            quantity: 1
          }
        ]
      }
    ]
    totalOrders.value = orders.value.length
    totalPages.value = 1
  } finally {
    loading.value = false
  }
}

// 处理状态变化
const handleStatusChange = (status: string) => {
  currentPage.value = 1
  loadOrders()
}

// 处理分页变化
const handlePageChange = (page: number) => {
  currentPage.value = page
  loadOrders()
}

// 支付订单
const payOrder = async (order: any) => {
  try {
    await ElMessageBox.confirm('确定要支付这个订单吗？', '确认支付', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'info'
    })
    
    // 模拟支付成功
    ElMessage.success('支付成功')
    order.status = 'paid'
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('支付失败，请重试')
    }
  }
}

// 确认收货
const confirmReceipt = async (order: any) => {
  try {
    await ElMessageBox.confirm('确定已收到商品吗？', '确认收货', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'info'
    })
    
    // 模拟确认收货
    ElMessage.success('确认收货成功')
    order.status = 'completed'
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('确认收货失败，请重试')
    }
  }
}

// 取消订单
const cancelOrder = async (order: any) => {
  try {
    await ElMessageBox.confirm('确定要取消这个订单吗？', '取消订单', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    // 模拟取消订单
    ElMessage.success('订单已取消')
    order.status = 'cancelled'
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('取消订单失败，请重试')
    }
  }
}

// 跳转到商品详情
const goToProduct = (productId: string) => {
  router.push(`/product/${productId}`)
}

// 跳转到订单详情
const goToOrderDetail = (order: any) => {
  router.push(`/orders/${order.id}`)
}

onMounted(() => {
  loadOrders()
})
</script>

<style scoped lang="scss">
.orders-page {
  padding: 20px 0;
  background: $background-base;
  min-height: 100vh;
}

.orders-header {
  background: #fff;
  padding: 20px 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  
  h1 {
    font-size: 24px;
    font-weight: bold;
    color: $text-primary;
    margin: 0;
  }
}

.order-filters {
  background: #fff;
  border-radius: 8px;
  margin-bottom: 20px;
  
  :deep(.el-tabs__header) {
    margin: 0;
    padding: 0 20px;
  }
}

.orders-list {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
}

.order-item {
  border: 1px solid $border-lighter;
  border-radius: 8px;
  margin-bottom: 20px;
  overflow: hidden;
  
  &:last-child {
    margin-bottom: 0;
  }
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: $background-base;
  border-bottom: 1px solid $border-lighter;
  
  .order-info {
    display: flex;
    gap: 20px;
    
    .order-number {
      font-weight: 500;
      color: $text-primary;
    }
    
    .order-time {
      color: $text-secondary;
    }
  }
}

.order-content {
  padding: 20px;
}

.order-products {
  margin-bottom: 20px;
  
  .product-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid $border-lighter;
    cursor: pointer;
    
    &:last-child {
      border-bottom: none;
    }
    
    &:hover {
      background: $background-base;
    }
  }
  
  .product-image {
    width: 80px;
    height: 80px;
    margin-right: 15px;
    border-radius: 8px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  
  .product-info {
    flex: 1;
    
    .product-name {
      font-size: 16px;
      font-weight: 500;
      color: $text-primary;
      margin: 0 0 5px 0;
    }
    
    .product-spec {
      font-size: 14px;
      color: $text-secondary;
      margin: 0 0 5px 0;
    }
    
    .product-price {
      font-size: 16px;
      font-weight: bold;
      color: $danger-color;
    }
  }
  
  .product-quantity {
    color: $text-secondary;
  }
}

.order-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  
  .summary-info {
    .total-amount {
      font-size: 16px;
      color: $text-primary;
      margin-bottom: 5px;
      
      .amount {
        font-size: 18px;
        font-weight: bold;
        color: $danger-color;
      }
    }
    
    .item-count {
      font-size: 14px;
      color: $text-secondary;
    }
  }
  
  .order-actions {
    display: flex;
    gap: 10px;
  }
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 30px;
}

.empty-orders {
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
  .order-header {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
  
  .order-summary {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .order-actions {
    width: 100%;
    justify-content: flex-end;
  }
  
  .product-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .product-image {
    width: 60px;
    height: 60px;
    margin-right: 0;
  }
}
</style>
