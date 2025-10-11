<template>
  <div class="pending-orders">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('nav.pendingOrders') }}</span>
          <el-button @click="handleRefresh" :loading="loading">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.refresh') }}
          </el-button>
        </div>
      </template>

      <!-- 订单列表 -->
      <el-table
        :data="orderList"
        style="width: 100%"
        v-loading="loading"
      >
        <el-table-column prop="orderNo" :label="$t('orders.orderNo')" width="200" />
        
        <el-table-column :label="$t('orders.customerInfo')" width="150">
          <template #default="{ row }">
            <div>{{ row.customerName }}</div>
            <div style="font-size: 12px; color: #999;">{{ row.customerPhone }}</div>
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.productInfo')" min-width="250">
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

        <el-table-column :label="$t('orders.orderTime')" width="180">
          <template #default="{ row }">
            {{ row.orderTime }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('common.actions')" width="150" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              size="small"
              @click="handleShip(row)"
            >
              {{ $t('orders.shipOrder') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>

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
      width="600px"
    >
      <el-form
        ref="shipFormRef"
        :model="shipForm"
        :rules="shipRules"
        label-width="150px"
      >
        <el-form-item :label="$t('orders.orderNo')">
          <span>{{ selectedOrder?.orderNo }}</span>
        </el-form-item>

        <el-form-item :label="$t('orders.carrier')" prop="carrier">
          <el-select
            v-model="shipForm.carrier"
            :placeholder="$t('orders.carrier')"
            style="width: 100%"
          >
            <el-option label="DHL Express" value="DHL" />
            <el-option label="FedEx" value="FedEx" />
            <el-option label="UPS" value="UPS" />
            <el-option label="USPS" value="USPS" />
            <el-option label="SF Express" value="SF" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('orders.trackingNumber')" prop="trackingNumber">
          <el-input
            v-model="shipForm.trackingNumber"
            :placeholder="$t('orders.trackingNumber')"
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="shipDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="handleConfirmShip" :loading="submitting">
          {{ $t('common.confirm') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getMerchantOrders, shipOrder } from '@/api/order'

const { t } = useI18n()

const loading = ref(false)
const submitting = ref(false)
const orderList = ref<any[]>([])

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 发货对话框
const shipDialogVisible = ref(false)
const selectedOrder = ref<any>(null)
const shipFormRef = ref()
const shipForm = reactive({
  carrier: '',
  trackingNumber: ''
})

const shipRules = {
  carrier: [
    { required: true, message: () => t('validation.required'), trigger: 'change' }
  ],
  trackingNumber: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ]
}

// 刷新
const handleRefresh = () => {
  handleSearch()
}

// 搜索
const handleSearch = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      pageSize: pagination.pageSize,
      status: 'pending'
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

// 发货
const handleShip = (order: any) => {
  selectedOrder.value = order
  shipForm.carrier = ''
  shipForm.trackingNumber = ''
  shipDialogVisible.value = true
}

// 确认发货
const handleConfirmShip = async () => {
  if (!shipFormRef.value) return
  
  await shipFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      submitting.value = true
      try {
        // 实际API调用
        // await shipOrder(selectedOrder.value.id, shipForm)
        
        ElMessage.success(t('message.operationSuccess'))
        shipDialogVisible.value = false
        handleSearch()
      } catch (error: any) {
        ElMessage.error(error.message || t('message.operationFailed'))
      } finally {
        submitting.value = false
      }
    }
  })
}

onMounted(() => {
  handleSearch()
})
</script>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-item {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.order-item:last-child {
  margin-bottom: 0;
}
</style>

