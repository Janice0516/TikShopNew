<template>
  <div class="earnings">
    <!-- 财务概览 -->
    <el-row :gutter="20" class="stats-row">
      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#67C23A" :size="48"><Money /></el-icon>
            <div class="stat-info">
              <div class="stat-value">RM{{ stats.accountBalance }}</div>
              <div class="stat-label">{{ $t('finance.accountBalance') }}</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#409EFF" :size="48"><TrendCharts /></el-icon>
            <div class="stat-info">
              <div class="stat-value">RM{{ stats.totalEarnings }}</div>
              <div class="stat-label">{{ $t('finance.totalEarnings') }}</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#E6A23C" :size="48"><Wallet /></el-icon>
            <div class="stat-info">
              <div class="stat-value">RM{{ stats.frozenAmount }}</div>
              <div class="stat-label">{{ $t('finance.frozenAmount') }}</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#F56C6C" :size="48"><CreditCard /></el-icon>
            <div class="stat-info">
              <div class="stat-value">RM{{ stats.totalWithdrawn }}</div>
              <div class="stat-label">{{ $t('finance.totalWithdrawn') }}</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 收益统计图表 -->
    <el-card class="chart-card">
      <template #header>
        <div class="card-header">
          <span>{{ $t('finance.earnings') }}</span>
          <el-radio-group v-model="chartPeriod" @change="handlePeriodChange">
            <el-radio-button label="7">{{ $t('common.last7Days') }}</el-radio-button>
            <el-radio-button label="30">{{ $t('common.last30Days') }}</el-radio-button>
            <el-radio-button label="90">{{ $t('common.last90Days') }}</el-radio-button>
          </el-radio-group>
        </div>
      </template>

      <!-- 图表占位 -->
      <div class="chart-placeholder">
        <el-icon :size="64" color="#409EFF"><TrendCharts /></el-icon>
        <p>{{ $t('finance.earningsChart') }}</p>
        <div class="chart-data">
          <div class="data-item">
            <span class="label">{{ $t('finance.todayEarnings') }}:</span>
            <span class="value">RM{{ todayEarnings }}</span>
          </div>
          <div class="data-item">
            <span class="label">{{ $t('finance.weekEarnings') }}:</span>
            <span class="value">RM{{ weekEarnings }}</span>
          </div>
          <div class="data-item">
            <span class="label">{{ $t('finance.monthEarnings') }}:</span>
            <span class="value">RM{{ monthEarnings }}</span>
          </div>
        </div>
      </div>
    </el-card>

    <!-- 资金流水 -->
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('finance.transactionHistory') }}</span>
          <el-button @click="handleRefresh" :loading="loading">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.refresh') }}
          </el-button>
        </div>
      </template>

      <!-- 筛选 -->
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item :label="$t('finance.transactionType')">
          <el-select
            v-model="searchForm.type"
            :placeholder="$t('common.all')"
            clearable
            style="width: 150px"
            @change="handleSearch"
          >
            <el-option :label="$t('common.all')" :value="null" />
            <el-option :label="$t('finance.orderIncome')" :value="'income'" />
            <el-option :label="$t('finance.withdrawExpense')" :value="'withdraw'" />
            <el-option :label="$t('finance.refund')" :value="'refund'" />
          </el-select>
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

      <!-- 流水表格 -->
      <el-table
        :data="flowList"
        style="width: 100%"
        v-loading="loading"
      >
        <el-table-column prop="id" label="ID" width="80" />
        
        <el-table-column :label="$t('finance.transactionType')" width="120">
          <template #default="{ row }">
            <el-tag :type="getTypeTagType(row.type)">
              {{ getTypeName(row.type) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.amount')" width="120">
          <template #default="{ row }">
            <span :class="getAmountClass(row.type)">
              {{ row.type === 'withdraw' ? '-' : '+' }}RM{{ row.amount }}
            </span>
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.balance')" width="120">
          <template #default="{ row }">
            RM{{ row.balance }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.transactionTime')" width="180">
          <template #default="{ row }">
            {{ row.transactionTime }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('common.details')" min-width="200">
          <template #default="{ row }">
            {{ row.description }}
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getFinanceStats, getFundFlow } from '@/api/finance'

const { t } = useI18n()

const loading = ref(false)
const chartPeriod = ref('30')

const stats = ref({
  accountBalance: '0.00',
  totalEarnings: '0.00',
  frozenAmount: '0.00',
  totalWithdrawn: '0.00'
})

const earnings = ref({
  today: '0.00',
  week: '0.00',
  month: '0.00'
})

const flowList = ref<any[]>([])

const searchForm = reactive({
  type: null as string | null
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 计算属性
const todayEarnings = computed(() => earnings.value.today)
const weekEarnings = computed(() => earnings.value.week)
const monthEarnings = computed(() => earnings.value.month)

// 获取类型标签样式
const getTypeTagType = (type: string) => {
  switch (type) {
    case 'income': return 'success'
    case 'withdraw': return 'warning'
    case 'refund': return 'danger'
    default: return 'info'
  }
}

// 获取类型名称
const getTypeName = (type: string) => {
  switch (type) {
    case 'income': return t('finance.orderIncome')
    case 'withdraw': return t('finance.withdrawExpense')
    case 'refund': return t('finance.refund')
    default: return type
  }
}

// 获取金额样式
const getAmountClass = (type: string) => {
  return type === 'withdraw' ? 'amount-negative' : 'amount-positive'
}

// 刷新
const handleRefresh = () => {
  loadStats()
  handleSearch()
}

// 加载统计数据
const loadStats = async () => {
  try {
    const res = await getFinanceStats()
    
    if (res.data && res.data.data) {
      stats.value = res.data.data.stats || stats.value
      earnings.value = res.data.data.earnings || earnings.value
    } else {
      // API返回空数据时保持默认值
      stats.value = {
        accountBalance: '0.00',
        totalEarnings: '0.00',
        frozenAmount: '0.00',
        totalWithdrawn: '0.00'
      }

      earnings.value = {
        today: '0.00',
        week: '0.00',
        month: '0.00'
      }
    }
  } catch (error) {
    console.error('Failed to load stats:', error)
    ElMessage.error(t('message.loadFailed'))
    // 出错时保持默认值
    stats.value = {
      accountBalance: '0.00',
      totalEarnings: '0.00',
      frozenAmount: '0.00',
      totalWithdrawn: '0.00'
    }

    earnings.value = {
      today: '0.00',
      week: '0.00',
      month: '0.00'
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
      type: searchForm.type
    }
    
    const res = await getFundFlow(params)
    
    if (res.data && res.data.data) {
      flowList.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      flowList.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('Failed to fetch fund flow:', error)
    ElMessage.error(t('message.loadFailed'))
    flowList.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  searchForm.type = null
  pagination.page = 1
  handleSearch()
}

// 周期变化
const handlePeriodChange = (period: string) => {
  console.log('Period changed to:', period)
  // 重新加载图表数据
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

.chart-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chart-placeholder {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.chart-placeholder p {
  margin: 20px 0;
  font-size: 16px;
}

.chart-data {
  display: flex;
  justify-content: space-around;
  margin-top: 30px;
}

.data-item {
  text-align: center;
}

.data-item .label {
  display: block;
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.data-item .value {
  display: block;
  font-size: 20px;
  font-weight: bold;
  color: #409EFF;
}

.search-form {
  margin-bottom: 20px;
}

.amount-positive {
  color: #67C23A;
  font-weight: 500;
}

.amount-negative {
  color: #F56C6C;
  font-weight: 500;
}
</style>

