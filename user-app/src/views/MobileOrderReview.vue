<template>
  <div class="order-review">
    <!-- 头部 -->
    <div class="header">
      <button class="back-btn" @click="goBack">
        <i class="el-icon-arrow-left"></i>
      </button>
      <h1>{{ $t('review.title') }}</h1>
      <div class="placeholder"></div>
    </div>

    <!-- 订单信息 -->
    <div class="order-info">
      <div class="order-header">
        <h3>{{ $t('review.orderInfo') }}</h3>
        <span class="order-number">{{ $t('review.orderNumber') }}: {{ order.orderNumber }}</span>
      </div>
      
      <div class="order-items">
        <div 
          v-for="item in order.items" 
          :key="item.id"
          class="order-item"
        >
          <div class="item-image">
            <img :src="item.product.image" :alt="item.product.name" />
          </div>
          <div class="item-info">
            <h4 class="item-name">{{ item.product.name }}</h4>
            <p class="item-spec">{{ item.specification }}</p>
            <div class="item-price">
              <span class="price">${{ formatPrice(item.price) }}</span>
              <span class="quantity">x{{ item.quantity }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 评价表单 -->
    <div class="review-form">
      <h3>{{ $t('review.writeReview') }}</h3>
      
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        class="review-form-content"
      >
        <!-- 商品评分 -->
        <div class="rating-section">
          <label class="rating-label">{{ $t('review.productRating') }}</label>
          <div class="rating-stars">
            <span 
              v-for="star in 5" 
              :key="star"
              class="star"
              :class="{ active: star <= form.rating }"
              @click="setRating(star)"
            >
              ⭐
            </span>
          </div>
          <span class="rating-text">{{ getRatingText(form.rating) }}</span>
        </div>

        <!-- 评价内容 -->
        <el-form-item :label="$t('review.content')" prop="content">
          <el-input
            v-model="form.content"
            type="textarea"
            :placeholder="$t('review.contentPlaceholder')"
            :rows="4"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>

        <!-- 图片上传 -->
        <el-form-item :label="$t('review.images')">
          <div class="image-upload">
            <div 
              v-for="(image, index) in form.images" 
              :key="index"
              class="uploaded-image"
            >
              <img :src="image" :alt="`Review image ${index + 1}`" />
              <button class="remove-image" @click="removeImage(index)">
                <i class="el-icon-close"></i>
              </button>
            </div>
            <div v-if="form.images.length < 5" class="upload-btn" @click="uploadImage">
              <i class="el-icon-plus"></i>
              <span>{{ $t('review.addImage') }}</span>
            </div>
          </div>
        </el-form-item>

        <!-- 匿名评价 -->
        <el-form-item>
          <el-checkbox v-model="form.isAnonymous">
            {{ $t('review.anonymousReview') }}
          </el-checkbox>
        </el-form-item>
      </el-form>
    </div>

    <!-- 提交按钮 -->
    <div class="submit-section">
      <button 
        class="submit-btn"
        :disabled="submitting"
        @click="submitReview"
      >
        <div v-if="submitting" class="loading-spinner"></div>
        {{ submitting ? $t('common.submitting') : $t('review.submitReview') }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { reviewApi } from '@/api'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

// 状态管理
const submitting = ref(false)
const formRef = ref<FormInstance>()
const order = ref<any>({})

// 表单数据
const form = reactive({
  rating: 5,
  content: '',
  images: [] as string[],
  isAnonymous: false
})

// 表单验证规则
const rules: FormRules = {
  content: [
    { required: true, message: t('review.contentRequired'), trigger: 'blur' },
    { min: 10, max: 500, message: t('review.contentLength'), trigger: 'blur' }
  ]
}

// 方法
const goBack = () => {
  router.go(-1)
}

const setRating = (rating: number) => {
  form.rating = rating
}

const getRatingText = (rating: number) => {
  const texts = [
    t('review.rating1'),
    t('review.rating2'),
    t('review.rating3'),
    t('review.rating4'),
    t('review.rating5')
  ]
  return texts[rating - 1] || ''
}

const uploadImage = () => {
  ElMessage.info(t('review.imageUploadComingSoon'))
}

const removeImage = (index: number) => {
  form.images.splice(index, 1)
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const loadOrderData = () => {
  // 从路由参数或本地存储获取订单数据
  const orderId = route.params.orderId as string
  // 这里应该调用API获取订单详情
  // 暂时使用模拟数据
  order.value = {
    orderNumber: 'ORD' + Date.now(),
    items: [
      {
        id: 1,
        product: {
          id: 1,
          name: 'Sample Product',
          image: 'https://via.placeholder.com/100x100'
        },
        specification: 'Size: M, Color: Red',
        price: 29.99,
        quantity: 1
      }
    ]
  }
}

const submitReview = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true

    const reviewData = {
      orderId: route.params.orderId,
      productId: order.value.items[0].product.id,
      rating: form.rating,
      content: form.content,
      images: form.images,
      isAnonymous: form.isAnonymous
    }

    await reviewApi.createReview(reviewData)
    ElMessage.success(t('review.submitSuccess'))
    
    // 返回订单列表
    router.push('/mobile/orders')
  } catch (error: any) {
    console.error('提交评价失败:', error)
    ElMessage.error(error.message || t('review.submitFailed'))
  } finally {
    submitting.value = false
  }
}

// 生命周期
onMounted(() => {
  loadOrderData()
})
</script>

<style scoped lang="scss">
.order-review {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #fff;
  border-bottom: 1px solid #eee;

  .back-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #f0f0f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #666;

    &:hover {
      background: #e0e0e0;
    }
  }

  h1 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #333;
  }

  .placeholder {
    width: 40px;
  }
}

.order-info {
  background: #fff;
  margin-bottom: 12px;
  padding: 20px;

  .order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;

    h3 {
      font-size: 16px;
      font-weight: 600;
      margin: 0;
      color: #333;
    }

    .order-number {
      font-size: 14px;
      color: #666;
    }
  }

  .order-items {
    .order-item {
      display: flex;
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px solid #f0f0f0;

      &:last-child {
        border-bottom: none;
      }

      .item-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }

      .item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        .item-name {
          font-size: 14px;
          font-weight: 600;
          color: #333;
          margin: 0 0 4px;
          line-height: 1.3;
        }

        .item-spec {
          font-size: 12px;
          color: #666;
          margin: 0 0 8px;
        }

        .item-price {
          display: flex;
          justify-content: space-between;
          align-items: center;

          .price {
            font-size: 14px;
            font-weight: 600;
            color: #f56c6c;
          }

          .quantity {
            font-size: 12px;
            color: #666;
          }
        }
      }
    }
  }
}

.review-form {
  background: #fff;
  margin-bottom: 12px;
  padding: 20px;

  h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 20px;
    color: #333;
  }
}

.rating-section {
  margin-bottom: 20px;

  .rating-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }

  .rating-stars {
    display: flex;
    gap: 4px;
    margin-bottom: 8px;

    .star {
      font-size: 24px;
      cursor: pointer;
      opacity: 0.3;
      transition: all 0.2s;

      &.active {
        opacity: 1;
      }

      &:hover {
        opacity: 1;
      }
    }
  }

  .rating-text {
    font-size: 14px;
    color: #666;
  }
}

.image-upload {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;

  .uploaded-image {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .remove-image {
      position: absolute;
      top: 4px;
      right: 4px;
      width: 20px;
      height: 20px;
      background: rgba(0, 0, 0, 0.6);
      color: white;
      border: none;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 12px;

      &:hover {
        background: rgba(0, 0, 0, 0.8);
      }
    }
  }

  .upload-btn {
    width: 80px;
    height: 80px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;

    &:hover {
      border-color: #409eff;
      color: #409eff;
    }

    i {
      font-size: 20px;
      margin-bottom: 4px;
    }

    span {
      font-size: 12px;
    }
  }
}

.submit-section {
  padding: 20px;

  .submit-btn {
    width: 100%;
    height: 48px;
    background: #409eff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;

    &:hover:not(:disabled) {
      background: #337ecc;
    }

    &:disabled {
      background: #c0c4cc;
      cursor: not-allowed;
    }

    .loading-spinner {
      width: 16px;
      height: 16px;
      border: 2px solid #fff;
      border-top: 2px solid transparent;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

