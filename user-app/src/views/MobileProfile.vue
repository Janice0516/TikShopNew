<template>
  <div class="mobile-profile">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <h1 class="header-title">{{ t('navigation.profile') }}</h1>
      <button class="settings-btn" @click="goToSettings">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
          <path d="M19.4 15A1.65 1.65 0 0 0 21 12A1.65 1.65 0 0 0 19.4 9A1.65 1.65 0 0 0 17 7.4A1.65 1.65 0 0 0 14 9A1.65 1.65 0 0 0 12 7.4A1.65 1.65 0 0 0 9 9A1.65 1.65 0 0 0 7.4 12A1.65 1.65 0 0 0 9 15A1.65 1.65 0 0 0 12 16.6A1.65 1.65 0 0 0 14 15A1.65 1.65 0 0 0 17 16.6A1.65 1.65 0 0 0 19.4 15Z" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
      <!-- Loading State -->
      <MobileLoading 
        :loading="isLoading"
        type="skeleton"
        skeleton-type="card"
        :skeleton-count="2"
        message="加载用户信息中..."
      />
      
      <!-- Error State -->
      <MobileError 
        :show="hasError"
        type="server"
        title="加载失败"
        message="无法加载用户信息，请重试"
        @retry="handleRetry"
      />
      
      <!-- User Info Card -->
      <div v-if="!isLoading && !hasError" class="user-card">
        <div class="user-avatar" @click="changeAvatar">
          <img v-if="userInfo.avatar" :src="userInfo.avatar" :alt="userInfo.name" />
          <div v-else class="avatar-placeholder">
            {{ userInfo.name?.charAt(0) || 'U' }}
          </div>
          <div class="avatar-edit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
        
        <div class="user-info">
          <h2 class="user-name">{{ userInfo.name || t('profile.defaultUser') }}</h2>
          <p class="user-phone">{{ userInfo.phone || t('profile.noPhone') }}</p>
          <p class="user-email">{{ userInfo.email || t('profile.noEmail') }}</p>
        </div>
        
        <button class="edit-profile-btn" @click="editProfile">
          {{ t('profile.editProfile') }}
        </button>
      </div>

      <!-- Quick Stats -->
      <div class="stats-section">
        <div class="stat-item" @click="goToOrders">
          <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M16 11V7A4 4 0 0 0 8 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ userStats.orders || 0 }}</div>
            <div class="stat-label">{{ t('profile.orders') }}</div>
          </div>
        </div>
        
        <div class="stat-item" @click="goToCart">
          <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <circle cx="9" cy="21" r="1" fill="currentColor"/>
              <circle cx="20" cy="21" r="1" fill="currentColor"/>
              <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ userStats.cartItems || 0 }}</div>
            <div class="stat-label">{{ t('profile.cartItems') }}</div>
          </div>
        </div>
        
        <div class="stat-item" @click="goToFavorites">
          <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M20.84 4.61A5.5 5.5 0 0 0 7.5 4.61L12 9L16.5 4.61A5.5 5.5 0 0 0 20.84 4.61Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ userStats.favorites || 0 }}</div>
            <div class="stat-label">{{ t('profile.favorites') }}</div>
          </div>
        </div>
      </div>

      <!-- Menu Sections -->
      <div class="menu-sections">
        <!-- Orders Section -->
        <div class="menu-section">
          <h3 class="section-title">{{ t('profile.orders') }}</h3>
          <div class="menu-items">
            <div class="menu-item" @click="goToOrders">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M16 11V7A4 4 0 0 0 8 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.allOrders') }}</div>
                <div class="menu-subtitle">{{ t('profile.viewOrderHistory') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            
            <div class="menu-item" @click="goToPendingOrders">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.pendingOrders') }}</div>
                <div class="menu-subtitle">{{ t('profile.trackPendingOrders') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Shopping Section -->
        <div class="menu-section">
          <h3 class="section-title">{{ t('profile.shopping') }}</h3>
          <div class="menu-items">
            <div class="menu-item" @click="goToCart">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="9" cy="21" r="1" fill="currentColor"/>
                  <circle cx="20" cy="21" r="1" fill="currentColor"/>
                  <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('navigation.cart') }}</div>
                <div class="menu-subtitle">{{ t('profile.viewCartItems') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            
            <div class="menu-item" @click="goToFavorites">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M20.84 4.61A5.5 5.5 0 0 0 7.5 4.61L12 9L16.5 4.61A5.5 5.5 0 0 0 20.84 4.61Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.favorites') }}</div>
                <div class="menu-subtitle">{{ t('profile.viewFavoriteItems') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Section -->
        <div class="menu-section">
          <h3 class="section-title">{{ t('profile.account') }}</h3>
          <div class="menu-items">
            <div class="menu-item" @click="goToSettings">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                  <path d="M19.4 15A1.65 1.65 0 0 0 21 12A1.65 1.65 0 0 0 19.4 9A1.65 1.65 0 0 0 17 7.4A1.65 1.65 0 0 0 14 9A1.65 1.65 0 0 0 12 7.4A1.65 1.65 0 0 0 9 9A1.65 1.65 0 0 0 7.4 12A1.65 1.65 0 0 0 9 15A1.65 1.65 0 0 0 12 16.6A1.65 1.65 0 0 0 14 15A1.65 1.65 0 0 0 17 16.6A1.65 1.65 0 0 0 19.4 15Z" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.settings') }}</div>
                <div class="menu-subtitle">{{ t('profile.accountSettings') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            
            <div class="menu-item" @click="goToAddresses">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M21 10C21 17 12 23 12 23S3 17 3 10A9 9 0 0 1 12 1A9 9 0 0 1 21 10Z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.addresses') }}</div>
                <div class="menu-subtitle">{{ t('profile.manageAddresses') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            
            <div class="menu-item" @click="goToPaymentMethods">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                  <line x1="1" y1="10" x2="23" y2="10" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.paymentMethods') }}</div>
                <div class="menu-subtitle">{{ t('profile.managePaymentMethods') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Support Section -->
        <div class="menu-section">
          <h3 class="section-title">{{ t('profile.support') }}</h3>
          <div class="menu-items">
            <div class="menu-item" @click="goToHelp">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                  <path d="M9.09 9A3 3 0 0 1 12 6A3 3 0 0 1 15 9A3 3 0 0 1 12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 17H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.help') }}</div>
                <div class="menu-subtitle">{{ t('profile.getHelp') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            
            <div class="menu-item" @click="goToContact">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2"/>
                  <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <div class="menu-content">
                <div class="menu-title">{{ t('profile.contact') }}</div>
                <div class="menu-subtitle">{{ t('profile.contactUs') }}</div>
              </div>
              <div class="menu-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Logout -->
        <div class="logout-section">
          <button class="logout-btn" @click="handleLogout">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M9 21H5A2 2 0 0 1 3 19V5A2 2 0 0 1 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ t('profile.logout') }}
          </button>
        </div>
      </div>
    </div>
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/stores/user'
import { useCartStore } from '@/stores/cart'
import { ElMessage, ElMessageBox } from 'element-plus'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileLoading from '@/components/common/MobileLoading.vue'
import MobileError from '@/components/common/MobileError.vue'

const router = useRouter()
const { t } = useI18n()
const userStore = useUserStore()
const cartStore = useCartStore()

// 状态管理
const isLoading = ref(false)
const hasError = ref(false)

// 用户信息
const userInfo = computed(() => userStore.userInfo || {})

// 用户统计数据
const userStats = ref({
  orders: 0,
  cartItems: 0,
  favorites: 0
})

// 方法
const goToSettings = () => {
  ElMessage.info('设置功能开发中...')
}

const editProfile = () => {
  router.push('/mobile/profile/edit')
}

const changeAvatar = () => {
  ElMessage.info('更换头像功能开发中...')
}

const goToOrders = () => {
  router.push('/mobile/orders')
}

const goToPendingOrders = () => {
  router.push('/mobile/orders?status=pending')
}

const goToCart = () => {
  router.push('/mobile/cart')
}

const goToFavorites = () => {
  router.push('/mobile/favorites')
}

const goToAddresses = () => {
  ElMessage.info('地址管理功能开发中...')
}

const goToPaymentMethods = () => {
  ElMessage.info('支付方式管理功能开发中...')
}

const goToHelp = () => {
  router.push('/mobile/help')
}

const goToContact = () => {
  ElMessage.info('联系我们功能开发中...')
}

const handleLogout = async () => {
  try {
    await ElMessageBox.confirm(
      t('profile.confirmLogout'),
      t('common.confirm'),
      {
        confirmButtonText: t('profile.logout'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    await userStore.logout()
    ElMessage.success(t('profile.logoutSuccess'))
    router.push('/mobile/login')
  } catch {
    // 用户取消登出
  }
}

// 获取用户统计数据
const loadUserStats = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    
    // 获取用户统计数据
    const [ordersResponse, favoritesResponse] = await Promise.all([
      orderApi.getUserOrders(),
      productApi.getUserFavorites()
    ])
    
    // 更新统计数据
    userStats.value = {
      orders: ordersResponse.data?.length || 0,
      cartItems: cartStore.cartCount,
      favorites: favoritesResponse.data?.length || 0
    }
    
    console.log('用户统计数据加载完成:', userStats.value)
  } catch (error: any) {
    console.error('获取用户统计数据失败:', error)
    hasError.value = true
    ElMessage.error('加载用户信息失败')
  } finally {
    isLoading.value = false
  }
}

// 重试加载
const handleRetry = () => {
  hasError.value = false
  loadUserStats()
}

onMounted(() => {
  loadUserStats()
})
</script>

<style scoped lang="scss">
.mobile-profile {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 80px; // 为底部导航栏留出空间
}

.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #000;
  border-bottom: 1px solid #333;
  position: sticky;
  top: 0;
  z-index: 100;

  .header-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }

  .settings-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }
}

.profile-content {
  padding: 20px;
}

.user-card {
  background: #1a1a1a;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 16px;

  .user-avatar {
    position: relative;
    cursor: pointer;

    img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .avatar-placeholder {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: #ff0050;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      font-weight: 600;
      color: #fff;
    }

    .avatar-edit {
      position: absolute;
      bottom: 0;
      right: 0;
      background: #ff0050;
      color: #fff;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid #000;
    }
  }

  .user-info {
    flex: 1;

    .user-name {
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 4px 0;
      color: #fff;
    }

    .user-phone {
      font-size: 14px;
      color: #ccc;
      margin: 0 0 4px 0;
    }

    .user-email {
      font-size: 14px;
      color: #ccc;
      margin: 0;
    }
  }

  .edit-profile-btn {
    background: #ff0050;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #e6004a;
    }
  }
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 24px;

  .stat-item {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #222;
      transform: translateY(-2px);
    }

    .stat-icon {
      color: #ff0050;
      margin-bottom: 8px;
      display: flex;
      justify-content: center;
    }

    .stat-content {
      .stat-number {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 4px;
      }

      .stat-label {
        font-size: 12px;
        color: #ccc;
      }
    }
  }
}

.menu-sections {
  .menu-section {
    margin-bottom: 24px;

    .section-title {
      font-size: 16px;
      font-weight: 600;
      color: #fff;
      margin: 0 0 12px 0;
      padding: 0 4px;
    }

    .menu-items {
      background: #1a1a1a;
      border-radius: 12px;
      overflow: hidden;

      .menu-item {
        display: flex;
        align-items: center;
        padding: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        border-bottom: 1px solid #222;

        &:last-child {
          border-bottom: none;
        }

        &:hover {
          background: #222;
        }

        &:active {
          background: #333;
        }

        .menu-icon {
          color: #ff0050;
          margin-right: 16px;
          display: flex;
          align-items: center;
        }

        .menu-content {
          flex: 1;

          .menu-title {
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            margin: 0 0 2px 0;
          }

          .menu-subtitle {
            font-size: 12px;
            color: #ccc;
            margin: 0;
          }
        }

        .menu-arrow {
          color: #666;
          margin-left: 8px;
        }
      }
    }
  }

  .logout-section {
    margin-top: 32px;

    .logout-btn {
      width: 100%;
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 16px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;

      &:hover {
        background: #e6004a;
      }

      &:active {
        transform: translateY(1px);
      }
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .profile-content {
    padding: 16px;
  }

  .user-card {
    padding: 20px;
    flex-direction: column;
    text-align: center;

    .user-avatar {
      margin-bottom: 8px;
    }

    .user-info {
      margin-bottom: 16px;
    }
  }

  .stats-section {
    gap: 8px;

    .stat-item {
      padding: 12px;

      .stat-content .stat-number {
        font-size: 18px;
      }
    }
  }

  .menu-sections .menu-section .menu-items .menu-item {
    padding: 14px;
  }
}

// 全局移动端样式
:global(body) {
  background: #000;
  color: #fff;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}
</style>
