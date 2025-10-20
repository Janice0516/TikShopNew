<template>
  <div class="my-products">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('products.myProducts') }}</span>
          <el-button type="primary" @click="goToSelectProducts">
            <el-icon><Plus /></el-icon>
            {{ $t('products.selectFromPlatform') }}
          </el-button>
        </div>
      </template>

      <!-- 数据统计 -->
      <el-row :gutter="20" class="stats-row">
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value">{{ stats.total }}</div>
            <div class="stat-label">{{ $t('products.totalProducts') }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value" style="color: #67C23A">{{ stats.onShelf }}</div>
            <div class="stat-label">{{ $t('products.onShelf') }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value" style="color: #E6A23C">{{ stats.offShelf }}</div>
            <div class="stat-label">{{ $t('products.offShelf') }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-value" style="color: #409EFF">RM{{ stats.totalRevenue }}</div>
            <div class="stat-label">{{ $t('dashboard.todaySales') }}</div>
          </div>
        </el-col>
      </el-row>

      <!-- 搜索和筛选 -->
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item :label="$t('products.status')">
          <el-select
            v-model="searchForm.status"
            :placeholder="$t('common.all')"
            clearable
            style="width: 150px"
            @change="handleSearch"
          >
            <el-option :label="$t('common.all')" :value="null" />
            <el-option :label="$t('products.onShelf')" :value="1" />
            <el-option :label="$t('products.offShelf')" :value="0" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('products.productName')">
          <el-input
            v-model="searchForm.keyword"
            :placeholder="$t('common.search')"
            clearable
            style="width: 300px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSearch">
            {{ $t('common.search') }}
          </el-button>
          <el-button @click="handleReset">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>

      <!-- 商品表格 -->
      <el-table
        :data="productList"
        style="width: 100%"
        v-loading="loading"
      >
        <el-table-column prop="id" label="ID" width="80" />
        
        <el-table-column :label="$t('products.productName')" min-width="250">
          <template #default="{ row }">
            <div class="product-info">
              <el-image
                :src="row.mainImage || '/placeholder.jpg'"
                style="width: 60px; height: 60px; margin-right: 10px"
                fit="cover"
              />
              <div>
                <div class="product-name">{{ row.name }}</div>
                <div class="product-brand">{{ row.brand }}</div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.costPrice')" width="120">
          <template #default="{ row }">
            RM{{ row.costPrice }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.salePrice')" width="120">
          <template #default="{ row }">
            <span style="color: #409EFF; font-weight: 500">RM{{ row.salePrice }}</span>
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.profit')" width="120">
          <template #default="{ row }">
            <el-tag type="success">
              RM{{ (row.salePrice - row.costPrice).toFixed(2) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.stock')" width="100">
          <template #default="{ row }">
            <el-tag :type="row.stock > 10 ? 'success' : 'warning'">
              {{ row.stock }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.sales')" width="100">
          <template #default="{ row }">
            {{ row.sales || 0 }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.status')" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'info'">
              {{ row.status === 1 ? $t('products.onShelf') : $t('products.offShelf') }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('common.actions')" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              size="small"
              @click="handleEditPrice(row)"
            >
              {{ $t('common.edit') }}
            </el-button>
            <el-button
              :type="row.status === 1 ? 'warning' : 'success'"
              size="small"
              @click="handleToggleStatus(row)"
            >
              {{ row.status === 1 ? $t('products.offShelf') : $t('products.onShelf') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <el-pagination
        v-model:current-page="pagination.page"
        v-model:page-size="pagination.pageSize"
        :total="pagination.total"
        :page-sizes="[10, 20, 50, 100]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSearch"
        @current-change="handleSearch"
        style="margin-top: 20px; justify-content: flex-end"
      />
    </el-card>

    <!-- 编辑价格对话框 -->
    <el-dialog
      v-model="priceDialogVisible"
      :title="$t('products.editProduct')"
      width="600px"
    >
      <el-form
        ref="priceFormRef"
        :model="priceForm"
        :rules="priceRules"
        label-width="150px"
      >
        <el-form-item :label="$t('products.productName')">
          <span>{{ selectedProduct?.name }}</span>
        </el-form-item>

        <el-form-item :label="$t('products.costPrice')">
          <span>RM{{ selectedProduct?.costPrice }}</span>
        </el-form-item>

        <el-form-item :label="$t('products.salePrice')" prop="salePrice">
          <el-input-number
            v-model="priceForm.salePrice"
            :min="0"
            :precision="2"
            :placeholder="$t('products.salePrice')"
          />
          <span style="margin-left: 10px; color: #999;">RM</span>
        </el-form-item>

        <el-form-item :label="$t('products.profit')">
          <el-tag type="success" size="large">
            RM{{ calculatedProfit }}
          </el-tag>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="priceDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="handleConfirmEdit" :loading="submitting">
          {{ $t('common.save') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getMerchantProducts, updateProductPrice, toggleProductStatus } from '@/api/product'

const { t } = useI18n()
const router = useRouter()

const loading = ref(false)
const submitting = ref(false)
const productList = ref<any[]>([])

const stats = ref({
  total: 0,
  onShelf: 0,
  offShelf: 0,
  totalRevenue: '0.00'
})

const searchForm = reactive({
  status: null as number | null,
  keyword: ''
})

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 价格对话框
const priceDialogVisible = ref(false)
const selectedProduct = ref<any>(null)
const priceFormRef = ref()
const priceForm = reactive({
  salePrice: 0
})

const priceRules = {
  salePrice: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { type: 'number', min: 0.01, message: () => t('validation.positive'), trigger: 'blur' }
  ]
}

// 计算利润
const calculatedProfit = computed(() => {
  if (!selectedProduct.value || !priceForm.salePrice) return '0.00'
  const profit = priceForm.salePrice - selectedProduct.value.costPrice
  return profit.toFixed(2)
})

// 跳转到选品页面
const goToSelectProducts = () => {
  router.push('/products/select-products')
}

// 搜索
const handleSearch = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      pageSize: pagination.pageSize,
      status: searchForm.status,
      keyword: searchForm.keyword
    }
    
    console.log('商家商品列表API调用参数:', params);
    
    // 调用真实API
    const res = await getMerchantProducts(params)
    console.log('商家商品列表API响应:', res);
    
    productList.value = res.list || []
    pagination.total = res.total || 0
    
    console.log('处理后的商品列表:', productList.value);
    console.log('商品总数:', pagination.total);
    
    // 更新统计
    stats.value = {
      total: productList.value.length,
      onShelf: productList.value.filter(p => p.status === 1).length,
      offShelf: productList.value.filter(p => p.status === 0).length,
      totalRevenue: productList.value.reduce((sum, p) => sum + (p.salePrice * p.sales), 0).toFixed(2)
    }
  } catch (error) {
    console.error('Failed to fetch products:', error)
    ElMessage.error('获取商品列表失败')
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  searchForm.status = null
  searchForm.keyword = ''
  pagination.page = 1
  handleSearch()
}

// 编辑价格
const handleEditPrice = (product: any) => {
  selectedProduct.value = product
  priceForm.salePrice = product.salePrice
  priceDialogVisible.value = true
}

// 确认编辑
const handleConfirmEdit = async () => {
  if (!priceFormRef.value) return
  
  await priceFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      submitting.value = true
      try {
        // 调用真实API
        await updateProductPrice(selectedProduct.value.id, { salePrice: priceForm.salePrice })
        
        ElMessage.success(t('message.updateSuccess'))
        priceDialogVisible.value = false
        handleSearch()
      } catch (error: any) {
        ElMessage.error(error.message || t('message.updateFailed'))
      } finally {
        submitting.value = false
      }
    }
  })
}

// 上下架
const handleToggleStatus = async (product: any) => {
  const action = product.status === 1 ? t('products.offShelf') : t('products.onShelf')
  
  try {
    await ElMessageBox.confirm(
      `确定要${action}该商品吗？`,
      t('common.warning'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    // 调用真实API
    await toggleProductStatus(product.id, product.status === 1 ? 0 : 1)
    
    ElMessage.success(t('message.operationSuccess'))
    handleSearch()
  } catch (error) {
    // 取消操作
  }
}

onMounted(() => {
  handleSearch()
})
</script>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stats-row {
  margin-bottom: 30px;
}

.stat-card {
  background: #f5f7fa;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.stat-value {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.search-form {
  margin-bottom: 20px;
}

.product-info {
  display: flex;
  align-items: center;
}

.product-name {
  font-weight: 500;
  margin-bottom: 5px;
}

.product-brand {
  font-size: 12px;
  color: #999;
}
</style>

