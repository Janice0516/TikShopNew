<template>
  <div class="debug-page">
    <h1>🔍 TikTok Shop 调试页面</h1>
    
    <div class="debug-section">
      <h2>📊 系统状态</h2>
      <p>✅ Vue.js 应用已加载</p>
      <p>✅ 路由系统正常</p>
      <p>✅ Element Plus 已加载</p>
    </div>
    
    <div class="debug-section">
      <h2>🌐 API 测试</h2>
      <el-button @click="testAPI" :loading="apiLoading" type="primary">
        测试 API 连接
      </el-button>
      <div v-if="apiResult" class="api-result">
        <h3>API 响应:</h3>
        <pre>{{ apiResult }}</pre>
      </div>
    </div>
    
    <div class="debug-section">
      <h2>🎨 样式测试</h2>
      <div class="test-card">
        <h3>测试卡片</h3>
        <p>如果你能看到这个卡片，说明样式系统正常。</p>
        <el-button type="success">成功按钮</el-button>
        <el-button type="warning">警告按钮</el-button>
        <el-button type="danger">危险按钮</el-button>
      </div>
    </div>
    
    <div class="debug-section">
      <h2>📱 商品数据测试</h2>
      <el-button @click="loadTestProducts" :loading="productsLoading" type="primary">
        加载测试商品
      </el-button>
      <div v-if="testProducts.length > 0" class="products-grid">
        <div v-for="product in testProducts" :key="product.id" class="product-card">
          <img :src="product.image" :alt="product.name" />
          <h3>{{ product.name }}</h3>
          <p>{{ product.description }}</p>
          <p class="price">RM{{ product.price }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { productApi } from '@/api'
import { ElMessage } from 'element-plus'

const apiLoading = ref(false)
const apiResult = ref('')
const productsLoading = ref(false)
const testProducts = ref<any[]>([])

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

// 加载测试商品
const loadTestProducts = async () => {
  productsLoading.value = true
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 6 })
    testProducts.value = (response?.list || []).map((product: any) => ({
      ...product,
      image: product.mainImage || `https://via.placeholder.com/300x200/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
      rating: 4.0, // 固定评分
      sales: product.sales || 0 // 真实销量
    }))
    ElMessage.success(`成功加载 ${testProducts.value.length} 个商品！`)
  } catch (error: any) {
    ElMessage.error('加载商品失败！')
    // 如果API失败，显示空状态，不使用模拟数据
    testProducts.value = []
  } finally {
    productsLoading.value = false
  }
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
  
  p {
    margin: 5px 0;
    color: #666;
  }
}

.api-result {
  margin-top: 15px;
  
  pre {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    overflow-x: auto;
    font-size: 12px;
  }
}

.test-card {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  
  h3 {
    color: #333;
    margin-bottom: 10px;
  }
  
  p {
    color: #666;
    margin-bottom: 15px;
  }
  
  .el-button {
    margin-right: 10px;
  }
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.product-card {
  background: #fff;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
  
  img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 10px;
  }
  
  h3 {
    color: #333;
    margin-bottom: 8px;
    font-size: 16px;
  }
  
  p {
    color: #666;
    margin-bottom: 8px;
    font-size: 14px;
  }
  
  .price {
    color: #409EFF;
    font-weight: bold;
    font-size: 18px;
  }
}
</style>
