<template>
  <div class="product-detail">
    <div class="container">
      <div class="product-content">
        <!-- 商品图片 -->
        <div class="product-images">
          <div class="main-image">
            <img :src="product.image" :alt="product.name" />
          </div>
          <div class="thumbnail-images" v-if="product.images && product.images.length > 1">
            <img 
              v-for="(image, index) in product.images" 
              :key="index"
              :src="image" 
              :alt="product.name"
              @click="currentImage = image"
              :class="{ active: currentImage === image }"
            />
          </div>
        </div>
        
        <!-- 商品信息 -->
        <div class="product-info">
          <h1 class="product-name">{{ product.name }}</h1>
          <p class="product-description">{{ product.description }}</p>
          
          <div class="product-price-section">
            <div class="current-price">RM{{ product.price }}</div>
            <div class="original-price" v-if="product.originalPrice">
              RM{{ product.originalPrice }}
            </div>
            <div class="discount" v-if="product.originalPrice">
              省RM{{ (product.originalPrice - product.price).toFixed(2) }}
            </div>
          </div>
          
          <div class="product-rating" v-if="product.rating">
            <el-rate 
              v-model="product.rating" 
              disabled 
              show-score 
              text-color="#ff9900"
              score-template="{value}"
            />
            <span class="rating-count">({{ product.reviewCount }}条评价)</span>
          </div>
          
          <div class="product-specs" v-if="product.specs">
            <h3>商品规格</h3>
            <div class="spec-list">
              <div v-for="(value, key) in product.specs" :key="key" class="spec-item">
                <span class="spec-label">{{ key }}:</span>
                <span class="spec-value">{{ value }}</span>
              </div>
            </div>
          </div>
          
          <div class="product-actions">
            <div class="quantity-selector">
              <label>数量:</label>
              <el-input-number 
                v-model="quantity" 
                :min="1" 
                :max="product.stock || 99"
                controls-position="right"
              />
            </div>
            
            <div class="action-buttons">
              <el-button 
                type="primary" 
                size="large"
                @click="addToCart"
                :loading="addingToCart"
              >
                加入购物车
              </el-button>
              <el-button 
                type="danger" 
                size="large"
                @click="buyNow"
                :loading="buyingNow"
              >
                立即购买
              </el-button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 商品详情 -->
      <div class="product-details">
        <el-tabs v-model="activeTab">
          <el-tab-pane label="商品详情" name="details">
            <div class="detail-content" v-html="product.detailContent"></div>
          </el-tab-pane>
          <el-tab-pane label="用户评价" name="reviews">
            <div class="reviews-content">
              <div v-for="review in product.reviews" :key="review.id" class="review-item">
                <div class="review-header">
                  <span class="reviewer-name">{{ review.userName }}</span>
                  <el-rate v-model="review.rating" disabled />
                  <span class="review-date">{{ review.date }}</span>
                </div>
                <p class="review-content">{{ review.content }}</p>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { productApi } from '@/api'
import { useCartStore } from '@/stores/cart'
import { ElMessage } from 'element-plus'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const product = ref<any>({})
const currentImage = ref('')
const quantity = ref(1)
const activeTab = ref('details')
const addingToCart = ref(false)
const buyingNow = ref(false)

// 加载商品详情
const loadProductDetail = async () => {
  try {
    const productId = route.params.id as string
    const response = await productApi.getProductDetail(productId)
    product.value = response
    
    // 设置默认图片
    currentImage.value = product.value.image || ''
    
    // 设置默认规格
    if (product.value.specs) {
      product.value.specs = {
        '品牌': product.value.brand || '未知',
        '型号': product.value.model || '未知',
        '颜色': product.value.color || '未知',
        '尺寸': product.value.size || '未知'
      }
    }
  } catch (error) {
    console.error('加载商品详情失败:', error)
    ElMessage.error('加载商品详情失败')
    
    // 使用默认数据
    product.value = {
      id: route.params.id,
      name: '商品名称',
      description: '商品描述',
      price: 99.99,
      originalPrice: 129.99,
      image: 'https://via.placeholder.com/400x400/409EFF/ffffff?text=商品图片',
      rating: 4.5,
      reviewCount: 128,
      stock: 50,
      detailContent: '<p>这里是商品详情内容</p>',
      reviews: [
        {
          id: 1,
          userName: '用户1',
          rating: 5,
          date: '2024-01-01',
          content: '商品质量很好，推荐购买！'
        }
      ]
    }
    currentImage.value = product.value.image
  }
}

// 添加到购物车
const addToCart = async () => {
  try {
    addingToCart.value = true
    await cartStore.addToCart(product.value.id, quantity.value)
    ElMessage.success('已添加到购物车')
  } catch (error) {
    ElMessage.error('添加失败，请重试')
  } finally {
    addingToCart.value = false
  }
}

// 立即购买
const buyNow = async () => {
  try {
    buyingNow.value = true
    await cartStore.addToCart(product.value.id, quantity.value)
    router.push('/order')
  } catch (error) {
    ElMessage.error('购买失败，请重试')
  } finally {
    buyingNow.value = false
  }
}

onMounted(() => {
  loadProductDetail()
})
</script>

<style scoped lang="scss">
.product-detail {
  padding: 20px 0;
  background: #fff;
}

.product-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-bottom: 40px;
}

.product-images {
  .main-image {
    width: 100%;
    height: 400px;
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  
  .thumbnail-images {
    display: flex;
    gap: 10px;
    
    img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      cursor: pointer;
      border: 2px solid transparent;
      
      &.active {
        border-color: $primary-color;
      }
    }
  }
}

.product-info {
  .product-name {
    font-size: 24px;
    font-weight: bold;
    color: $text-primary;
    margin-bottom: 15px;
  }
  
  .product-description {
    font-size: 16px;
    color: $text-secondary;
    line-height: 1.6;
    margin-bottom: 20px;
  }
  
  .product-price-section {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    
    .current-price {
      font-size: 28px;
      font-weight: bold;
      color: $danger-color;
    }
    
    .original-price {
      font-size: 18px;
      color: $text-secondary;
      text-decoration: line-through;
    }
    
    .discount {
      background: $danger-color;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
    }
  }
  
  .product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
    
    .rating-count {
      color: $text-secondary;
    }
  }
  
  .product-specs {
    margin-bottom: 30px;
    
    h3 {
      font-size: 18px;
      margin-bottom: 15px;
      color: $text-primary;
    }
    
    .spec-list {
      .spec-item {
        display: flex;
        margin-bottom: 8px;
        
        .spec-label {
          width: 80px;
          color: $text-secondary;
        }
        
        .spec-value {
          color: $text-primary;
        }
      }
    }
  }
  
  .product-actions {
    .quantity-selector {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
      
      label {
        font-weight: 500;
      }
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      
      .el-button {
        flex: 1;
      }
    }
  }
}

.product-details {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.detail-content {
  line-height: 1.6;
  color: $text-primary;
}

.reviews-content {
  .review-item {
    padding: 15px 0;
    border-bottom: 1px solid $border-lighter;
    
    &:last-child {
      border-bottom: none;
    }
    
    .review-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
      
      .reviewer-name {
        font-weight: 500;
        color: $text-primary;
      }
      
      .review-date {
        color: $text-secondary;
        font-size: 12px;
      }
    }
    
    .review-content {
      color: $text-regular;
      line-height: 1.5;
    }
  }
}

@media (max-width: 768px) {
  .product-content {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .product-images .main-image {
    height: 300px;
  }
  
  .product-info .product-name {
    font-size: 20px;
  }
  
  .product-info .product-price-section .current-price {
    font-size: 24px;
  }
  
  .product-info .product-actions .action-buttons {
    flex-direction: column;
  }
}
</style>
