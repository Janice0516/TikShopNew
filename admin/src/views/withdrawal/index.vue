<template>
  <div class="withdrawal-management">
    <div class="page-header">
      <h2>商户提现管理</h2>
    </div>

    <!-- 搜索表单 -->
    <el-card class="search-card" shadow="never">
      <el-form :model="queryForm" :inline="true" class="search-form">
        <el-form-item label="商户ID">
          <el-input
            v-model="queryForm.merchantId"
            placeholder="请输入商户ID"
            clearable
            style="width: 150px;"
          />
        </el-form-item>
        <el-form-item label="提现状态">
          <el-select
            v-model="queryForm.status"
            placeholder="请选择"
            clearable
            style="width: 150px;"
          >
            <el-option label="全部" :value="'all'" />
            <el-option label="待审核" :value="0" />
            <el-option label="已通过" :value="1" />
            <el-option label="已拒绝" :value="2" />
            <el-option label="已打款" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item label="申请时间">
          <el-date-picker
            v-model="queryForm.dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            style="width: 240px;"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" border stripe>
        <el-table-column prop="id" label="提现ID" width="80" />
        <el-table-column prop="merchantId" label="商户ID" width="100" />
        <el-table-column prop="withdrawalAmount" label="提现金额" width="120">
          <template #default="scope">
            RM{{ scope.row.withdrawalAmount }}
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
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="scope">
            <el-button 
              v-if="scope.row.status === 0" 
              type="success" 
              link 
              @click="handleApprove(scope.row)"
            >
              通过
            </el-button>
            <el-button 
              v-if="scope.row.status === 0" 
              type="danger" 
              link 
              @click="handleReject(scope.row)"
            >
              拒绝
            </el-button>
            <el-button 
              v-if="scope.row.status === 1" 
              type="primary" 
              link 
              @click="handleComplete(scope.row)"
            >
              标记打款
            </el-button>
            <el-button type="info" link @click="handleView(scope.row)">查看</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>

    <!-- 查看详情对话框 -->
    <el-dialog
      v-model="detailDialogVisible"
      title="提现详情"
      width="600px"
      :close-on-click-modal="false"
    >
      <div v-if="currentWithdrawal" class="withdrawal-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="提现ID">{{ currentWithdrawal.id }}</el-descriptions-item>
          <el-descriptions-item label="商户ID">{{ currentWithdrawal.merchantId }}</el-descriptions-item>
          <el-descriptions-item label="提现金额">RM{{ currentWithdrawal.withdrawalAmount }}</el-descriptions-item>
          <el-descriptions-item label="银行名称">{{ currentWithdrawal.bankName }}</el-descriptions-item>
          <el-descriptions-item label="银行账号">{{ currentWithdrawal.bankAccount }}</el-descriptions-item>
          <el-descriptions-item label="账户持有人">{{ currentWithdrawal.accountHolder }}</el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="getStatusType(currentWithdrawal.status)">
              {{ getStatusText(currentWithdrawal.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="申请时间">{{ formatTime(currentWithdrawal.createTime) }}</el-descriptions-item>
          <el-descriptions-item label="处理时间" :span="2">
            {{ currentWithdrawal.processedAt ? formatTime(currentWithdrawal.processedAt) : '-' }}
          </el-descriptions-item>
          <el-descriptions-item label="备注" :span="2">
            {{ currentWithdrawal.remark || '-' }}
          </el-descriptions-item>
          <el-descriptions-item label="管理员备注" :span="2">
            {{ currentWithdrawal.adminRemark || '-' }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      <template #footer>
        <el-button @click="detailDialogVisible = false">关闭</el-button>
      </template>
    </el-dialog>

    <!-- 处理提现对话框 -->
    <el-dialog
      v-model="processDialogVisible"
      :title="processDialogTitle"
      width="500px"
      :close-on-click-modal="false"
    >
      <el-form :model="processForm" :rules="processRules" ref="processFormRef" label-width="100px">
        <el-form-item label="处理结果">
          <el-radio-group v-model="processForm.status">
            <el-radio :label="1">通过</el-radio>
            <el-radio :label="2">拒绝</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="管理员备注" prop="adminRemark">
          <el-input
            v-model="processForm.adminRemark"
            type="textarea"
            :rows="4"
            placeholder="请输入处理备注"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="processDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleProcessConfirm">确认</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import type { FormInstance } from 'element-plus'
import { getWithdrawalList, updateWithdrawalStatus } from '@/api/withdrawal'

// 响应式数据
const loading = ref(false)
const tableData = ref<any[]>([])
const detailDialogVisible = ref(false)
const processDialogVisible = ref(false)
const processDialogTitle = ref('')
const currentWithdrawal = ref<any>(null)
const processFormRef = ref<FormInstance>()

// 查询表单
const queryForm = reactive({
  merchantId: '',
  status: 'all',
  dateRange: null,
})

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0,
})

// 处理表单
const processForm = reactive({
  status: 1,
  adminRemark: '',
})

// 表单验证规则
const processRules = {
  adminRemark: [
    { required: true, message: '请输入管理员备注', trigger: 'blur' }
  ]
}

// 获取提现列表
const getWithdrawalListData = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.page,
      pageSize: pagination.pageSize,
    }
    
    if (queryForm.merchantId) {
      params.merchantId = queryForm.merchantId
    }
    if (queryForm.status !== 'all') {
      params.status = queryForm.status
    }
    if (queryForm.dateRange && queryForm.dateRange.length === 2) {
      params.startDate = queryForm.dateRange[0]
      params.endDate = queryForm.dateRange[1]
    }

    const res = await getWithdrawalList(params)
    
    if (res.data && res.data.data) {
      tableData.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      tableData.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('获取提现列表失败:', error)
    ElMessage.error('获取提现列表失败')
    tableData.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 查询
const handleQuery = () => {
  pagination.page = 1
  getWithdrawalListData()
}

// 重置
const handleReset = () => {
  queryForm.merchantId = ''
  queryForm.status = 'all'
  queryForm.dateRange = null
  pagination.page = 1
  getWithdrawalListData()
}

// 分页大小改变
const handleSizeChange = (val: number) => {
  pagination.pageSize = val
  pagination.page = 1
  getWithdrawalListData()
}

// 当前页改变
const handleCurrentChange = (val: number) => {
  pagination.page = val
  getWithdrawalListData()
}

// 查看详情
const handleView = (row: any) => {
  currentWithdrawal.value = row
  detailDialogVisible.value = true
}

// 通过
const handleApprove = (row: any) => {
  processForm.status = 1
  processForm.adminRemark = ''
  currentWithdrawal.value = row
  processDialogTitle.value = '通过提现申请'
  processDialogVisible.value = true
}

// 拒绝
const handleReject = (row: any) => {
  processForm.status = 2
  processForm.adminRemark = ''
  currentWithdrawal.value = row
  processDialogTitle.value = '拒绝提现申请'
  processDialogVisible.value = true
}

// 标记打款
const handleComplete = async (row: any) => {
  try {
    await ElMessageBox.confirm('确认标记为已打款？', '提示', {
      confirmButtonText: '确认',
      cancelButtonText: '取消',
      type: 'warning',
    })

    // 调用真实API
    await updateWithdrawalStatus(row.id, {
      status: 3,
      adminRemark: '已打款'
    })
    
    ElMessage.success('标记打款成功')
    getWithdrawalListData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('标记打款失败:', error)
      ElMessage.error('标记打款失败')
    }
  }
}

// 确认处理
const handleProcessConfirm = async () => {
  if (!processFormRef.value) return
  
  try {
    await processFormRef.value.validate()
    
    if (currentWithdrawal.value) {
      await updateWithdrawalStatus(currentWithdrawal.value.id, {
        status: processForm.status,
        adminRemark: processForm.adminRemark
      })
    }
    
    ElMessage.success('处理成功')
    processDialogVisible.value = false
    getWithdrawalListData()
  } catch (error) {
    console.error('处理失败:', error)
    ElMessage.error('处理失败')
  }
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
  const typeMap: { [key: number]: string } = {
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
  getWithdrawalListData()
})
</script>

<style scoped>
.withdrawal-management {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0;
  color: #303133;
}

.search-card {
  margin-bottom: 20px;
}

.search-form {
  margin-bottom: 0;
}

.table-card {
  margin-bottom: 20px;
}

.pagination-container {
  margin-top: 20px;
  text-align: right;
}

.withdrawal-detail {
  padding: 20px 0;
}
</style>
