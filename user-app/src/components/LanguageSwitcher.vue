<template>
  <div class="language-switcher">
    <el-dropdown 
      @command="handleLanguageChange" 
      trigger="click"
      placement="bottom-end"
    >
      <el-button class="language-btn" :class="{ 'mobile': isMobile }">
        <el-icon><Setting /></el-icon>
        <span v-if="!isMobile" class="language-text">{{ currentLanguageName }}</span>
        <el-icon class="arrow-down"><ArrowDown /></el-icon>
      </el-button>
      
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item 
            v-for="lang in languages" 
            :key="lang.value"
            :command="lang.value"
            :class="{ 'active': currentLocale === lang.value }"
          >
            <div class="language-option">
              <span class="flag">{{ lang.flag }}</span>
              <span class="name">{{ lang.name }}</span>
              <el-icon v-if="currentLocale === lang.value" class="check">
                <Check />
              </el-icon>
            </div>
          </el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { Setting, ArrowDown, Check } from '@element-plus/icons-vue'

const { locale } = useI18n()

// 响应式检测
const isMobile = ref(false)

const languages = [
  { value: 'zh-CN', name: '简体中文', flag: '🇨🇳' },
  { value: 'en-US', name: 'English', flag: '🇺🇸' },
  { value: 'ms-MY', name: 'Bahasa Melayu', flag: '🇲🇾' }
]

const currentLocale = computed(() => locale.value)

const currentLanguageName = computed(() => {
  const lang = languages.find(l => l.value === currentLocale.value)
  return lang?.name || 'English'
})

const handleLanguageChange = (langValue: string) => {
  locale.value = langValue
  localStorage.setItem('locale', langValue)
  
  // 触发自定义事件，通知其他组件语言已更改
  window.dispatchEvent(new CustomEvent('languageChanged', { 
    detail: { locale: langValue } 
  }))
}

// 检测屏幕尺寸
const checkScreenSize = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
})
</script>

<style scoped lang="scss">
.language-switcher {
  .language-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: #fff;
    color: #333;
    font-size: 14px;
    transition: all 0.3s ease;
    
    &:hover {
      border-color: #409eff;
      color: #409eff;
    }
    
    &.mobile {
      padding: 6px 8px;
      font-size: 12px;
      
      .language-text {
        display: none;
      }
    }
    
    .language-text {
      font-weight: 500;
    }
    
    .arrow-down {
      font-size: 12px;
      transition: transform 0.3s ease;
    }
  }
  
  :deep(.el-dropdown-menu) {
    min-width: 160px;
    padding: 4px 0;
  }
  
  .language-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    width: 100%;
    
    .flag {
      font-size: 16px;
    }
    
    .name {
      flex: 1;
      font-size: 14px;
      color: #333;
    }
    
    .check {
      color: #409eff;
      font-size: 14px;
    }
  }
  
  .el-dropdown-item.active {
    background-color: #f0f9ff;
    color: #409eff;
  }
}

// 移动端优化
@media (max-width: 768px) {
  .language-switcher {
    .language-btn {
      min-width: auto;
      padding: 6px 8px;
      
      .language-text {
        display: none;
      }
    }
  }
}

// 深色主题支持
@media (prefers-color-scheme: dark) {
  .language-switcher {
    .language-btn {
      background: #2d2d2d;
      border-color: #404040;
      color: #fff;
      
      &:hover {
        border-color: #409eff;
        color: #409eff;
      }
    }
    
    .language-option {
      .name {
        color: #fff;
      }
    }
    
    .el-dropdown-item.active {
      background-color: #1e3a5f;
    }
  }
}
</style>
