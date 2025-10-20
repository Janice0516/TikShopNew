<template>
  <div class="invite-code-management">
    <div class="page-header">
      <h2>{{ $t('inviteCode.title') }}</h2>
      <el-button type="primary" @click="showCreateDialog">
        <el-icon><Plus /></el-icon>
        {{ $t('inviteCode.createInviteCode') }}
      </el-button>
    </div>

    <!-- 统计卡片 -->
    <div class="stats-cards">
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-number">{{ stats.total }}</div>
          <div class="stat-label">{{ $t('inviteCode.totalCodes') }}</div>
        </div>
      </el-card>
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-number">{{ stats.active }}</div>
          <div class="stat-label">{{ $t('inviteCode.activeCodes') }}</div>
        </div>
      </el-card>
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-number">{{ stats.totalUsed }}</div>
          <div class="stat-label">{{ $t('inviteCode.totalUsed') }}</div>
        </div>
      </el-card>
    </div>

    <!-- 搜索和筛选 -->
    <el-card class="search-card">
      <el-form :model="searchForm" inline>
        <el-form-item :label="$t('inviteCode.inviteCode')">
          <el-input 
            v-model="searchForm.inviteCode" 
            :placeholder="$t('inviteCode.searchPlaceholder')"
            clearable
          />
        </el-form-item>
        <el-form-item :label="$t('inviteCode.salespersonName')">
          <el-input 
            v-model="searchForm.salespersonName" 
            :placeholder="$t('inviteCode.salespersonPlaceholder')"
            clearable
          />
        </el-form-item>
        <el-form-item :label="$t('common.status')">
          <el-select v-model="searchForm.status" clearable>
            <el-option :label="$t('inviteCode.all')" :value="undefined" />
            <el-option :label="$t('inviteCode.active')" :value="1" />
            <el-option :label="$t('inviteCode.disabled')" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSearch">
            <el-icon><Search /></el-icon>
            {{ $t('common.search') }}
          </el-button>
          <el-button @click="handleReset">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 邀请码列表 -->
    <el-card class="table-card">
      <el-table 
        :data="inviteCodes" 
        v-loading="loading"
        stripe
        border
      >
        <el-table-column prop="inviteCode" :label="$t('inviteCode.inviteCode')" width="150" />
        <el-table-column prop="salespersonName" :label="$t('inviteCode.salespersonName')" width="120" />
        <el-table-column prop="salespersonPhone" :label="$t('inviteCode.salespersonPhone')" width="130" />
        <el-table-column prop="usedCount" :label="$t('inviteCode.usedCount')" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.usedCount > 0 ? 'success' : 'info'">
              {{ row.usedCount }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="maxUsage" :label="$t('inviteCode.maxUsage')" width="100" align="center">
          <template #default="{ row }">
            {{ row.maxUsage === 0 ? $t('inviteCode.unlimited') : row.maxUsage }}
          </template>
        </el-table-column>
        <el-table-column prop="status" :label="$t('common.status')" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? $t('inviteCode.active') : $t('inviteCode.disabled') }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="expireTime" :label="$t('inviteCode.expireTime')" width="150">
          <template #default="{ row }">
            {{ row.expireTime ? formatDate(row.expireTime) : $t('inviteCode.noExpire') }}
          </template>
        </el-table-column>
        <el-table-column prop="createTime" :label="$t('inviteCode.createTime')" width="150">
          <template #default="{ row }">
            {{ formatDate(row.createTime) }}
          </template>
        </el-table-column>
        <el-table-column :label="$t('common.actions')" width="200" fixed="right">
          <template #default="{ row }">
            <el-button 
              size="small" 
              :type="row.status === 1 ? 'warning' : 'success'"
              @click="toggleStatus(row)"
            >
              {{ row.status === 1 ? $t('inviteCode.disable') : $t('inviteCode.enable') }}
            </el-button>
            <el-button 
              size="small" 
              type="danger" 
              @click="handleDelete(row)"
            >
              {{ $t('common.delete') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.limit"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>

    <!-- 创建邀请码对话框 -->
    <el-dialog
      v-model="createDialogVisible"
      :title="$t('inviteCode.createInviteCode')"
      width="600px"
      @close="resetCreateForm"
    >
      <el-form
        ref="createFormRef"
        :model="createForm"
        :rules="createRules"
        label-width="140px"
      >
        <el-form-item :label="$t('inviteCode.salespersonName')" prop="salespersonName">
          <el-input v-model="createForm.salespersonName" />
        </el-form-item>
        <el-form-item :label="$t('inviteCode.salespersonPhone')" prop="salespersonPhone">
          <el-input v-model="createForm.salespersonPhone" />
        </el-form-item>
        <el-form-item :label="$t('inviteCode.salespersonId')" prop="salespersonId">
          <el-input v-model="createForm.salespersonId" />
        </el-form-item>
        <el-form-item :label="$t('inviteCode.maxUsage')" prop="maxUsage">
          <el-input-number 
            v-model="createForm.maxUsage" 
            :min="0" 
            :max="9999"
            style="width: 100%"
          />
          <div class="form-tip">{{ $t('inviteCode.maxUsageTip') }}</div>
        </el-form-item>
        <el-form-item :label="$t('inviteCode.expireTime')" prop="expireTime">
          <el-date-picker
            v-model="createForm.expireTime"
            type="datetime"
            :placeholder="$t('inviteCode.selectExpireTime')"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item :label="$t('inviteCode.remark')" prop="remark">
          <el-input 
            v-model="createForm.remark" 
            type="textarea" 
            :rows="3"
            :placeholder="$t('inviteCode.remarkPlaceholder')"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="createDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" :loading="createLoading" @click="handleCreate">
          {{ $t('common.confirm') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { useI18n } from 'vue-i18n'
import { 
  createInviteCode, 
  getInviteCodeList, 
  getInviteCodeStats, 
  updateInviteCodeStatus, 
  deleteInviteCode 
} from '@/api/invite-code'

const { t } = useI18n()

// 响应式数据
const loading = ref(false)
const createLoading = ref(false)
const createDialogVisible = ref(false)
const inviteCodes = ref([])
const stats = ref({
  total: 0,
  active: 0,
  disabled: 0,
  totalUsed: 0
})

// 搜索表单
const searchForm = reactive({
  inviteCode: '',
  salespersonName: '',
  status: undefined
})

// 分页
const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

// 创建表单
const createForm = reactive({
  salespersonName: '',
  salespersonPhone: '',
  salespersonId: '',
  maxUsage: 0,
  expireTime: null,
  remark: ''
})

const createFormRef = ref<FormInstance>()

const createRules: FormRules = {
  salespersonName: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ]
}

// 方法
const loadStats = async () => {
  try {
    const response = await getInviteCodeStats()
    if (response.code === 200) {
      stats.value = response.data
    }
  } catch (error) {
    console.error('加载统计失败:', error)
  }
}

const loadInviteCodes = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    }
    const response = await getInviteCodeList(params)
    if (response.code === 200) {
      inviteCodes.value = response.data.items
      pagination.total = response.data.total
    }
  } catch (error) {
    console.error('加载邀请码列表失败:', error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  pagination.page = 1
  loadInviteCodes()
}

const handleReset = () => {
  Object.assign(searchForm, {
    inviteCode: '',
    salespersonName: '',
    status: undefined
  })
  pagination.page = 1
  loadInviteCodes()
}

const handleSizeChange = (size: number) => {
  pagination.limit = size
  pagination.page = 1
  loadInviteCodes()
}

const handleCurrentChange = (page: number) => {
  pagination.page = page
  loadInviteCodes()
}

const showCreateDialog = () => {
  createDialogVisible.value = true
}

const resetCreateForm = () => {
  Object.assign(createForm, {
    salespersonName: '',
    salespersonPhone: '',
    salespersonId: '',
    maxUsage: 0,
    expireTime: null,
    remark: ''
  })
  createFormRef.value?.resetFields()
}

const handleCreate = async () => {
  if (!createFormRef.value) return
  
  await createFormRef.value.validate(async (valid) => {
    if (valid) {
      createLoading.value = true
      try {
        const response = await createInviteCode(createForm)
        if (response.code === 200) {
          ElMessage.success(t('inviteCode.createSuccess'))
          createDialogVisible.value = false
          loadInviteCodes()
          loadStats()
        } else {
          ElMessage.error(response.message || t('inviteCode.createFailed'))
        }
      } catch (error: any) {
        ElMessage.error(error.message || t('inviteCode.createFailed'))
      } finally {
        createLoading.value = false
      }
    }
  })
}

const toggleStatus = async (row: any) => {
  try {
    const newStatus = row.status === 1 ? 0 : 1
    const response = await updateInviteCodeStatus(row.id, newStatus)
    if (response.code === 200) {
      ElMessage.success(t('inviteCode.statusUpdated'))
      loadInviteCodes()
      loadStats()
    } else {
      ElMessage.error(response.message || t('inviteCode.updateFailed'))
    }
  } catch (error: any) {
    ElMessage.error(error.message || t('inviteCode.updateFailed'))
  }
}

const handleDelete = async (row: any) => {
  try {
    await ElMessageBox.confirm(
      t('inviteCode.deleteConfirm', { code: row.inviteCode }),
      t('common.confirm'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    const response = await deleteInviteCode(row.id)
    if (response.code === 200) {
      ElMessage.success(t('inviteCode.deleteSuccess'))
      loadInviteCodes()
      loadStats()
    } else {
      ElMessage.error(response.message || t('inviteCode.deleteFailed'))
    }
  } catch (error: any) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || t('inviteCode.deleteFailed'))
    }
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString()
}

// 生命周期
onMounted(() => {
  loadStats()
  loadInviteCodes()
})
</script>

<style scoped lang="scss">
.invite-code-management {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  
  h2 {
    margin: 0;
    color: #333;
  }
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.stat-card {
  .stat-content {
    text-align: center;
    
    .stat-number {
      font-size: 32px;
      font-weight: bold;
      color: #409EFF;
      margin-bottom: 8px;
    }
    
    .stat-label {
      font-size: 14px;
      color: #666;
    }
  }
}

.search-card {
  margin-bottom: 20px;
}

.table-card {
  .pagination-container {
    margin-top: 20px;
    text-align: right;
  }
}

.form-tip {
  font-size: 12px;
  color: #999;
  margin-top: 5px;
}
</style>
