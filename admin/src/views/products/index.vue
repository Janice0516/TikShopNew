<template>
  <div class="products-page">
    <el-card>
      <!-- 搜索栏 -->
      <el-form :inline="true" :model="queryForm" class="search-form">
        <el-form-item label="商品名称">
          <el-input
            v-model="queryForm.keyword"
            placeholder="请输入商品名称"
            clearable
          />
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="queryForm.status" placeholder="请选择" clearable>
            <el-option label="全部" :value="undefined" />
            <el-option :label="$t('common.status.active')" :value="1" />
            <el-option :label="$t('common.status.inactive')" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery" :icon="Search">
            {{ $t('common.search') }}
          </el-button>
          <el-button @click="handleReset">{{ $t('common.reset') }}</el-button>
        </el-form-item>
      </el-form>

      <!-- 操作栏 -->
      <div class="toolbar">
        <el-button type="primary" @click="handleAdd" :icon="Plus">
          {{ $t('nav.addProduct') }}
        </el-button>
      </div>

      <!-- 表格 -->
      <el-table :data="tableData" v-loading="loading" border stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column label="商品图片" width="100">
          <template #default="{ row }">
            <el-image
              :src="row.mainImage"
              :preview-src-list="[row.mainImage]"
              fit="cover"
              style="width: 60px; height: 60px"
            />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="商品名称" min-width="200" show-overflow-tooltip />
        <el-table-column prop="costPrice" label="Cost Price" width="100">
          <template #default="{ row }">
            RM{{ row.costPrice }}
          </template>
        </el-table-column>
        <el-table-column prop="stock" label="库存" width="100" />
        <el-table-column prop="sales" label="销量" width="100" />
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? $t('common.status.active') : $t('common.status.inactive') }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              link
              @click="handleEdit(row)"
            >
              {{ $t('common.edit') }}
            </el-button>
            <el-button
              :type="row.status === 1 ? 'warning' : 'success'"
              link
              @click="handleToggleStatus(row)"
            >
              {{ row.status === 1 ? $t('common.status.inactive') : $t('common.status.active') }}
            </el-button>
            <el-button
              type="danger"
              link
              @click="handleDelete(row)"
            >
              {{ $t('common.delete') }}
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus } from '@element-plus/icons-vue'
import { getProductList, deleteProduct, updateProductStatus } from '@/api/product'

const router = useRouter()

const queryForm = reactive({
  keyword: '',
  status: undefined as number | undefined
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

const tableData = ref([])
const loading = ref(false)

// 查询商品列表
const handleQuery = async () => {
  loading.value = true
  try {
    const res = await getProductList({
      page: pagination.page,
      pageSize: pagination.pageSize,
      keyword: queryForm.keyword,
      status: queryForm.status
    })
    tableData.value = res.data.list
    pagination.total = res.data.total
  } catch (error) {
    console.error('查询失败：', error)
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  queryForm.keyword = ''
  queryForm.status = undefined
  pagination.page = 1
  handleQuery()
}

// 添加商品
const handleAdd = () => {
  router.push('/products/add')
}

// 编辑商品
const handleEdit = (row: any) => {
  router.push(`/products/edit/${row.id}`)
}

// 切换状态
const handleToggleStatus = async (row: any) => {
  try {
    const newStatus = row.status === 1 ? 0 : 1
    await updateProductStatus(row.id, newStatus)
    ElMessage.success(newStatus === 1 ? '上架成功' : '下架成功')
    handleQuery()
  } catch (error) {
    console.error('操作失败：', error)
  }
}

// 删除商品
const handleDelete = async (row: any) => {
  ElMessageBox.confirm('确定要删除该商品吗？', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    try {
      await deleteProduct(row.id)
      ElMessage.success('删除成功')
      handleQuery()
    } catch (error) {
      console.error('删除失败：', error)
    }
  })
}

onMounted(() => {
  handleQuery()
})
</script>

<style scoped>
.products-page {
  padding: 20px;
}

.search-form {
  margin-bottom: 20px;
}

.toolbar {
  margin-bottom: 20px;
}
</style>

