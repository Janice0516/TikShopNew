<template>
  <div class="product-detail">
    <div class="container">
      <div class="product-content">
        <!-- 商品图片轮播 -->
        <div class="product-images">
          <div class="main-image-container">
            <div class="main-image" @click="openImageModal">
              <img :src="currentImage" :alt="product.name" />
              <div class="image-overlay" v-if="allImages.length > 1">
                <div class="image-counter">
                  {{ currentImageIndex + 1 }} / {{ allImages.length }}
                </div>
                <div class="zoom-icon">🔍</div>
              </div>
            </div>
            
            <!-- 左右切换按钮 -->
            <div class="nav-buttons" v-if="allImages.length > 1">
              <button 
                class="nav-btn prev-btn" 
                @click="prevImage"
                :disabled="currentImageIndex === 0"
              >
                ‹
              </button>
              <button 
                class="nav-btn next-btn" 
                @click="nextImage"
                :disabled="currentImageIndex === allImages.length - 1"
              >
                ›
              </button>
            </div>
          </div>
          
          <!-- 缩略图导航 -->
          <div class="thumbnail-container" v-if="allImages.length > 1">
            <div class="thumbnail-scroll" ref="thumbnailScroll">
              <div class="thumbnail-list">
                <div 
                  v-for="(image, index) in allImages" 
                  :key="index"
                  class="thumbnail-item"
                  :class="{ active: currentImageIndex === index }"
                  @click="selectImage(index)"
                >
                  <img :src="image" :alt="product.name" />
                  <div class="thumbnail-overlay" v-if="index === 0 && product.video">
                    <div class="play-icon">▶</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="scroll-indicator" v-if="showScrollIndicator">
              <div class="scroll-arrow right" @click="scrollThumbnails('right')">›</div>
            </div>
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
              {{ t('productDetail.save') }}RM{{ (product.originalPrice - product.price).toFixed(2) }}
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
            <span class="rating-count">({{ product.reviewCount }}{{ t('productDetail.reviews') }})</span>
          </div>
          
          <!-- 商家信息 -->
          <div class="merchant-info" v-if="product.merchantName">
            <div class="merchant-header">
              <span class="merchant-label">{{ t('productDetail.merchant') }}：</span>
              <span class="merchant-name" @click="goToShop">{{ product.merchantName }}</span>
              <el-button 
                type="primary" 
                size="small" 
                @click="goToShop"
                class="shop-btn"
              >
                {{ t('productDetail.enterShop') }}
              </el-button>
            </div>
          </div>
          
          <div class="product-specs" v-if="product.specs">
            <h3>{{ t('productDetail.specifications') }}</h3>
            <div class="spec-list">
              <div v-for="(value, key) in product.specs" :key="key" class="spec-item">
                <span class="spec-label">{{ key }}:</span>
                <span class="spec-value">{{ value }}</span>
              </div>
            </div>
          </div>
          
          <div class="product-actions">
            <div class="quantity-selector">
              <label>{{ t('common.quantity') }}:</label>
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
                {{ t('home.addToCart') }}
              </el-button>
              <el-button 
                type="danger" 
                size="large"
                @click="buyNow"
                :loading="buyingNow"
              >
                {{ t('home.buyNow') }}
              </el-button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 商品详情 -->
      <div class="product-details">
        <el-tabs v-model="activeTab">
          <el-tab-pane :label="t('productDetail.productDetails')" name="details">
            <div class="detail-content" v-html="product.detailContent"></div>
          </el-tab-pane>
          <el-tab-pane :label="t('productDetail.userReviews')" name="reviews">
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
    
    <!-- 图片查看模态框 -->
    <el-dialog
      v-model="imageModalVisible"
      :show-close="false"
      :close-on-click-modal="true"
      :close-on-press-escape="true"
      width="90%"
      class="image-modal"
    >
      <div class="modal-content">
        <div class="modal-image-container">
          <img :src="currentImage" :alt="product.name" class="modal-image" />
          
          <!-- 模态框导航按钮 -->
          <div class="modal-nav-buttons" v-if="allImages.length > 1">
            <button 
              class="modal-nav-btn prev-btn" 
              @click="prevImage"
              :disabled="currentImageIndex === 0"
            >
              ‹
            </button>
            <button 
              class="modal-nav-btn next-btn" 
              @click="nextImage"
              :disabled="currentImageIndex === allImages.length - 1"
            >
              ›
            </button>
          </div>
          
          <!-- 关闭按钮 -->
          <button class="close-btn" @click="imageModalVisible = false">×</button>
        </div>
        
        <!-- 模态框缩略图 -->
        <div class="modal-thumbnails" v-if="allImages.length > 1">
          <div 
            v-for="(image, index) in allImages" 
            :key="index"
            class="modal-thumbnail"
            :class="{ active: currentImageIndex === index }"
            @click="selectImage(index)"
          >
            <img :src="image" :alt="product.name" />
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { productApi } from '@/api'
import { useCartStore } from '@/stores/cart'
import { ElMessage } from 'element-plus'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const cartStore = useCartStore()

const product = ref<any>({})
const currentImage = ref('')
const currentImageIndex = ref(0)
const allImages = ref<string[]>([])
const imageModalVisible = ref(false)
const thumbnailScroll = ref<HTMLElement>()
const showScrollIndicator = ref(false)
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
    
    // 处理图片数据
    const images: string[] = []
    
    // 添加主图
    if (product.value.image) {
      images.push(product.value.image)
    }
    
    // 添加附图
    if (product.value.images) {
      try {
        const additionalImages = JSON.parse(product.value.images)
        if (Array.isArray(additionalImages)) {
          images.push(...additionalImages)
        }
      } catch (error) {
        console.error('解析附图失败:', error)
      }
    }
    
    // 如果没有图片，使用默认图片
    if (images.length === 0) {
      images.push('https://via.placeholder.com/400x400/409EFF/ffffff?text=商品图片')
    }
    
    allImages.value = images
    currentImage.value = images[0]
    currentImageIndex.value = 0
    
    // 检查是否需要显示滚动指示器
    setTimeout(() => {
      checkScrollIndicator()
    }, 100)
    
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
    allImages.value = [product.value.image]
    currentImage.value = product.value.image
    currentImageIndex.value = 0
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

// 跳转到商家店铺
const goToShop = () => {
  if (product.value.merchantId) {
    router.push(`/shop/${product.value.merchantId}`)
  }
}

// 图片轮播相关方法
const selectImage = (index: number) => {
  currentImageIndex.value = index
  currentImage.value = allImages.value[index]
}

const prevImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
    currentImage.value = allImages.value[currentImageIndex.value]
  }
}

const nextImage = () => {
  if (currentImageIndex.value < allImages.value.length - 1) {
    currentImageIndex.value++
    currentImage.value = allImages.value[currentImageIndex.value]
  }
}

const openImageModal = () => {
  imageModalVisible.value = true
}

const checkScrollIndicator = () => {
  if (thumbnailScroll.value) {
    const container = thumbnailScroll.value
    const list = container.querySelector('.thumbnail-list') as HTMLElement
    if (list) {
      showScrollIndicator.value = list.scrollWidth > container.clientWidth
    }
  }
}

const scrollThumbnails = (direction: 'left' | 'right') => {
  if (thumbnailScroll.value) {
    const container = thumbnailScroll.value
    const scrollAmount = 100
    const currentScroll = container.scrollLeft
    
    if (direction === 'right') {
      container.scrollTo({
        left: currentScroll + scrollAmount,
        behavior: 'smooth'
      })
    } else {
      container.scrollTo({
        left: currentScroll - scrollAmount,
        behavior: 'smooth'
      })
    }
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
  .main-image-container {
    position: relative;
    width: 100%;
    height: 400px;
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    
    .main-image {
      width: 100%;
      height: 100%;
      position: relative;
      cursor: pointer;
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
      }
      
      &:hover img {
        transform: scale(1.05);
      }
      
      .image-overlay {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        
        .image-counter {
          background: rgba(0, 0, 0, 0.6);
          color: white;
          padding: 6px 12px;
          border-radius: 20px;
          font-size: 12px;
          font-weight: 500;
        }
        
        .zoom-icon {
          background: rgba(0, 0, 0, 0.6);
          color: white;
          width: 32px;
          height: 32px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 16px;
        }
      }
    }
    
    .nav-buttons {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 100%;
      display: flex;
      justify-content: space-between;
      padding: 0 15px;
      pointer-events: none;
      
      .nav-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-size: 20px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        pointer-events: all;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        
        &:hover:not(:disabled) {
          background: white;
          transform: scale(1.1);
        }
        
        &:disabled {
          opacity: 0.3;
          cursor: not-allowed;
        }
      }
    }
  }
  
  .thumbnail-container {
    position: relative;
    
    .thumbnail-scroll {
      overflow-x: auto;
      overflow-y: hidden;
      scrollbar-width: none;
      -ms-overflow-style: none;
      
      &::-webkit-scrollbar {
        display: none;
      }
      
      .thumbnail-list {
        display: flex;
        gap: 8px;
        padding: 5px 0;
        
        .thumbnail-item {
          position: relative;
          flex-shrink: 0;
          width: 80px;
          height: 80px;
          border-radius: 8px;
          overflow: hidden;
          cursor: pointer;
          border: 3px solid transparent;
          transition: all 0.3s ease;
          
          &:hover {
            transform: scale(1.05);
          }
          
          &.active {
            border-color: #409eff;
            box-shadow: 0 0 0 2px rgba(64, 158, 255, 0.3);
          }
          
          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
          
          .thumbnail-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            
            .play-icon {
              color: white;
              font-size: 24px;
              font-weight: bold;
            }
          }
        }
      }
    }
    
    .scroll-indicator {
      position: absolute;
      right: -15px;
      top: 50%;
      transform: translateY(-50%);
      
      .scroll-arrow {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-size: 16px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        
        &:hover {
          background: white;
          transform: scale(1.1);
        }
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
  
  .merchant-info {
    margin-bottom: 30px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    
    .merchant-header {
      display: flex;
      align-items: center;
      gap: 10px;
      
      .merchant-label {
        color: $text-secondary;
        font-size: 14px;
      }
      
      .merchant-name {
        color: $primary-color;
        font-weight: 500;
        cursor: pointer;
        transition: color 0.3s ease;
        
        &:hover {
          color: darken($primary-color, 10%);
        }
      }
      
      .shop-btn {
        margin-left: auto;
      }
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
  
  .product-images .main-image-container {
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

/* 图片模态框样式 */
.image-modal {
  :deep(.el-dialog) {
    background: rgba(0, 0, 0, 0.9);
    border-radius: 0;
    margin: 0;
    max-height: 100vh;
    height: 100vh;
  }
  
  :deep(.el-dialog__body) {
    padding: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

.modal-content {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  
  .modal-image-container {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    
    .modal-image {
      max-width: 90%;
      max-height: 80vh;
      object-fit: contain;
      border-radius: 8px;
    }
    
    .modal-nav-buttons {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 100%;
      display: flex;
      justify-content: space-between;
      padding: 0 30px;
      
      .modal-nav-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        
        &:hover:not(:disabled) {
          background: white;
          transform: scale(1.1);
        }
        
        &:disabled {
          opacity: 0.3;
          cursor: not-allowed;
        }
      }
    }
    
    .close-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      font-size: 24px;
      font-weight: bold;
      color: #333;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      
      &:hover {
        background: white;
        transform: scale(1.1);
      }
    }
  }
  
  .modal-thumbnails {
    display: flex;
    gap: 10px;
    padding: 20px;
    overflow-x: auto;
    max-width: 100%;
    
    .modal-thumbnail {
      flex-shrink: 0;
      width: 60px;
      height: 60px;
      border-radius: 6px;
      overflow: hidden;
      cursor: pointer;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      
      &:hover {
        transform: scale(1.1);
      }
      
      &.active {
        border-color: #409eff;
        box-shadow: 0 0 0 2px rgba(64, 158, 255, 0.3);
      }
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
  }
}
</style>