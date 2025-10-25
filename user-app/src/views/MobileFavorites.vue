<template>
  <div class="favorites-page">
    <!-- 头部 -->
    <div class="header">
      <button class="back-btn" @click="goBack">
        <i class="el-icon-arrow-left"></i>
      </button>
      <h1>{{ $t('favorites.title') }}</h1>
      <button v-if="favorites.length > 0" class="clear-btn" @click="clearAllFavorites">
        {{ $t('favorites.clearAll') }}
      </button>
      <div v-else class="placeholder"></div>
    </div>

    <!-- 收藏列表 -->
    <div class="favorites-content">
      <div v-if="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>{{ $t('common.loading') }}</p>
      </div>

      <div v-else-if="favorites.length === 0" class="empty-state">
        <div class="empty-icon">❤️</div>
        <h3>{{ $t('favorites.noFavorites') }}</h3>
        <p>{{ $t('favorites.noFavoritesDesc') }}</p>
        <button class="go-shopping-btn" @click="goShopping">
          {{ $t('favorites.goShopping') }}
        </button>
      </div>

      <div v-else class="favorites-list">
        <div 
          v-for="item in favorites" 
          :key="item.id"
          class="favorite-item"
        >
          <div class="product-image" @click="goToProduct(item.product)">
            <img :src="item.product.image" :alt="item.product.name" />
          </div>
          
          <div class="product-info">
            <h3 class="product-name" @click="goToProduct(item.product)">
              {{ item.product.name }}
            </h3>
            <p class="product-desc">{{ item.product.description }}</p>
            
            <div class="product-price">
              <span class="current-price">${{ formatPrice(item.product.price) }}</span>
              <span v-if="item.product.originalPrice" class="original-price">
                ${{ formatPrice(item.product.originalPrice) }}
              </span>
            </div>
            
            <div class="product-actions">
              <button class="add-to-cart-btn" @click="addToCart(item.product)">
                {{ $t('favorites.addToCart') }}
              </button>
              <button class="remove-btn" @click="removeFavorite(item.product.id)">
                {{ $t('favorites.remove') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { favoritesApi } from '@/api'
import { useCartStore } from '@/stores/cart'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const { t } = useI18n()
const cartStore = useCartStore()
const userStore = useUserStore()

// 状态管理
const loading = ref(false)
const favorites = ref<any[]>([])

// 方法
const goBack = () => {
  router.go(-1)
}

const goShopping = () => {
  router.push('/mobile')
}

const goToProduct = (product: any) => {
  router.push(`/mobile/product/${product.id}`)
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const addToCart = async (product: any) => {
  try {
    // 检查用户是否登录
    if (!userStore.isLoggedIn) {
      ElMessage.warning(t('common.loginRequired'))
      router.push('/mobile/login')
      return
    }
    
    await cartStore.addToCart(String(product.id), 1)
    ElMessage.success(t('favorites.addToCartSuccess'))
  } catch (error: any) {
    console.error('加入购物车失败:', error)
    ElMessage.error(error.message || t('favorites.addToCartFailed'))
  }
}

const removeFavorite = async (productId: string) => {
  try {
    await ElMessageBox.confirm(
      t('favorites.removeConfirm'),
      t('common.confirm'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning',
      }
    )
    
    await favoritesApi.removeFromFavorites(productId)
    ElMessage.success(t('favorites.removeSuccess'))
    await loadFavorites()
  } catch (error: any) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || t('favorites.removeFailed'))
    }
  }
}

const clearAllFavorites = async () => {
  try {
    await ElMessageBox.confirm(
      t('favorites.clearAllConfirm'),
      t('common.confirm'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning',
      }
    )
    
    // 批量删除收藏
    for (const item of favorites.value) {
      await favoritesApi.removeFromFavorites(item.product.id)
    }
    
    ElMessage.success(t('favorites.clearAllSuccess'))
    await loadFavorites()
  } catch (error: any) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || t('favorites.clearAllFailed'))
    }
  }
}

const loadFavorites = async () => {
  try {
    loading.value = true
    const response = await favoritesApi.getFavorites()
    favorites.value = response.data || []
  } catch (error: any) {
    console.error('加载收藏失败:', error)
    ElMessage.error(error.message || t('favorites.loadFailed'))
  } finally {
    loading.value = false
  }
}

// 生命周期
onMounted(() => {
  loadFavorites()
})
</script>

<style scoped lang="scss">
.favorites-page {
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

  .clear-btn {
    background: #f56c6c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #f78989;
    }
  }

  .placeholder {
    width: 60px;
  }
}

.favorites-content {
  padding: 20px;
}

.loading {
  text-align: center;
  padding: 40px 0;

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #409eff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
  }

  p {
    color: #666;
    margin: 0;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 60px 20px;

  .empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
  }

  h3 {
    font-size: 18px;
    color: #333;
    margin: 0 0 8px;
  }

  p {
    color: #666;
    margin: 0 0 24px;
  }

  .go-shopping-btn {
    background: #409eff;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;

    &:hover {
      background: #337ecc;
    }
  }
}

.favorites-list {
  .favorite-item {
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    gap: 12px;

    .product-image {
      width: 100px;
      height: 100px;
      border-radius: 8px;
      overflow: hidden;
      cursor: pointer;
      flex-shrink: 0;

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }

    .product-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;

      .product-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0 0 4px;
        cursor: pointer;
        line-height: 1.3;

        &:hover {
          color: #409eff;
        }
      }

      .product-desc {
        font-size: 14px;
        color: #666;
        margin: 0 0 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }

      .product-price {
        margin-bottom: 12px;

        .current-price {
          font-size: 16px;
          font-weight: 600;
          color: #f56c6c;
          margin-right: 8px;
        }

        .original-price {
          font-size: 14px;
          color: #999;
          text-decoration: line-through;
        }
      }

      .product-actions {
        display: flex;
        gap: 8px;

        button {
          flex: 1;
          padding: 8px 12px;
          border: 1px solid #ddd;
          background: #fff;
          border-radius: 4px;
          font-size: 14px;
          cursor: pointer;
          transition: all 0.2s;

          &.add-to-cart-btn {
            color: #409eff;
            border-color: #409eff;

            &:hover {
              background: #409eff;
              color: white;
            }
          }

          &.remove-btn {
            color: #f56c6c;
            border-color: #f56c6c;

            &:hover {
              background: #f56c6c;
              color: white;
            }
          }
        }
      }
    }
  }
}
</style>

