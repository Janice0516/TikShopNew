<template>
  <div class="mobile-loading" v-if="loading">
    <div class="loading-overlay">
      <div class="loading-content">
        <!-- 骨架屏加载 -->
        <div v-if="type === 'skeleton'" class="skeleton-container">
          <div 
            v-for="i in skeletonCount" 
            :key="i" 
            class="skeleton-item"
            :class="skeletonType"
          ></div>
        </div>
        
        <!-- 旋转加载器 -->
        <div v-else-if="type === 'spinner'" class="spinner-container">
          <div class="spinner" :class="spinnerSize">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
          </div>
          <p v-if="message" class="loading-message">{{ message }}</p>
        </div>
        
        <!-- 脉冲加载器 -->
        <div v-else-if="type === 'pulse'" class="pulse-container">
          <div class="pulse-dot" :class="pulseSize"></div>
          <div class="pulse-dot" :class="pulseSize"></div>
          <div class="pulse-dot" :class="pulseSize"></div>
        </div>
        
        <!-- 进度条 -->
        <div v-else-if="type === 'progress'" class="progress-container">
          <div class="progress-bar">
            <div 
              class="progress-fill" 
              :style="{ width: `${progress}%` }"
            ></div>
          </div>
          <p v-if="message" class="loading-message">{{ message }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  loading: boolean
  type?: 'skeleton' | 'spinner' | 'pulse' | 'progress'
  message?: string
  skeletonCount?: number
  skeletonType?: 'card' | 'list' | 'text' | 'image'
  spinnerSize?: 'small' | 'medium' | 'large'
  pulseSize?: 'small' | 'medium' | 'large'
  progress?: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'spinner',
  skeletonCount: 3,
  skeletonType: 'card',
  spinnerSize: 'medium',
  pulseSize: 'medium',
  progress: 0
})

const pulseSize = computed(() => {
  const sizes = {
    small: 'pulse-small',
    medium: 'pulse-medium',
    large: 'pulse-large'
  }
  return sizes[props.pulseSize]
})
</script>

<style scoped lang="scss">
.mobile-loading {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.loading-overlay {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.loading-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.loading-message {
  color: #fff;
  font-size: 14px;
  text-align: center;
  margin: 0;
}

/* 骨架屏样式 */
.skeleton-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 100%;
  max-width: 400px;
  padding: 20px;
}

.skeleton-item {
  background: linear-gradient(90deg, 
    rgba(255, 255, 255, 0.1) 25%, 
    rgba(255, 255, 255, 0.2) 50%, 
    rgba(255, 255, 255, 0.1) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
  border-radius: 8px;
  
  &.card {
    height: 200px;
  }
  
  &.list {
    height: 60px;
  }
  
  &.text {
    height: 20px;
  }
  
  &.image {
    height: 120px;
    border-radius: 12px;
  }
}

@keyframes skeleton-loading {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* 旋转加载器样式 */
.spinner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.spinner {
  position: relative;
  
  &.small {
    width: 32px;
    height: 32px;
  }
  
  &.medium {
    width: 48px;
    height: 48px;
  }
  
  &.large {
    width: 64px;
    height: 64px;
  }
}

.spinner-ring {
  position: absolute;
  width: 100%;
  height: 100%;
  border: 3px solid transparent;
  border-top: 3px solid #ff0050;
  border-radius: 50%;
  animation: spinner-rotate 1.2s linear infinite;
  
  &:nth-child(2) {
    animation-delay: -0.3s;
    border-top-color: #ff4081;
  }
  
  &:nth-child(3) {
    animation-delay: -0.6s;
    border-top-color: #ff80ab;
  }
  
  &:nth-child(4) {
    animation-delay: -0.9s;
    border-top-color: #ffb3d1;
  }
}

@keyframes spinner-rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 脉冲加载器样式 */
.pulse-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pulse-dot {
  background: #ff0050;
  border-radius: 50%;
  animation: pulse-animation 1.4s ease-in-out infinite both;
  
  &.pulse-small {
    width: 8px;
    height: 8px;
  }
  
  &.pulse-medium {
    width: 12px;
    height: 12px;
  }
  
  &.pulse-large {
    width: 16px;
    height: 16px;
  }
  
  &:nth-child(2) {
    animation-delay: -0.16s;
  }
  
  &:nth-child(3) {
    animation-delay: -0.32s;
  }
}

@keyframes pulse-animation {
  0%, 80%, 100% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* 进度条样式 */
.progress-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  width: 100%;
  max-width: 300px;
}

.progress-bar {
  width: 100%;
  height: 4px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #ff0050, #ff4081);
  border-radius: 2px;
  transition: width 0.3s ease;
}

/* 响应式设计 */
@media (max-width: 480px) {
  .skeleton-container {
    padding: 16px;
  }
  
  .loading-message {
    font-size: 13px;
  }
  
  .spinner {
    &.small { width: 28px; height: 28px; }
    &.medium { width: 40px; height: 40px; }
    &.large { width: 56px; height: 56px; }
  }
  
  .pulse-dot {
    &.pulse-small { width: 6px; height: 6px; }
    &.pulse-medium { width: 10px; height: 10px; }
    &.pulse-large { width: 14px; height: 14px; }
  }
}
</style>
