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
            RM{{ row.payAmount }}
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
        <el-table-column prop="logisticsStatus" label="物流状态" width="150">
          <template #default="{ row }">
            <el-tag v-if="row.logisticsStatus" type="info" size="small">
              {{ row.logisticsStatus }}
            </el-tag>
            <span v-else class="text-gray-400">暂无物流信息</span>
          </template>
        </el-table-column>
        <el-table-column prop="trackingNumber" label="快递单号" width="150">
          <template #default="{ row }">
            <span v-if="row.trackingNumber">{{ row.trackingNumber }}</span>
            <span v-else class="text-gray-400">暂无</span>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="下单时间" width="180" show-overflow-tooltip />
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              link
              @click="handleDetail(row)"
            >
              查看详情
            </el-button>
            <el-button
              type="success"
              link
              @click="handleRemark(row)"
            >
              添加备注
            </el-button>
            <el-button
              type="warning"
              link
              @click="handleLogistics(row)"
            >
              物流跟踪
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

    <!-- 订单备注对话框 -->
    <el-dialog
      v-model="remarkDialogVisible"
      title="订单备注"
      width="600px"
    >
      <el-form
        ref="remarkFormRef"
        :model="remarkForm"
        :rules="remarkRules"
        label-width="100px"
      >
        <el-form-item label="订单号">
          <el-input v-model="currentOrder.orderNo" disabled />
        </el-form-item>
        <el-form-item label="管理员备注" prop="adminRemark">
          <el-input
            v-model="remarkForm.adminRemark"
            type="textarea"
            :rows="4"
            placeholder="请输入管理员备注信息"
          />
        </el-form-item>
        <el-form-item label="物流状态" prop="logisticsStatus">
          <el-input
            v-model="remarkForm.logisticsStatus"
            placeholder="请输入物流状态，如：已到达北京分拣中心"
          />
        </el-form-item>
        <el-form-item label="快递单号" prop="trackingNumber">
          <el-input
            v-model="remarkForm.trackingNumber"
            placeholder="请输入快递单号"
          />
        </el-form-item>
        <el-form-item label="物流公司" prop="logisticsCompany">
          <el-input
            v-model="remarkForm.logisticsCompany"
            placeholder="请输入物流公司"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="remarkDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveRemark" :loading="remarkSaving">
          保存
        </el-button>
      </template>
    </el-dialog>

    <!-- 物流跟踪对话框 -->
    <el-dialog
      v-model="logisticsDialogVisible"
      title="物流跟踪"
      width="800px"
    >
      <div class="logistics-info">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="订单号">{{ currentOrder.orderNo }}</el-descriptions-item>
          <el-descriptions-item label="收货人">{{ currentOrder.receiverName }}</el-descriptions-item>
          <el-descriptions-item label="联系电话">{{ currentOrder.receiverPhone }}</el-descriptions-item>
          <el-descriptions-item label="收货地址">
            {{ currentOrder.receiverProvince }} {{ currentOrder.receiverCity }} {{ currentOrder.receiverDistrict }} {{ currentOrder.receiverAddress }}
          </el-descriptions-item>
          <el-descriptions-item label="快递单号">
            <span v-if="currentOrder.trackingNumber">{{ currentOrder.trackingNumber }}</span>
            <span v-else class="text-gray-400">暂无</span>
          </el-descriptions-item>
          <el-descriptions-item label="物流公司">
            <span v-if="currentOrder.logisticsCompany">{{ currentOrder.logisticsCompany }}</span>
            <span v-else class="text-gray-400">暂无</span>
          </el-descriptions-item>
          <el-descriptions-item label="当前状态">
            <el-tag v-if="currentOrder.logisticsStatus" type="info">
              {{ currentOrder.logisticsStatus }}
            </el-tag>
            <span v-else class="text-gray-400">暂无物流信息</span>
          </el-descriptions-item>
          <el-descriptions-item label="更新时间">
            <span v-if="currentOrder.logisticsUpdateTime">
              {{ formatDate(currentOrder.logisticsUpdateTime) }}
            </span>
            <span v-else class="text-gray-400">暂无</span>
          </el-descriptions-item>
        </el-descriptions>

        <div class="logistics-history" v-if="logisticsHistory.length > 0">
          <h4>物流历史</h4>
          <el-timeline>
            <el-timeline-item
              v-for="(item, index) in logisticsHistory"
              :key="index"
              :timestamp="formatDate(item.time)"
              placement="top"
            >
              <el-card>
                <h4>{{ item.status }}</h4>
                <p>{{ item.location }}</p>
                <p class="text-gray-500">{{ item.remark }}</p>
              </el-card>
            </el-timeline-item>
          </el-timeline>
        </div>

        <div class="logistics-update">
          <h4>更新物流状态</h4>
          <el-form
            ref="logisticsFormRef"
            :model="logisticsForm"
            :rules="logisticsRules"
            label-width="100px"
          >
            <el-form-item label="物流状态" prop="logisticsStatus">
              <el-input
                v-model="logisticsForm.logisticsStatus"
                placeholder="请输入物流状态"
              />
            </el-form-item>
            <el-form-item label="备注" prop="adminRemark">
              <el-input
                v-model="logisticsForm.adminRemark"
                type="textarea"
                :rows="3"
                placeholder="请输入备注信息"
              />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="updateLogistics" :loading="logisticsSaving">
                更新物流状态
              </el-button>
            </el-form-item>
          </el-form>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, type FormInstance } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { getOrderList } from '@/api/order'
import { 
  updateOrderRemark, 
  updateOrderLogistics, 
  getLogisticsHistory 
} from '@/api/order-remark'

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

// 备注相关
const remarkDialogVisible = ref(false)
const remarkSaving = ref(false)
const remarkFormRef = ref<FormInstance>()
const currentOrder = ref<any>({})
const remarkForm = reactive({
  adminRemark: '',
  logisticsStatus: '',
  trackingNumber: '',
  logisticsCompany: ''
})

const remarkRules = {
  adminRemark: [
    { max: 500, message: '备注长度不能超过500个字符', trigger: 'blur' }
  ],
  logisticsStatus: [
    { max: 100, message: '物流状态长度不能超过100个字符', trigger: 'blur' }
  ],
  trackingNumber: [
    { max: 50, message: '快递单号长度不能超过50个字符', trigger: 'blur' }
  ],
  logisticsCompany: [
    { max: 50, message: '物流公司长度不能超过50个字符', trigger: 'blur' }
  ]
}

// 物流跟踪相关
const logisticsDialogVisible = ref(false)
const logisticsSaving = ref(false)
const logisticsFormRef = ref<FormInstance>()
const logisticsHistory = ref([])
const logisticsForm = reactive({
  logisticsStatus: '',
  adminRemark: ''
})

const logisticsRules = {
  logisticsStatus: [
    { required: true, message: '物流状态不能为空', trigger: 'blur' },
    { max: 100, message: '物流状态长度不能超过100个字符', trigger: 'blur' }
  ]
}

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

// 添加备注
const handleRemark = (row: any) => {
  currentOrder.value = row
  remarkForm.adminRemark = row.adminRemark || ''
  remarkForm.logisticsStatus = row.logisticsStatus || ''
  remarkForm.trackingNumber = row.trackingNumber || ''
  remarkForm.logisticsCompany = row.logisticsCompany || ''
  remarkDialogVisible.value = true
}

// 保存备注
const saveRemark = async () => {
  if (!remarkFormRef.value) return

  await remarkFormRef.value.validate(async (valid) => {
    if (valid) {
      remarkSaving.value = true
      try {
        await updateOrderRemark(currentOrder.value.id, remarkForm)
        ElMessage.success('备注保存成功')
        remarkDialogVisible.value = false
        handleQuery() // 刷新列表
      } catch (error) {
        ElMessage.error(error.message || '保存失败')
      } finally {
        remarkSaving.value = false
      }
    }
  })
}

// 物流跟踪
const handleLogistics = async (row: any) => {
  currentOrder.value = row
  logisticsForm.logisticsStatus = ''
  logisticsForm.adminRemark = ''
  
  try {
    const response = await getLogisticsHistory(row.id)
    if (response.code === 200) {
      logisticsHistory.value = response.data
    }
  } catch (error) {
    console.error('获取物流历史失败:', error)
  }
  
  logisticsDialogVisible.value = true
}

// 更新物流状态
const updateLogistics = async () => {
  if (!logisticsFormRef.value) return

  await logisticsFormRef.value.validate(async (valid) => {
    if (valid) {
      logisticsSaving.value = true
      try {
        await updateOrderLogistics({
          orderId: currentOrder.value.id,
          logisticsStatus: logisticsForm.logisticsStatus,
          adminRemark: logisticsForm.adminRemark
        })
        ElMessage.success('物流状态更新成功')
        logisticsDialogVisible.value = false
        handleQuery() // 刷新列表
      } catch (error) {
        ElMessage.error(error.message || '更新失败')
      } finally {
        logisticsSaving.value = false
      }
    }
  })
}

// 格式化日期
const formatDate = (date: string | Date) => {
  return new Date(date).toLocaleString()
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

