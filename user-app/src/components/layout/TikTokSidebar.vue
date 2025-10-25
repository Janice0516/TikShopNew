<template>
  <aside class="sidebar">
    <nav class="sidebar-nav">
      <div class="nav-item" @click="handleSell">
        <span class="nav-icon">🛍️</span>
        <span class="nav-text">{{ $t('sidebar.sell') }}</span>
      </div>
      <div class="nav-item" @click="handleMore">
        <span class="nav-icon">⋯</span>
        <span class="nav-text">{{ $t('sidebar.more') }}</span>
      </div>
      <button class="sidebar-login-btn" @click="handleLogin">
        {{ $t('sidebar.login') }}
      </button>
      <div class="nav-links">
        <router-link to="/" class="nav-link">{{ $t('sidebar.startShopping') }}</router-link>
        <a href="#" class="nav-link" @click="handleMakeMoney">{{ $t('sidebar.makeMoney') }}</a>
        <a href="#" class="nav-link" @click="handleCompanyInfo">{{ $t('sidebar.companyInfo') }}</a>
        <a href="#" class="nav-link" @click="handleCustomerSupport">{{ $t('sidebar.customerSupport') }}</a>
        <a href="#" class="nav-link" @click="handlePolicyLegal">{{ $t('sidebar.policyLegal') }}</a>
      </div>
    </nav>
  </aside>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const { t } = useI18n()

const handleSell = () => {
  // 跳转到分类页面
  router.push('/categories')
}

const handleMore = () => {
  // 跳转到客服页面
  window.open('https://direct.lc.chat/19346006/', '_blank')
}

const handleLogin = () => {
  router.push('/login')
}

const handleMakeMoney = () => {
  // 跳转到赚钱页面
  window.open('https://www.tiktok.com/business/en', '_blank')
}

const handleCompanyInfo = () => {
  // 跳转到公司信息页面
  window.open('https://www.tiktok.com/about', '_blank')
}

const handleCustomerSupport = () => {
  // 跳转到客服页面
  window.open('https://direct.lc.chat/19346006/', '_blank')
}

const handlePolicyLegal = () => {
  // 跳转到政策法律页面
  window.open('https://www.tiktok.com/legal', '_blank')
}
</script>

<style scoped lang="scss">
.sidebar {
  width: 240px;
  background: #fff;
  border-right: 1px solid #e5e5e5;
  padding: 20px 0;
  min-height: calc(100vh - 60px);
  
  
  .sidebar-nav {
    padding: 20px 0;
    
    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      
      &:hover {
        background: #f5f5f5;
      }
      
      .nav-icon {
        font-size: 18px;
      }
      
      .nav-text {
        font-size: 14px;
        font-weight: 500;
        color: #333;
      }
    }
    
    .sidebar-login-btn {
      width: calc(100% - 40px);
      margin: 20px;
      background: #ff0050;
      color: #fff;
      border: none;
      padding: 12px;
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
    
    .nav-links {
      padding: 20px 0;
      
      .nav-link {
        display: block;
        padding: 8px 20px;
        color: #666;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
        
        &:hover {
          color: #000;
          background: #f5f5f5;
        }
        
        &.router-link-active {
          color: #ff0050;
          background: #fff5f5;
          font-weight: 500;
        }
      }
    }
  }
}

// 移动端优化
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    min-height: auto;
    border-right: none;
    border-bottom: 1px solid #e5e5e5;
    
    .sidebar-header {
      padding: 0 16px 16px;
      
      .sidebar-logo {
        height: 28px;
      }
    }
    
    .sidebar-nav {
      padding: 16px 0;
      
      .nav-item {
        padding: 10px 16px;
        
        .nav-icon {
          font-size: 16px;
        }
        
        .nav-text {
          font-size: 13px;
        }
      }
      
      .sidebar-login-btn {
        margin: 16px;
        padding: 10px;
        font-size: 13px;
      }
      
      .nav-links {
        padding: 16px 0;
        
        .nav-link {
          padding: 6px 16px;
          font-size: 13px;
        }
      }
    }
  }
}
</style>
