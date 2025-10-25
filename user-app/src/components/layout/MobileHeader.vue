<template>
  <header class="mobile-header">
    <div class="header-content">
      <div class="logo-section">
        <img src="/logo.png" alt="TikTok Shop" class="logo-image" />
      </div>
      
      <div class="search-section">
        <div class="search-container" @click="handleSearchClick">
          <span class="search-icon">🔍</span>
          <span class="search-placeholder">{{ $t('home.searchPlaceholder') }}</span>
        </div>
      </div>
      
      <div class="user-section">
        <div class="user-icon" @click="handleUserClick">👤</div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const { t } = useI18n()

const handleSearchClick = () => {
  router.push('/search')
}

const handleUserClick = () => {
  router.push('/login')
}
</script>

<style scoped lang="scss">
.mobile-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #000;
  z-index: 1000;
  padding: 0 12px;
  
  .header-content {
    display: flex;
    align-items: center;
    height: 100%;
    gap: 8px;
    max-width: 100%;
  }
  
  .logo-section {
    flex-shrink: 0;
    
    .logo-image {
      height: 28px;
      width: auto;
    }
  }
  
  .search-section {
    flex: 1;
    min-width: 0; // 防止flex项目溢出
    
      .search-container {
        display: flex;
        align-items: center;
        background: #1a1a1a;
        border-radius: 16px;
        padding: 6px 12px;
        gap: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        max-width: 100%;
        
        /* 触屏优化 */
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
        min-height: 44px;
        
        &:hover {
          background: #333;
        }
        
        &:active {
          transform: scale(0.98);
          transition: transform 0.1s ease;
        }
      
      .search-icon {
        color: #666;
        font-size: 14px;
        flex-shrink: 0;
      }
      
      .search-placeholder {
        color: #666;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        min-width: 0;
      }
    }
  }
  
  .user-section {
    flex-shrink: 0;
    
    .user-icon {
      width: 28px;
      height: 28px;
      background: #333;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      
      /* 触屏优化 */
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
      min-height: 44px;
      min-width: 44px;
      
      &:hover {
        background: #555;
      }
      
      &:active {
        transform: scale(0.95);
        transition: transform 0.1s ease;
      }
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .mobile-header {
    padding: 0 8px;
    
    .header-content {
      gap: 6px;
    }
    
    .logo-section .logo-image {
      height: 24px;
    }
    
    .search-section .search-container {
      padding: 5px 10px;
      
      .search-placeholder {
        font-size: 11px;
      }
    }
    
    .user-section .user-icon {
      width: 24px;
      height: 24px;
      font-size: 12px;
    }
  }
}

@media (min-width: 769px) {
  .mobile-header {
    display: none;
  }
}
</style>