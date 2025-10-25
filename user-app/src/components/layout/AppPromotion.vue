<template>
  <div class="app-promotion" v-if="!isDismissed">
    <div class="promotion-content">
      <div class="promotion-text">
        <h3>TikTok Discovery e-commerce</h3>
        <p>Discover amazing products</p>
      </div>
      <button class="open-app-btn" @click="openApp">
        Open app
      </button>
      <button class="close-btn" @click="dismissPromotion">
        ✕
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const isDismissed = ref(false)

const openApp = () => {
  // 跳转到TikTok下载页面
  window.open('https://www.tiktok.com/download', '_blank')
}

const dismissPromotion = () => {
  isDismissed.value = true
}
</script>

<style scoped lang="scss">
.app-promotion {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #000;
  border-top: 1px solid #333;
  z-index: 1000;
  
  .promotion-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    position: relative;
    
    .promotion-text {
      flex: 1;
      
      h3 {
        font-size: 14px;
        font-weight: bold;
        color: #fff;
        margin: 0 0 4px 0;
      }
      
      p {
        font-size: 12px;
        color: #666;
        margin: 0;
      }
    }
    
    .open-app-btn {
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 8px 16px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      margin-right: 8px;
      transition: background-color 0.3s ease;
      
      &:hover {
        background: #e6004a;
      }
    }
    
    .close-btn {
      background: transparent;
      color: #666;
      border: none;
      font-size: 16px;
      cursor: pointer;
      padding: 4px;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.3s ease;
      
      &:hover {
        color: #fff;
        background: #333;
      }
    }
  }
}

// 响应式设计
@media (min-width: 769px) {
  .app-promotion {
    display: none;
  }
}
</style>
