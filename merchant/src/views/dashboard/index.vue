<template>
  <div class="tiktok-dashboard">
    <!-- TikTok风格顶部区域 -->
    <div class="tiktok-header">
      <div class="header-content">
        <div class="welcome-section">
          <div class="avatar-container">
            <div class="avatar">
              <span class="avatar-text">{{ getInitials(merchantStore.merchantInfo?.username) }}</span>
            </div>
            <div class="status-indicator"></div>
          </div>
          <div class="welcome-text">
            <h1 class="greeting">{{ getGreeting() }}, {{ merchantStore.merchantInfo?.username }}!</h1>
            <p class="subtitle">Ready to grow your business? 🚀</p>
          </div>
        </div>
        <div class="header-actions">
          <div class="notification-btn" @click="showNotifications">
            <el-icon :size="24"><Bell /></el-icon>
            <span class="notification-badge" v-if="stats.pendingWithdraw > 0">{{ stats.pendingWithdraw }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- TikTok风格统计卡片 -->
    <div class="stats-container">
      <div class="stats-grid">
        <!-- 收入卡片 -->
        <div class="stat-card primary-card" @click="goToFinance">
          <div class="card-header">
            <div class="card-icon">
              <el-icon :size="28"><Money /></el-icon>
            </div>
            <div class="card-trend">
              <el-icon :size="16" color="#67C23A"><TrendCharts /></el-icon>
              <span class="trend-text">+12.5%</span>
            </div>
          </div>
          <div class="card-content">
            <div class="card-value">${{ stats.accountBalance }}</div>
            <div class="card-label">Account Balance</div>
          </div>
          <div class="card-footer">
            <span class="card-action">View Details →</span>
          </div>
        </div>

        <!-- 订单卡片 -->
        <div class="stat-card success-card" @click="goToOrders">
          <div class="card-header">
            <div class="card-icon">
              <el-icon :size="28"><Document /></el-icon>
            </div>
            <div class="card-trend">
              <el-icon :size="16" color="#67C23A"><TrendCharts /></el-icon>
              <span class="trend-text">+8.2%</span>
            </div>
          </div>
          <div class="card-content">
            <div class="card-value">{{ stats.todayOrders }}</div>
            <div class="card-label">Today's Orders</div>
          </div>
          <div class="card-footer">
            <span class="card-action">Manage →</span>
          </div>
        </div>

        <!-- 商品卡片 -->
        <div class="stat-card warning-card" @click="goToProducts">
          <div class="card-header">
            <div class="card-icon">
              <el-icon :size="28"><Goods /></el-icon>
            </div>
            <div class="card-trend">
              <el-icon :size="16" color="#67C23A"><TrendCharts /></el-icon>
              <span class="trend-text">+15.3%</span>
            </div>
          </div>
          <div class="card-content">
            <div class="card-value">{{ stats.totalProducts }}</div>
            <div class="card-label">Total Products</div>
          </div>
          <div class="card-footer">
            <span class="card-action">Manage →</span>
          </div>
        </div>

        <!-- 待处理卡片 -->
        <div class="stat-card danger-card" @click="goToPending">
          <div class="card-header">
            <div class="card-icon">
              <el-icon :size="28"><Clock /></el-icon>
            </div>
            <div class="card-trend">
              <el-icon :size="16" color="#F56C6C"><ArrowDown /></el-icon>
              <span class="trend-text">-2.1%</span>
            </div>
          </div>
          <div class="card-content">
            <div class="card-value">{{ stats.pendingShipment }}</div>
            <div class="card-label">Pending Shipment</div>
          </div>
          <div class="card-footer">
            <span class="card-action">Review →</span>
          </div>
        </div>
      </div>
    </div>

    <!-- TikTok风格快速操作 -->
    <div class="quick-actions-container">
      <div class="section-header">
        <h2 class="section-title">Quick Actions</h2>
        <div class="section-decoration"></div>
      </div>
      <div class="actions-grid">
        <div class="action-item" @click="goToRecharge">
          <div class="action-icon recharge-icon">
            <el-icon :size="24"><Money /></el-icon>
          </div>
          <span class="action-text">Recharge</span>
        </div>
        <div class="action-item" @click="goToWithdraw">
          <div class="action-icon withdraw-icon">
            <el-icon :size="24"><CreditCard /></el-icon>
          </div>
          <span class="action-text">Withdraw</span>
        </div>
        <div class="action-item" @click="goToProducts">
          <div class="action-icon products-icon">
            <el-icon :size="24"><Goods /></el-icon>
          </div>
          <span class="action-text">Products</span>
        </div>
        <div class="action-item" @click="goToOrders">
          <div class="action-icon orders-icon">
            <el-icon :size="24"><Document /></el-icon>
          </div>
          <span class="action-text">Orders</span>
        </div>
      </div>
    </div>

    <!-- TikTok风格数据概览 -->
    <div class="overview-container">
      <div class="section-header">
        <h2 class="section-title">Business Overview</h2>
        <div class="section-decoration"></div>
      </div>
      <div class="overview-cards">
        <div class="overview-card">
          <div class="overview-header">
            <span class="overview-label">Total Earnings</span>
            <el-icon :size="20" color="#67C23A"><TrendCharts /></el-icon>
          </div>
          <div class="overview-value">${{ stats.totalEarnings }}</div>
          <div class="overview-chart">
            <div class="chart-bar" style="height: 60%"></div>
            <div class="chart-bar" style="height: 80%"></div>
            <div class="chart-bar" style="height: 45%"></div>
            <div class="chart-bar" style="height: 90%"></div>
            <div class="chart-bar" style="height: 70%"></div>
            <div class="chart-bar" style="height: 85%"></div>
            <div class="chart-bar" style="height: 95%"></div>
          </div>
        </div>
        <div class="overview-card">
          <div class="overview-header">
            <span class="overview-label">Frozen Amount</span>
            <el-icon :size="20" color="#E6A23C"><Wallet /></el-icon>
          </div>
          <div class="overview-value">${{ stats.frozenAmount }}</div>
          <div class="overview-chart">
            <div class="chart-bar" style="height: 30%"></div>
            <div class="chart-bar" style="height: 50%"></div>
            <div class="chart-bar" style="height: 25%"></div>
            <div class="chart-bar" style="height: 40%"></div>
            <div class="chart-bar" style="height: 35%"></div>
            <div class="chart-bar" style="height: 45%"></div>
            <div class="chart-bar" style="height: 55%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- TikTok风格底部装饰 -->
    <div class="bottom-decoration">
      <div class="decoration-wave"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useMerchantStore } from '@/stores/merchant'
import { ElMessage } from 'element-plus'
import { getOrderStats } from '@/api/order'
import { getFinanceStats } from '@/api/finance'

const router = useRouter()
const merchantStore = useMerchantStore()

const stats = ref({
  // 财务数据
  accountBalance: '0.00',
  totalEarnings: '0.00',
  frozenAmount: '0.00',
  totalWithdrawn: '0.00',
  // 业务数据
  todayOrders: 0,
  totalProducts: 0,
  pendingShipment: 0,
  pendingWithdraw: 0
})

// TikTok风格功能函数
const getInitials = (name: string) => {
  if (!name) return 'M'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getGreeting = () => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good Morning'
  if (hour < 18) return 'Good Afternoon'
  return 'Good Evening'
}

const showNotifications = () => {
  ElMessage.info('No new notifications')
}

// 导航功能
const goToFinance = () => {
  router.push('/finance/recharge')
}

const goToRecharge = () => {
  router.push('/finance/recharge')
}

const goToWithdraw = () => {
  router.push('/finance/withdraw')
}

const goToProducts = () => {
  router.push('/products/my-products')
}

const goToOrders = () => {
  router.push('/orders/pending')
}

const goToPending = () => {
  router.push('/orders/pending')
}

onMounted(async () => {
  // 从商家信息中获取财务数据
  if (merchantStore.merchantInfo) {
    stats.value.accountBalance = merchantStore.merchantInfo.balance || '0.00'
    stats.value.frozenAmount = merchantStore.merchantInfo.frozenAmount || '0.00'
    stats.value.totalEarnings = merchantStore.merchantInfo.totalIncome || '0.00'
    stats.value.totalWithdrawn = merchantStore.merchantInfo.totalWithdraw || '0.00'
  }
  
  // 获取真实的统计数据
  try {
    const [orderStatsRes, financeStatsRes] = await Promise.all([
      getOrderStats(),
      getFinanceStats()
    ])
    
    if (orderStatsRes.data && orderStatsRes.data.data) {
      const orderStats = orderStatsRes.data.data
      stats.value.todayOrders = orderStats.todayOrders || 0
      stats.value.totalProducts = orderStats.totalProducts || 0
      stats.value.pendingShipment = orderStats.pendingShipment || 0
    }
    
    if (financeStatsRes.data && financeStatsRes.data.data) {
      const financeStats = financeStatsRes.data.data
      stats.value.pendingWithdraw = financeStats.pendingWithdraw || 0
    }
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
      pendingWithdraw: 0
    }
  }
})
</script>

<style scoped>
.tiktok-dashboard {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow-x: hidden;
}

/* TikTok风格顶部区域 */
.tiktok-header {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  padding: 30px 20px;
  border-radius: 0 0 30px 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.tiktok-header::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  animation: float 6s ease-in-out infinite;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  z-index: 2;
}

.welcome-section {
  display: flex;
  align-items: center;
  gap: 20px;
}

.avatar-container {
  position: relative;
}

.avatar {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  border: 3px solid rgba(255, 255, 255, 0.3);
}

.avatar-text {
  font-size: 20px;
  font-weight: bold;
  color: #667eea;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.status-indicator {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 16px;
  height: 16px;
  background: #67C23A;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.welcome-text {
  color: white;
}

.greeting {
  font-size: 24px;
  font-weight: bold;
  margin: 0 0 8px 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.subtitle {
  font-size: 16px;
  margin: 0;
  opacity: 0.9;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.notification-btn {
  position: relative;
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.notification-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.notification-btn .el-icon {
  color: white;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff4444;
  color: white;
  font-size: 12px;
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* TikTok风格统计卡片 */
.stats-container {
  padding: 30px 20px;
  position: relative;
  z-index: 2;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 25px;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-card:hover::before {
  opacity: 1;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-icon {
  width: 50px;
  height: 50px;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 2;
}

.primary-card .card-icon {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.success-card .card-icon {
  background: linear-gradient(135deg, #67C23A 0%, #85ce61 100%);
  color: white;
}

.warning-card .card-icon {
  background: linear-gradient(135deg, #E6A23C 0%, #f0c78a 100%);
  color: white;
}

.danger-card .card-icon {
  background: linear-gradient(135deg, #F56C6C 0%, #f89898 100%);
  color: white;
}

.card-trend {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 14px;
  font-weight: bold;
}

.trend-text {
  color: #67C23A;
}

.card-content {
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
}

.card-value {
  font-size: 32px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 8px;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.card-label {
  font-size: 16px;
  color: #7f8c8d;
  font-weight: 500;
}

.card-footer {
  position: relative;
  z-index: 2;
}

.card-action {
  font-size: 14px;
  color: #667eea;
  font-weight: 600;
  transition: all 0.3s ease;
}

.stat-card:hover .card-action {
  color: #764ba2;
  transform: translateX(5px);
}

/* TikTok风格快速操作 */
.quick-actions-container {
  padding: 0 20px 30px;
  position: relative;
  z-index: 2;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}

.section-title {
  font-size: 24px;
  font-weight: bold;
  color: white;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.section-decoration {
  flex: 1;
  height: 3px;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, transparent 100%);
  border-radius: 2px;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
}

.action-item {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 25px 15px;
  text-align: center;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.action-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.action-item:hover {
  transform: translateY(-8px) scale(1.05);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.action-item:hover::before {
  opacity: 1;
}

.action-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  position: relative;
  z-index: 2;
}

.recharge-icon {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.withdraw-icon {
  background: linear-gradient(135deg, #67C23A 0%, #85ce61 100%);
  color: white;
}

.products-icon {
  background: linear-gradient(135deg, #E6A23C 0%, #f0c78a 100%);
  color: white;
}

.orders-icon {
  background: linear-gradient(135deg, #F56C6C 0%, #f89898 100%);
  color: white;
}

.action-text {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  position: relative;
  z-index: 2;
}

/* TikTok风格数据概览 */
.overview-container {
  padding: 0 20px 30px;
  position: relative;
  z-index: 2;
}

.overview-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.overview-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 25px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.overview-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.overview-card:hover::before {
  opacity: 1;
}

.overview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
}

.overview-label {
  font-size: 16px;
  color: #7f8c8d;
  font-weight: 500;
}

.overview-value {
  font-size: 28px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.overview-chart {
  display: flex;
  align-items: end;
  gap: 8px;
  height: 60px;
  position: relative;
  z-index: 2;
}

.chart-bar {
  flex: 1;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 4px 4px 0 0;
  transition: all 0.3s ease;
  animation: growUp 1s ease-out;
}

.chart-bar:nth-child(odd) {
  background: linear-gradient(135deg, #67C23A 0%, #85ce61 100%);
}

.chart-bar:hover {
  transform: scaleY(1.1);
}

/* TikTok风格底部装饰 */
.bottom-decoration {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 100px;
  overflow: hidden;
}

.decoration-wave {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 100px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
  border-radius: 50% 50% 0 0;
  transform: scaleX(2);
}

/* 动画效果 */
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes growUp {
  from { height: 0; }
  to { height: var(--height); }
}

/* 响应式设计 */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
  
  .actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .overview-cards {
    grid-template-columns: 1fr;
  }
  
  .greeting {
    font-size: 20px;
  }
  
  .avatar {
    width: 50px;
    height: 50px;
  }
  
  .avatar-text {
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .tiktok-header {
    padding: 20px 15px;
  }
  
  .stats-container,
  .quick-actions-container,
  .overview-container {
    padding-left: 15px;
    padding-right: 15px;
  }
  
  .actions-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
  
  .action-item {
    padding: 20px 10px;
  }
  
  .action-icon {
    width: 50px;
    height: 50px;
  }
}
</style>

