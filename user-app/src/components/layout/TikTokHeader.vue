<template>
  <header class="tiktok-header">
    <div class="header-content">
      <div class="logo">
        <router-link to="/">
          <img src="/tts_logo_light_theme.747c3f9e.png?v=2" alt="TikTok Shop" />
        </router-link>
      </div>
      <div class="header-actions">
        <button class="get-app-btn" @click="handleGetApp">
          {{ $t('header.getApp') }}
        </button>
        <button class="login-btn" @click="handleLogin">
          {{ $t('header.login') }}
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const { t } = useI18n()

const handleGetApp = () => {
  // 跳转到应用下载页面
  window.open('https://www.tiktok.com/download', '_blank')
}

const handleLogin = () => {
  // 跳转到登录页面
  router.push('/login')
}
</script>

<style scoped lang="scss">
.tiktok-header {
  background: #fff;
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0;
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 1px solid #e5e5e5;
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0 20px;
    
    .logo {
      img {
        height: 32px;
        transition: opacity 0.3s ease;
        
        &:hover {
          opacity: 0.8;
        }
      }
    }
    
    .header-actions {
      display: flex;
      gap: 12px;
      
      .get-app-btn {
        background: #f5f5f5;
        color: #000;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        
        &:hover {
          background: #e5e5e5;
          transform: translateY(-1px);
        }
        
        &:active {
          transform: translateY(0);
        }
      }
      
      .login-btn {
        background: #ff0050;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        
        &:hover {
          background: #e6004a;
          transform: translateY(-1px);
        }
        
        &:active {
          transform: translateY(0);
        }
      }
    }
  }
}

// 移动端优化
@media (max-width: 768px) {
  .tiktok-header {
    height: 50px;
    padding: 0 16px;
    
    .header-content {
      .logo img {
        height: 28px;
      }
      
      .header-actions {
        gap: 8px;
        
        .get-app-btn,
        .login-btn {
          padding: 6px 12px;
          font-size: 13px;
        }
      }
    }
  }
}
</style>
