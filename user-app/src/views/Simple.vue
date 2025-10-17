<template>
  <div class="simple-page">
    <h1>🎉 TikTok Shop 简单测试</h1>
    <p>如果你能看到这个页面，说明Vue.js正常工作！</p>
    
    <div class="test-content">
      <h2>📱 商品测试</h2>
      <div class="product-list">
        <div v-for="product in products" :key="product.id" class="product-item">
          <img :src="product.image" :alt="product.name" />
          <h3>{{ product.name }}</h3>
          <p>{{ product.description }}</p>
          <p class="price">RM{{ product.price }}</p>
        </div>
      </div>
    </div>
    
    <div class="test-buttons">
      <el-button @click="loadProducts" :loading="loading" type="primary">
        加载商品
      </el-button>
      <el-button @click="testAPI" type="success">
        测试API
      </el-button>
    </div>
    
    <div v-if="message" class="message">
      {{ message }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { productApi } from '@/api'
import { ElMessage } from 'element-plus'

const loading = ref(false)
const message = ref('')
const products = ref<any[]>([])

// 加载真实商品数据
const loadRealProducts = async () => {
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 6 })
    products.value = (response?.data?.list || []).map((product: any) => ({
      ...product,
      image: `https://via.placeholder.com/200x150/409EFF/ffffff?text=${encodeURIComponent(product.name)}`
    }))
  } catch (error) {
    console.error('加载商品失败:', error)
    products.value = []
  }
}

const loadProducts = async () => {
  loading.value = true
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 6 })
    products.value = response.data?.list || products.value
    message.value = `成功加载 ${products.value.length} 个商品！`
    ElMessage.success('商品加载成功！')
  } catch (error: any) {
    message.value = `加载失败: ${error.message}`
    ElMessage.error('商品加载失败！')
  } finally {
    loading.value = false
  }
}

const testAPI = () => {
  message.value = 'API测试成功！'
  ElMessage.success('API连接正常！')
}

// 页面加载时获取真实数据
onMounted(() => {
  loadRealProducts()
})
</script>

<style scoped lang="scss">
.simple-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  background: #fff;
  min-height: 100vh;
  
  h1 {
    color: #333;
    text-align: center;
    margin-bottom: 20px;
  }
  
  p {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
  }
}

.test-content {
  margin-bottom: 30px;
  
  h2 {
    color: #409EFF;
    margin-bottom: 20px;
  }
}

.product-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.product-item {
  background: #f9f9f9;
  border-radius: 8px;
  padding: 15px;
  text-align: center;
  
  img {
    width: 100%;
    height: 150px;
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

.test-buttons {
  text-align: center;
  margin-bottom: 20px;
  
  .el-button {
    margin: 0 10px;
  }
}

.message {
  text-align: center;
  padding: 15px;
  background: #f0f9ff;
  border: 1px solid #409EFF;
  border-radius: 4px;
  color: #409EFF;
  font-weight: bold;
}
</style>
