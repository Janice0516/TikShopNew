<template>
  <div class="app-layout" :class="{ 'dark-theme': isDarkTheme }">
    <!-- 主要内容 -->
    <main class="app-main">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const isDarkTheme = ref(false) // 默认浅色主题
</script>

<style scoped lang="scss">
.app-layout {
  min-height: 100vh;
  background: #fff;
  color: #333;
  
  &.dark-theme {
    background: #000;
    color: #fff;
  }
}

.app-main {
  flex: 1;
  min-height: 100vh;
}
</style>
