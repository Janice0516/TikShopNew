<template>
  <div class="orders-page">
    <el-card>
      <!-- 搜索栏 -->
      <el-form :inline="true" :model="queryForm" class="search-form">
        <el-form-item label="订单号">
          <el-input
            v-model="queryForm.orderNo"
            placeholder="请输入订单号"
            clearable
          />
        </el-form-item>
        <el-form-item label="订单状态">
          <el-select v-model="queryForm.orderStatus" placeholder="请选择" clearable>
            <el-option label="全部" :value="undefined" />
            <el-option label="待付款" :value="1" />
            <el-option label="待发货" :value="2" />
            <el-option label="待收货" :value="3" />
            <el-option label="已完成" :value="4" />
            <el-option label="已取消" :value="5" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery" :icon="Search">
            搜索
          </el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 表格 -->
      <el-table :data="tableData" v-loading="loading" border stripe>
        <el-table-column prop="orderNo" label="订单号" width="180" />
        <el-table-column prop="userId" label="用户ID" width="100" />
        <el-table-column prop="payAmount" label="Order Amount" width="120">
          <template #default="{ row }">
            ${{ row.payAmount }}
          </template>
        </el-table-column>
        <el-table-column prop="orderStatus" label="订单状态" width="100">
          <template #default="{ row }">
            <el-tag :type="getStatusType(row.orderStatus)">
              {{ getStatusText(row.orderStatus) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="payStatus" label="支付状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.payStatus === 1 ? 'success' : 'info'">
              {{ row.payStatus === 1 ? '已支付' : '未支付' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="receiverName" label="收货人" width="100" />
        <el-table-column prop="receiverPhone" label="联系电话" width="120" />
        <el-table-column prop="createTime" label="下单时间" width="180" show-overflow-tooltip />
        <el-table-column label="操作" width="120" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              link
              @click="handleDetail(row)"
            >
              查看详情
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <el-pagination
        v-model:current-page="pagination.page"
        v-model:page-size="pagination.pageSize"
        :page-sizes="[10, 20, 50, 100]"
        :total="pagination.total"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleQuery"
        @current-change="handleQuery"
        class="mt-20"
      />
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Search } from '@element-plus/icons-vue'
import { getOrderList } from '@/api/order'

const router = useRouter()

const queryForm = reactive({
  orderNo: '',
  orderStatus: undefined as number | undefined
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

const tableData = ref([])
const loading = ref(false)

// 查询订单列表
const handleQuery = async () => {
  loading.value = true
  try {
    const res = await getOrderList({
      page: pagination.page,
      pageSize: pagination.pageSize,
      orderNo: queryForm.orderNo,
      orderStatus: queryForm.orderStatus
    })
    tableData.value = res.data.list
    pagination.total = res.data.total
  } catch (error) {
    console.error('查询失败：', error)
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  queryForm.orderNo = ''
  queryForm.orderStatus = undefined
  pagination.page = 1
  handleQuery()
}

// 查看详情
const handleDetail = (row: any) => {
  router.push(`/orders/${row.id}`)
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
  handleQuery()
})
</script>

<style scoped>
.orders-page {
  padding: 20px;
}

.search-form {
  margin-bottom: 20px;
}
</style>

