<template>
  <div class="user-management">
    <!-- 搜索区域 -->
    <el-card class="search-card">
      <el-form :model="queryForm" :inline="true" class="search-form">
        <el-form-item label="手机号">
          <el-input
            v-model="queryForm.phone"
            placeholder="请输入手机号"
            clearable
            style="width: 200px"
          />
        </el-form-item>
        <el-form-item label="昵称">
          <el-input
            v-model="queryForm.nickname"
            placeholder="请输入昵称"
            clearable
            style="width: 200px"
          />
        </el-form-item>
        <el-form-item label="状态">
          <el-select
            v-model="queryForm.status"
            placeholder="请选择状态"
            clearable
            style="width: 150px"
          >
            <el-option label="全部" value="" />
            <el-option label="正常" :value="1" />
            <el-option label="禁用" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item label="注册时间">
          <el-date-picker
            v-model="queryForm.dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            style="width: 240px"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery" :loading="loading">
            <el-icon><Search /></el-icon>
            查询
          </el-button>
          <el-button @click="handleReset">
            <el-icon><Refresh /></el-icon>
            重置
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 数据表格 -->
    <el-card class="table-card">
      <template #header>
        <div class="card-header">
          <span>用户列表</span>
          <div class="header-actions">
            <el-button type="primary" @click="handleRefresh">
              <el-icon><Refresh /></el-icon>
              刷新
            </el-button>
          </div>
        </div>
      </template>

      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        style="width: 100%"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="phone" label="手机号" width="120" />
        <el-table-column prop="nickname" label="昵称" width="120">
          <template #default="{ row }">
            <span v-if="row.nickname">{{ row.nickname }}</span>
            <span v-else class="text-gray-400">未设置</span>
          </template>
        </el-table-column>
        <el-table-column prop="avatar" label="头像" width="80">
          <template #default="{ row }">
            <el-avatar
              v-if="row.avatar"
              :src="row.avatar"
              :size="40"
              shape="circle"
            />
            <el-avatar v-else :size="40" shape="circle">
              <el-icon><User /></el-icon>
            </el-avatar>
          </template>
        </el-table-column>
        <el-table-column prop="gender" label="性别" width="80">
          <template #default="{ row }">
            <el-tag :type="getGenderTagType(row.gender)">
              {{ getGenderText(row.gender) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="getStatusTagType(row.status)">
              {{ getStatusText(row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="lastLoginTime" label="最后登录" width="160">
          <template #default="{ row }">
            <span v-if="row.lastLoginTime">{{ formatDate(row.lastLoginTime) }}</span>
            <span v-else class="text-gray-400">从未登录</span>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="注册时间" width="160">
          <template #default="{ row }">
            {{ formatDate(row.createTime) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              size="small"
              @click="handleView(row)"
            >
              查看
            </el-button>
            <el-button
              v-if="row.status === 1"
              type="warning"
              size="small"
              @click="handleDisable(row)"
            >
              禁用
            </el-button>
            <el-button
              v-else
              type="success"
              size="small"
              @click="handleEnable(row)"
            >
              启用
            </el-button>
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

    <!-- 用户详情对话框 -->
    <el-dialog
      v-model="detailDialogVisible"
      title="用户详情"
      width="600px"
      :close-on-click-modal="false"
    >
      <div v-if="currentUser" class="user-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="用户ID">
            {{ currentUser.id }}
          </el-descriptions-item>
          <el-descriptions-item label="手机号">
            {{ currentUser.phone }}
          </el-descriptions-item>
          <el-descriptions-item label="昵称">
            {{ currentUser.nickname || '未设置' }}
          </el-descriptions-item>
          <el-descriptions-item label="性别">
            {{ getGenderText(currentUser.gender) }}
          </el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="getStatusTagType(currentUser.status)">
              {{ getStatusText(currentUser.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="最后登录时间">
            {{ currentUser.lastLoginTime ? formatDate(currentUser.lastLoginTime) : '从未登录' }}
          </el-descriptions-item>
          <el-descriptions-item label="注册时间" :span="2">
            {{ formatDate(currentUser.createTime) }}
          </el-descriptions-item>
        </el-descriptions>
        
        <div v-if="currentUser.avatar" class="avatar-section">
          <h4>头像</h4>
          <el-avatar :src="currentUser.avatar" :size="100" shape="circle" />
        </div>
      </div>
      
      <template #footer>
        <el-button @click="detailDialogVisible = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, User } from '@element-plus/icons-vue'
import { getUserList, updateUserStatus } from '@/api/user'

// 响应式数据
const loading = ref(false)
const tableData = ref<any[]>([])
const selectedRows = ref([])
const detailDialogVisible = ref(false)
const currentUser = ref<any>(null)

// 查询表单
const queryForm = reactive({
  phone: '',
  nickname: '',
  status: '',
  dateRange: null
})

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

// 获取用户列表
const getUserListData = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.page,
      pageSize: pagination.pageSize,
    }
    
    if (queryForm.phone) {
      params.phone = queryForm.phone
    }
    if (queryForm.nickname) {
      params.nickname = queryForm.nickname
    }
    if (queryForm.status !== '') {
      params.status = queryForm.status
    }
    if (queryForm.dateRange && queryForm.dateRange.length === 2) {
      params.startDate = queryForm.dateRange[0]
      params.endDate = queryForm.dateRange[1]
    }

    const res = await getUserList(params)
    
    if (res.data && res.data.data) {
      tableData.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      tableData.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('获取用户列表失败:', error)
    ElMessage.error('获取用户列表失败')
    tableData.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 查询
const handleQuery = () => {
  pagination.page = 1
  getUserListData()
}

// 重置
const handleReset = () => {
  queryForm.phone = ''
  queryForm.nickname = ''
  queryForm.status = ''
  queryForm.dateRange = null
  pagination.page = 1
  getUserListData()
}

// 刷新
const handleRefresh = () => {
  getUserListData()
}

// 分页大小改变
const handleSizeChange = (val: number) => {
  pagination.pageSize = val
  pagination.page = 1
  getUserListData()
}

// 当前页改变
const handleCurrentChange = (val: number) => {
  pagination.page = val
  getUserListData()
}

// 选择改变
const handleSelectionChange = (selection: any[]) => {
  selectedRows.value = selection
}

// 查看详情
const handleView = (row: any) => {
  currentUser.value = row
  detailDialogVisible.value = true
}

// 禁用用户
const handleDisable = async (row: any) => {
  try {
    await ElMessageBox.confirm(
      `确认禁用用户 ${row.phone}？`,
      '确认禁用',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
    )

    await updateUserStatus(row.id, { status: 0 })
    ElMessage.success('禁用成功')
    getUserListData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('禁用失败:', error)
      ElMessage.error('禁用失败')
    }
  }
}

// 启用用户
const handleEnable = async (row: any) => {
  try {
    await ElMessageBox.confirm(
      `确认启用用户 ${row.phone}？`,
      '确认启用',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
    )

    await updateUserStatus(row.id, { status: 1 })
    ElMessage.success('启用成功')
    getUserListData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('启用失败:', error)
      ElMessage.error('启用失败')
    }
  }
}

// 获取性别标签类型
const getGenderTagType = (gender: number) => {
  switch (gender) {
    case 1: return 'primary'
    case 2: return 'danger'
    default: return 'info'
  }
}

// 获取性别文本
const getGenderText = (gender: number) => {
  switch (gender) {
    case 1: return '男'
    case 2: return '女'
    default: return '未知'
  }
}

// 获取状态标签类型
const getStatusTagType = (status: number) => {
  switch (status) {
    case 1: return 'success'
    case 0: return 'danger'
    default: return 'info'
  }
}

// 获取状态文本
const getStatusText = (status: number) => {
  switch (status) {
    case 1: return '正常'
    case 0: return '禁用'
    default: return '未知'
  }
}

// 格式化日期
const formatDate = (date: string | Date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleString('zh-CN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// 初始化
onMounted(() => {
  getUserListData()
})
</script>

<style scoped>
.user-management {
  padding: 20px;
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

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.pagination-container {
  margin-top: 20px;
  display: flex;
  justify-content: center;
}

.user-detail {
  padding: 20px 0;
}

.avatar-section {
  margin-top: 20px;
  text-align: center;
}

.avatar-section h4 {
  margin-bottom: 10px;
  color: #606266;
}

.text-gray-400 {
  color: #9ca3af;
}
</style>
