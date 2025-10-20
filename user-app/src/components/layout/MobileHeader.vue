<template>
  <header class="mobile-header">
    <div class="header-content">
      <!-- Logo -->
      <div class="logo-section">
        <img src="/logo.png" alt="TikTok Shop" class="logo-image" />
      </div>
      
      <!-- Search Bar -->
      <div class="search-section">
        <div class="search-bar">
          <el-icon class="search-icon"><Search /></el-icon>
          <input 
            type="text" 
            placeholder="Search" 
            class="search-input"
            v-model="searchQuery"
            @keyup.enter="handleSearch"
          />
        </div>
      </div>
      
      <!-- User Profile -->
      <div class="profile-section">
        <el-icon class="profile-icon"><User /></el-icon>
      </div>
    </div>
    
    <!-- Navigation Tabs -->
    <div class="nav-tabs">
      <div 
        v-for="tab in navTabs" 
        :key="tab.id"
        class="nav-tab"
        :class="{ active: activeTab === tab.id }"
        @click="handleTabClick(tab.id)"
      >
        {{ tab.name }}
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Search, User } from '@element-plus/icons-vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')
const activeTab = ref('all')

const navTabs = ref([
  { id: 'all', name: 'All' },
  { id: 'womenswear', name: 'Womenswear & Underwear' },
  { id: 'electronics', name: 'Phones & Electronics' },
  { id: 'accessories', name: 'Fashion Accessories' },
  { id: 'menswear', name: 'Menswear & Underwear' }
])

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(`/search?q=${encodeURIComponent(searchQuery.value)}`)
  }
}

const handleTabClick = (tabId: string) => {
  activeTab.value = tabId
  // 可以根据 tabId 筛选商品
  emit('tab-change', tabId)
}

const emit = defineEmits<{
  tabChange: [tabId: string]
}>()
</script>

<style scoped lang="scss">
.mobile-header {
  background: #000;
  color: #fff;
  padding: 12px 16px 0;
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.header-content {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.logo-section {
  flex-shrink: 0;
  
  .logo-image {
    height: 32px;
    width: auto;
    max-width: 120px;
  }
}

.search-section {
  flex: 1;
  
  .search-bar {
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid #ff0050;
    border-radius: 20px;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    
    .search-icon {
      color: #ff0050;
      font-size: 16px;
    }
    
    .search-input {
      background: transparent;
      border: none;
      outline: none;
      color: #fff;
      flex: 1;
      font-size: 14px;
      
      &::placeholder {
        color: rgba(255, 255, 255, 0.6);
      }
    }
  }
}

.profile-section {
  flex-shrink: 0;
  
  .profile-icon {
    font-size: 24px;
    color: #fff;
    cursor: pointer;
    
    &:hover {
      color: #ff0050;
    }
  }
}

.nav-tabs {
  display: flex;
  gap: 0;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  -ms-overflow-style: none;
  
  &::-webkit-scrollbar {
    display: none;
  }
  
  .nav-tab {
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
    
    &:hover {
      color: #fff;
    }
    
    &.active {
      color: #fff;
      border-bottom-color: #ff0050;
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .mobile-header {
    padding: 10px 12px 0;
  }
  
  .header-content {
    gap: 8px;
  }
  
  .logo-section .logo-image {
    height: 28px;
    max-width: 100px;
  }
  
  .search-section .search-bar {
    padding: 6px 12px;
    
    .search-input {
      font-size: 13px;
    }
  }
  
  .profile-section .profile-icon {
    font-size: 20px;
  }
  
  .nav-tabs .nav-tab {
    padding: 10px 12px;
    font-size: 13px;
  }
}
</style>
