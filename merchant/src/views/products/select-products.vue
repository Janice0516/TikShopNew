<template>
  <div class="select-products">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('products.selectFromPlatform') }}</span>
          <el-button type="primary" @click="refreshList" :loading="loading">
            <el-icon><Refresh /></el-icon>
            {{ $t('common.refresh') }}
          </el-button>
        </div>
      </template>

      <!-- 搜索栏 -->
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item :label="$t('products.category')">
          <el-select
            v-model="searchForm.categoryId"
            :placeholder="$t('common.all')"
            clearable
            style="width: 200px"
            @change="handleSearch"
          >
            <el-option
              v-for="cat in categories"
              :key="cat.id"
              :label="cat.name"
              :value="cat.id"
            />
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

      <!-- 商品列表 -->
      <el-table
        :data="productList"
        style="width: 100%"
        v-loading="loading"
      >
        <el-table-column prop="id" label="ID" width="80" />
        
        <el-table-column :label="$t('products.productName')" min-width="200">
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

        <el-table-column :label="$t('products.category')" width="120">
          <template #default="{ row }">
            {{ getCategoryName(row.categoryId) }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.costPrice')" width="120">
          <template #default="{ row }">
            ${{ row.costPrice }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.suggestedPrice')" width="120">
          <template #default="{ row }">
            ${{ row.suggestPrice || '-' }}
          </template>
        </el-table-column>

        <el-table-column :label="$t('products.stock')" width="100">
          <template #default="{ row }">
            <el-tag :type="row.stock > 0 ? 'success' : 'danger'">
              {{ row.stock }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column :label="$t('common.actions')" width="150" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              size="small"
              @click="handleSelectProduct(row)"
            >
              {{ $t('products.selectProduct') }}
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

    <!-- 设置价格对话框 -->
    <el-dialog
      v-model="priceDialogVisible"
      :title="$t('products.setPricing')"
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
          <span>${{ selectedProduct?.costPrice }}</span>
        </el-form-item>

        <el-form-item :label="$t('products.suggestedPrice')">
          <span>${{ selectedProduct?.suggestPrice || '-' }}</span>
        </el-form-item>

        <el-form-item :label="$t('products.yourPrice')" prop="salePrice">
          <el-input-number
            v-model="priceForm.salePrice"
            :min="0"
            :precision="2"
            :placeholder="$t('products.yourPrice')"
            @change="calculateProfit"
          />
          <span style="margin-left: 10px; color: #999;">USD</span>
        </el-form-item>

        <el-form-item :label="$t('products.profit')">
          <el-tag type="success" size="large">
            ${{ calculatedProfit }}
          </el-tag>
        </el-form-item>

        <el-form-item :label="$t('products.profitMargin')">
          <el-tag type="warning" size="large">
            {{ profitMargin }}%
          </el-tag>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="priceDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="handleConfirmSelect" :loading="submitting">
          {{ $t('common.confirm') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getPlatformProducts, selectProduct, getCategories } from '@/api/product'

const { t } = useI18n()

const loading = ref(false)
const submitting = ref(false)
const productList = ref<any[]>([])
const categories = ref<any[]>([])

const searchForm = reactive({
  categoryId: null as number | null,
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

// 计算利润率
const profitMargin = computed(() => {
  if (!selectedProduct.value || !priceForm.salePrice) return '0.00'
  const margin = ((priceForm.salePrice - selectedProduct.value.costPrice) / priceForm.salePrice) * 100
  return margin.toFixed(2)
})

// 获取分类名称
const getCategoryName = (categoryId: number) => {
  const category = categories.value.find(c => c.id === categoryId)
  return category?.name || '-'
}

// 刷新列表
const refreshList = () => {
  handleSearch()
}

// 搜索
const handleSearch = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      pageSize: pagination.pageSize,
      categoryId: searchForm.categoryId,
      keyword: searchForm.keyword
    }
    
    console.log('开始获取商品列表，参数:', params)
    
    // 调用真实API
    await getPlatformProducts(params)
    console.log('商品API响应:', res)
    
    if (res && res.data && res.data.list) {
      productList.value = res.data.list
      pagination.total = res.data.total || 0
      console.log('商品列表加载成功:', productList.value.length, '个商品')
    } else {
      console.error('API响应格式不正确:', res)
      ElMessage.error('API响应格式不正确')
    }
    
  } catch (error) {
    console.error('Failed to fetch products:', error)
    ElMessage.error('获取商品列表失败: ' + (error.message || '未知错误'))
  } finally {
    loading.value = false
  }
}

// 重置
const handleReset = () => {
  searchForm.categoryId = null
  searchForm.keyword = ''
  pagination.page = 1
  handleSearch()
}

// 选择商品
const handleSelectProduct = (product: any) => {
  selectedProduct.value = product
  priceForm.salePrice = product.suggestPrice || product.costPrice * 1.5
  priceDialogVisible.value = true
}

// 计算利润
const calculateProfit = () => {
  // 触发计算
}

// 确认选品
const handleConfirmSelect = async () => {
  if (!priceFormRef.value) return
  
  await priceFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      submitting.value = true
      try {
        const data = {
          productId: selectedProduct.value.id,
          salePrice: priceForm.salePrice,
          profitMargin: parseFloat(calculatedProfit.value)
        }
        
        // 调用真实API
        await selectProduct(data)
        
        ElMessage.success(t('message.operationSuccess'))
        priceDialogVisible.value = false
        handleSearch()
      } catch (error: any) {
        ElMessage.error(error.message || t('message.operationFailed'))
      } finally {
        submitting.value = false
      }
    }
  })
}

// 加载分类
const loadCategories = async () => {
  try {
    console.log('开始加载分类...')
    // 调用真实API
    await getCategories()
    console.log('分类API响应:', res)
    
    if (res && res.data && res.data.data) {
      categories.value = res.data.data
      console.log('分类加载成功:', categories.value.length, '个分类')
    } else {
      console.error('分类API响应格式不正确:', res)
      ElMessage.error('获取分类失败')
    }
  } catch (error) {
    console.error('Failed to load categories:', error)
    ElMessage.error('获取分类失败: ' + (error.message || '未知错误'))
  }
}

onMounted(() => {
  loadCategories()
  handleSearch()
})
</script>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
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

