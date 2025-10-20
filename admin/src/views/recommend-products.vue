<template>
  <div class="recommend-products">
    <div class="page-header">
      <h1>推荐商品管理</h1>
      <p>管理 Top Deals 和 Popular Items 推荐商品</p>
    </div>

    <!-- 统计卡片 -->
    <div class="stats-cards">
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon popular">🔥</div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.totalPopular }}</div>
            <div class="stat-label">热门商品总数</div>
          </div>
        </div>
      </el-card>
      
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon active-popular">⭐</div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.activePopular }}</div>
            <div class="stat-label">活跃热门商品</div>
          </div>
        </div>
      </el-card>
      
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon top-deal">💎</div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.totalTopDeal }}</div>
            <div class="stat-label">Top Deals 总数</div>
          </div>
        </div>
      </el-card>
      
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon active-top-deal">🎯</div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.activeTopDeal }}</div>
            <div class="stat-label">活跃 Top Deals</div>
          </div>
        </div>
      </el-card>
    </div>

    <!-- 筛选和搜索 -->
    <el-card class="filter-card">
      <div class="filter-row">
        <el-select v-model="filters.recommendType" placeholder="推荐类型" style="width: 150px">
          <el-option label="全部" value="all" />
          <el-option label="热门商品" value="popular" />
          <el-option label="Top Deals" value="top_deal" />
        </el-select>
        
        <el-select v-model="filters.status" placeholder="推荐状态" style="width: 150px">
          <el-option label="全部" value="all" />
          <el-option label="活跃中" value="active" />
          <el-option label="已过期" value="expired" />
        </el-select>
        
        <el-input
          v-model="filters.keyword"
          placeholder="搜索商品名称或商家"
          style="width: 250px"
          clearable
        />
        
        <el-button type="primary" @click="loadRecommendProducts">
          <el-icon><Search /></el-icon>
          搜索
        </el-button>
        
        <el-button @click="resetFilters">
          <el-icon><Refresh /></el-icon>
          重置
        </el-button>
      </div>
    </el-card>

    <!-- 商品列表 -->
    <el-card class="table-card">
      <template #header>
        <div class="card-header">
          <span>推荐商品列表</span>
          <el-button type="primary" @click="showBatchUpdateDialog">
            <el-icon><Edit /></el-icon>
            批量设置
          </el-button>
        </div>
      </template>

      <el-table
        v-loading="loading"
        :data="products"
        stripe
        style="width: 100%"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        
        <el-table-column label="商品信息" width="300">
          <template #default="{ row }">
            <div class="product-info">
              <img :src="row.productImage || '/placeholder.jpg'" class="product-image" />
              <div class="product-details">
                <div class="product-name">{{ row.productName }}</div>
                <div class="product-brand">{{ row.brand }}</div>
                <div class="product-category">{{ row.categoryName }}</div>
              </div>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column label="商家" width="150">
          <template #default="{ row }">
            <div class="merchant-info">
              <div class="merchant-name">{{ row.merchantName }}</div>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column label="价格" width="120">
          <template #default="{ row }">
            <div class="price-info">
              <div class="sale-price">RM{{ row.salePrice }}</div>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column label="销量" width="100">
          <template #default="{ row }">
            <el-tag type="info">{{ row.sales }}</el-tag>
          </template>
        </el-table-column>
        
        <el-table-column label="推荐类型" width="120">
          <template #default="{ row }">
            <div class="recommend-types">
              <el-tag v-if="row.isPopular" type="warning" size="small">热门</el-tag>
              <el-tag v-if="row.isTopDeal" type="danger" size="small">Top Deal</el-tag>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column label="推荐理由" width="150">
          <template #default="{ row }">
            <div class="recommend-reason">{{ row.recommendReason || '-' }}</div>
          </template>
        </el-table-column>
        
        <el-table-column label="优先级" width="100">
          <template #default="{ row }">
            <el-tag :type="getPriorityType(row.recommendPriority)">
              {{ row.recommendPriority }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column label="推荐时间" width="200">
          <template #default="{ row }">
            <div class="recommend-time">
              <div v-if="row.recommendStartTime">
                开始: {{ formatDate(row.recommendStartTime) }}
              </div>
              <div v-if="row.recommendEndTime">
                结束: {{ formatDate(row.recommendEndTime) }}
              </div>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button size="small" @click="editRecommendProduct(row)">
              <el-icon><Edit /></el-icon>
              编辑
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination">
        <el-pagination
          v-model:current-page="pagination.current"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadRecommendProducts"
          @current-change="loadRecommendProducts"
        />
      </div>
    </el-card>

    <!-- 编辑推荐商品对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      :title="editingProduct ? '编辑推荐设置' : '新增推荐商品'"
      width="600px"
    >
      <el-form :model="editForm" :rules="editRules" ref="editFormRef" label-width="120px">
        <el-form-item label="商品名称">
          <el-input :value="editingProduct?.productName" disabled />
        </el-form-item>
        
        <el-form-item label="商家名称">
          <el-input :value="editingProduct?.merchantName" disabled />
        </el-form-item>
        
        <el-form-item label="推荐类型" prop="recommendTypes">
          <el-checkbox-group v-model="editForm.recommendTypes">
            <el-checkbox label="popular">热门商品</el-checkbox>
            <el-checkbox label="top_deal">Top Deals</el-checkbox>
          </el-checkbox-group>
        </el-form-item>
        
        <el-form-item label="推荐理由" prop="recommendReason">
          <el-input
            v-model="editForm.recommendReason"
            type="textarea"
            :rows="3"
            placeholder="请输入推荐理由"
          />
        </el-form-item>
        
        <el-form-item label="推荐优先级" prop="recommendPriority">
          <el-slider
            v-model="editForm.recommendPriority"
            :min="0"
            :max="100"
            :step="1"
            show-input
          />
        </el-form-item>
        
        <el-form-item label="推荐开始时间">
          <el-date-picker
            v-model="editForm.recommendStartTime"
            type="datetime"
            placeholder="选择开始时间"
            format="YYYY-MM-DD HH:mm:ss"
            value-format="YYYY-MM-DD HH:mm:ss"
          />
        </el-form-item>
        
        <el-form-item label="推荐结束时间">
          <el-date-picker
            v-model="editForm.recommendEndTime"
            type="datetime"
            placeholder="选择结束时间"
            format="YYYY-MM-DD HH:mm:ss"
            value-format="YYYY-MM-DD HH:mm:ss"
          />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="editDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveRecommendProduct" :loading="saving">
          保存
        </el-button>
      </template>
    </el-dialog>

    <!-- 批量设置对话框 -->
    <el-dialog
      v-model="batchDialogVisible"
      title="批量设置推荐商品"
      width="500px"
    >
      <el-form :model="batchForm" label-width="120px">
        <el-form-item label="推荐类型">
          <el-checkbox-group v-model="batchForm.recommendTypes">
            <el-checkbox label="popular">热门商品</el-checkbox>
            <el-checkbox label="top_deal">Top Deals</el-checkbox>
          </el-checkbox-group>
        </el-form-item>
        
        <el-form-item label="推荐理由">
          <el-input
            v-model="batchForm.recommendReason"
            type="textarea"
            :rows="3"
            placeholder="请输入推荐理由"
          />
        </el-form-item>
        
        <el-form-item label="推荐优先级">
          <el-slider
            v-model="batchForm.recommendPriority"
            :min="0"
            :max="100"
            :step="1"
            show-input
          />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="batchDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveBatchUpdate" :loading="saving">
          批量保存
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Edit } from '@element-plus/icons-vue'
import request from '@/utils/request'

// 响应式数据
const loading = ref(false)
const saving = ref(false)
const products = ref([])
const selectedProducts = ref([])
const editDialogVisible = ref(false)
const batchDialogVisible = ref(false)
const editingProduct = ref(null)
const editFormRef = ref()

// 统计数据
const stats = ref({
  totalPopular: 0,
  activePopular: 0,
  totalTopDeal: 0,
  activeTopDeal: 0,
  expiredRecommendations: 0
})

// 筛选条件
const filters = reactive({
  recommendType: 'all',
  status: 'all',
  keyword: ''
})

// 分页
const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0
})

// 编辑表单
const editForm = reactive({
  recommendTypes: [],
  recommendReason: '',
  recommendPriority: 0,
  recommendStartTime: '',
  recommendEndTime: ''
})

// 批量设置表单
const batchForm = reactive({
  recommendTypes: [],
  recommendReason: '',
  recommendPriority: 0
})

// 表单验证规则
const editRules = {
  recommendTypes: [
    { required: true, message: '请选择至少一种推荐类型', trigger: 'change' }
  ]
}

// 方法
const loadRecommendProducts = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current,
      pageSize: pagination.pageSize,
      recommendType: filters.recommendType,
      status: filters.status,
      keyword: filters.keyword
    }
    
    const response = await request.get('/admin/recommend-products', { params })
    const data = response.data
    
    if (data.code === 200) {
      products.value = data.data.list
      pagination.total = data.data.total
    } else {
      ElMessage.error(data.message || '加载失败')
    }
  } catch (error) {
    console.error('加载推荐商品失败:', error)
    ElMessage.error('加载失败')
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await request.get('/admin/recommend-products/stats')
    const data = response.data
    
    if (data.code === 200) {
      stats.value = data.data
    }
  } catch (error) {
    console.error('加载统计失败:', error)
  }
}

const editRecommendProduct = (product) => {
  editingProduct.value = product
  editForm.recommendTypes = []
  if (product.isPopular) editForm.recommendTypes.push('popular')
  if (product.isTopDeal) editForm.recommendTypes.push('top_deal')
  editForm.recommendReason = product.recommendReason || ''
  editForm.recommendPriority = product.recommendPriority || 0
  editForm.recommendStartTime = product.recommendStartTime || ''
  editForm.recommendEndTime = product.recommendEndTime || ''
  editDialogVisible.value = true
}

const saveRecommendProduct = async () => {
  if (!editFormRef.value) return
  
  const valid = await editFormRef.value.validate()
  if (!valid) return
  
  saving.value = true
  try {
    const updateData = {
      isPopular: editForm.recommendTypes.includes('popular'),
      isTopDeal: editForm.recommendTypes.includes('top_deal'),
      recommendReason: editForm.recommendReason,
      recommendPriority: editForm.recommendPriority,
      recommendStartTime: editForm.recommendStartTime,
      recommendEndTime: editForm.recommendEndTime
    }
    
    const response = await request.put(`/admin/recommend-products/${editingProduct.value.id}`, updateData)
    const data = response.data
    
    if (data.code === 200) {
      ElMessage.success('保存成功')
      editDialogVisible.value = false
      loadRecommendProducts()
      loadStats()
    } else {
      ElMessage.error(data.message || '保存失败')
    }
  } catch (error) {
    console.error('保存失败:', error)
    ElMessage.error('保存失败')
  } finally {
    saving.value = false
  }
}

const showBatchUpdateDialog = () => {
  if (selectedProducts.value.length === 0) {
    ElMessage.warning('请先选择要设置的商品')
    return
  }
  
  batchForm.recommendTypes = []
  batchForm.recommendReason = ''
  batchForm.recommendPriority = 0
  batchDialogVisible.value = true
}

const saveBatchUpdate = async () => {
  saving.value = true
  try {
    const updates = selectedProducts.value.map(product => ({
      id: product.id,
      data: {
        isPopular: batchForm.recommendTypes.includes('popular'),
        isTopDeal: batchForm.recommendTypes.includes('top_deal'),
        recommendReason: batchForm.recommendReason,
        recommendPriority: batchForm.recommendPriority
      }
    }))
    
    const response = await request.post('/admin/recommend-products/batch-update', { updates })
    const data = response.data
    
    if (data.code === 200) {
      ElMessage.success('批量设置成功')
      batchDialogVisible.value = false
      loadRecommendProducts()
      loadStats()
    } else {
      ElMessage.error(data.message || '批量设置失败')
    }
  } catch (error) {
    console.error('批量设置失败:', error)
    ElMessage.error('批量设置失败')
  } finally {
    saving.value = false
  }
}

const handleSelectionChange = (selection) => {
  selectedProducts.value = selection
}

const resetFilters = () => {
  filters.recommendType = 'all'
  filters.status = 'all'
  filters.keyword = ''
  pagination.current = 1
  loadRecommendProducts()
}

const getPriorityType = (priority) => {
  if (priority >= 80) return 'danger'
  if (priority >= 60) return 'warning'
  if (priority >= 40) return 'success'
  return 'info'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleString('zh-CN')
}

// 生命周期
onMounted(() => {
  loadRecommendProducts()
  loadStats()
})
</script>

<style scoped lang="scss">
.recommend-products {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
  
  h1 {
    margin: 0 0 8px 0;
    color: #303133;
  }
  
  p {
    margin: 0;
    color: #909399;
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
    display: flex;
    align-items: center;
    gap: 15px;
    
    .stat-icon {
      font-size: 2.5em;
      
      &.popular { color: #ff6b35; }
      &.active-popular { color: #ffd700; }
      &.top-deal { color: #e74c3c; }
      &.active-top-deal { color: #9b59b6; }
    }
    
    .stat-info {
      .stat-value {
        font-size: 2em;
        font-weight: bold;
        color: #303133;
        line-height: 1;
      }
      
      .stat-label {
        color: #909399;
        font-size: 0.9em;
        margin-top: 4px;
      }
    }
  }
}

.filter-card {
  margin-bottom: 20px;
  
  .filter-row {
    display: flex;
    gap: 15px;
    align-items: center;
  }
}

.table-card {
  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .product-info {
    display: flex;
    gap: 10px;
    
    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .product-details {
      flex: 1;
      
      .product-name {
        font-weight: 500;
        color: #303133;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
      
      .product-brand {
        color: #909399;
        font-size: 0.9em;
        margin-bottom: 2px;
      }
      
      .product-category {
        color: #c0c4cc;
        font-size: 0.8em;
      }
    }
  }
  
  .merchant-info {
    .merchant-name {
      color: #303133;
      font-weight: 500;
    }
  }
  
  .price-info {
    .sale-price {
      color: #e74c3c;
      font-weight: bold;
      font-size: 1.1em;
    }
  }
  
  .recommend-types {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .recommend-reason {
    color: #606266;
    font-size: 0.9em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .recommend-time {
    font-size: 0.8em;
    color: #909399;
    line-height: 1.4;
  }
  
  .pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
  }
}
</style>
