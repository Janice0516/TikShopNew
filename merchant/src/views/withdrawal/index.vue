<template>
  <div class="withdrawal-page">
    <div class="page-header">
      <h2>提现申请</h2>
      <p class="page-desc">申请将您的账户余额提现到指定银行账户</p>
    </div>

    <!-- 账户余额信息 -->
    <el-card class="balance-card" shadow="never">
      <div class="balance-info">
        <div class="balance-item">
          <span class="label">可提现余额：</span>
          <span class="amount">¥{{ availableBalance.toFixed(2) }}</span>
        </div>
        <div class="balance-item">
          <span class="label">冻结金额：</span>
          <span class="amount frozen">¥{{ frozenAmount.toFixed(2) }}</span>
        </div>
      </div>
    </el-card>

    <!-- 提现表单 -->
    <el-card class="form-card" shadow="never">
      <el-form
        ref="withdrawalFormRef"
        :model="withdrawalForm"
        :rules="withdrawalRules"
        label-width="120px"
        class="withdrawal-form"
      >
        <el-form-item label="提现金额" prop="withdrawalAmount">
          <el-input
            v-model="withdrawalForm.withdrawalAmount"
            type="number"
            placeholder="请输入提现金额"
            :max="availableBalance"
            style="width: 200px;"
          >
            <template #append>元</template>
          </el-input>
          <div class="form-tip">
            最小提现金额：¥{{ minWithdrawalAmount }}，最大提现金额：¥{{ availableBalance.toFixed(2) }}
          </div>
        </el-form-item>

        <el-form-item label="银行名称" prop="bankName">
          <el-select
            v-model="withdrawalForm.bankName"
            placeholder="请选择银行"
            style="width: 200px;"
          >
            <el-option label="中国银行" value="中国银行" />
            <el-option label="工商银行" value="工商银行" />
            <el-option label="建设银行" value="建设银行" />
            <el-option label="农业银行" value="农业银行" />
            <el-option label="招商银行" value="招商银行" />
            <el-option label="交通银行" value="交通银行" />
            <el-option label="民生银行" value="民生银行" />
            <el-option label="兴业银行" value="兴业银行" />
            <el-option label="浦发银行" value="浦发银行" />
            <el-option label="中信银行" value="中信银行" />
          </el-select>
        </el-form-item>

        <el-form-item label="银行账号" prop="bankAccount">
          <el-input
            v-model="withdrawalForm.bankAccount"
            placeholder="请输入银行账号"
            style="width: 300px;"
            maxlength="25"
          />
          <div class="form-tip">
            请输入正确的银行账号，提现将转入此账户
          </div>
        </el-form-item>

        <el-form-item label="账户持有人" prop="accountHolder">
          <el-input
            v-model="withdrawalForm.accountHolder"
            placeholder="请输入账户持有人姓名"
            style="width: 200px;"
            maxlength="20"
          />
        </el-form-item>

        <el-form-item label="备注" prop="remark">
          <el-input
            v-model="withdrawalForm.remark"
            type="textarea"
            :rows="3"
            placeholder="请输入备注信息（可选）"
            style="width: 400px;"
            maxlength="200"
            show-word-limit
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSubmit" :loading="submitting">
            提交申请
          </el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 提现记录 -->
    <el-card class="records-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>提现记录</span>
          <el-button type="primary" size="small" @click="getWithdrawalRecords">
            刷新
          </el-button>
        </div>
      </template>

      <el-table :data="withdrawalRecords" v-loading="recordsLoading" border stripe>
        <el-table-column prop="id" label="申请ID" width="80" />
        <el-table-column prop="withdrawalAmount" label="提现金额" width="120">
          <template #default="scope">
            ¥{{ scope.row.withdrawalAmount }}
          </template>
        </el-table-column>
        <el-table-column prop="bankName" label="银行名称" width="120" />
        <el-table-column prop="bankAccount" label="银行账号" width="180" />
        <el-table-column prop="accountHolder" label="账户持有人" width="120" />
        <el-table-column prop="status" label="状态" width="100">
          <template #default="scope">
            <el-tag :type="getStatusType(scope.row.status)">
              {{ getStatusText(scope.row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="申请时间" width="180">
          <template #default="scope">
            {{ formatTime(scope.row.createTime) }}
          </template>
        </el-table-column>
        <el-table-column prop="processedAt" label="处理时间" width="180">
          <template #default="scope">
            {{ scope.row.processedAt ? formatTime(scope.row.processedAt) : '-' }}
          </template>
        </el-table-column>
        <el-table-column prop="adminRemark" label="处理备注" min-width="150">
          <template #default="scope">
            {{ scope.row.adminRemark || '-' }}
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[5, 10, 20]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import type { FormInstance } from 'element-plus'
import { createWithdrawal, getMerchantWithdrawals, getMerchantBalance } from '@/api/withdrawal'

// 响应式数据
const withdrawalFormRef = ref<FormInstance>()
const submitting = ref(false)
const recordsLoading = ref(false)
const withdrawalRecords = ref([])

// 账户信息
const availableBalance = ref(5000.00) // 可提现余额
const frozenAmount = ref(1000.00) // 冻结金额
const minWithdrawalAmount = ref(100) // 最小提现金额

// 获取商户余额信息
const getBalanceInfo = async () => {
  try {
    const res = await getMerchantBalance()
    if (res.data && res.data.data) {
      availableBalance.value = res.data.data.availableBalance || 0
      frozenAmount.value = res.data.data.frozenBalance || 0
      minWithdrawalAmount.value = res.data.data.minWithdrawalAmount || 100
    }
  } catch (error) {
    console.error('获取余额信息失败:', error)
    // 使用默认值
  }
}

// 提现表单
const withdrawalForm = reactive({
  withdrawalAmount: '',
  bankName: '',
  bankAccount: '',
  accountHolder: '',
  remark: ''
})

// 表单验证规则
const withdrawalRules = {
  withdrawalAmount: [
    { required: true, message: '请输入提现金额', trigger: 'blur' },
    { 
      validator: (rule: any, value: any, callback: any) => {
        const amount = parseFloat(value)
        if (isNaN(amount) || amount <= 0) {
          callback(new Error('提现金额必须大于0'))
        } else if (amount < minWithdrawalAmount) {
          callback(new Error(`提现金额不能少于${minWithdrawalAmount}元`))
        } else if (amount > availableBalance.value) {
          callback(new Error('提现金额不能超过可提现余额'))
        } else {
          callback()
        }
      }, 
      trigger: 'blur' 
    }
  ],
  bankName: [
    { required: true, message: '请选择银行', trigger: 'change' }
  ],
  bankAccount: [
    { required: true, message: '请输入银行账号', trigger: 'blur' },
    { 
      pattern: /^[0-9]{16,25}$/, 
      message: '请输入正确的银行账号（16-25位数字）', 
      trigger: 'blur' 
    }
  ],
  accountHolder: [
    { required: true, message: '请输入账户持有人姓名', trigger: 'blur' },
    { min: 2, max: 20, message: '姓名长度在2-20个字符', trigger: 'blur' }
  ]
}

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 提交提现申请
const handleSubmit = async () => {
  if (!withdrawalFormRef.value) return
  
  try {
    await withdrawalFormRef.value.validate()
    
    await ElMessageBox.confirm(
      `确认提现 ¥${withdrawalForm.withdrawalAmount} 到 ${withdrawalForm.bankName} 账户 ${withdrawalForm.bankAccount}？`,
      '确认提现',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
    )

    submitting.value = true
    
    await createWithdrawal({
      withdrawalAmount: parseFloat(withdrawalForm.withdrawalAmount),
      bankName: withdrawalForm.bankName,
      bankAccount: withdrawalForm.bankAccount,
      accountHolder: withdrawalForm.accountHolder,
      remark: withdrawalForm.remark
    })

    ElMessage.success('提现申请提交成功，请等待审核')
    handleReset()
    getWithdrawalRecords()
    
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('提现申请失败:', error)
      ElMessage.error('提现申请失败，请重试')
    }
  } finally {
    submitting.value = false
  }
}

// 重置表单
const handleReset = () => {
  withdrawalForm.withdrawalAmount = ''
  withdrawalForm.bankName = ''
  withdrawalForm.bankAccount = ''
  withdrawalForm.accountHolder = ''
  withdrawalForm.remark = ''
  withdrawalFormRef.value?.clearValidate()
}

// 获取提现记录
const getWithdrawalRecords = async () => {
  recordsLoading.value = true
  try {
    const params = {
      page: pagination.page,
      pageSize: pagination.pageSize
    }

    const res = await getMerchantWithdrawals(params)
    
    if (res.data && res.data.data) {
      withdrawalRecords.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      // 如果API不可用，使用模拟数据
      const mockData = {
        list: [
          {
            id: 1,
            withdrawalAmount: 1000.00,
            bankName: '中国银行',
            bankAccount: '6217000000000000000',
            accountHolder: '张三',
            status: 0,
            remark: '急需用钱',
            adminRemark: '',
            processedAt: null,
            createTime: '2024-01-08 10:00:00',
          },
          {
            id: 2,
            withdrawalAmount: 2000.00,
            bankName: '工商银行',
            bankAccount: '6222020000000000000',
            accountHolder: '张三',
            status: 1,
            remark: '正常提现',
            adminRemark: '审核通过',
            processedAt: '2024-01-08 11:00:00',
            createTime: '2024-01-08 09:00:00',
          },
        ],
        total: 2,
      }
      withdrawalRecords.value = mockData.list
      pagination.total = mockData.total
    }
  } catch (error) {
    console.error('获取提现记录失败:', error)
    ElMessage.error('获取提现记录失败')
    
    // 使用模拟数据作为后备
    const mockData = {
      list: [
        {
          id: 1,
          withdrawalAmount: 1000.00,
          bankName: '中国银行',
          bankAccount: '6217000000000000000',
          accountHolder: '张三',
          status: 0,
          remark: '急需用钱',
          adminRemark: '',
          processedAt: null,
          createTime: '2024-01-08 10:00:00',
        },
        {
          id: 2,
          withdrawalAmount: 2000.00,
          bankName: '工商银行',
          bankAccount: '6222020000000000000',
          accountHolder: '张三',
          status: 1,
          remark: '正常提现',
          adminRemark: '审核通过',
          processedAt: '2024-01-08 11:00:00',
          createTime: '2024-01-08 09:00:00',
        },
      ],
      total: 2,
    }
    withdrawalRecords.value = mockData.list
    pagination.total = mockData.total
  } finally {
    recordsLoading.value = false
  }
}

// 分页大小改变
const handleSizeChange = (val: number) => {
  pagination.pageSize = val
  pagination.page = 1
  getWithdrawalRecords()
}

// 当前页改变
const handleCurrentChange = (val: number) => {
  pagination.page = val
  getWithdrawalRecords()
}

// 获取状态文本
const getStatusText = (status: number) => {
  const statusMap = {
    0: '待审核',
    1: '已通过',
    2: '已拒绝',
    3: '已打款',
  }
  return statusMap[status] || '未知'
}

// 获取状态类型
const getStatusType = (status: number) => {
  const typeMap = {
    0: 'warning',
    1: 'success',
    2: 'danger',
    3: 'info',
  }
  return typeMap[status] || 'info'
}

// 格式化时间
const formatTime = (time: string) => {
  if (!time) return '-'
  return new Date(time).toLocaleString('zh-CN')
}

// 组件挂载时获取数据
onMounted(() => {
  getBalanceInfo()
  getWithdrawalRecords()
})
</script>

<style scoped>
.withdrawal-page {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.page-desc {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.balance-card {
  margin-bottom: 20px;
}

.balance-info {
  display: flex;
  gap: 40px;
}

.balance-item {
  display: flex;
  align-items: center;
}

.balance-item .label {
  color: #606266;
  margin-right: 8px;
}

.balance-item .amount {
  font-size: 18px;
  font-weight: bold;
  color: #67c23a;
}

.balance-item .amount.frozen {
  color: #e6a23c;
}

.form-card {
  margin-bottom: 20px;
}

.withdrawal-form {
  max-width: 600px;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.records-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pagination-container {
  margin-top: 20px;
  text-align: right;
}
</style>
