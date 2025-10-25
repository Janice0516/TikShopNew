<template>
  <div class="dashboard">
    <div class="page-header">
      <h1>{{ $t('nav.dashboard') }}</h1>
    </div>

    <!-- 统计卡片 -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value">{{ totalProducts }}</div>
        <div class="stat-label">{{ $t('products.totalProducts') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ totalOrders }}</div>
        <div class="stat-label">{{ $t('orders.totalOrders') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">RM{{ totalRevenue }}</div>
        <div class="stat-label">{{ $t('finance.totalRevenue') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ totalCustomers }}</div>
        <div class="stat-label">{{ $t('shop.totalCustomers') }}</div>
      </div>
    </div>

    <!-- 快速操作 -->
    <div class="quick-actions">
      <h2>{{ $t('common.quickActions') }}</h2>
      <div class="action-buttons">
        <router-link to="/products/my-products" class="action-btn">
          <i class="icon-products"></i>
          <span>{{ $t('nav.myProducts') }}</span>
        </router-link>
        <router-link to="/products/select-products" class="action-btn">
          <i class="icon-add"></i>
          <span>{{ $t('products.selectFromPlatform') }}</span>
        </router-link>
        <router-link to="/orders" class="action-btn">
          <i class="icon-orders"></i>
          <span>{{ $t('nav.orders') }}</span>
        </router-link>
        <router-link to="/finance" class="action-btn">
          <i class="icon-finance"></i>
          <span>{{ $t('nav.finance') }}</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

// 响应式数据
const totalProducts = ref(10)
const totalOrders = ref(25)
const totalRevenue = ref(12500)
const totalCustomers = ref(150)

onMounted(() => {
  // 加载仪表板数据
})
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.page-header {
  margin-bottom: 30px;
}

.page-header h1 {
  margin: 0;
  font-size: 28px;
  color: #2c3e50;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.quick-actions {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-actions h2 {
  margin: 0 0 20px 0;
  font-size: 20px;
  color: #2c3e50;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  text-decoration: none;
  color: #2c3e50;
  transition: background-color 0.3s;
}

.action-btn:hover {
  background-color: #e9ecef;
}

.action-btn i {
  font-size: 24px;
  margin-bottom: 10px;
}

.action-btn span {
  font-size: 14px;
  font-weight: 500;
}
</style>
