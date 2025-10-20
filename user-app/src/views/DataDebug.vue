<template>
  <div class="debug-page">
    <h1>🔍 真实数据调试页面</h1>
    
    <div class="debug-section">
      <h2>📊 API状态</h2>
      <el-button @click="testAPI" :loading="apiLoading" type="primary">
        测试API连接
      </el-button>
      <div v-if="apiResult" class="api-result">
        <h3>API响应:</h3>
        <pre>{{ apiResult }}</pre>
      </div>
    </div>
    
    <div class="debug-section">
      <h2>🛍️ 商品数据 ({{ products.length }} 个)</h2>
      <el-button @click="loadProducts" :loading="productsLoading" type="success">
        加载商品数据
      </el-button>
      <div v-if="products.length > 0" class="products-list">
        <div v-for="product in products.slice(0, 5)" :key="product.id" class="product-item">
          <div class="product-info">
            <h4>{{ product.name }}</h4>
            <p><strong>价格:</strong> RM{{ product.price }}</p>
            <p><strong>原价:</strong> {{ product.originalPrice ? 'RM' + product.originalPrice : '无' }}</p>
            <p><strong>描述:</strong> {{ product.description }}</p>
            <p><strong>图片:</strong> {{ product.image }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="debug-section">
      <h2>📂 分类数据 ({{ categories.length }} 个)</h2>
      <el-button @click="loadCategories" :loading="categoriesLoading" type="warning">
        加载分类数据
      </el-button>
      <div v-if="categories.length > 0" class="categories-list">
        <div v-for="category in categories" :key="category.id" class="category-item">
          <span class="category-name">{{ category.name }}</span>
        </div>
      </div>
    </div>
    
    <div class="debug-section">
      <h2>🔧 数据转换测试</h2>
      <el-button @click="testDataTransformation" type="info">
        测试数据转换
      </el-button>
      <div v-if="transformationResult" class="transformation-result">
        <h3>转换结果:</h3>
        <pre>{{ transformationResult }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { productApi, categoryApi } from '@/api'
import { ElMessage } from 'element-plus'

const apiLoading = ref(false)
const productsLoading = ref(false)
const categoriesLoading = ref(false)
const apiResult = ref('')
const transformationResult = ref('')
const products = ref<any[]>([])
const categories = ref<any[]>([])

// 测试API连接
const testAPI = async () => {
  apiLoading.value = true
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 5 })
    apiResult.value = JSON.stringify(response, null, 2)
    ElMessage.success('API连接成功！')
  } catch (error: any) {
    apiResult.value = `错误: ${error.message}`
    ElMessage.error('API连接失败！')
  } finally {
    apiLoading.value = false
  }
}

// 加载商品数据
const loadProducts = async () => {
  productsLoading.value = true
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 10 })
    const apiProducts = response?.list || []
    
    // 使用真实数据转换
    products.value = apiProducts.map((product: any) => {
      const suggestPrice = parseFloat(product.suggestPrice) || 0
      const costPrice = parseFloat(product.costPrice) || 0
      const currentPrice = suggestPrice || costPrice
      const originalPrice = suggestPrice && costPrice && suggestPrice > costPrice ? costPrice : null
      
      return {
        id: product.id,
        name: product.name,
        description: product.description,
        price: currentPrice,
        originalPrice: originalPrice,
        image: product.mainImage || `https://via.placeholder.com/300x300/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
        rating: 4.0, // 固定评分，不使用随机数
        sales: product.sales || 0 // 使用真实销量
      }
    })
    
    ElMessage.success(`成功加载 ${products.value.length} 个商品！`)
  } catch (error: any) {
    ElMessage.error('加载商品失败！')
    products.value = []
  } finally {
    productsLoading.value = false
  }
}

// 加载分类数据
const loadCategories = async () => {
  categoriesLoading.value = true
  try {
    const response = await categoryApi.getCategories()
    categories.value = response.data || []
    ElMessage.success(`成功加载 ${categories.value.length} 个分类！`)
  } catch (error: any) {
    ElMessage.error('加载分类失败！')
    // 使用基础分类数据
    categories.value = [
      { id: '1', name: 'Womenswear & Underwear' },
      { id: '2', name: 'Phones & Electronics' },
      { id: '3', name: 'Fashion Accessories' },
      { id: '4', name: 'Menswear & Underwear' },
      { id: '5', name: 'Home Supplies' }
    ]
    ElMessage.info('使用基础分类数据')
  } finally {
    categoriesLoading.value = false
  }
}

// 测试数据转换
const testDataTransformation = () => {
  const testProduct = {
    id: "115",
    name: "Yonex Badminton Shuttlecock 12pcs",
    suggestPrice: "40.00",
    costPrice: "25.00",
    description: "High-quality feather shuttlecocks for badminton.",
    mainImage: "/static/products/yonex-shuttlecock-12pcs.jpg"
  }
  
  const suggestPrice = parseFloat(testProduct.suggestPrice) || 0
  const costPrice = parseFloat(testProduct.costPrice) || 0
  const currentPrice = suggestPrice || costPrice
  const originalPrice = suggestPrice && costPrice && suggestPrice > costPrice ? costPrice : null
  
  const transformed = {
    id: testProduct.id,
    name: testProduct.name,
    description: testProduct.description,
    price: currentPrice,
    originalPrice: originalPrice,
    image: testProduct.mainImage,
    suggestPrice: suggestPrice,
    costPrice: costPrice
  }
  
  transformationResult.value = JSON.stringify(transformed, null, 2)
}
</script>

<style scoped lang="scss">
.debug-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  background: #fff;
  min-height: 100vh;
  
  h1 {
    color: #333;
    text-align: center;
    margin-bottom: 30px;
  }
}

.debug-section {
  margin-bottom: 40px;
  padding: 20px;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  background: #f9f9f9;
  
  h2 {
    color: #409EFF;
    margin-bottom: 15px;
  }
  
  .el-button {
    margin-bottom: 15px;
  }
}

.api-result, .transformation-result {
  margin-top: 15px;
  
  pre {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    overflow-x: auto;
    font-size: 12px;
    max-height: 300px;
    overflow-y: auto;
  }
}

.products-list, .categories-list {
  margin-top: 15px;
}

.product-item, .category-item {
  background: #fff;
  padding: 15px;
  border-radius: 4px;
  margin-bottom: 10px;
  border: 1px solid #e5e5e5;
  
  h4 {
    color: #333;
    margin-bottom: 8px;
  }
  
  p {
    margin: 4px 0;
    color: #666;
    font-size: 14px;
  }
}

.category-item {
  .category-name {
    color: #333;
    font-weight: bold;
  }
}
</style>
