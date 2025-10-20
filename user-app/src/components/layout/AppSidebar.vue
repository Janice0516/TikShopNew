<template>
  <aside class="sidebar">
    <div class="sidebar-content">
      <div class="sidebar-logo">
        <img src="/logo.png" alt="TikTok Shop" class="logo-image" />
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-item" role="button" tabindex="0" @click="handleProductsClick">
          <span class="nav-icon">🛍️</span>
          <span class="nav-text">{{ $t('navigation.products') }}</span>
        </div>
        <div class="nav-item" role="button" tabindex="0" @click="handleCategoriesClick">
          <span class="nav-icon">📂</span>
          <span class="nav-text">{{ $t('navigation.categories') }}</span>
        </div>
        <div class="nav-item" role="button" tabindex="0" @click="handleMoreClick">
          <span class="nav-icon">⋯</span>
          <span class="nav-text">{{ $t('common.more') }}</span>
        </div>
      </nav>
      
      <div class="sidebar-login">
        <button class="login-btn-large" @click="handleLoginClick">{{ $t('navigation.login') }}</button>
      </div>
      
      <div class="sidebar-footer">
        <a href="#" class="footer-link">{{ $t('home.startShopping') }}</a>
        <a href="#" class="footer-link">{{ $t('footer.company') }}</a>
        <a href="#" class="footer-link">{{ $t('footer.help') }}</a>
        <a href="#" class="footer-link">{{ $t('footer.contact') }}</a>
        <a href="#" class="footer-link">{{ $t('footer.legal') }}</a>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { nextTick } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const scrollToHash = async (hash: string) => {
  if (router.currentRoute.value.path !== '/') {
    await router.push({ path: '/', hash })
    return
  }
  await nextTick()
  const el = document.querySelector(hash)
  if (el) (el as HTMLElement).scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const handleProductsClick = () => scrollToHash('#products')
const handleCategoriesClick = () => scrollToHash('#categories')
const handleMoreClick = () => scrollToHash('#page-footer')
const handleLoginClick = () => router.push('/login')
</script>

<style scoped lang="scss">
.sidebar {
  position: fixed;
  left: 0;
  top: 60px;
  width: 200px;
  height: calc(100vh - 60px);
  background: #f8f9fa;
  border-right: 1px solid #e5e5e5;
  overflow-y: auto;
  z-index: 999;
}

.sidebar-content {
  padding: 20px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.sidebar-logo {
  margin-bottom: 30px;
  text-align: center;
}

.logo-image {
  height: 50px;
  width: auto;
  max-width: 150px;
}

.sidebar-nav {
  flex: 1;
  margin-bottom: 20px;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  margin-bottom: 8px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.2s;

  &:hover {
    background: #e9ecef;
  }
}

.nav-icon {
  font-size: 18px;
  margin-right: 12px;
  width: 20px;
  text-align: center;
}

.nav-text {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.sidebar-login {
  margin-bottom: 20px;
}

.login-btn-large {
  width: 100%;
  padding: 12px 16px;
  background: #ff0050;
  border: none;
  border-radius: 8px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;

  &:hover {
    background: #e6004a;
  }
}

.sidebar-footer {
  border-top: 1px solid #e5e5e5;
  padding-top: 20px;
}

.footer-link {
  display: block;
  padding: 8px 0;
  color: #666;
  text-decoration: none;
  font-size: 12px;
  transition: color 0.2s;

  &:hover {
    color: #333;
  }
}
</style>
