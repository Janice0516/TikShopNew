<template>
  <div class="dashboard">
    <!-- 统计卡片 -->
    <div class="stats-grid">
      <!-- 账户余额 -->
      <div class="stat-card">
        <div class="card-header">
          <div class="card-icon">
            <el-icon :size="28"><Money /></el-icon>
          </div>
          <div class="card-trend" :class="{ 'trend-up': stats.revenueChange >= 0, 'trend-down': stats.revenueChange < 0 }">
            <el-icon :size="16" :color="stats.revenueChange >= 0 ? '#67C23A' : '#F56C6C'">
              <TrendCharts v-if="stats.revenueChange >= 0" />
              <ArrowDown v-else />
            </el-icon>
            <span class="trend-text">{{ stats.revenueChange >= 0 ? '+' : '' }}{{ stats.revenueChange.toFixed(1) }}%</span>
          </div>
        </div>
        <div class="card-content">
          <div class="card-value">RM{{ stats.accountBalance }}</div>
          <div class="card-label">{{ $t('dashboard.accountBalance') }}</div>
        </div>
        <div class="card-footer">
          <span class="card-action">{{ $t('dashboard.viewDetails') }} →</span>
        </div>
      </div>

      <!-- 今日订单 -->
      <div class="stat-card">
        <div class="card-header">
          <div class="card-icon">
            <el-icon :size="28"><Document /></el-icon>
          </div>
          <div class="card-trend" :class="{ 'trend-up': stats.todayOrdersChange >= 0, 'trend-down': stats.todayOrdersChange < 0 }">
            <el-icon :size="16" :color="stats.todayOrdersChange >= 0 ? '#67C23A' : '#F56C6C'">
              <TrendCharts v-if="stats.todayOrdersChange >= 0" />
              <ArrowDown v-else />
            </el-icon>
            <span class="trend-text">{{ stats.todayOrdersChange >= 0 ? '+' : '' }}{{ stats.todayOrdersChange.toFixed(1) }}%</span>
          </div>
        </div>
        <div class="card-content">
          <div class="card-value">{{ stats.todayOrders }}</div>
          <div class="card-label">{{ $t('dashboard.todayOrders') }}</div>
        </div>
        <div class="card-footer">
          <span class="card-action">{{ $t('dashboard.manage') }} →</span>
        </div>
      </div>

      <!-- 总商品数 -->
      <div class="stat-card">
        <div class="card-header">
          <div class="card-icon">
            <el-icon :size="28"><Goods /></el-icon>
          </div>
          <div class="card-trend" :class="{ 'trend-up': stats.productsChange >= 0, 'trend-down': stats.productsChange < 0 }">
            <el-icon :size="16" :color="stats.productsChange >= 0 ? '#67C23A' : '#F56C6C'">
              <TrendCharts v-if="stats.productsChange >= 0" />
              <ArrowDown v-else />
            </el-icon>
            <span class="trend-text">{{ stats.productsChange >= 0 ? '+' : '' }}{{ stats.productsChange.toFixed(1) }}%</span>
          </div>
        </div>
        <div class="card-content">
          <div class="card-value">{{ stats.totalProducts }}</div>
          <div class="card-label">{{ $t('dashboard.totalProducts') }}</div>
        </div>
        <div class="card-footer">
          <span class="card-action">{{ $t('dashboard.manage') }} →</span>
        </div>
      </div>

      <!-- 待发货 -->
      <div class="stat-card">
        <div class="card-header">
          <div class="card-icon">
            <el-icon :size="28"><Clock /></el-icon>
          </div>
          <div class="card-trend" :class="{ 'trend-up': stats.pendingOrdersChange >= 0, 'trend-down': stats.pendingOrdersChange < 0 }">
            <el-icon :size="16" :color="stats.pendingOrdersChange >= 0 ? '#67C23A' : '#F56C6C'">
              <TrendCharts v-if="stats.pendingOrdersChange >= 0" />
              <ArrowDown v-else />
            </el-icon>
            <span class="trend-text">{{ stats.pendingOrdersChange >= 0 ? '+' : '' }}{{ stats.pendingOrdersChange.toFixed(1) }}%</span>
          </div>
        </div>
        <div class="card-content">
          <div class="card-value">{{ stats.pendingShipment }}</div>
          <div class="card-label">{{ $t('dashboard.pendingShipment') }}</div>
        </div>
        <div class="card-footer">
          <span class="card-action">{{ $t('dashboard.review') }} →</span>
        </div>
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
        <router-link to="/orders/pending" class="action-btn">
          <i class="icon-orders"></i>
          <span>{{ $t('nav.orders') }}</span>
        </router-link>
        <router-link to="/finance/earnings" class="action-btn">
          <i class="icon-finance"></i>
          <span>{{ $t('nav.finance') }}</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Money, Document, Goods, Clock, TrendCharts, ArrowDown } from '@element-plus/icons-vue'
import { getDashboardStats } from '../../api/dashboard'
import { getFinanceStats } from '../../api/finance'
import { getOrderStats } from '../../api/order'

const loading = ref(false)

// 响应式数据
const stats = ref({
  accountBalance: '0.00',
  totalEarnings: '0.00',
  frozenAmount: '0.00',
  totalWithdrawn: '0.00',
  todayOrders: 0,
  totalProducts: 0,
  pendingShipment: 0,
  // 添加百分比变化数据
  revenueChange: 0,
  todayOrdersChange: 0,
  productsChange: 0,
  pendingOrdersChange: 0
})

// 加载仪表板数据
const loadDashboardData = async () => {
  try {
    loading.value = true
    
    // 加载仪表板统计
    const dashboardStatsRes = await getDashboardStats()
    console.log('Dashboard stats response:', dashboardStatsRes)
    if (dashboardStatsRes && dashboardStatsRes.data) {
      const dashboardStats = dashboardStatsRes.data
      stats.value.totalProducts = dashboardStats.totalProducts || 0
      stats.value.pendingShipment = dashboardStats.pendingOrders || 0
      stats.value.productsChange = dashboardStats.productsChange || 0
      stats.value.pendingOrdersChange = dashboardStats.pendingOrdersChange || 0
    }
    
    // 加载财务统计
    const financeStatsRes = await getFinanceStats()
    console.log('Finance stats response:', financeStatsRes)
    if (financeStatsRes && financeStatsRes.data) {
      const financeStats = financeStatsRes.data
      stats.value.accountBalance = financeStats.totalRevenue?.toFixed(2) || '0.00'
      stats.value.totalEarnings = financeStats.totalRevenue?.toFixed(2) || '0.00'
      stats.value.revenueChange = financeStats.revenueChange || 0
    }
    
    // 加载订单统计
    const orderStatsRes = await getOrderStats()
    console.log('Order stats response:', orderStatsRes)
    if (orderStatsRes && orderStatsRes.data) {
      const orderStats = orderStatsRes.data
      stats.value.todayOrders = orderStats.todayOrders || 0
      stats.value.todayOrdersChange = orderStats.todayOrdersChange || 0
    }
    
    console.log('Dashboard stats loaded:', stats.value)
    
  } catch (error) {
    console.error('Failed to load dashboard stats:', error)
    ElMessage.error('Failed to load dashboard data')
    // 出错时保持默认值
    stats.value = {
      accountBalance: '0.00',
      totalEarnings: '0.00',
      frozenAmount: '0.00',
      totalWithdrawn: '0.00',
      todayOrders: 0,
      totalProducts: 0,
      pendingShipment: 0,
      revenueChange: 0,
      todayOrdersChange: 0,
      productsChange: 0,
      pendingOrdersChange: 0
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
.dashboard {
  padding: 20px;
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
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.card-icon {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.card-trend {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 14px;
  font-weight: 500;
}

.trend-up {
  color: #67C23A;
}

.trend-down {
  color: #F56C6C;
}

.trend-text {
  font-size: 12px;
}

.card-content {
  margin-bottom: 15px;
}

.card-value {
  font-size: 24px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 5px;
}

.card-label {
  font-size: 14px;
  color: #666;
}

.card-footer {
  border-top: 1px solid #f0f0f0;
  padding-top: 15px;
}

.card-action {
  color: #3498db;
  font-size: 14px;
  cursor: pointer;
  transition: color 0.2s;
}

.card-action:hover {
  color: #2980b9;
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
