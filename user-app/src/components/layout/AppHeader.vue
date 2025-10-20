<template>
  <header class="top-header">
    <div class="header-content">
      <div class="header-left">
        <div class="logo">
          <img src="/logo.png" alt="TikTok Shop" class="logo-image" />
        </div>
      </div>
      
      <div class="header-right">
        <LanguageSwitcher />
        <a class="get-app-btn" href="https://www.tiktok.com/download" target="_blank" rel="noopener" title="Download TikTok">
          {{ $t('home.getApp') }}
        </a>
        <button class="login-btn">{{ $t('navigation.login') }}</button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import LanguageSwitcher from '../LanguageSwitcher.vue'
</script>

<style scoped lang="scss">
.top-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #fff;
  border-bottom: 1px solid #e5e5e5;
  z-index: 1000;
  display: flex;
  align-items: center;
}

.header-content {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
}

.logo-image {
  height: 40px;
  width: auto;
  max-width: 200px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.get-app-btn {
  padding: 8px 16px;
  background: #f5f5f5;
  border: none;
  border-radius: 6px;
  color: #333;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;

  &:hover {
    background: #e8e8e8;
  }
}

.login-btn {
  padding: 8px 16px;
  background: #ff0050;
  border: none;
  border-radius: 6px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;

  &:hover {
    background: #e6004a;
  }
}
</style>
