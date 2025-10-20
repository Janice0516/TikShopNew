<template>
  <div class="role-management">
    <div class="page-header">
      <h2>{{ $t('rolePermission.roleManagement') }}</h2>
      <el-button type="primary" @click="showCreateDialog = true">
        <el-icon><Plus /></el-icon>
        {{ $t('rolePermission.createRole') }}
      </el-button>
    </div>

    <!-- 搜索表单 -->
    <el-card class="search-card">
      <el-form :model="searchForm" inline>
        <el-form-item :label="$t('rolePermission.roleCode')">
          <el-input
            v-model="searchForm.code"
            :placeholder="$t('rolePermission.searchPlaceholder')"
            clearable
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.roleName')">
          <el-input
            v-model="searchForm.name"
            :placeholder="$t('rolePermission.searchPlaceholder')"
            clearable
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.status')">
          <el-select v-model="searchForm.status" clearable>
            <el-option :label="$t('rolePermission.all')" :value="undefined" />
            <el-option :label="$t('rolePermission.active')" :value="1" />
            <el-option :label="$t('rolePermission.disabled')" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="loadRoles">
            <el-icon><Search /></el-icon>
            {{ $t('common.search') }}
          </el-button>
          <el-button @click="resetSearch">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 角色列表 -->
    <el-card>
      <el-table :data="roleList" v-loading="loading">
        <el-table-column prop="code" :label="$t('rolePermission.roleCode')" width="150" />
        <el-table-column prop="name" :label="$t('rolePermission.roleName')" width="150" />
        <el-table-column prop="description" :label="$t('rolePermission.description')" min-width="200" />
        <el-table-column :label="$t('rolePermission.permissions')" min-width="200">
          <template #default="{ row }">
            <el-tag
              v-for="permission in row.permissions"
              :key="permission.id"
              size="small"
              class="permission-tag"
            >
              {{ permission.name }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column :label="$t('rolePermission.status')" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? $t('rolePermission.active') : $t('rolePermission.disabled') }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column :label="$t('rolePermission.isSystem')" width="100">
          <template #default="{ row }">
            <el-tag :type="row.isSystem === 1 ? 'warning' : 'info'">
              {{ row.isSystem === 1 ? $t('rolePermission.system') : $t('rolePermission.custom') }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" :label="$t('rolePermission.createTime')" width="180">
          <template #default="{ row }">
            {{ formatDate(row.createTime) }}
          </template>
        </el-table-column>
        <el-table-column :label="$t('common.actions')" width="200" fixed="right">
          <template #default="{ row }">
            <el-button size="small" @click="editRole(row)">
              <el-icon><Edit /></el-icon>
              {{ $t('common.edit') }}
            </el-button>
            <el-button
              size="small"
              :type="row.status === 1 ? 'warning' : 'success'"
              @click="toggleStatus(row)"
            >
              {{ row.status === 1 ? $t('rolePermission.disable') : $t('rolePermission.enable') }}
            </el-button>
            <el-button
              v-if="row.isSystem === 0"
              size="small"
              type="danger"
              @click="deleteRole(row)"
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
          v-model:page-size="pagination.limit"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadRoles"
          @current-change="loadRoles"
        />
      </div>
    </el-card>

    <!-- 创建/编辑角色对话框 -->
    <el-dialog
      v-model="showCreateDialog"
      :title="editingRole ? $t('rolePermission.editRole') : $t('rolePermission.createRole')"
      width="600px"
    >
      <el-form
        ref="roleFormRef"
        :model="roleForm"
        :rules="roleRules"
        label-width="100px"
      >
        <el-form-item :label="$t('rolePermission.roleCode')" prop="code">
          <el-input
            v-model="roleForm.code"
            :placeholder="$t('rolePermission.roleCodePlaceholder')"
            :disabled="editingRole"
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.roleName')" prop="name">
          <el-input
            v-model="roleForm.name"
            :placeholder="$t('rolePermission.roleNamePlaceholder')"
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.description')" prop="description">
          <el-input
            v-model="roleForm.description"
            type="textarea"
            :placeholder="$t('rolePermission.descriptionPlaceholder')"
            :rows="3"
          />
        </el-form-item>
        <el-form-item :label="$t('rolePermission.permissions')" prop="permissionIds">
          <el-checkbox-group v-model="roleForm.permissionIds">
            <div v-for="(permissions, group) in groupedPermissions" :key="group" class="permission-group">
              <div class="group-title">{{ group }}</div>
              <el-checkbox
                v-for="permission in permissions"
                :key="permission.id"
                :label="permission.id"
                class="permission-checkbox"
              >
                {{ permission.name }}
              </el-checkbox>
            </div>
          </el-checkbox-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showCreateDialog = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="saveRole" :loading="saving">
          {{ $t('common.save') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance } from 'element-plus'
import { Plus, Search, Refresh, Edit, Delete } from '@element-plus/icons-vue'
import { useI18n } from 'vue-i18n'
import {
  getRoleList,
  createRole,
  updateRole,
  deleteRole as deleteRoleApi,
  getPermissionsByGroup
} from '@/api/role-permission'

const { t } = useI18n()

// 响应式数据
const loading = ref(false)
const saving = ref(false)
const showCreateDialog = ref(false)
const editingRole = ref(null)
const roleFormRef = ref<FormInstance>()

const searchForm = reactive({
  code: '',
  name: '',
  status: undefined
})

const roleForm = reactive({
  code: '',
  name: '',
  description: '',
  permissionIds: []
})

const roleRules = {
  code: [
    { required: true, message: t('rolePermission.roleCodeRequired'), trigger: 'blur' },
    { min: 2, max: 50, message: t('rolePermission.roleCodeLength'), trigger: 'blur' }
  ],
  name: [
    { required: true, message: t('rolePermission.roleNameRequired'), trigger: 'blur' },
    { min: 2, max: 100, message: t('rolePermission.roleNameLength'), trigger: 'blur' }
  ]
}

const roleList = ref([])
const groupedPermissions = ref({})
const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

// 方法
const loadRoles = async () => {
  loading.value = true
  try {
    const response = await getRoleList({
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    })
    if (response.code === 200) {
      roleList.value = response.data.list
      pagination.total = response.data.total
    }
  } catch (error) {
    ElMessage.error(t('rolePermission.loadFailed'))
  } finally {
    loading.value = false
  }
}

const loadPermissions = async () => {
  try {
    const response = await getPermissionsByGroup()
    if (response.code === 200) {
      groupedPermissions.value = response.data
    }
  } catch (error) {
    ElMessage.error(t('rolePermission.loadPermissionsFailed'))
  }
}

const resetSearch = () => {
  Object.assign(searchForm, {
    code: '',
    name: '',
    status: undefined
  })
  loadRoles()
}

const editRole = (role) => {
  editingRole.value = role
  Object.assign(roleForm, {
    code: role.code,
    name: role.name,
    description: role.description,
    permissionIds: role.permissions?.map(p => p.id) || []
  })
  showCreateDialog.value = true
}

const saveRole = async () => {
  if (!roleFormRef.value) return

  await roleFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        if (editingRole.value) {
          await updateRole(editingRole.value.id, roleForm)
          ElMessage.success(t('rolePermission.updateSuccess'))
        } else {
          await createRole(roleForm)
          ElMessage.success(t('rolePermission.createSuccess'))
        }
        showCreateDialog.value = false
        resetForm()
        loadRoles()
      } catch (error) {
        ElMessage.error(error.message || t('rolePermission.saveFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

const resetForm = () => {
  editingRole.value = null
  Object.assign(roleForm, {
    code: '',
    name: '',
    description: '',
    permissionIds: []
  })
  roleFormRef.value?.resetFields()
}

const toggleStatus = async (role) => {
  try {
    const newStatus = role.status === 1 ? 0 : 1
    await updateRole(role.id, { status: newStatus })
    ElMessage.success(t('rolePermission.statusUpdated'))
    loadRoles()
  } catch (error) {
    ElMessage.error(error.message || t('rolePermission.updateFailed'))
  }
}

const deleteRole = async (role) => {
  try {
    await ElMessageBox.confirm(
      t('rolePermission.deleteConfirm', { name: role.name }),
      t('common.confirm'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    await deleteRoleApi(role.id)
    ElMessage.success(t('rolePermission.deleteSuccess'))
    loadRoles()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || t('rolePermission.deleteFailed'))
    }
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

// 生命周期
onMounted(() => {
  loadRoles()
  loadPermissions()
})
</script>

<style scoped>
.role-management {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.search-card {
  margin-bottom: 20px;
}

.permission-tag {
  margin-right: 5px;
  margin-bottom: 5px;
}

.permission-group {
  margin-bottom: 15px;
}

.group-title {
  font-weight: bold;
  margin-bottom: 8px;
  color: #409eff;
}

.permission-checkbox {
  display: block;
  margin-bottom: 5px;
}

.pagination {
  margin-top: 20px;
  text-align: right;
}
</style>
