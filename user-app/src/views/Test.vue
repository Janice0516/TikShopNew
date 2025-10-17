<template>
  <div class="simple-test">
    <h1>🎉 TikTok Shop 测试页面</h1>
    <p>如果你能看到这个页面，说明Vue.js应用正常工作！</p>
    
    <div class="test-section">
      <h2>📱 商品展示测试</h2>
      <div class="product-grid">
        <div v-for="product in products" :key="product.id" class="product-card">
          <img :src="product.image" :alt="product.name" />
          <h3>{{ product.name }}</h3>
          <p>{{ product.description }}</p>
          <p class="price">RM{{ product.price }}</p>
        </div>
      </div>
    </div>
    
    <div class="test-section">
      <h2>🔧 功能测试</h2>
      <el-button @click="testAPI" :loading="loading" type="primary">
        测试API连接
      </el-button>
      <p v-if="apiStatus">{{ apiStatus }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { productApi } from '@/api'
import { ElMessage } from 'element-plus'

const loading = ref(false)
const apiStatus = ref('')
const products = ref<any[]>([])

// 加载真实商品数据
const loadRealProducts = async () => {
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 6 })
    products.value = (response?.data?.list || []).map((product: any) => ({
      ...product,
      image: `https://via.placeholder.com/300x200/409EFF/ffffff?text=${encodeURIComponent(product.name)}`
    }))
  } catch (error) {
    console.error('加载商品失败:', error)
    products.value = []
  }
}

const testAPI = async () => {
  loading.value = true
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 5 })
    apiStatus.value = `✅ API连接成功！获取到 ${response.data?.list?.length || 0} 个商品`
    ElMessage.success('API连接成功！')
  } catch (error: any) {
    apiStatus.value = `❌ API连接失败: ${error.message}`
    ElMessage.error('API连接失败！')
  } finally {
    loading.value = false
  }
}

// 页面加载时获取真实数据
onMounted(() => {
  loadRealProducts()
})
</script>

<style scoped lang="scss">
.simple-test {
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

.test-section {
  margin-bottom: 40px;
  padding: 20px;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  background: #f9f9f9;
  
  h2 {
    color: #409EFF;
    margin-bottom: 15px;
  }
}

.product-grid {
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
