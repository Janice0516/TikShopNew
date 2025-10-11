<template>
  <div class="withdraw">
    <!-- 账户余额 -->
    <el-card class="balance-card">
      <template #header>
        <span>{{ $t('finance.accountBalance') }}</span>
      </template>
      
      <div class="balance-info">
        <div class="balance-item">
          <div class="balance-label">{{ $t('finance.availableBalance') }}</div>
          <div class="balance-value">${{ availableBalance }}</div>
        </div>
        <div class="balance-item">
          <div class="balance-label">{{ $t('finance.frozenAmount') }}</div>
          <div class="balance-value frozen">${{ frozenAmount }}</div>
        </div>
      </div>
    </el-card>

    <!-- 提现申请 -->
    <el-card>
      <template #header>
        <span>{{ $t('finance.withdrawNow') }}</span>
      </template>

      <el-form
        ref="withdrawFormRef"
        :model="withdrawForm"
        :rules="withdrawRules"
        label-width="150px"
        class="withdraw-form"
      >
        <el-form-item :label="$t('finance.withdrawAmount')" prop="amount">
          <el-input-number
            v-model="withdrawForm.amount"
            :min="minWithdrawAmount"
            :max="parseFloat(availableBalance)"
            :precision="2"
            :placeholder="$t('finance.withdrawAmount')"
            style="width: 200px"
            @change="calculateFee"
          />
          <span style="margin-left: 10px; color: #999;">USD</span>
          <div class="form-tip">
            {{ $t('finance.minimumWithdraw') }} ${{ minWithdrawAmount }}
          </div>
        </el-form-item>

        <el-form-item :label="$t('finance.bankName')" prop="bankName">
          <el-input
            v-model="withdrawForm.bankName"
            :placeholder="$t('finance.bankName')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item :label="$t('finance.bankAccount')" prop="bankAccount">
          <el-input
            v-model="withdrawForm.bankAccount"
            :placeholder="$t('finance.bankAccount')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item :label="$t('finance.accountName')" prop="accountName">
          <el-input
            v-model="withdrawForm.accountName"
            :placeholder="$t('finance.accountName')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleWithdraw" :loading="submitting">
            {{ $t('finance.submitWithdraw') }}
          </el-button>
          <el-button @click="handleReset">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>

      <!-- 费用计算 -->
      <el-card class="fee-card" v-if="withdrawForm.amount > 0">
        <template #header>
          <span>{{ $t('finance.feeCalculation') }}</span>
        </template>
        
        <div class="fee-details">
          <div class="fee-item">
            <span class="fee-label">{{ $t('finance.withdrawAmount') }}:</span>
            <span class="fee-value">${{ withdrawForm.amount || '0.00' }}</span>
          </div>
          <div class="fee-item">
            <span class="fee-label">{{ $t('finance.withdrawFee') }}:</span>
            <span class="fee-value">${{ withdrawFee }}</span>
          </div>
          <el-divider />
          <div class="fee-item total">
            <span class="fee-label">{{ $t('finance.actualAmount') }}:</span>
            <span class="fee-value">${{ actualAmount }}</span>
          </div>
        </div>
      </el-card>
    </el-card>

    <!-- 提现记录 -->
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('finance.withdrawHistory') }}</span>
          <el-button @click="handleRefresh" :loading="loading">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.refresh') }}
          </el-button>
        </div>
      </template>

      <el-table
        :data="withdrawList"
        style="width: 100%"
        v-loading="loading"
      >
        <el-table-column prop="id" label="ID" width="80" />
        
        <el-table-column :label="$t('finance.withdrawAmount')" width="120">
          <template #default="{ row }">
            <span style="color: #409EFF; font-weight: 500">${{ row.amount }}</span>
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.withdrawFee')" width="120">
          <template #default="{ row }">
            ${{ row.fee }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.actualAmount')" width="120">
          <template #default="{ row }">
            <span style="color: #67C23A; font-weight: 500">${{ row.actualAmount }}</span>
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.bankName')" width="150">
          <template #default="{ row }">
            {{ row.bankName }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.bankAccount')" width="200">
          <template #default="{ row }">
            {{ maskBankAccount(row.bankAccount) }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('orders.orderStatus')" width="120">
          <template #default="{ row }">
            <el-tag :type="getStatusTagType(row.status)">
              {{ getStatusName(row.status) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('finance.transactionTime')" width="180">
          <template #default="{ row }">
            {{ row.createTime }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('common.details')" min-width="200">
          <template #default="{ row }">
            {{ row.remark || '-' }}
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
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getWithdrawHistory } from '@/api/finance'

const { t } = useI18n()

const loading = ref(false)
const submitting = ref(false)
const withdrawFormRef = ref()

const availableBalance = ref('2,456.78')
const frozenAmount = ref('123.45')
const minWithdrawAmount = ref(50.00)

const withdrawForm = reactive({
  amount: 0,
  bankName: '',
  bankAccount: '',
  accountName: ''
})

const withdrawRules = {
  amount: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { type: 'number', min: minWithdrawAmount.value, message: () => t('finance.minimumWithdraw') + ` $${minWithdrawAmount.value}`, trigger: 'blur' }
  ],
  bankName: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  bankAccount: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  accountName: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ]
}

const withdrawList = ref<any[]>([])

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 计算提现手续费 (2%)
const withdrawFee = computed(() => {
  if (!withdrawForm.amount) return '0.00'
  const fee = withdrawForm.amount * 0.02
  return fee.toFixed(2)
})

// 计算实际到账金额
const actualAmount = computed(() => {
  if (!withdrawForm.amount) return '0.00'
  const actual = withdrawForm.amount - parseFloat(withdrawFee.value)
  return actual.toFixed(2)
})

// 计算手续费
const calculateFee = () => {
  // 触发计算
}

// 获取状态标签样式
const getStatusTagType = (status: number) => {
  switch (status) {
    case 0: return 'warning'  // 待审核
    case 1: return 'success'  // 已通过
    case 2: return 'danger'   // 已拒绝
    case 3: return 'info'     // 已完成
    default: return 'info'
  }
}

// 获取状态名称
const getStatusName = (status: number) => {
  switch (status) {
    case 0: return t('finance.pendingReview')
    case 1: return t('finance.approved')
    case 2: return t('finance.rejected')
    case 3: return t('finance.completed')
    default: return '-'
  }
}

// 脱敏银行卡号
const maskBankAccount = (account: string) => {
  if (!account) return '-'
  if (account.length <= 8) return account
  return account.substring(0, 4) + '****' + account.substring(account.length - 4)
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
      pageSize: pagination.pageSize
    }
    
    const res = await getWithdrawHistory(params)
    
    if (res.data && res.data.data) {
      withdrawList.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      withdrawList.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('Failed to fetch withdraw history:', error)
    ElMessage.error(t('message.loadFailed'))
    withdrawList.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  withdrawForm.amount = 0
  withdrawForm.bankName = ''
  withdrawForm.bankAccount = ''
  withdrawForm.accountName = ''
}

// 提现
const handleWithdraw = async () => {
  if (!withdrawFormRef.value) return
  
  await withdrawFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      try {
        await ElMessageBox.confirm(
          `${t('finance.confirmWithdraw')} $${withdrawForm.amount}?`,
          t('common.warning'),
          {
            confirmButtonText: t('common.confirm'),
            cancelButtonText: t('common.cancel'),
            type: 'warning'
          }
        )
        
        submitting.value = true
        
        // 实际API调用
        // await applyWithdraw(withdrawForm)
        
        ElMessage.success(t('finance.withdrawSubmitted'))
        handleReset()
        handleSearch()
      } catch (error: unknown) {
        if (error !== 'cancel') {
          ElMessage.error((error as Error).message || t('message.operationFailed'))
        }
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
.balance-card {
  margin-bottom: 20px;
}

.balance-info {
  display: flex;
  gap: 40px;
}

.balance-item {
  text-align: center;
}

.balance-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.balance-value {
  font-size: 24px;
  font-weight: bold;
  color: #67C23A;
}

.balance-value.frozen {
  color: #E6A23C;
}

.withdraw-form {
  max-width: 600px;
}

.form-tip {
  font-size: 12px;
  color: #999;
  margin-top: 5px;
}

.fee-card {
  margin-top: 20px;
  background-color: #f8f9fa;
}

.fee-details {
  padding: 10px 0;
}

.fee-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.fee-item.total {
  font-weight: bold;
  font-size: 16px;
}

.fee-label {
  color: #666;
}

.fee-value {
  color: #333;
  font-weight: 500;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>

