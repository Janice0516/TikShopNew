<template>
  <div class="home">
    <h1>🎉 TikTok Shop</h1>
    <p>欢迎来到TikTok Shop！</p>
    
    <div class="loading" v-if="loading">
      <p>正在加载商品...</p>
    </div>
    
    <div class="products" v-else>
      <h2>热门商品</h2>
      <div class="product-grid">
        <div v-for="product in products" :key="product.id" class="product-card">
          <img :src="product.image" :alt="product.name" />
          <h3>{{ product.name }}</h3>
          <p>{{ product.description }}</p>
          <p class="price">RM{{ product.price }}</p>
        </div>
      </div>
    </div>
    
    <div class="error" v-if="error">
      <p>加载失败: {{ error }}</p>
      <button @click="loadProducts">重试</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { productApi } from '@/api'

const loading = ref(true)
const error = ref('')
const products = ref<any[]>([])

const loadProducts = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await productApi.getProducts({ page: 1, pageSize: 8 })
    products.value = (response?.list || []).map((product: any) => ({
      ...product,
      image: product.mainImage || `https://via.placeholder.com/300x200/409EFF/ffffff?text=${encodeURIComponent(product.name)}`,
      rating: 4.0, // 固定评分
      sales: product.sales || 0 // 真实销量
    }))
    console.log('商品加载成功:', products.value.length)
  } catch (err: any) {
    error.value = err.message
    console.error('商品加载失败:', err)
    
    // 如果API失败，显示空状态，不使用模拟数据
    products.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  console.log('Home组件已挂载')
  loadProducts()
})
</script>

<style scoped lang="scss">
.home {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  background: #fff;
  min-height: 100vh;
  
  h1 {
    color: #333;
    text-align: center;
    margin-bottom: 20px;
    font-size: 32px;
  }
  
  p {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
    font-size: 18px;
  }
}

.loading {
  text-align: center;
  padding: 50px;
  
  p {
    color: #409EFF;
    font-size: 20px;
  }
}

.products {
  h2 {
    color: #409EFF;
    margin-bottom: 20px;
    text-align: center;
  }
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.product-card {
  background: #f9f9f9;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
  }
  
  h3 {
    color: #333;
    margin-bottom: 10px;
    font-size: 18px;
    font-weight: bold;
  }
  
  p {
    color: #666;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 1.4;
  }
  
  .price {
    color: #409EFF;
    font-weight: bold;
    font-size: 20px;
  }
}

.error {
  text-align: center;
  padding: 30px;
  background: #fef0f0;
  border: 1px solid #f56c6c;
  border-radius: 8px;
  margin: 20px 0;
  
  p {
    color: #f56c6c;
    margin-bottom: 15px;
  }
  
  button {
    background: #409EFF;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    
    &:hover {
      background: #337ecc;
    }
  }
}

@media (max-width: 768px) {
  .product-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
  }
  
  .product-card {
    padding: 15px;
  }
}
</style>
