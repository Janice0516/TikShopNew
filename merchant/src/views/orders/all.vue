<template>
  <div class="all-orders">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('nav.allOrders') }}</span>
          <el-button @click="handleRefresh" :loading="loading">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.refresh') }}
          </el-button>
        </div>
      </template>

      <!-- 订单统计 -->
      <el-row :gutter="20" class="stats-row">
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value">{{ stats.total }}</div>
            <div class="stat-label">{{ $t('orders.totalOrders') }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value" style="color: #E6A23C">{{ stats.pending }}</div>
            <div class="stat-label">{{ $t('orders.pendingOrders') }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value" style="color: #67C23A">{{ stats.shipped }}</div>
            <div class="stat-label">{{ $t('orders.shippedOrders') }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value" style="color: #409EFF">{{ stats.completed }}</div>
            <div class="stat-label">{{ $t('orders.completedOrders') }}</div>
          </div>
        </el-col>
      </el-row>

      <!-- 筛选条件 -->
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item :label="$t('orders.orderStatus')">
          <el-select
            v-model="searchForm.status"
            :placeholder="$t('common.all')"
            clearable
            style="width: 150px"
            @change="handleSearch"
          >
            <el-option :label="$t('common.all')" :value="null" />
            <el-option :label="$t('orders.pending')" :value="'pending'" />
            <el-option :label="$t('orders.shipped')" :value="'shipped'" />
            <el-option :label="$t('orders.completed')" :value="'completed'" />
            <el-option :label="$t('orders.cancelled')" :value="'cancelled'" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('orders.orderNo')">
          <el-input
            v-model="searchForm.orderNo"
            :placeholder="$t('orders.orderNo')"
            clearable
            style="width: 200px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>

        <el-form-item :label="$t('orders.customerName')">
          <el-input
            v-model="searchForm.customerName"
            :placeholder="$t('orders.customerName')"
            clearable
            style="width: 150px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>

        <el-form-item :label="$t('orders.orderTime')">
          <el-date-picker
            v-model="searchForm.dateRange"
            type="daterange"
            :range-separator="$t('common.to')"
            :start-placeholder="$t('common.startDate')"
            :end-placeholder="$t('common.endDate')"
            style="width: 240px"
            @change="handleSearch"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSearch">
            {{ $t('common.search') }}
          </el-button>
          <el-button @click="handleReset">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>

      <!-- 订单列表 -->
      <el-table
        :data="orderList"
        style="width: 100%"
        v-loading="loading"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        
        <el-table-column prop="orderNo" :label="$t('orders.orderNo')" width="200" />
        
        <el-table-column :label="$t('orders.customerInfo')" width="150">
          <template #default="{ row }">
            <div>{{ row.customerName }}</div>
            <div style="font-size: 12px; color: #999;">{{ row.customerPhone }}</div>
          </template>
        </el-table-column>

        <el-table-column :label="$t('orders.productInfo')" min-width="250">
          <template #default="{ row }">
            <div v-for="item in row.items" :key="item.id" class="order-item">
              <el-image
                :src="item.image || '/placeholder.jpg'"
                style="width: 40px; height: 40px; margin-right: 10px"
                fit="cover"
              />
              <div>
                <div>{{ item.productName }}</div>
                <div style="font-size: 12px; color: #999;">x{{ item.quantity }}</div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column :label="$t('orders.totalAmount')" width="120">
          <template #default="{ row }">
            <span style="color: #409EFF; font-weight: 500">${{ row.totalAmount }}</span>
          </template>
        </el-table-column>

        <el-table-column :label="$t('orders.yourProfit')" width="120">
          <template #default="{ row }">
            <el-tag type="success">
              ${{ row.profit }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('orders.orderStatus')" width="120">
          <template #default="{ row }">
            <el-tag :type="getStatusTagType(row.status)">
              {{ getStatusName(row.status) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('orders.orderTime')" width="180">
          <template #default="{ row }">
            {{ row.orderTime }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('common.actions')" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              v-if="row.status === 'pending'"
              type="primary"
              size="small"
              @click="handleShip(row)"
            >
              {{ $t('orders.shipOrder') }}
            </el-button>
            <el-button
              v-if="row.status === 'shipped'"
              type="success"
              size="small"
              @click="handleComplete(row)"
            >
              {{ $t('orders.completeOrder') }}
            </el-button>
            <el-button
              type="info"
              size="small"
              @click="handleViewDetail(row)"
            >
              {{ $t('orders.viewDetail') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 批量操作 -->
      <div class="batch-actions" v-if="selectedOrders.length > 0">
        <el-button type="primary" @click="batchShip">
          {{ $t('orders.batchShip') }} ({{ selectedOrders.length }})
        </el-button>
        <el-button type="success" @click="batchComplete">
          {{ $t('orders.batchComplete') }} ({{ selectedOrders.length }})
        </el-button>
      </div>

      <!-- 分页 -->
      <el-pagination
        v-model:current-page="pagination.page"
        v-model:page-size="pagination.pageSize"
        :total="pagination.total"
        :page-sizes="[10, 20, 50, 100]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSearch"
        @current-change="handleSearch"
        style="margin-top: 20px; justify-content: flex-end"
      />
    </el-card>

    <!-- 发货对话框 -->
    <el-dialog
      v-model="shipDialogVisible"
      :title="$t('orders.shipOrder')"
      width="500px"
    >
      <div class="ship-confirm-content">
        <div class="order-info">
          <h3>{{ $t('orders.orderInfo') }}</h3>
          <div class="info-item">
            <span class="label">{{ $t('orders.orderNo') }}:</span>
            <span class="value">{{ selectedOrder?.orderNo }}</span>
          </div>
          <div class="info-item">
            <span class="label">{{ $t('orders.customerName') }}:</span>
            <span class="value">{{ selectedOrder?.customerName }}</span>
          </div>
          <div class="info-item">
            <span class="label">{{ $t('orders.totalAmount') }}:</span>
            <span class="value">${{ selectedOrder?.totalAmount }}</span>
          </div>
        </div>

        <div class="platform-notice">
          <el-alert
            :title="$t('orders.platformShippingNotice')"
            type="info"
            :closable="false"
            show-icon
          >
            <template #default>
              <p>{{ $t('orders.platformShippingDescription') }}</p>
            </template>
          </el-alert>
        </div>

        <el-form
          ref="shipFormRef"
          :model="shipForm"
          label-width="120px"
        >
          <el-form-item :label="$t('orders.shippingNote')">
            <el-input
              v-model="shipForm.note"
              type="textarea"
              :rows="3"
              :placeholder="$t('orders.shippingNotePlaceholder')"
            />
          </el-form-item>
        </el-form>
      </div>

      <template #footer>
        <el-button @click="shipDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="handleConfirmShip" :loading="submitting">
          {{ $t('orders.confirmShipToPlatform') }}
        </el-button>
      </template>
    </el-dialog>

    <!-- 订单详情对话框 -->
    <el-dialog
      v-model="detailDialogVisible"
      :title="$t('orders.orderDetail')"
      width="800px"
    >
      <div v-if="selectedOrder" class="order-detail">
        <!-- 订单基本信息 -->
        <el-descriptions :column="2" border>
          <el-descriptions-item :label="$t('orders.orderNo')">
            {{ selectedOrder.orderNo }}
          </el-descriptions-item>
          <el-descriptions-item :label="$t('orders.orderTime')">
            {{ selectedOrder.orderTime }}
          </el-descriptions-item>
          <el-descriptions-item :label="$t('orders.orderStatus')">
            <el-tag :type="getStatusTagType(selectedOrder.status)">
              {{ getStatusName(selectedOrder.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item :label="$t('orders.paymentStatus')">
            <el-tag type="success">{{ $t('orders.paid') }}</el-tag>
          </el-descriptions-item>
        </el-descriptions>

        <!-- 收货地址 -->
        <el-card class="detail-card">
          <template #header>
            <span>{{ $t('orders.shippingAddress') }}</span>
          </template>
          <div class="address-info">
            <div class="recipient-name">{{ selectedOrder.customerName }}</div>
            <div class="recipient-phone">{{ selectedOrder.customerPhone }}</div>
            <div class="address-detail">{{ selectedOrder.shippingAddress }}</div>
          </div>
        </el-card>

        <!-- 商品信息 -->
        <el-card class="detail-card">
          <template #header>
            <span>{{ $t('orders.productInfo') }}</span>
          </template>
          <el-table :data="selectedOrder.items" style="width: 100%">
            <el-table-column :label="$t('orders.productName')" min-width="200">
              <template #default="{ row }">
                <div class="product-info">
                  <el-image
                    :src="row.image || '/placeholder.jpg'"
                    style="width: 60px; height: 60px; margin-right: 10px"
                    fit="cover"
                  />
                  <div>
                    <div>{{ row.productName }}</div>
                    <div style="font-size: 12px; color: #999;">{{ row.specs }}</div>
                  </div>
                </div>
              </template>
            </el-table-column>
            <el-table-column :label="$t('orders.quantity')" width="100">
              <template #default="{ row }">
                {{ row.quantity }}
              </template>
            </el-table-column>
            <el-table-column :label="$t('orders.unitPrice')" width="120">
              <template #default="{ row }">
                ${{ row.salePrice }}
              </template>
            </el-table-column>
            <el-table-column :label="$t('orders.subtotal')" width="120">
              <template #default="{ row }">
                ${{ row.totalPrice }}
              </template>
            </el-table-column>
          </el-table>
        </el-card>

        <!-- 费用明细 -->
        <el-card class="detail-card">
          <template #header>
            <span>{{ $t('orders.costBreakdown') }}</span>
          </template>
          <el-descriptions :column="1" border>
            <el-descriptions-item :label="$t('orders.orderAmount')">
              ${{ selectedOrder.totalAmount }}
            </el-descriptions-item>
            <el-descriptions-item :label="$t('orders.shippingFee')">
              ${{ selectedOrder.freight }}
            </el-descriptions-item>
            <el-descriptions-item :label="$t('orders.totalPaid')">
              <span style="color: #f56c6c; font-weight: bold;">${{ selectedOrder.payAmount }}</span>
            </el-descriptions-item>
            <el-descriptions-item :label="$t('orders.yourProfit')">
              <span style="color: #67c23a; font-weight: bold;">${{ selectedOrder.profit }}</span>
            </el-descriptions-item>
          </el-descriptions>
        </el-card>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getMerchantOrders } from '@/api/order'

const { t } = useI18n()

const loading = ref(false)
const submitting = ref(false)
const shipDialogVisible = ref(false)
const detailDialogVisible = ref(false)
const shipFormRef = ref()

const selectedOrder = ref<any>(null)
const selectedOrders = ref<any[]>([])

const stats = ref({
  total: 0,
  pending: 0,
  shipped: 0,
  completed: 0
})

const orderList = ref<any[]>([])

const searchForm = reactive({
  status: null as string | null,
  orderNo: '',
  customerName: '',
  dateRange: null as any
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

const shipForm = reactive({
  note: ''
})

// 模拟订单数据 - 已移除，只使用真实API数据

// 获取状态标签样式
const getStatusTagType = (status: string) => {
  switch (status) {
    case 'pending': return 'warning'
    case 'shipped': return 'success'
    case 'completed': return 'info'
    case 'cancelled': return 'danger'
    default: return 'info'
  }
}

// 获取状态名称
const getStatusName = (status: string) => {
  switch (status) {
    case 'pending': return t('orders.pending')
    case 'shipped': return t('orders.shipped')
    case 'completed': return t('orders.completed')
    case 'cancelled': return t('orders.cancelled')
    default: return status
  }
}

// 刷新
const handleRefresh = () => {
  loadStats()
  handleSearch()
}

// 加载统计数据
const loadStats = async () => {
  try {
    // 实际API调用
    const res = await getMerchantOrders({ page: 1, pageSize: 1000 })
    if (res.data && res.data.data) {
      const orders = res.data.data.list || []
      stats.value = {
        total: orders.length,
        pending: orders.filter((order: any) => order.status === 'pending').length,
        shipped: orders.filter((order: any) => order.status === 'shipped').length,
        completed: orders.filter((order: any) => order.status === 'completed').length
      }
    }
  } catch (error) {
    console.error('Failed to load stats:', error)
    stats.value = {
      total: 0,
      pending: 0,
      shipped: 0,
      completed: 0
    }
  }
}

// 搜索
const handleSearch = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      pageSize: pagination.pageSize,
      status: searchForm.status,
      orderNo: searchForm.orderNo,
      customerName: searchForm.customerName
    }
    
    const res = await getMerchantOrders(params)
    
    if (res.data && res.data.data) {
      orderList.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      orderList.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('Failed to fetch orders:', error)
    ElMessage.error(t('message.loadFailed'))
    orderList.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  searchForm.status = null
  searchForm.orderNo = ''
  searchForm.customerName = ''
  searchForm.dateRange = null
  pagination.page = 1
  handleSearch()
}

// 选择变化
const handleSelectionChange = (selection: any[]) => {
  selectedOrders.value = selection
}

// 发货
const handleShip = (order: any) => {
  selectedOrder.value = order
  shipForm.note = ''
  shipDialogVisible.value = true
}

// 确认发货
const handleConfirmShip = async () => {
  submitting.value = true
  try {
    // 实际API调用 - 平台统一配送
    // await shipOrderToPlatform(selectedOrder.value.id, { note: shipForm.note })
    
    selectedOrder.value.status = 'shipped'
    ElMessage.success(t('orders.shipToPlatformSuccess'))
    shipDialogVisible.value = false
    handleSearch()
    loadStats()
  } catch (error) {
    ElMessage.error(t('message.operationFailed'))
  } finally {
    submitting.value = false
  }
}

// 完成订单
const handleComplete = (order: any) => {
  ElMessageBox.confirm(
    t('orders.confirmComplete'),
    t('common.warning'),
    {
      confirmButtonText: t('common.confirm'),
      cancelButtonText: t('common.cancel'),
      type: 'warning'
    }
  ).then(async () => {
    try {
      // 实际API调用
      // await completeOrder(order.id)
      
      order.status = 'completed'
      ElMessage.success(t('orders.completeSuccess'))
      handleSearch()
      loadStats()
    } catch (error) {
      ElMessage.error(t('message.operationFailed'))
    }
  })
}

// 查看详情
const handleViewDetail = (order: any) => {
  selectedOrder.value = order
  detailDialogVisible.value = true
}

// 批量发货
const batchShip = () => {
  if (selectedOrders.value.length === 0) {
    ElMessage.warning(t('orders.selectOrders'))
    return
  }
  
  ElMessageBox.confirm(
    t('orders.confirmBatchShip'),
    t('common.warning'),
    {
      confirmButtonText: t('common.confirm'),
      cancelButtonText: t('common.cancel'),
      type: 'warning'
    }
  ).then(async () => {
    try {
      // 实际API调用
      // await batchShipOrders(selectedOrders.value.map(order => order.id))
      
      selectedOrders.value.forEach(order => {
        order.status = 'shipped'
      })
      ElMessage.success(t('orders.batchShipSuccess'))
      handleSearch()
      loadStats()
    } catch (error) {
      ElMessage.error(t('message.operationFailed'))
    }
  })
}

// 批量完成
const batchComplete = () => {
  if (selectedOrders.value.length === 0) {
    ElMessage.warning(t('orders.selectOrders'))
    return
  }
  
  ElMessageBox.confirm(
    t('orders.confirmBatchComplete'),
    t('common.warning'),
    {
      confirmButtonText: t('common.confirm'),
      cancelButtonText: t('common.cancel'),
      type: 'warning'
    }
  ).then(async () => {
    try {
      // 实际API调用
      // await batchCompleteOrders(selectedOrders.value.map(order => order.id))
      
      selectedOrders.value.forEach(order => {
        order.status = 'completed'
      })
      ElMessage.success(t('orders.batchCompleteSuccess'))
      handleSearch()
      loadStats()
    } catch (error) {
      ElMessage.error(t('message.operationFailed'))
    }
  })
}

onMounted(() => {
  loadStats()
  handleSearch()
})
</script>

<style scoped>
.stats-row {
  margin-bottom: 20px;
}

.stat-card {
  text-align: center;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.search-form {
  margin-bottom: 20px;
}

.order-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.order-item:last-child {
  margin-bottom: 0;
}

.batch-actions {
  margin: 20px 0;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.detail-card {
  margin-bottom: 20px;
}

.address-info {
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.recipient-name {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 5px;
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

.product-info {
  display: flex;
  align-items: center;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ship-confirm-content {
  padding: 10px 0;
}

.order-info {
  margin-bottom: 20px;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.order-info h3 {
  margin: 0 0 15px 0;
  font-size: 16px;
  color: #303133;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-item .label {
  font-size: 14px;
  color: #606266;
  font-weight: 500;
}

.info-item .value {
  font-size: 14px;
  color: #303133;
  font-weight: 600;
}

.platform-notice {
  margin-bottom: 20px;
}

.platform-notice p {
  margin: 0;
  font-size: 14px;
  line-height: 1.5;
}
</style>

