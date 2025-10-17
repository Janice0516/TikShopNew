<template>
  <div class="not-found">
    <div class="not-found-content">
      <div class="error-code">404</div>
      <h1>页面不存在</h1>
      <p>抱歉，您访问的页面不存在或已被删除</p>
      <el-button type="primary" @click="goHome">返回首页</el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const goHome = () => {
  router.push('/')
}
</script>

<style scoped lang="scss">
.not-found {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $background-base;
}

.not-found-content {
  text-align: center;
  
  .error-code {
    font-size: 120px;
    font-weight: bold;
    color: $primary-color;
    margin-bottom: 20px;
  }
  
  h1 {
    font-size: 32px;
    color: $text-primary;
    margin-bottom: 15px;
  }
  
  p {
    font-size: 16px;
    color: $text-secondary;
    margin-bottom: 30px;
  }
}

@media (max-width: 768px) {
  .not-found-content {
    .error-code {
      font-size: 80px;
    }
    
    h1 {
      font-size: 24px;
    }
  }
}
</style>
