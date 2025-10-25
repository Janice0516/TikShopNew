<template>
  <div class="breadcrumb-navigation">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <ol class="breadcrumb-list">
        <li class="breadcrumb-item">
          <router-link to="/" class="breadcrumb-link">
            {{ $t('breadcrumb.home') }}
          </router-link>
        </li>
        <li class="breadcrumb-separator" aria-hidden="true">></li>
        <li class="breadcrumb-item" v-if="breadcrumbs.length > 0">
          <router-link 
            :to="breadcrumbs[0].path" 
            class="breadcrumb-link"
            v-if="breadcrumbs[0].path"
          >
            {{ breadcrumbs[0].name }}
          </router-link>
          <span v-else class="breadcrumb-current">{{ breadcrumbs[0].name }}</span>
        </li>
        <template v-for="(breadcrumb, index) in breadcrumbs.slice(1)" :key="index">
          <li class="breadcrumb-separator" aria-hidden="true">></li>
          <li class="breadcrumb-item">
            <router-link 
              :to="breadcrumb.path" 
              class="breadcrumb-link"
              v-if="breadcrumb.path && !breadcrumb.current"
            >
              {{ breadcrumb.name }}
            </router-link>
            <span v-else class="breadcrumb-current">{{ breadcrumb.name }}</span>
          </li>
        </template>
      </ol>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

interface Breadcrumb {
  name: string
  path?: string
  current?: boolean
}

interface Props {
  breadcrumbs: Breadcrumb[]
}

defineProps<Props>()
</script>

<style scoped lang="scss">
.breadcrumb-navigation {
  margin-bottom: 20px;
}

.breadcrumbs {
  .breadcrumb-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 14px;
    color: #666;
  }
  
  .breadcrumb-item {
    display: flex;
    align-items: center;
  }
  
  .breadcrumb-link {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
    
    &:hover {
      color: #ff0050;
    }
  }
  
  .breadcrumb-current {
    color: #000;
    font-weight: 500;
  }
  
  .breadcrumb-separator {
    margin: 0 8px;
    color: #999;
    user-select: none;
  }
}

// 移动端优化
@media (max-width: 768px) {
  .breadcrumb-navigation {
    margin-bottom: 16px;
  }
  
  .breadcrumbs {
    .breadcrumb-list {
      font-size: 13px;
    }
    
    .breadcrumb-separator {
      margin: 0 6px;
    }
  }
}
</style>


