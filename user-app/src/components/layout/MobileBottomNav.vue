<template>
  <div class="mobile-bottom-nav">
    <div class="nav-items">
      <div 
        v-for="item in navItems" 
        :key="item.id"
        class="nav-item"
        :class="{ 'active': activeTab === item.id }"
        @click="navigateTo(item)"
      >
        <div class="nav-icon">
          <component :is="item.icon" />
        </div>
        <span class="nav-label">{{ item.label }}</span>
        <div v-if="item.badge && item.badge > 0" class="nav-badge">
          {{ item.badge > 99 ? '99+' : item.badge }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useCartStore } from '@/stores/cart'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()
const cartStore = useCartStore()
const userStore = useUserStore()

const activeTab = ref('home')

// 导航图标组件
const HomeIcon = {
  template: `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `
}

const CategoriesIcon = {
  template: `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <rect x="14" y="14" width="7" height="7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `
}

const CartIcon = {
  template: `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <circle cx="9" cy="21" r="1" fill="currentColor"/>
      <circle cx="20" cy="21" r="1" fill="currentColor"/>
      <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `
}

const ProfileIcon = {
  template: `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <path d="M20 21V19A4 4 0 0 0 16 15H8A4 4 0 0 0 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `
}

const SearchIcon = {
  template: `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
      <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `
}

// 导航项配置
const navItems = computed(() => [
  {
    id: 'home',
    label: t('navigation.home'),
    icon: HomeIcon,
    path: '/mobile',
    badge: 0
  },
  {
    id: 'categories',
    label: t('navigation.categories'),
    icon: CategoriesIcon,
    path: '/mobile/categories',
    badge: 0
  },
  {
    id: 'search',
    label: t('navigation.search'),
    icon: SearchIcon,
    path: '/mobile/search',
    badge: 0
  },
  {
    id: 'cart',
    label: t('navigation.cart'),
    icon: CartIcon,
    path: '/mobile/cart',
    badge: cartStore.cartCount
  },
  {
    id: 'profile',
    label: t('navigation.profile'),
    icon: ProfileIcon,
    path: userStore.isLoggedIn ? '/mobile/profile' : '/mobile/login',
    badge: 0
  }
])

// 方法
const navigateTo = (item: any) => {
  activeTab.value = item.id
  router.push(item.path)
}

// 根据当前路由设置活动标签
const updateActiveTab = () => {
  const path = route.path
  
  if (path.startsWith('/mobile/cart')) {
    activeTab.value = 'cart'
  } else if (path.startsWith('/mobile/categories')) {
    activeTab.value = 'categories'
  } else if (path.startsWith('/mobile/search')) {
    activeTab.value = 'search'
  } else if (path.startsWith('/mobile/profile') || path.startsWith('/mobile/login')) {
    activeTab.value = 'profile'
  } else if (path === '/mobile' || path === '/mobile/') {
    activeTab.value = 'home'
  }
}

// 监听路由变化
import { watch } from 'vue'
watch(() => route.path, updateActiveTab, { immediate: true })
</script>

<style scoped lang="scss">
.mobile-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #000;
  border-top: 1px solid #333;
  z-index: 1000;
  padding: 8px 0;
  padding-bottom: calc(8px + env(safe-area-inset-bottom));
}

.nav-items {
  display: flex;
  justify-content: space-around;
  align-items: center;
  max-width: 500px;
  margin: 0 auto;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  min-width: 60px;
  
  /* 触屏优化 */
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
  min-height: 44px;

  &:hover {
    transform: translateY(-1px);
  }

  &:active {
    transform: scale(0.95);
    transition: transform 0.1s ease;
  }

  .nav-icon {
    color: #666;
    transition: color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;

    :deep(svg) {
      width: 24px;
      height: 24px;
    }
  }

  .nav-label {
    font-size: 10px;
    color: #666;
    font-weight: 500;
    transition: color 0.2s;
    text-align: center;
    line-height: 1.2;
  }

  .nav-badge {
    position: absolute;
    top: 4px;
    right: 8px;
    background: #ff0050;
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
  }

  &.active {
    .nav-icon {
      color: #ff0050;
    }

    .nav-label {
      color: #ff0050;
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .nav-item {
    padding: 6px 8px;
    min-width: 50px;

    .nav-label {
      font-size: 9px;
    }

    .nav-icon :deep(svg) {
      width: 22px;
      height: 22px;
    }
  }
}

// 为有底部导航的页面添加底部间距
:global(.has-bottom-nav) {
  padding-bottom: 80px;
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
