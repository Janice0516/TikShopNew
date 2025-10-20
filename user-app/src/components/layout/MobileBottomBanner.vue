<template>
  <div class="bottom-banner" v-if="showBanner">
    <div class="banner-content">
      <div class="banner-left">
        <img src="/logo.png" alt="TikTok Shop" class="banner-logo" />
        <span class="banner-text">TikTok Discovery e-commerce</span>
      </div>
      
      <div class="banner-right">
        <button class="open-app-btn" @click="handleOpenApp">
          Open app
        </button>
        <button class="close-btn" @click="closeBanner">
          <el-icon><Close /></el-icon>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Close } from '@element-plus/icons-vue'

const showBanner = ref(true)

const handleOpenApp = () => {
  // 这里可以添加打开应用的逻辑
  console.log('Opening app...')
  // 可以跳转到应用商店或显示下载链接
}

const closeBanner = () => {
  showBanner.value = false
}
</script>

<style scoped lang="scss">
.bottom-banner {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #000;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  z-index: 1000;
  padding: 12px 16px;
}

.banner-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 100%;
}

.banner-left {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  
  .banner-logo {
    height: 24px;
    width: auto;
    max-width: 80px;
  }
  
  .banner-text {
    color: #fff;
    font-size: 12px;
    font-weight: 500;
  }
}

.banner-right {
  display: flex;
  align-items: center;
  gap: 8px;
  
  .open-app-btn {
    background: #ff0050;
    color: #fff;
    border: none;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:hover {
      background: #e6004a;
      transform: scale(1.05);
    }
    
    &:active {
      transform: scale(0.95);
    }
  }
  
  .close-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    
    &:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }
    
    .el-icon {
      font-size: 16px;
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .bottom-banner {
    padding: 10px 12px;
  }
  
  .banner-left {
    gap: 6px;
    
    .banner-logo {
      height: 20px;
      max-width: 70px;
    }
    
    .banner-text {
      font-size: 11px;
    }
  }
  
  .banner-right {
    gap: 6px;
    
    .open-app-btn {
      padding: 6px 12px;
      font-size: 11px;
    }
    
    .close-btn .el-icon {
      font-size: 14px;
    }
  }
}

@media (max-width: 360px) {
  .banner-left .banner-text {
    display: none;
  }
}
</style>
