<template>
  <div class="order-detail">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-left">
        <el-button @click="goBack" :icon="ArrowLeft">
          {{ $t('common.back') }}
        </el-button>
        <h1>{{ $t('orders.orderDetails') }}</h1>
      </div>
      <div class="header-right">
        <el-tag 
          :type="getOrderStatusType(orderDetail?.orderStatus)"
          size="large"
        >
          {{ getOrderStatusText(orderDetail?.orderStatus) }}
        </el-tag>
      </div>
    </div>

    <!-- 加载状态 -->
    <div v-if="loading" class="loading-container">
      <el-skeleton :rows="8" animated />
    </div>

    <!-- 订单详情内容 -->
    <div v-else-if="orderDetail" class="order-content">
      <!-- 订单基本信息 -->
      <el-card class="order-card">
        <template #header>
          <div class="card-header">
            <span>{{ $t('orders.orderInfo') }}</span>
            <span class="order-no">{{ $t('orders.orderNo') }}: {{ orderDetail.orderNo }}</span>
          </div>
        </template>

        <el-row :gutter="20">
          <el-col :span="8">
            <div class="info-item">
              <label>{{ $t('orders.orderTime') }}:</label>
              <span>{{ formatDate(orderDetail.createTime) }}</span>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="info-item">
              <label>{{ $t('orders.paymentStatus') }}:</label>
              <el-tag :type="getPaymentStatusType(orderDetail.payStatus)">
                {{ getPaymentStatusText(orderDetail.payStatus) }}
              </el-tag>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="info-item">
              <label>{{ $t('orders.payType') }}:</label>
              <span>{{ getPayTypeText(orderDetail.payType) }}</span>
            </div>
          </el-col>
        </el-row>
      </el-card>

      <!-- 收货地址信息 -->
      <el-card class="order-card">
        <template #header>
          <span>{{ $t('orders.shippingInfo') }}</span>
        </template>

        <div class="shipping-info">
          <div class="receiver-info">
            <div class="receiver-name">
              <el-icon><User /></el-icon>
              <span>{{ orderDetail.receiverName }}</span>
            </div>
            <div class="receiver-phone">
              <el-icon><Phone /></el-icon>
              <span>{{ orderDetail.receiverPhone }}</span>
            </div>
          </div>
          <div class="receiver-address">
            <el-icon><Location /></el-icon>
            <span>{{ orderDetail.receiverProvince }} {{ orderDetail.receiverCity }} {{ orderDetail.receiverDistrict }} {{ orderDetail.receiverAddress }}</span>
          </div>
        </div>
      </el-card>

      <!-- 商品信息 -->
      <el-card class="order-card">
        <template #header>
          <span>{{ $t('orders.productInfo') }}</span>
        </template>

        <el-table :data="orderDetail.items" style="width: 100%">
          <el-table-column :label="$t('orders.productImage')" width="100">
            <template #default="{ row }">
              <el-image
                :src="row.productImage"
                :alt="row.productName"
                style="width: 60px; height: 60px"
                fit="cover"
                :preview-src-list="[row.productImage]"
              />
            </template>
          </el-table-column>
          <el-table-column :label="$t('orders.productName')" prop="productName" />
          <el-table-column :label="$t('orders.skuName')" prop="skuName" />
          <el-table-column :label="$t('orders.quantity')" prop="quantity" width="80" />
          <el-table-column :label="$t('orders.costPrice')" width="100">
            <template #default="{ row }">
              <span class="price">${{ row.costPrice }}</span>
            </template>
          </el-table-column>
          <el-table-column :label="$t('orders.salePrice')" width="100">
            <template #default="{ row }">
              <span class="price">${{ row.salePrice }}</span>
            </template>
          </el-table-column>
          <el-table-column :label="$t('orders.subtotal')" width="100">
            <template #default="{ row }">
              <span class="price">${{ row.totalPrice }}</span>
            </template>
          </el-table-column>
        </el-table>
      </el-card>

      <!-- 订单金额信息 -->
      <el-card class="order-card">
        <template #header>
          <span>{{ $t('orders.amountInfo') }}</span>
        </template>

        <div class="amount-info">
          <div class="amount-row">
            <span>{{ $t('orders.totalAmount') }}:</span>
            <span class="amount">${{ orderDetail.totalAmount }}</span>
          </div>
          <div class="amount-row">
            <span>{{ $t('orders.costAmount') }}:</span>
            <span class="amount">${{ orderDetail.costAmount }}</span>
          </div>
          <div class="amount-row">
            <span>{{ $t('orders.freight') }}:</span>
            <span class="amount">${{ orderDetail.freight }}</span>
          </div>
          <div class="amount-row">
            <span>{{ $t('orders.discountAmount') }}:</span>
            <span class="amount">-${{ orderDetail.discountAmount }}</span>
          </div>
          <el-divider />
          <div class="amount-row total">
            <span>{{ $t('orders.payAmount') }}:</span>
            <span class="amount total-amount">${{ orderDetail.payAmount }}</span>
          </div>
          <div class="amount-row profit">
            <span>{{ $t('orders.yourProfit') }}:</span>
            <span class="amount profit-amount">${{ orderDetail.merchantProfit }}</span>
          </div>
        </div>
      </el-card>

      <!-- 物流信息 -->
      <el-card v-if="orderDetail.trackingNumber" class="order-card">
        <template #header>
          <span>{{ $t('orders.logisticsInfo') }}</span>
        </template>

        <div class="logistics-info">
          <div class="logistics-row">
            <span>{{ $t('orders.trackingNumber') }}:</span>
            <span class="tracking-number">{{ orderDetail.trackingNumber }}</span>
          </div>
          <div class="logistics-row">
            <span>{{ $t('orders.carrier') }}:</span>
            <span>{{ orderDetail.carrier }}</span>
          </div>
          <div class="logistics-row">
            <span>{{ $t('orders.shipmentDate') }}:</span>
            <span>{{ formatDate(orderDetail.shipTime) }}</span>
          </div>
        </div>
      </el-card>

      <!-- 操作按钮 -->
      <div class="action-buttons">
        <el-button @click="goBack">{{ $t('common.back') }}</el-button>
        <el-button 
          v-if="canShipOrder"
          type="primary" 
          @click="showShipDialog"
          :loading="shipping"
        >
          {{ $t('orders.shipOrder') }}
        </el-button>
        <el-button 
          v-if="orderDetail.orderStatus === 2"
          type="success" 
          @click="confirmReceive"
          :loading="confirming"
        >
          {{ $t('orders.confirmReceive') }}
        </el-button>
      </div>
    </div>

    <!-- 发货对话框 -->
    <el-dialog
      v-model="shipDialogVisible"
      :title="$t('orders.shipOrder')"
      width="500px"
    >
      <el-form :model="shipForm" :rules="shipRules" ref="shipFormRef" label-width="120px">
        <el-form-item :label="$t('orders.trackingNumber')" prop="trackingNumber">
          <el-input
            v-model="shipForm.trackingNumber"
            :placeholder="$t('orders.trackingNumberPlaceholder')"
          />
        </el-form-item>
        <el-form-item :label="$t('orders.carrier')" prop="carrier">
          <el-select
            v-model="shipForm.carrier"
            :placeholder="$t('orders.selectCarrier')"
            style="width: 100%"
          >
            <el-option label="DHL" value="DHL" />
            <el-option label="FedEx" value="FedEx" />
            <el-option label="UPS" value="UPS" />
            <el-option label="USPS" value="USPS" />
            <el-option label="顺丰速运" value="SF" />
            <el-option label="中通快递" value="ZTO" />
            <el-option label="圆通速递" value="YTO" />
            <el-option label="申通快递" value="STO" />
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('orders.remark')" prop="remark">
          <el-input
            v-model="shipForm.remark"
            type="textarea"
            :rows="3"
            :placeholder="$t('orders.shipRemarkPlaceholder')"
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="shipDialogVisible = false">{{ $t('common.cancel') }}</el-button>
        <el-button type="primary" @click="handleShip" :loading="shipping">
          {{ $t('orders.confirmShip') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { ArrowLeft, User, Phone, Location } from '@element-plus/icons-vue'
import { getOrderDetail, shipOrder } from '@/api/order'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()

// 响应式数据
const loading = ref(true)
const orderDetail = ref<any>(null)
const shipping = ref(false)
const confirming = ref(false)
const shipDialogVisible = ref(false)
const shipFormRef = ref<FormInstance>()

// 发货表单
const shipForm = reactive({
  trackingNumber: '',
  carrier: '',
  remark: ''
})

// 发货表单验证规则
const shipRules: FormRules = {
  trackingNumber: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  carrier: [
    { required: true, message: () => t('validation.required'), trigger: 'change' }
  ]
}

// 计算属性
const canShipOrder = computed(() => {
  return orderDetail.value?.orderStatus === 2 && orderDetail.value?.payStatus === 1
})

// 方法
const goBack = () => {
  router.back()
}

const loadOrderDetail = async () => {
  try {
    loading.value = true
    const orderId = route.params.id as string
    const res = await getOrderDetail(parseInt(orderId))
    orderDetail.value = res.data
  } catch (error) {
    console.error('Failed to load order detail:', error)
    ElMessage.error(t('message.operationFailed'))
  } finally {
    loading.value = false
  }
}

const showShipDialog = () => {
  shipDialogVisible.value = true
}

const handleShip = async () => {
  if (!shipFormRef.value) return
  
  await shipFormRef.value.validate(async (valid) => {
    if (valid) {
      shipping.value = true
      try {
        await shipOrder(orderDetail.value.id, shipForm)
        ElMessage.success(t('orders.shipSuccess'))
        shipDialogVisible.value = false
        await loadOrderDetail() // 重新加载订单详情
      } catch (error) {
        console.error('Failed to ship order:', error)
        ElMessage.error(t('orders.shipFailed'))
      } finally {
        shipping.value = false
      }
    }
  })
}

const confirmReceive = async () => {
  try {
    await ElMessageBox.confirm(
      t('orders.confirmReceiveMessage'),
      t('common.warning'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    confirming.value = true
    // 这里应该调用确认收货的API
    ElMessage.success(t('orders.receiveSuccess'))
    await loadOrderDetail()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Failed to confirm receive:', error)
      ElMessage.error(t('orders.receiveFailed'))
    }
  } finally {
    confirming.value = false
  }
}

// 状态相关方法
const getOrderStatusType = (status: number) => {
  const statusMap: Record<number, string> = {
    1: 'warning',  // 待付款
    2: 'primary',  // 待发货
    3: 'info',     // 待收货
    4: 'success',  // 已完成
    5: 'danger'    // 已取消
  }
  return statusMap[status] || 'info'
}

const getOrderStatusText = (status: number) => {
  const statusMap: Record<number, string> = {
    1: t('orders.pendingPayment'),
    2: t('orders.pendingShipment'),
    3: t('orders.pendingReceive'),
    4: t('orders.completed'),
    5: t('orders.cancelled')
  }
  return statusMap[status] || t('orders.unknown')
}

const getPaymentStatusType = (status: number) => {
  const statusMap: Record<number, string> = {
    0: 'warning',  // 未支付
    1: 'success',  // 已支付
    2: 'info'      // 已退款
  }
  return statusMap[status] || 'info'
}

const getPaymentStatusText = (status: number) => {
  const statusMap: Record<number, string> = {
    0: t('orders.unpaid'),
    1: t('orders.paid'),
    2: t('orders.refunded')
  }
  return statusMap[status] || t('orders.unknown')
}

const getPayTypeText = (type: number) => {
  const typeMap: Record<number, string> = {
    1: t('orders.alipay'),
    2: t('orders.wechat'),
    3: t('orders.bankCard'),
    4: t('orders.paypal')
  }
  return typeMap[type] || t('orders.unknown')
}

const formatDate = (date: string | Date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString()
}

// 生命周期
onMounted(() => {
  loadOrderDetail()
})
</script>

<style scoped>
.order-detail {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e4e7ed;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.header-left h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #303133;
}

.order-no {
  font-size: 14px;
  color: #909399;
  font-weight: normal;
}

.loading-container {
  padding: 20px;
}

.order-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.order-card {
  border-radius: 8px;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
  color: #303133;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.info-item label {
  font-size: 14px;
  color: #909399;
  font-weight: 500;
}

.info-item span {
  font-size: 16px;
  color: #303133;
  font-weight: 600;
}

.shipping-info {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.receiver-info {
  display: flex;
  gap: 30px;
  align-items: center;
}

.receiver-name,
.receiver-phone {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  color: #303133;
  font-weight: 600;
}

.receiver-address {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 14px;
  color: #606266;
  line-height: 1.5;
}

.price {
  font-weight: 600;
  color: #e6a23c;
}

.amount-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.amount-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.amount-row.total {
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

.amount-row.profit {
  font-size: 16px;
  font-weight: 600;
  color: #67c23a;
}

.amount {
  font-weight: 600;
  color: #e6a23c;
}

.total-amount {
  font-size: 20px;
  color: #f56c6c;
}

.profit-amount {
  color: #67c23a;
}

.logistics-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.logistics-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.tracking-number {
  font-weight: 600;
  color: #409eff;
  font-family: monospace;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
  padding: 20px 0;
  border-top: 1px solid #e4e7ed;
}

@media (max-width: 768px) {
  .order-detail {
    padding: 10px;
  }
  
  .page-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .receiver-info {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>

