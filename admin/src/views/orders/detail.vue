<template>
  <div class="order-detail-page">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>订单详情</span>
          <el-button @click="goBack">返回</el-button>
        </div>
      </template>

      <div v-if="orderDetail" class="order-detail">
        <!-- 订单信息 -->
        <el-descriptions title="订单信息" :column="2" border>
          <el-descriptions-item label="订单号">{{ orderDetail.orderNo }}</el-descriptions-item>
          <el-descriptions-item label="订单状态">
            <el-tag :type="getStatusType(orderDetail.orderStatus)">
              {{ getStatusText(orderDetail.orderStatus) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="下单时间">{{ orderDetail.createTime }}</el-descriptions-item>
          <el-descriptions-item label="支付时间">{{ orderDetail.payTime || '-' }}</el-descriptions-item>
          <el-descriptions-item label="Order Amount">RM{{ orderDetail.totalAmount }}</el-descriptions-item>
          <el-descriptions-item label="Shipping Fee">RM{{ orderDetail.freight }}</el-descriptions-item>
          <el-descriptions-item label="Total Paid">
            <span style="color: #f56c6c; font-weight: bold;">RM{{ orderDetail.payAmount }}</span>
          </el-descriptions-item>
          <el-descriptions-item label="买家留言">{{ orderDetail.buyerMessage || '-' }}</el-descriptions-item>
        </el-descriptions>

        <!-- 收货信息 -->
        <el-descriptions title="收货信息" :column="2" border class="mt-20">
          <el-descriptions-item label="收货人">{{ orderDetail.receiverName }}</el-descriptions-item>
          <el-descriptions-item label="联系电话">{{ orderDetail.receiverPhone }}</el-descriptions-item>
          <el-descriptions-item label="收货地址" :span="2">
            {{ orderDetail.receiverProvince }} {{ orderDetail.receiverCity }} 
            {{ orderDetail.receiverDistrict }} {{ orderDetail.receiverAddress }}
          </el-descriptions-item>
        </el-descriptions>

        <!-- 商品信息 -->
        <div class="mt-20">
          <h3>商品信息</h3>
          <el-table :data="orderDetail.items" border>
            <el-table-column prop="productName" label="商品名称" min-width="200" />
            <el-table-column prop="skuName" label="规格" width="150" />
            <el-table-column prop="quantity" label="数量" width="100" />
            <el-table-column prop="salePrice" label="Unit Price" width="120">
              <template #default="{ row }">
                RM{{ row.salePrice }}
              </template>
            </el-table-column>
            <el-table-column prop="totalPrice" label="Subtotal" width="120">
              <template #default="{ row }">
                RM{{ row.totalPrice }}
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>

      <div v-else v-loading="loading" style="min-height: 400px;"></div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getOrderDetail } from '@/api/order'

const router = useRouter()
const route = useRoute()

const orderDetail = ref<any>(null)
const loading = ref(false)

// 获取订单详情
const fetchOrderDetail = async () => {
  loading.value = true
  try {
    const id = Number(route.params.id)
    const res = await getOrderDetail(id)
    orderDetail.value = res.data
  } catch (error) {
    console.error('获取订单详情失败：', error)
  } finally {
    loading.value = false
  }
}

// 返回
const goBack = () => {
  router.back()
}

// 获取状态文本
const getStatusText = (status: number) => {
  const statusMap: Record<number, string> = {
    1: '待付款',
    2: '待发货',
    3: '待收货',
    4: '已完成',
    5: '已取消'
  }
  return statusMap[status] || '未知'
}

// 获取状态类型
const getStatusType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'warning',
    2: 'primary',
    3: 'info',
    4: 'success',
    5: 'danger'
  }
  return typeMap[status] || 'info'
}

onMounted(() => {
  fetchOrderDetail()
})
</script>

<style scoped>
.order-detail-page {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-detail h3 {
  margin-bottom: 10px;
  color: #333;
}
</style>

