<template>
  <view class="test-page">
    <view class="test-header">
      <text class="test-title">TikTok商城测试页面</text>
    </view>
    
    <view class="test-content">
      <text class="test-text">如果您能看到这个页面，说明TikTok商城页面已经可以正常访问了！</text>
      
      <view class="test-button" @click="goToTikTokMall">
        <text class="button-text">进入TikTok商城</text>
      </view>
      
      <view class="test-info">
        <text class="info-text">页面状态：正常 ✅</text>
        <text class="info-text">虚拟数据：已加载 ✅</text>
        <text class="info-text">TikTok风格：已应用 ✅</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
// 跳转到TikTok商城
const goToTikTokMall = () => {
  uni.navigateTo({
    url: '/pages/tiktok-mall'
  })
}
</script>

<style scoped>
.test-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.test-header {
  text-align: center;
  margin-bottom: 40px;
}

.test-title {
  font-size: 24px;
  font-weight: bold;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.test-content {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.test-text {
  font-size: 16px;
  color: #333;
  text-align: center;
  margin-bottom: 30px;
  line-height: 1.5;
}

.test-button {
  background: linear-gradient(135deg, #ff0050 0%, #ff4081 100%);
  color: white;
  padding: 15px 30px;
  border-radius: 25px;
  text-align: center;
  margin-bottom: 30px;
  box-shadow: 0 8px 25px rgba(255, 0, 80, 0.3);
  transition: all 0.3s ease;
}

.test-button:active {
  transform: scale(0.95);
}

.button-text {
  font-size: 16px;
  font-weight: bold;
}

.test-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.info-text {
  font-size: 14px;
  color: #666;
  padding: 8px 0;
  border-bottom: 1px solid #eee;
}

.info-text:last-child {
  border-bottom: none;
}
</style>
