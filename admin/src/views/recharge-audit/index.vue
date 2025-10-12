<template>
  <div class="recharge-audit">
    <!-- 统计卡片 -->
    <el-row :gutter="20" class="stats-row">
      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#E6A23C" :size="48"><Clock /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.pendingCount }}</div>
              <div class="stat-label">待审核</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#67C23A" :size="48"><Check /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.approvedCount }}</div>
              <div class="stat-label">已通过</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#F56C6C" :size="48"><Close /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.rejectedCount }}</div>
              <div class="stat-label">已拒绝</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#409EFF" :size="48"><Money /></el-icon>
            <div class="stat-info">
              <div class="stat-value">RM{{ stats.totalAmount }}</div>
              <div class="stat-label">总充值金额</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 筛选条件 -->
    <el-card class="filter-card">
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item label="商户ID">
          <el-input
            v-model="searchForm.merchantId"
            placeholder="请输入商户ID"
            clearable
            style="width: 150px"
          />
        </el-form-item>

        <el-form-item label="状态">
          <el-select
            v-model="searchForm.status"
            placeholder="请选择状态"
            clearable
            style="width: 150px"
          >
            <el-option label="待审核" :value="0" />
            <el-option label="已通过" :value="1" />
            <el-option label="已拒绝" :value="2" />
          </el-select>
        </el-form-item>

        <el-form-item label="支付方式">
          <el-select
            v-model="searchForm.paymentMethod"
            placeholder="请选择支付方式"
            clearable
            style="width: 150px"
          >
            <el-option label="银行转账" value="bank_transfer" />
            <el-option label="信用卡" value="credit_card" />
            <el-option label="PayPal" value="paypal" />
            <el-option label="支付宝" value="alipay" />
            <el-option label="微信支付" value="wechat_pay" />
          </el-select>
        </el-form-item>

        <el-form-item label="申请时间">
          <el-date-picker
            v-model="dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            @change="handleDateChange"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSearch">
            <el-icon><Search /></el-icon>
            搜索
          </el-button>
          <el-button @click="handleReset">
            <el-icon><Refresh /></el-icon>
            重置
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 充值记录表格 -->
    <el-card>
      <template #header>
        <div class="card-header">
          <span>充值审核记录</span>
          <el-button @click="handleRefresh" :loading="loading">
            <el-icon><Refresh /></el-icon>
            刷新
          </el-button>
        </div>
      </template>

      <el-table
        :data="rechargeList"
        style="width: 100%"
        v-loading="loading"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        
        <el-table-column prop="id" label="ID" width="80" />
        
        <el-table-column label="商户信息" width="200">
          <template #default="{ row }">
            <div class="merchant-info">
              <div class="merchant-name">{{ row.merchant?.merchantName || '未知商户' }}</div>
              <div class="merchant-uid">UID: {{ row.merchant?.merchantUid || '-' }}</div>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="amount" label="充值金额" width="120">
          <template #default="{ row }">
            <span class="amount">RM{{ row.amount }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="paymentMethod" label="支付方式" width="120">
          <template #default="{ row }">
            <el-tag :type="getPaymentMethodType(row.paymentMethod)">
              {{ getPaymentMethodName(row.paymentMethod) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="paymentReference" label="支付凭证" width="150" show-overflow-tooltip />

        <el-table-column prop="remark" label="备注" width="200" show-overflow-tooltip />

        <el-table-column label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="getStatusType(row.status)">
              {{ getStatusName(row.status) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="createdAt" label="申请时间" width="180">
          <template #default="{ row }">
            {{ formatDate(row.createdAt) }}
          </template>
        </el-table-column>

        <el-table-column label="审核信息" width="200">
          <template #default="{ row }">
            <div v-if="row.status !== 0" class="audit-info">
              <div>审核人: {{ row.adminName || '-' }}</div>
              <div>审核时间: {{ formatDate(row.auditTime) }}</div>
              <div v-if="row.auditReason">原因: {{ row.auditReason }}</div>
            </div>
            <span v-else class="pending-text">待审核</span>
          </template>
        </el-table-column>

        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              v-if="row.status === 0"
              type="success"
              size="small"
              @click="handleAudit(row, 1)"
            >
              <el-icon><Check /></el-icon>
              通过
            </el-button>
            <el-button
              v-if="row.status === 0"
              type="danger"
              size="small"
              @click="handleAudit(row, 2)"
            >
              <el-icon><Close /></el-icon>
              拒绝
            </el-button>
            <el-button
              type="primary"
              size="small"
              @click="handleViewDetail(row)"
            >
              <el-icon><View /></el-icon>
              详情
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

    <!-- 审核对话框 -->
    <el-dialog
      v-model="auditDialogVisible"
      :title="auditForm.status === 1 ? '审核通过' : '审核拒绝'"
      width="500px"
    >
      <el-form :model="auditForm" label-width="100px">
        <el-form-item label="充值金额">
          <span>RM{{ auditForm.amount }}</span>
        </el-form-item>
        <el-form-item label="支付方式">
          <span>{{ getPaymentMethodName(auditForm.paymentMethod) }}</span>
        </el-form-item>
        <el-form-item label="支付凭证">
          <span>{{ auditForm.paymentReference || '-' }}</span>
        </el-form-item>
        <el-form-item label="备注">
          <span>{{ auditForm.remark || '-' }}</span>
        </el-form-item>
        <el-form-item label="审核原因" required>
          <el-input
            v-model="auditForm.auditReason"
            type="textarea"
            :rows="3"
            :placeholder="auditForm.status === 1 ? '请输入通过原因' : '请输入拒绝原因'"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="auditDialogVisible = false">取消</el-button>
        <el-button
          type="primary"
          :loading="auditLoading"
          @click="handleSubmitAudit"
        >
          确认{{ auditForm.status === 1 ? '通过' : '拒绝' }}
        </el-button>
      </template>
    </el-dialog>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="detailDialogVisible"
      title="充值详情"
      width="600px"
    >
      <div v-if="currentRecharge" class="recharge-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="充值ID">{{ currentRecharge.id }}</el-descriptions-item>
          <el-descriptions-item label="商户名称">{{ currentRecharge.merchant?.merchantName || '-' }}</el-descriptions-item>
          <el-descriptions-item label="商户UID">{{ currentRecharge.merchant?.merchantUid || '-' }}</el-descriptions-item>
          <el-descriptions-item label="充值金额">RM{{ currentRecharge.amount }}</el-descriptions-item>
          <el-descriptions-item label="支付方式">{{ getPaymentMethodName(currentRecharge.paymentMethod) }}</el-descriptions-item>
          <el-descriptions-item label="支付凭证">{{ currentRecharge.paymentReference || '-' }}</el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="getStatusType(currentRecharge.status)">
              {{ getStatusName(currentRecharge.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="申请时间">{{ formatDate(currentRecharge.createdAt) }}</el-descriptions-item>
          <el-descriptions-item label="审核人" v-if="currentRecharge.status !== 0">{{ currentRecharge.adminName || '-' }}</el-descriptions-item>
          <el-descriptions-item label="审核时间" v-if="currentRecharge.status !== 0">{{ formatDate(currentRecharge.auditTime) }}</el-descriptions-item>
          <el-descriptions-item label="审核原因" v-if="currentRecharge.auditReason" :span="2">{{ currentRecharge.auditReason }}</el-descriptions-item>
          <el-descriptions-item label="备注" v-if="currentRecharge.remark" :span="2">{{ currentRecharge.remark }}</el-descriptions-item>
        </el-descriptions>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { getRechargeList, auditRecharge, getRechargeStats } from '@/api/recharge'

const loading = ref(false)
const auditLoading = ref(false)
const auditDialogVisible = ref(false)
const detailDialogVisible = ref(false)
const dateRange = ref<[string, string] | null>(null)

const stats = ref({
  pendingCount: 0,
  approvedCount: 0,
  rejectedCount: 0,
  totalAmount: '0.00'
})

const rechargeList = ref<any[]>([])
const currentRecharge = ref<any>(null)

const searchForm = reactive({
  merchantId: '',
  status: undefined as number | undefined,
  paymentMethod: '',
  startDate: '',
  endDate: ''
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

const auditForm = reactive({
  id: 0,
  amount: 0,
  paymentMethod: '',
  paymentReference: '',
  remark: '',
  status: 1,
  auditReason: ''
})

// 获取支付方式类型
const getPaymentMethodType = (method: string) => {
  const types: Record<string, string> = {
    'bank_transfer': 'primary',
    'credit_card': 'success',
    'paypal': 'warning',
    'alipay': 'info',
    'wechat_pay': 'success'
  }
  return types[method] || 'info'
}

// 获取支付方式名称
const getPaymentMethodName = (method: string) => {
  const names: Record<string, string> = {
    'bank_transfer': '银行转账',
    'credit_card': '信用卡',
    'paypal': 'PayPal',
    'alipay': '支付宝',
    'wechat_pay': '微信支付'
  }
  return names[method] || method
}

// 获取状态类型
const getStatusType = (status: number) => {
  const types: Record<number, string> = {
    0: 'warning',
    1: 'success',
    2: 'danger'
  }
  return types[status] || 'info'
}

// 获取状态名称
const getStatusName = (status: number) => {
  const names: Record<number, string> = {
    0: '待审核',
    1: '已通过',
    2: '已拒绝'
  }
  return names[status] || '未知'
}

// 格式化日期
const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('zh-CN')
}

// 获取统计数据
const loadStats = async () => {
  try {
    const res = await getRechargeStats()
    stats.value = res.data
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
}

// 获取充值列表
const loadRechargeList = async () => {
  loading.value = true
  try {
    const params = {
      ...searchForm,
      page: pagination.page,
      pageSize: pagination.pageSize
    }
    
    const res = await getRechargeList(params)
    rechargeList.value = res.data.list
    pagination.total = res.data.total
  } catch (error) {
    console.error('Failed to load recharge list:', error)
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.page = 1
  loadRechargeList()
}

// 重置
const handleReset = () => {
  Object.assign(searchForm, {
    merchantId: '',
    status: undefined,
    paymentMethod: '',
    startDate: '',
    endDate: ''
  })
  dateRange.value = null
  pagination.page = 1
  handleSearch()
}

// 日期范围变化
const handleDateChange = (dates: [string, string] | null) => {
  if (dates) {
    searchForm.startDate = dates[0]
    searchForm.endDate = dates[1]
  } else {
    searchForm.startDate = ''
    searchForm.endDate = ''
  }
}

// 刷新
const handleRefresh = () => {
  loadStats()
  loadRechargeList()
}

// 选择变化
const handleSelectionChange = (selection: any[]) => {
  console.log('Selection changed:', selection)
}

// 审核
const handleAudit = (row: any, status: number) => {
  Object.assign(auditForm, {
    id: row.id,
    amount: row.amount,
    paymentMethod: row.paymentMethod,
    paymentReference: row.paymentReference,
    remark: row.remark,
    status,
    auditReason: ''
  })
  auditDialogVisible.value = true
}

// 提交审核
const handleSubmitAudit = async () => {
  if (!auditForm.auditReason.trim()) {
    ElMessage.warning('请输入审核原因')
    return
  }

  auditLoading.value = true
  try {
    await auditRecharge({
      id: auditForm.id,
      status: auditForm.status,
      auditReason: auditForm.auditReason
    })

    ElMessage.success(auditForm.status === 1 ? '审核通过成功' : '审核拒绝成功')
    auditDialogVisible.value = false
    
    // 刷新数据
    loadStats()
    loadRechargeList()
  } catch (error: any) {
    ElMessage.error(error.message || '审核失败')
  } finally {
    auditLoading.value = false
  }
}

// 查看详情
const handleViewDetail = (row: any) => {
  currentRecharge.value = row
  detailDialogVisible.value = true
}

onMounted(() => {
  loadStats()
  loadRechargeList()
})
</script>

<style scoped>
.recharge-audit {
  padding: 20px;
}

.stats-row {
  margin-bottom: 20px;
}

.stat-card {
  cursor: pointer;
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 20px;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 28px;
  font-weight: bold;
  color: #333;
}

.stat-label {
  font-size: 14px;
  color: #999;
  margin-top: 5px;
}

.filter-card {
  margin-bottom: 20px;
}

.search-form {
  margin-bottom: 0;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.merchant-info {
  line-height: 1.4;
}

.merchant-name {
  font-weight: 500;
  color: #333;
}

.merchant-uid {
  font-size: 12px;
  color: #999;
}

.amount {
  font-weight: 500;
  color: #67C23A;
}

.audit-info {
  font-size: 12px;
  line-height: 1.4;
  color: #666;
}

.pending-text {
  color: #E6A23C;
  font-style: italic;
}

.recharge-detail {
  padding: 10px 0;
}
</style>
