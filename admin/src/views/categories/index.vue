<template>
  <div class="categories-page">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>分类管理</span>
          <el-button type="primary" @click="handleAdd">
            <el-icon><Plus /></el-icon>
            添加分类
          </el-button>
        </div>
      </template>

      <!-- 搜索栏 -->
      <div class="search-bar">
        <el-form :model="searchForm" inline>
          <el-form-item label="状态">
            <el-select v-model="searchForm.status" placeholder="请选择状态" clearable>
              <el-option label="全部" :value="undefined" />
              <el-option label="启用" :value="1" />
              <el-option label="禁用" :value="0" />
            </el-select>
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
      </div>

      <!-- 分类表格 -->
      <el-table 
        :data="categoryList" 
        border 
        stripe 
        row-key="id" 
        default-expand-all
        v-loading="loading"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="分类名称" min-width="200">
          <template #default="{ row }">
            <div class="category-name">
              <el-icon v-if="row.icon" class="category-icon">
                <component :is="row.icon" />
              </el-icon>
              <span>{{ row.name }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="level" label="层级" width="100">
          <template #default="{ row }">
            <el-tag :type="getLevelTagType(row.level)">
              {{ getLevelText(row.level) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="sort" label="排序" width="100" />
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-switch
              v-model="row.status"
              :active-value="1"
              :inactive-value="0"
              @change="handleStatusChange(row)"
            />
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="创建时间" width="180">
          <template #default="{ row }">
            {{ formatDate(row.createTime) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" @click="handleEdit(row)">
              编辑
            </el-button>
            <el-button type="success" size="small" @click="handleAddChild(row)">
              添加子分类
            </el-button>
            <el-button 
              type="danger" 
              size="small" 
              @click="handleDelete(row)"
              :disabled="row.children && row.children.length > 0"
            >
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 添加/编辑分类对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑分类' : '添加分类'"
      width="600px"
      @close="handleDialogClose"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="100px"
      >
        <el-form-item label="父分类" prop="parentId">
          <el-tree-select
            v-model="form.parentId"
            :data="parentCategoryOptions"
            :props="{ value: 'id', label: 'name', children: 'children' }"
            placeholder="请选择父分类"
            clearable
            check-strictly
          />
        </el-form-item>
        <el-form-item label="分类名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入分类名称" />
        </el-form-item>
        <el-form-item label="层级" prop="level">
          <el-input-number
            v-model="form.level"
            :min="1"
            :max="3"
            :disabled="isEdit"
          />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input-number v-model="form.sort" :min="0" />
        </el-form-item>
        <el-form-item label="图标" prop="icon">
          <el-input v-model="form.icon" placeholder="请输入图标名称" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">启用</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          确定
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox, FormInstance, FormRules } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { 
  getCategoryList, 
  getCategoryTree, 
  createCategory, 
  updateCategory, 
  deleteCategory, 
  updateCategoryStatus 
} from '@/api/category'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const categoryList = ref([])
const parentCategoryOptions = ref([])
const dialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()

// 搜索表单
const searchForm = reactive({
  status: undefined
})

// 表单数据
const form = reactive({
  id: 0,
  parentId: 0,
  name: '',
  level: 1,
  sort: 0,
  icon: '',
  status: 1
})

// 表单验证规则
const rules: FormRules = {
  name: [
    { required: true, message: '请输入分类名称', trigger: 'blur' },
    { min: 1, max: 50, message: '分类名称长度在 1 到 50 个字符', trigger: 'blur' }
  ],
  level: [
    { required: true, message: '请选择层级', trigger: 'change' }
  ],
  sort: [
    { required: true, message: '请输入排序', trigger: 'blur' }
  ]
}

// 获取分类列表
const fetchCategories = async () => {
  loading.value = true
  try {
    const res = await getCategoryList(searchForm)
    categoryList.value = res.data
  } catch (error) {
    console.error('获取分类失败：', error)
    ElMessage.error('获取分类失败')
  } finally {
    loading.value = false
  }
}

// 获取父分类选项
const fetchParentCategories = async () => {
  try {
    const res = await getCategoryTree()
    parentCategoryOptions.value = res.data
  } catch (error) {
    console.error('获取父分类失败：', error)
  }
}

// 搜索
const handleSearch = () => {
  fetchCategories()
}

// 重置搜索
const handleReset = () => {
  searchForm.status = undefined
  fetchCategories()
}

// 添加分类
const handleAdd = () => {
  isEdit.value = false
  resetForm()
  dialogVisible.value = true
}

// 添加子分类
const handleAddChild = (row: any) => {
  isEdit.value = false
  resetForm()
  form.parentId = row.id
  form.level = row.level + 1
  dialogVisible.value = true
}

// 编辑分类
const handleEdit = (row: any) => {
  isEdit.value = true
  Object.assign(form, row)
  dialogVisible.value = true
}

// 删除分类
const handleDelete = async (row: any) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除分类"${row.name}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await deleteCategory(row.id)
    ElMessage.success('删除成功')
    fetchCategories()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

// 状态切换
const handleStatusChange = async (row: any) => {
  try {
    await updateCategoryStatus(row.id, row.status)
    ElMessage.success('状态更新成功')
  } catch (error) {
    ElMessage.error('状态更新失败')
    // 恢复原状态
    row.status = row.status === 1 ? 0 : 1
  }
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return
  
  await formRef.value.validate(async (valid) => {
    if (valid) {
      submitting.value = true
      try {
        if (isEdit.value) {
          await updateCategory(form.id, form)
          ElMessage.success('更新成功')
        } else {
          await createCategory(form)
          ElMessage.success('创建成功')
        }
        dialogVisible.value = false
        fetchCategories()
      } catch (error) {
        ElMessage.error(isEdit.value ? '更新失败' : '创建失败')
      } finally {
        submitting.value = false
      }
    }
  })
}

// 关闭对话框
const handleDialogClose = () => {
  resetForm()
  formRef.value?.resetFields()
}

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    id: 0,
    parentId: 0,
    name: '',
    level: 1,
    sort: 0,
    icon: '',
    status: 1
  })
}

// 获取层级标签类型
const getLevelTagType = (level: number) => {
  switch (level) {
    case 1: return 'primary'
    case 2: return 'success'
    case 3: return 'warning'
    default: return 'info'
  }
}

// 获取层级文本
const getLevelText = (level: number) => {
  switch (level) {
    case 1: return '一级分类'
    case 2: return '二级分类'
    case 3: return '三级分类'
    default: return '未知'
  }
}

// 格式化日期
const formatDate = (date: string) => {
  if (!date) return ''
  return new Date(date).toLocaleString()
}

onMounted(() => {
  fetchCategories()
  fetchParentCategories()
})
</script>

<style scoped>
.categories-page {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.search-bar {
  margin-bottom: 20px;
  padding: 20px;
  background-color: #f5f5f5;
  border-radius: 4px;
}

.category-name {
  display: flex;
  align-items: center;
  gap: 8px;
}

.category-icon {
  font-size: 16px;
  color: #409EFF;
}

:deep(.el-table .el-table__row) {
  cursor: pointer;
}

:deep(.el-table .el-table__row:hover) {
  background-color: #f5f7fa;
}

:deep(.el-tree-select) {
  width: 100%;
}
</style>

