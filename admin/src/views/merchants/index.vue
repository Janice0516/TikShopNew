<template>
  <div class="merchants-page">
    <el-card>
      <!-- 搜索栏 -->
      <el-form :inline="true" :model="queryForm" class="search-form">
        <el-form-item label="用户名">
          <el-input 
            v-model="queryForm.username" 
            placeholder="请输入用户名" 
            clearable
            style="width: 150px;"
          />
        </el-form-item>
        <el-form-item label="商家名称">
          <el-input 
            v-model="queryForm.merchantName" 
            placeholder="请输入商家名称" 
            clearable
            style="width: 150px;"
          />
        </el-form-item>
        <el-form-item label="联系人">
          <el-input 
            v-model="queryForm.contactName" 
            placeholder="请输入联系人" 
            clearable
            style="width: 150px;"
          />
        </el-form-item>
        <el-form-item label="联系电话">
          <el-input 
            v-model="queryForm.contactPhone" 
            placeholder="请输入联系电话" 
            clearable
            style="width: 150px;"
          />
        </el-form-item>
        <el-form-item label="推荐码">
          <el-input 
            v-model="queryForm.inviteCode" 
            placeholder="请输入推荐码" 
            clearable
            style="width: 150px;"
          />
        </el-form-item>
        <el-form-item label="商家状态">
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
            <el-option label="已禁用" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery" :icon="Search">
            {{ $t('common.search') }}
          </el-button>
          <el-button @click="handleReset">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>

      <!-- 表格 -->
      <el-table :data="tableData" v-loading="loading" border stripe>
        <el-table-column prop="username" label="用户名" width="120" />
        <el-table-column prop="merchantUid" label="商家UID" width="140" />
        <el-table-column prop="merchantName" label="商家名称" min-width="150" />
        <el-table-column prop="shopName" label="店铺名称" min-width="150" />
        <el-table-column prop="contactName" label="联系人" width="100" />
        <el-table-column prop="contactPhone" label="联系电话" width="120" />
        <el-table-column prop="inviteCode" label="推荐码" width="120" show-overflow-tooltip />
        <el-table-column prop="salespersonName" label="推荐人" width="100" />
        <el-table-column prop="salespersonPhone" label="推荐人电话" width="120" />
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="getStatusType(row.status)">
              {{ getStatusText(row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="creditRating" label="信用评级" width="120">
          <template #default="{ row }">
            <div v-if="row.creditRating" class="credit-rating-cell">
              <el-tag :type="getCreditLevelType(row.creditRating.level)" size="small">
                {{ row.creditRating.level }}
              </el-tag>
              <div class="credit-score">{{ row.creditRating.score }}</div>
            </div>
            <el-tag v-else type="info" size="small">未评级</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="申请时间" width="180" show-overflow-tooltip />
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <template v-if="row.status === 0">
              <el-button
                type="success"
                link
                @click="handleAudit(row, 1)"
              >
                {{ $t('common.status.approved') }}
              </el-button>
              <el-button
                type="danger"
                link
                @click="handleAudit(row, 2)"
              >
                {{ $t('common.status.rejected') }}
              </el-button>
            </template>
            <el-button
              type="primary"
              link
              @click="handleEdit(row)"
            >
              {{ $t('common.edit') }}
            </el-button>
            <el-button
              type="warning"
              link
              @click="handleResetPassword(row)"
            >
              重置密码
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

    <!-- 编辑商家对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      title="编辑商家信息"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="editFormRef"
        :model="editForm"
        :rules="editFormRules"
        label-width="100px"
      >
        <el-form-item label="商家UID" prop="merchantUid">
          <el-input v-model="editForm.merchantUid" disabled />
        </el-form-item>
        <el-form-item label="用户名" prop="username">
          <el-input v-model="editForm.username" disabled />
        </el-form-item>
        <el-form-item label="商家名称" prop="merchantName">
          <el-input v-model="editForm.merchantName" placeholder="请输入商家名称" />
        </el-form-item>
        <el-form-item label="店铺名称" prop="shopName">
          <el-input v-model="editForm.shopName" placeholder="请输入店铺名称" />
        </el-form-item>
        <el-form-item label="联系人" prop="contactName">
          <el-input v-model="editForm.contactName" placeholder="请输入联系人" />
        </el-form-item>
        <el-form-item label="联系电话" prop="contactPhone">
          <el-input v-model="editForm.contactPhone" placeholder="请输入联系电话" />
        </el-form-item>
        <el-form-item label="商家状态" prop="status">
          <el-select v-model="editForm.status" placeholder="请选择状态" style="width: 100%">
            <el-option label="待审核" :value="0" />
            <el-option label="已通过" :value="1" />
            <el-option label="已拒绝" :value="2" />
            <el-option label="已禁用" :value="3" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="editDialogVisible = false">取消</el-button>
          <el-button type="primary" @click="handleSaveEdit" :loading="editLoading">
            保存
          </el-button>
        </span>
      </template>
    </el-dialog>

    <!-- 重置密码对话框 -->
    <el-dialog
      v-model="resetPasswordDialogVisible"
      title="重置商家密码"
      width="400px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="resetPasswordFormRef"
        :model="resetPasswordForm"
        :rules="resetPasswordFormRules"
        label-width="100px"
      >
        <el-form-item label="商家名称">
          <el-input v-model="resetPasswordForm.merchantName" disabled />
        </el-form-item>
        <el-form-item label="新密码" prop="newPassword">
          <el-input
            v-model="resetPasswordForm.newPassword"
            type="password"
            placeholder="请输入新密码"
            show-password
          />
        </el-form-item>
        <el-form-item label="确认密码" prop="confirmPassword">
          <el-input
            v-model="resetPasswordForm.confirmPassword"
            type="password"
            placeholder="请再次输入新密码"
            show-password
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="resetPasswordDialogVisible = false">取消</el-button>
          <el-button type="primary" @click="handleSaveResetPassword" :loading="resetPasswordLoading">
            重置密码
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { getMerchantList, auditMerchant, updateMerchant, resetMerchantPassword } from '@/api/merchant'

const queryForm = reactive({
  username: '' as string,
  merchantName: '' as string,
  contactName: '' as string,
  contactPhone: '' as string,
  inviteCode: '' as string,
  status: 'all' as string | number
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

const tableData = ref([])
const loading = ref(false)

// 编辑商家相关
const editDialogVisible = ref(false)
const editLoading = ref(false)
const editFormRef = ref()
const editForm = reactive({
  id: 0,
  merchantUid: '',
  username: '',
  merchantName: '',
  shopName: '',
  contactName: '',
  contactPhone: '',
  status: 0
})

const editFormRules = {
  merchantName: [
    { required: true, message: '请输入商家名称', trigger: 'blur' }
  ],
  shopName: [
    { required: true, message: '请输入店铺名称', trigger: 'blur' }
  ],
  contactName: [
    { required: true, message: '请输入联系人', trigger: 'blur' }
  ],
  contactPhone: [
    { required: true, message: '请输入联系电话', trigger: 'blur' },
    { pattern: /^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/, message: '请输入正确的马来西亚手机号格式', trigger: 'blur' }
  ],
  status: [
    { required: true, message: '请选择商家状态', trigger: 'change' }
  ]
}

// 重置密码相关
const resetPasswordDialogVisible = ref(false)
const resetPasswordLoading = ref(false)
const resetPasswordFormRef = ref()
const resetPasswordForm = reactive({
  id: 0,
  merchantName: '',
  newPassword: '',
  confirmPassword: ''
})

const resetPasswordFormRules = {
  newPassword: [
    { required: true, message: '请输入新密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能少于6位', trigger: 'blur' }
  ],
  confirmPassword: [
    { required: true, message: '请确认新密码', trigger: 'blur' },
    {
      validator: (_rule: any, value: any, callback: any) => {
        if (value !== resetPasswordForm.newPassword) {
          callback(new Error('两次输入的密码不一致'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 查询商家列表
const handleQuery = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.page,
      pageSize: pagination.pageSize
    }
    
    // 后端只支持 keyword：优先用商家名称/联系人/用户名
    const parts: string[] = []
    if (queryForm.merchantName.trim()) parts.push(queryForm.merchantName.trim())
    if (queryForm.contactName.trim()) parts.push(queryForm.contactName.trim())
    if (queryForm.username.trim()) parts.push(queryForm.username.trim())
    if (queryForm.contactPhone.trim()) parts.push(queryForm.contactPhone.trim())
    if (queryForm.inviteCode.trim()) parts.push(queryForm.inviteCode.trim())
    const keyword = parts.join(' ').trim()
    if (keyword) params.keyword = keyword
    
    const res = await getMerchantList(params)
    console.log('API响应:', res) // 调试日志
    // 处理嵌套的数据结构
    if (res.data && res.data.data && res.data.data.data) {
      tableData.value = res.data.data.data.list || []
      pagination.total = res.data.data.data.total || 0
    } else if (res.data && res.data.data) {
      tableData.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else if (res.data && res.data.list) {
      tableData.value = res.data.list || []
      pagination.total = res.data.total || 0
    } else {
      tableData.value = []
      pagination.total = 0
    }
  } catch (error: any) {
    console.error('查询失败：', error)
    // 更友好的错误提示
    const status = error?.response?.status
    if (status === 401) {
      ElMessage.error('未登录或令牌失效，请重新登录')
    } else if (status === 403) {
      ElMessage.error('没有权限执行该操作')
    } else {
      const msg = error?.response?.data?.message || '查询失败，请稍后重试'
      ElMessage.error(msg)
    }
  } finally {
    loading.value = false
  }
}

// 重置搜索条件
const handleReset = () => {
  queryForm.username = ''
  queryForm.merchantName = ''
  queryForm.contactName = ''
  queryForm.contactPhone = ''
  queryForm.inviteCode = ''
  queryForm.status = 'all'
  pagination.page = 1
  handleQuery()
}

// 编辑商家
const handleEdit = (row: any) => {
  editForm.id = row.id
  editForm.merchantUid = row.merchantUid
  editForm.username = row.username
  editForm.merchantName = row.merchantName
  editForm.shopName = row.shopName
  editForm.contactName = row.contactName
  editForm.contactPhone = row.contactPhone
  editForm.status = row.status
  editDialogVisible.value = true
}

// 保存编辑
const handleSaveEdit = async () => {
  if (!editFormRef.value) return
  
  try {
    await editFormRef.value.validate()
    editLoading.value = true
    
    await updateMerchant(editForm.id, {
      merchantName: editForm.merchantName,
      shopName: editForm.shopName,
      contactName: editForm.contactName,
      contactPhone: editForm.contactPhone,
      status: editForm.status
    })
    
    ElMessage.success('商家信息更新成功')
    editDialogVisible.value = false
    handleQuery() // 刷新列表
  } catch (error) {
    console.error('更新失败：', error)
    ElMessage.error('更新失败，请稍后重试')
  } finally {
    editLoading.value = false
  }
}

// 重置密码
const handleResetPassword = (row: any) => {
  resetPasswordForm.id = row.id
  resetPasswordForm.merchantName = row.merchantName
  resetPasswordForm.newPassword = ''
  resetPasswordForm.confirmPassword = ''
  resetPasswordDialogVisible.value = true
}

// 保存重置密码
const handleSaveResetPassword = async () => {
  if (!resetPasswordFormRef.value) return
  
  try {
    await resetPasswordFormRef.value.validate()
    resetPasswordLoading.value = true
    
    await resetMerchantPassword(resetPasswordForm.id, {
      newPassword: resetPasswordForm.newPassword
    })
    
    ElMessage.success('密码重置成功')
    resetPasswordDialogVisible.value = false
  } catch (error) {
    console.error('重置密码失败：', error)
    ElMessage.error('重置密码失败，请稍后重试')
  } finally {
    resetPasswordLoading.value = false
  }
}

// 审核商家
const handleAudit = async (row: any, status: number) => {
  if (status === 2) {
    // 拒绝需要填写原因
    ElMessageBox.prompt('请输入拒绝原因', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      inputPattern: /.+/,
      inputErrorMessage: '请输入拒绝原因'
    }).then(async ({ value }) => {
      try {
        await auditMerchant(row.id, status, value)
        ElMessage.success('审核成功')
        handleQuery()
      } catch (error) {
        console.error('审核失败：', error)
      }
    })
  } else {
    // 通过
    ElMessageBox.confirm('确定要通过该商家的申请吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(async () => {
      try {
        await auditMerchant(row.id, status)
        ElMessage.success('审核成功')
        handleQuery()
      } catch (error) {
        console.error('审核失败：', error)
      }
    })
  }
}

// 获取状态文本
const getStatusText = (status: number) => {
  const statusMap: Record<number, string> = {
    0: '待审核',
    1: '已通过',
    2: '已拒绝',
    3: '已禁用'
  }
  return statusMap[status] || '未知'
}

// 获取信用等级标签类型
const getCreditLevelType = (level: string) => {
  const typeMap: Record<string, string> = {
    'AAA': 'success',
    'AA': '',
    'A': 'warning',
    'BBB': 'warning',
    'BB': 'danger',
    'B': 'danger',
    'C': 'danger'
  }
  return typeMap[level] || 'info'
}

// 获取状态类型
const getStatusType = (status: number) => {
  const typeMap: Record<number, string> = {
    0: 'warning',
    1: 'success',
    2: 'danger',
    3: 'info'
  }
  return typeMap[status] || 'info'
}

onMounted(() => {
  handleQuery()
})
</script>

<style scoped>
.merchants-page {
  padding: 20px;
}

.search-form {
  margin-bottom: 20px;
}

.credit-rating-cell {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.credit-score {
  font-size: 12px;
  color: #909399;
}
</style>

