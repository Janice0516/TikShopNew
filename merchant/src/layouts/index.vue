<template>
  <div class="merchant-layout">
    <!-- 侧边栏 -->
    <div class="sidebar">
      <div class="sidebar-header">
        <h2>TiktokShop Merchant</h2>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li v-for="route in menuRoutes" :key="route.path" :class="{ active: $route.path === route.path }">
            <router-link :to="route.path" class="nav-link">
              <i :class="route.icon"></i>
              <span>{{ $t(route.label) }}</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>

    <!-- 主内容区 -->
    <div class="main-content">
      <!-- 顶部导航 -->
      <header class="header">
        <div class="header-left">
          <h1>{{ $t(currentPageTitle) }}</h1>
        </div>
        <div class="header-right">
          <div class="language-switcher">
            <select v-model="currentLocale" @change="changeLanguage">
              <option value="zh">中文</option>
              <option value="en">English</option>
              <option value="ms">Bahasa Melayu</option>
            </select>
          </div>
          <div class="user-info">
            <span>Merchant</span>
          </div>
        </div>
      </header>

      <!-- 页面内容 -->
      <main class="content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { setLanguage } from '@/i18n'

const route = useRoute()
const { locale } = useI18n()

const currentLocale = ref(locale.value)

// 菜单路由配置
const menuRoutes = [
  { path: '/dashboard', label: 'nav.dashboard', icon: 'icon-dashboard' },
  { path: '/products/my-products', label: 'nav.myProducts', icon: 'icon-products' },
  { path: '/products/select-products', label: 'nav.selectProducts', icon: 'icon-add' },
  { path: '/orders/pending', label: 'nav.orders', icon: 'icon-orders' },
  { path: '/finance/earnings', label: 'nav.finance', icon: 'icon-finance' },
  { path: '/shop', label: 'nav.shop', icon: 'icon-shop' },
  { path: '/credit-rating', label: 'nav.creditRating', icon: 'icon-credit' },
  { path: '/settings', label: 'nav.settings', icon: 'icon-settings' }
]

// 当前页面标题 - 修复逻辑错误
const currentPageTitle = computed(() => {
  // 修复：使用route.path而不是route.path === route.path
  const currentRoute = menuRoutes.find(r => r.path === route.path)
  return currentRoute?.label || 'nav.dashboard'
})

// 切换语言
const changeLanguage = (event: Event) => {
  const target = event.target as HTMLSelectElement
  const newLocale = target.value
  setLanguage(newLocale)
  currentLocale.value = newLocale
}

// 监听路由变化
watch(() => route.path, (newPath) => {
  // 更新当前页面标题
}, { immediate: true })
</script>

<style scoped>
.merchant-layout {
  display: flex;
  height: 100vh;
  background-color: #f5f5f5;
}

.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #34495e;
}

.sidebar-header h2 {
  margin: 0;
  font-size: 18px;
  font-weight: bold;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
}

.sidebar-nav ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.sidebar-nav li {
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #ecf0f1;
  text-decoration: none;
  transition: background-color 0.3s;
}

.nav-link:hover {
  background-color: #34495e;
}

.nav-link.active {
  background-color: #3498db;
}

.nav-link i {
  margin-right: 10px;
  width: 20px;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.header {
  background-color: white;
  padding: 0 30px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-left h1 {
  margin: 0;
  font-size: 24px;
  color: #2c3e50;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.language-switcher select {
  padding: 5px 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: white;
}

.user-info {
  color: #666;
  font-size: 14px;
}

.content {
  flex: 1;
  padding: 30px;
  overflow-y: auto;
}
</style>
