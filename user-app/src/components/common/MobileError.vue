<template>
  <div class="mobile-error" v-if="show">
    <div class="error-container">
      <div class="error-icon">
        <component :is="errorIcon" />
      </div>
      
      <h3 class="error-title">{{ title }}</h3>
      
      <p class="error-message">{{ message }}</p>
      
      <div class="error-actions">
        <button 
          v-if="showRetry" 
          @click="handleRetry" 
          class="retry-button"
        >
          {{ retryText }}
        </button>
        
        <button 
          v-if="showGoHome" 
          @click="handleGoHome" 
          class="home-button"
        >
          {{ homeText }}
        </button>
      </div>
      
      <div v-if="showDetails && errorDetails" class="error-details">
        <details>
          <summary>错误详情</summary>
          <pre class="error-stack">{{ errorDetails }}</pre>
        </details>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'

interface Props {
  show: boolean
  type?: 'network' | 'server' | 'notFound' | 'permission' | 'generic'
  title?: string
  message?: string
  errorDetails?: string
  showRetry?: boolean
  showGoHome?: boolean
  retryText?: string
  homeText?: string
  onRetry?: () => void
}

const props = withDefaults(defineProps<Props>(), {
  type: 'generic',
  title: '出现错误',
  message: '抱歉，发生了意外错误',
  showRetry: true,
  showGoHome: true,
  retryText: '重试',
  homeText: '返回首页'
})

const emit = defineEmits<{
  retry: []
  goHome: []
}>()

const router = useRouter()

// 错误图标组件
const NetworkErrorIcon = {
  template: `
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
      <path d="M1 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="#ff0050" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M9 22V12H15V22" stroke="#ff0050" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <circle cx="12" cy="8" r="1" fill="#ff0050"/>
    </svg>
  `
}

const ServerErrorIcon = {
  template: `
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="4" rx="2" stroke="#ff0050" stroke-width="2"/>
      <rect x="3" y="10" width="18" height="4" rx="2" stroke="#ff0050" stroke-width="2"/>
      <rect x="3" y="16" width="18" height="4" rx="2" stroke="#ff0050" stroke-width="2"/>
      <line x1="9" y1="4" x2="9" y2="20" stroke="#ff0050" stroke-width="2"/>
    </svg>
  `
}

const NotFoundIcon = {
  template: `
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
      <circle cx="12" cy="12" r="10" stroke="#ff0050" stroke-width="2"/>
      <line x1="15" y1="9" x2="9" y2="15" stroke="#ff0050" stroke-width="2"/>
      <line x1="9" y1="9" x2="15" y2="15" stroke="#ff0050" stroke-width="2"/>
    </svg>
  `
}

const PermissionIcon = {
  template: `
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
      <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="#ff0050" stroke-width="2"/>
      <circle cx="12" cy="7" r="4" stroke="#ff0050" stroke-width="2"/>
    </svg>
  `
}

const GenericErrorIcon = {
  template: `
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
      <circle cx="12" cy="12" r="10" stroke="#ff0050" stroke-width="2"/>
      <line x1="12" y1="8" x2="12" y2="12" stroke="#ff0050" stroke-width="2"/>
      <line x1="12" y1="16" x2="12.01" y2="16" stroke="#ff0050" stroke-width="2"/>
    </svg>
  `
}

const errorIcon = computed(() => {
  const icons = {
    network: NetworkErrorIcon,
    server: ServerErrorIcon,
    notFound: NotFoundIcon,
    permission: PermissionIcon,
    generic: GenericErrorIcon
  }
  return icons[props.type]
})

const handleRetry = () => {
  emit('retry')
  props.onRetry?.()
}

const handleGoHome = () => {
  emit('goHome')
  router.push('/mobile')
}
</script>

<style scoped lang="scss">
.mobile-error {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #000;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  max-width: 400px;
  width: 100%;
}

.error-icon {
  margin-bottom: 24px;
  opacity: 0.8;
}

.error-title {
  font-size: 20px;
  font-weight: 600;
  color: #fff;
  margin: 0 0 12px 0;
}

.error-message {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0 0 24px 0;
  line-height: 1.5;
}

.error-actions {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
}

.retry-button, .home-button {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 44px;
  min-width: 44px;
  
  /* 触屏优化 */
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}

.retry-button {
  background: #ff0050;
  color: #fff;
  
  &:hover {
    background: #e6004a;
  }
  
  &:active {
    transform: scale(0.98);
  }
}

.home-button {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  
  &:hover {
    background: rgba(255, 255, 255, 0.2);
  }
  
  &:active {
    transform: scale(0.98);
  }
}

.error-details {
  width: 100%;
  margin-top: 16px;
  
  details {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 12px;
    
    summary {
      color: rgba(255, 255, 255, 0.8);
      font-size: 12px;
      cursor: pointer;
      margin-bottom: 8px;
      
      &:hover {
        color: #fff;
      }
    }
  }
  
  .error-stack {
    color: rgba(255, 255, 255, 0.6);
    font-size: 11px;
    font-family: 'Courier New', monospace;
    white-space: pre-wrap;
    word-break: break-all;
    margin: 0;
    padding: 8px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
  }
}

/* 响应式设计 */
@media (max-width: 480px) {
  .mobile-error {
    padding: 16px;
  }
  
  .error-title {
    font-size: 18px;
  }
  
  .error-message {
    font-size: 13px;
  }
  
  .retry-button, .home-button {
    padding: 10px 20px;
    font-size: 13px;
  }
  
  .error-actions {
    flex-direction: column;
    width: 100%;
    
    .retry-button, .home-button {
      width: 100%;
    }
  }
}

/* 动画效果 */
.mobile-error {
  animation: error-fade-in 0.3s ease-out;
}

@keyframes error-fade-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
