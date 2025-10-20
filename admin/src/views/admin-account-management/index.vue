<template>
  <div class="admin-account-management">
    <div class="page-header">
      <h2>{{ $t('rolePermission.adminAccountManagement') }}</h2>
      <div class="header-actions">
        <el-button type="success" @click="showBatchCreateDialog = true">
          <el-icon><UserFilled /></el-icon>
          {{ $t('rolePermission.batchCreateSalesperson') }}
        </el-button>
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          {{ $t('rolePermission.createAdmin') }}
        </el-button>
      </div>
    </div>

    <!-- 管理员列表 -->
    <el-card>
      <el-table :data="adminList" v-loading="loading">
        <el-table-column prop="username" :label="$t('rolePermission.username')" width="120" />
        <el-table-column prop="nickname" :label="$t('rolePermission.nickname')" width="120" />
        <el-table-column prop="position" :label="$t('rolePermission.position')" width="100" />
        <el-table-column prop="phone" :label="$t('rolePermission.phone')" width="130" />
        <el-table-column prop="email" :label="$t('rolePermission.email')" min-width="150" />
        <el-table-column :label="$t('rolePermission.role')" width="120">
          <template #default="{ row }">
            <el-tag v-if="row.roleEntity" type="primary">
              {{ row.roleEntity.name }}
            </el-tag>
            <span v-else>{{ row.role || 'admin' }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="$t('rolePermission.status')" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? $t('rolePermission.active') : $t('rolePermission.disabled') }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" :label="$t('rolePermission.createTime')" width="180">
          <template #default="{ row }">
            {{ formatDate(row.createTime) }}
          </template>
        </el-table-column>
        <el-table-column :label="$t('common.actions')" width="250" fixed="right">
          <template #default="{ row }">
            <el-button size="small" @click="editAdmin(row)">
              <el-icon><Edit /></el-icon>
              {{ $t('common.edit') }}
            </el-button>
            <el-button size="small" @click="resetPassword(row)">
              <el-icon><Key /></el-icon>
              {{ $t('rolePermission.resetPassword') }}
            </el-button>
            <el-button
              v-if="row.username !== 'admin'"
              size="small"
              type="danger"
              @click="deleteAdmin(row)"
            >
              <el-icon><Delete /></el-icon>
              {{ $t('common.delete') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadAdmins"
          @current-change="loadAdmins"
        />
      </div>
    </el-card>

    <!-- 创建/编辑管理员对话框 -->
    <el-dialog
      v-model="showCreateDialog"
      :title="editingAdmin ? $t('rolePermission.editAdmin') : $t('rolePermission.createAdmin')"
      width="600px"
    >
      <el-form
        ref="adminFormRef"
        :model="adminForm"
        :rules="adminRules"
        label-width="100px"
      >
        <el-form-item :label="$t('rolePermission.username')" prop="username">
          <el-input
            v-model="adminForm.username"
            :placeholder="$t('rolePermission.usernamePlaceholder')"
            :disabled="editingAdmin"
          />
        </el-form-item>
        <el-form-item v-if="!editingAdmin" :label="$t('rolePermission.password')" prop="password">
          <el-input
            v-model="adminForm.password"
            type="password"
            :placeholder="$t('rolePermission.passwordPlaceholder')"
            show-password
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.nickname')" prop="nickname">
          <el-input
            v-model="adminForm.nickname"
            :placeholder="$t('rolePermission.nicknamePlaceholder')"
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.position')" prop="position">
          <el-select v-model="adminForm.position" :placeholder="$t('rolePermission.positionPlaceholder')">
            <el-option label="超级管理员" value="超级管理员" />
            <el-option label="销售经理" value="销售经理" />
            <el-option label="财务经理" value="财务经理" />
            <el-option label="业务员" value="业务员" />
            <el-option label="客服" value="客服" />
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('rolePermission.phone')" prop="phone">
          <el-input
            v-model="adminForm.phone"
            :placeholder="$t('rolePermission.phonePlaceholder')"
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.email')" prop="email">
          <el-input
            v-model="adminForm.email"
            :placeholder="$t('rolePermission.emailPlaceholder')"
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.role')" prop="roleId">
          <el-select v-model="adminForm.roleId" :placeholder="$t('rolePermission.rolePlaceholder')">
            <el-option
              v-for="role in allRoles"
              :key="role.id"
              :label="role.name"
              :value="role.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('rolePermission.remark')" prop="remark">
          <el-input
            v-model="adminForm.remark"
            type="textarea"
            :placeholder="$t('rolePermission.remarkPlaceholder')"
            :rows="3"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showCreateDialog = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="saveAdmin" :loading="saving">
          {{ $t('common.save') }}
        </el-button>
      </template>
    </el-dialog>

    <!-- 批量创建业务员对话框 -->
    <el-dialog
      v-model="showBatchCreateDialog"
      :title="$t('rolePermission.batchCreateSalesperson')"
      width="800px"
    >
      <div class="batch-create-form">
        <el-form
          ref="batchFormRef"
          :model="batchForm"
          :rules="batchRules"
          label-width="100px"
        >
          <el-form-item :label="$t('rolePermission.defaultPassword')" prop="defaultPassword">
            <el-input
              v-model="batchForm.defaultPassword"
              type="password"
              :placeholder="$t('rolePermission.defaultPasswordPlaceholder')"
              show-password
            />
          </el-form-item>
        </el-form>

        <div class="salesperson-list">
          <div class="list-header">
            <h4>{{ $t('rolePermission.salespersonList') }}</h4>
            <el-button type="primary" size="small" @click="addSalesperson">
              <el-icon><Plus /></el-icon>
              {{ $t('rolePermission.addSalesperson') }}
            </el-button>
          </div>
          <div class="list-content">
            <div
              v-for="(item, index) in batchForm.salespersonList"
              :key="index"
              class="salesperson-item"
            >
              <el-form-item :label="$t('rolePermission.username')">
                <el-input
                  v-model="item.username"
                  :placeholder="$t('rolePermission.usernamePlaceholder')"
                />
              </el-form-item>
              <el-form-item :label="$t('rolePermission.nickname')">
                <el-input
                  v-model="item.nickname"
                  :placeholder="$t('rolePermission.nicknamePlaceholder')"
                />
              </el-form-item>
              <el-form-item :label="$t('rolePermission.phone')">
                <el-input
                  v-model="item.phone"
                  :placeholder="$t('rolePermission.phonePlaceholder')"
                />
              </el-form-item>
              <el-form-item :label="$t('rolePermission.email')">
                <el-input
                  v-model="item.email"
                  :placeholder="$t('rolePermission.emailPlaceholder')"
                />
              </el-form-item>
              <el-button
                type="danger"
                size="small"
                @click="removeSalesperson(index)"
              >
                <el-icon><Delete /></el-icon>
                {{ $t('common.delete') }}
              </el-button>
            </div>
          </div>
        </div>
      </div>
      <template #footer>
        <el-button @click="showBatchCreateDialog = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="batchCreateSalesperson" :loading="batchSaving">
          {{ $t('rolePermission.batchCreate') }}
        </el-button>
      </template>
    </el-dialog>

    <!-- 重置密码对话框 -->
    <el-dialog
      v-model="showResetPasswordDialog"
      :title="$t('rolePermission.resetPassword')"
      width="400px"
    >
      <el-form
        ref="passwordFormRef"
        :model="passwordForm"
        :rules="passwordRules"
        label-width="100px"
      >
        <el-form-item :label="$t('rolePermission.newPassword')" prop="password">
          <el-input
            v-model="passwordForm.password"
            type="password"
            :placeholder="$t('rolePermission.newPasswordPlaceholder')"
            show-password
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.confirmPassword')" prop="confirmPassword">
          <el-input
            v-model="passwordForm.confirmPassword"
            type="password"
            :placeholder="$t('rolePermission.confirmPasswordPlaceholder')"
            show-password
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showResetPasswordDialog = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="confirmResetPassword" :loading="passwordSaving">
          {{ $t('rolePermission.confirmReset') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance } from 'element-plus'
import { Plus, Edit, Delete, Key, UserFilled } from '@element-plus/icons-vue'
import { useI18n } from 'vue-i18n'
import {
  getAdminList,
  createAdmin,
  updateAdmin,
  deleteAdmin as deleteAdminApi,
  resetAdminPassword,
  createSalespersonAccounts,
  getAllRoles
} from '@/api/role-permission'

const { t } = useI18n()

// 响应式数据
const loading = ref(false)
const saving = ref(false)
const batchSaving = ref(false)
const passwordSaving = ref(false)
const showCreateDialog = ref(false)
const showBatchCreateDialog = ref(false)
const showResetPasswordDialog = ref(false)
const editingAdmin = ref(null)
const resettingAdmin = ref(null)

const adminFormRef = ref<FormInstance>()
const batchFormRef = ref<FormInstance>()
const passwordFormRef = ref<FormInstance>()

const adminForm = reactive({
  username: '',
  password: '',
  nickname: '',
  position: '',
  phone: '',
  email: '',
  roleId: '',
  remark: ''
})

const batchForm = reactive({
  defaultPassword: '',
  salespersonList: [
    {
      username: '',
      nickname: '',
      phone: '',
      email: ''
    }
  ]
})

const passwordForm = reactive({
  password: '',
  confirmPassword: ''
})

const adminRules = {
  username: [
    { required: true, message: t('rolePermission.usernameRequired'), trigger: 'blur' },
    { min: 3, max: 50, message: t('rolePermission.usernameLength'), trigger: 'blur' }
  ],
  password: [
    { required: true, message: t('rolePermission.passwordRequired'), trigger: 'blur' },
    { min: 6, max: 20, message: t('rolePermission.passwordLength'), trigger: 'blur' }
  ],
  nickname: [
    { required: true, message: t('rolePermission.nicknameRequired'), trigger: 'blur' }
  ],
  phone: [
    { pattern: /^1[3-9]\d{9}$/, message: t('rolePermission.phoneFormat'), trigger: 'blur' }
  ],
  email: [
    { type: 'email', message: t('rolePermission.emailFormat'), trigger: 'blur' }
  ]
}

const batchRules = {
  defaultPassword: [
    { required: true, message: t('rolePermission.defaultPasswordRequired'), trigger: 'blur' },
    { min: 6, max: 20, message: t('rolePermission.passwordLength'), trigger: 'blur' }
  ]
}

const passwordRules = {
  password: [
    { required: true, message: t('rolePermission.passwordRequired'), trigger: 'blur' },
    { min: 6, max: 20, message: t('rolePermission.passwordLength'), trigger: 'blur' }
  ],
  confirmPassword: [
    { required: true, message: t('rolePermission.confirmPasswordRequired'), trigger: 'blur' },
    {
      validator: (rule, value, callback) => {
        if (value !== passwordForm.password) {
          callback(new Error(t('rolePermission.passwordMismatch')))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

const adminList = ref([])
const allRoles = ref([])
const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 方法
const loadAdmins = async () => {
  loading.value = true
  try {
    const response = await getAdminList({
      page: pagination.page,
      pageSize: pagination.pageSize
    })
    if (response.code === 200) {
      adminList.value = response.data.data.list
      pagination.total = response.data.data.total
    }
  } catch (error) {
    ElMessage.error(t('rolePermission.loadAdminsFailed'))
  } finally {
    loading.value = false
  }
}

const loadRoles = async () => {
  try {
    const response = await getAllRoles()
    if (response.code === 200) {
      allRoles.value = response.data
    }
  } catch (error) {
    ElMessage.error(t('rolePermission.loadRolesFailed'))
  }
}

const editAdmin = (admin) => {
  editingAdmin.value = admin
  Object.assign(adminForm, {
    username: admin.username,
    password: '',
    nickname: admin.nickname || '',
    position: admin.position || '',
    phone: admin.phone || '',
    email: admin.email || '',
    roleId: admin.roleId || '',
    remark: admin.remark || ''
  })
  showCreateDialog.value = true
}

const saveAdmin = async () => {
  if (!adminFormRef.value) return

  await adminFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        if (editingAdmin.value) {
          const { password, ...updateData } = adminForm
          await updateAdmin(editingAdmin.value.id.toString(), updateData)
          ElMessage.success(t('rolePermission.updateSuccess'))
        } else {
          await createAdmin(adminForm)
          ElMessage.success(t('rolePermission.createSuccess'))
        }
        showCreateDialog.value = false
        resetAdminForm()
        loadAdmins()
      } catch (error) {
        ElMessage.error(error.message || t('rolePermission.saveFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

const resetAdminForm = () => {
  editingAdmin.value = null
  Object.assign(adminForm, {
    username: '',
    password: '',
    nickname: '',
    position: '',
    phone: '',
    email: '',
    roleId: '',
    remark: ''
  })
  adminFormRef.value?.resetFields()
}

const resetPassword = (admin) => {
  resettingAdmin.value = admin
  Object.assign(passwordForm, {
    password: '',
    confirmPassword: ''
  })
  showResetPasswordDialog.value = true
}

const confirmResetPassword = async () => {
  if (!passwordFormRef.value) return

  await passwordFormRef.value.validate(async (valid) => {
    if (valid) {
      passwordSaving.value = true
      try {
        await resetAdminPassword(resettingAdmin.value.id.toString(), passwordForm.password)
        ElMessage.success(t('rolePermission.resetPasswordSuccess'))
        showResetPasswordDialog.value = false
      } catch (error) {
        ElMessage.error(error.message || t('rolePermission.resetPasswordFailed'))
      } finally {
        passwordSaving.value = false
      }
    }
  })
}

const deleteAdmin = async (admin) => {
  try {
    await ElMessageBox.confirm(
      t('rolePermission.deleteAdminConfirm', { name: admin.username }),
      t('common.confirm'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    await deleteAdminApi(admin.id.toString())
    ElMessage.success(t('rolePermission.deleteSuccess'))
    loadAdmins()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || t('rolePermission.deleteFailed'))
    }
  }
}

const addSalesperson = () => {
  batchForm.salespersonList.push({
    username: '',
    nickname: '',
    phone: '',
    email: ''
  })
}

const removeSalesperson = (index) => {
  if (batchForm.salespersonList.length > 1) {
    batchForm.salespersonList.splice(index, 1)
  }
}

const batchCreateSalesperson = async () => {
  if (!batchFormRef.value) return

  await batchFormRef.value.validate(async (valid) => {
    if (valid) {
      batchSaving.value = true
      try {
        const salespersonData = batchForm.salespersonList.map(item => ({
          ...item,
          password: batchForm.defaultPassword
        }))
        const response = await createSalespersonAccounts(salespersonData)
        ElMessage.success(t('rolePermission.batchCreateSuccess'))
        showBatchCreateDialog.value = false
        resetBatchForm()
        loadAdmins()
      } catch (error) {
        ElMessage.error(error.message || t('rolePermission.batchCreateFailed'))
      } finally {
        batchSaving.value = false
      }
    }
  })
}

const resetBatchForm = () => {
  Object.assign(batchForm, {
    defaultPassword: '',
    salespersonList: [
      {
        username: '',
        nickname: '',
        phone: '',
        email: ''
      }
    ]
  })
  batchFormRef.value?.resetFields()
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

// 生命周期
onMounted(() => {
  loadAdmins()
  loadRoles()
})
</script>

<style scoped>
.admin-account-management {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.batch-create-form {
  max-height: 500px;
  overflow-y: auto;
}

.salesperson-list {
  margin-top: 20px;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.salesperson-item {
  border: 1px solid #e4e7ed;
  border-radius: 4px;
  padding: 15px;
  margin-bottom: 15px;
  position: relative;
}

.salesperson-item .el-form-item {
  margin-bottom: 10px;
}

.salesperson-item .el-button {
  position: absolute;
  top: 15px;
  right: 15px;
}

.pagination {
  margin-top: 20px;
  text-align: right;
}
</style>
