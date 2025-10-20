<template>
  <div class="dashboard">
    <!-- 统计卡片 -->
    <el-row :gutter="20" class="stats-row">
      <el-col :span="6">
        <el-card class="box-card" @click="goToProducts">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#409EFF" :size="48"><Goods /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.products }}</div>
              <div class="stat-label">商品总数</div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="box-card" @click="goToMerchants">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#67C23A" :size="48"><Shop /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.merchants }}</div>
              <div class="stat-label">活跃商家</div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="box-card" @click="goToOrders">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#E6A23C" :size="48"><Document /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.orders }}</div>
              <div class="stat-label">订单总数</div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="box-card" @click="goToUsers">
          <div class="stat-item">
            <el-icon class="stat-icon" color="#F56C6C" :size="48"><User /></el-icon>
            <div class="stat-info">
              <div class="stat-value">{{ stats.users }}</div>
              <div class="stat-label">注册用户</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 图表区域 -->
    <el-row :gutter="20" class="chart-row">
      <el-col :span="12">
        <el-card>
          <template #header>
            <div class="card-header">
              <span>销售趋势 (最近30天)</span>
              <el-button-group>
                <el-button :type="chartPeriod === '7d' ? 'primary' : ''" @click="chartPeriod = '7d'">7D</el-button>
                <el-button :type="chartPeriod === '30d' ? 'primary' : ''" @click="chartPeriod = '30d'">30D</el-button>
                <el-button :type="chartPeriod === '90d' ? 'primary' : ''" @click="chartPeriod = '90d'">90D</el-button>
              </el-button-group>
            </div>
          </template>
          <div ref="salesChartRef" class="chart-container"></div>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card>
          <template #header>
            <span>订单状态分布</span>
          </template>
          <div ref="orderChartRef" class="chart-container"></div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 数据表格 -->
    <el-row :gutter="20" class="table-row">
      <el-col :span="12">
        <el-card>
          <template #header>
            <div class="card-header">
              <span>最近订单</span>
              <el-button type="primary" @click="goToOrders">查看全部</el-button>
            </div>
          </template>
          <el-table :data="recentOrders" style="width: 100%" max-height="300">
            <el-table-column prop="orderNo" label="订单号" width="120" />
            <el-table-column prop="userName" label="客户" width="100" />
            <el-table-column prop="totalAmount" label="金额" width="100">
              <template #default="{ row }">
                RM{{ row.totalAmount }}
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" width="100">
              <template #default="{ row }">
                <el-tag :type="getStatusTagType(row.status)">
                  {{ getStatusName(row.status) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="createTime" label="时间" width="120" />
          </el-table>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card>
          <template #header>
            <div class="card-header">
              <span>热销商品</span>
              <el-button type="primary" @click="goToProducts">查看全部</el-button>
            </div>
          </template>
          <el-table :data="topProducts" style="width: 100%" max-height="300">
            <el-table-column prop="name" label="商品名称" min-width="150" />
            <el-table-column prop="sales" label="销量" width="80" />
            <el-table-column prop="suggestPrice" label="价格" width="100">
              <template #default="{ row }">
                RM{{ row.suggestPrice }}
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" width="80">
              <template #default="{ row }">
                <el-tag :type="row.status === 1 ? 'success' : 'danger'">
                  {{ row.status === 1 ? '上架' : '下架' }}
                </el-tag>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>

    <!-- 系统信息 -->
    <el-row :gutter="20" class="info-row">
      <el-col :span="24">
        <el-card>
          <template #header>
            <span>系统信息</span>
          </template>
          <div class="welcome">
            <h2>🎉 欢迎使用电商管理后台</h2>
            <p>这是一个基于 Vue3 + Element Plus + TypeScript 构建的现代化管理后台</p>
            <el-divider />
            <h3>✅ 已完成功能：</h3>
            <ul>
              <li>用户认证 (登录/登出)</li>
              <li>商品管理 (增删改查)</li>
              <li>订单管理 (列表/详情)</li>
              <li>商家管理 (审核功能)</li>
              <li>推荐商品管理</li>
              <li>数据可视化 (图表和统计)</li>
              <li>响应式布局</li>
            </ul>
            <el-divider />
            <h3>📊 实时统计数据：</h3>
            <el-row :gutter="20">
              <el-col :span="6">
                <div class="info-item">
                  <div class="info-value">{{ stats.products }}</div>
                  <div class="info-label">商品</div>
                </div>
              </el-col>
              <el-col :span="6">
                <div class="info-item">
                  <div class="info-value">{{ stats.merchants }}</div>
                  <div class="info-label">商家</div>
                </div>
              </el-col>
              <el-col :span="6">
                <div class="info-item">
                  <div class="info-value">{{ stats.orders }}</div>
                  <div class="info-label">订单</div>
                </div>
              </el-col>
              <el-col :span="6">
                <div class="info-item">
                  <div class="info-value">{{ stats.users }}</div>
                  <div class="info-label">用户</div>
                </div>
              </el-col>
            </el-row>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import * as echarts from 'echarts'
import { ElMessage } from 'element-plus'
import { Goods, Shop, Document, User } from '@element-plus/icons-vue'
import { getDashboardStats } from '@/api/admin'

const router = useRouter()

const salesChartRef = ref()
const orderChartRef = ref()
const chartPeriod = ref('30d')

const stats = ref({
  products: 0,
  merchants: 0,
  orders: 0,
  users: 0
})

const recentOrders = ref([])

const topProducts = ref([])

// 获取状态标签样式
const getStatusTagType = (status: string) => {
  switch (status) {
    case 'pending': return 'warning'
    case 'shipped': return 'success'
    case 'completed': return 'info'
    case 'cancelled': return 'danger'
    default: return 'info'
  }
}

// 获取状态名称
const getStatusName = (status: string) => {
  switch (status) {
    case 'pending': return '待处理'
    case 'shipped': return '已发货'
    case 'completed': return '已完成'
    case 'cancelled': return '已取消'
    default: return status
  }
}

// 初始化销售趋势图表
const initSalesChart = () => {
  if (!salesChartRef.value) return
  
  const chart = echarts.init(salesChartRef.value)
  
  const option = {
    title: {
      text: '销售收益',
      left: 'center',
      textStyle: {
        fontSize: 16,
        fontWeight: 'normal'
      }
    },
    tooltip: {
      trigger: 'axis',
      formatter: '{b}: RM{c}'
    },
    xAxis: {
      type: 'category',
      data: ['Jan 1', 'Jan 2', 'Jan 3', 'Jan 4', 'Jan 5', 'Jan 6', 'Jan 7']
    },
    yAxis: {
      type: 'value',
      axisLabel: {
        formatter: 'RM{value}'
      }
    },
    series: [{
      data: [1200, 1500, 1800, 2200, 1900, 2500, 2800],
      type: 'line',
      smooth: true,
      areaStyle: {
        color: {
          type: 'linear',
          x: 0,
          y: 0,
          x2: 0,
          y2: 1,
          colorStops: [{
            offset: 0, color: 'rgba(64, 158, 255, 0.3)'
          }, {
            offset: 1, color: 'rgba(64, 158, 255, 0.1)'
          }]
        }
      },
      lineStyle: {
        color: '#409EFF'
      },
      itemStyle: {
        color: '#409EFF'
      }
    }]
  }
  
  chart.setOption(option)
  
  // 响应式调整
  window.addEventListener('resize', () => {
    chart.resize()
  })
}

// 初始化订单状态图表
const initOrderChart = () => {
  if (!orderChartRef.value) return
  
  const chart = echarts.init(orderChartRef.value)
  
  const option = {
    title: {
      text: '订单状态',
      left: 'center',
      textStyle: {
        fontSize: 16,
        fontWeight: 'normal'
      }
    },
    tooltip: {
      trigger: 'item',
      formatter: '{a} <br/>{b}: {c} ({d}%)'
    },
    legend: {
      orient: 'vertical',
      left: 'left',
      data: ['待处理', '已发货', '已完成', '已取消']
    },
    series: [{
      name: '订单状态',
      type: 'pie',
      radius: '50%',
      center: ['60%', '50%'],
      data: [
        { value: 35, name: '待处理', itemStyle: { color: '#E6A23C' } },
        { value: 25, name: '已发货', itemStyle: { color: '#67C23A' } },
        { value: 30, name: '已完成', itemStyle: { color: '#409EFF' } },
        { value: 10, name: '已取消', itemStyle: { color: '#F56C6C' } }
      ],
      emphasis: {
        itemStyle: {
          shadowBlur: 10,
          shadowOffsetX: 0,
          shadowColor: 'rgba(0, 0, 0, 0.5)'
        }
      }
    }]
  }
  
  chart.setOption(option)
  
  // 响应式调整
  window.addEventListener('resize', () => {
    chart.resize()
  })
}

// 加载统计数据
const loadStats = async () => {
  try {
    const res = await getDashboardStats()
    
    if (res.data && res.data.data) {
      stats.value = res.data.data.stats || stats.value
      recentOrders.value = res.data.data.recentOrders || []
      topProducts.value = res.data.data.topProducts || []
    } else {
      // API返回空数据时保持默认值
      stats.value = {
        products: 0,
        merchants: 0,
        orders: 0,
        users: 0
      }
      recentOrders.value = []
      topProducts.value = []
    }
  } catch (error) {
    console.error('Failed to load stats:', error)
    ElMessage.error('Failed to load dashboard statistics')
    // 出错时保持默认值
    stats.value = {
      products: 0,
      merchants: 0,
      orders: 0,
      users: 0
    }
    recentOrders.value = []
    topProducts.value = []
  }
}

// 导航方法
const goToProducts = () => {
  router.push('/products')
}

const goToMerchants = () => {
  router.push('/merchants')
}

const goToOrders = () => {
  router.push('/orders')
}

const goToUsers = () => {
  router.push('/users')
}

onMounted(async () => {
  await loadStats()
  
  // 等待DOM渲染完成后初始化图表
  await nextTick()
  initSalesChart()
  initOrderChart()
})
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.stats-row {
  margin-bottom: 20px;
}

.chart-row {
  margin-bottom: 20px;
}

.table-row {
  margin-bottom: 20px;
}

.info-row {
  margin-bottom: 20px;
}

.box-card {
  cursor: pointer;
  transition: all 0.3s;
}

.box-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 20px;
}

.stat-icon {
  font-size: 48px;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 32px;
  font-weight: bold;
  color: #333;
}

.stat-label {
  font-size: 14px;
  color: #999;
  margin-top: 5px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chart-container {
  height: 300px;
  width: 100%;
}

.welcome {
  line-height: 1.8;
}

.welcome h2 {
  color: #409EFF;
  margin-bottom: 10px;
}

.welcome h3 {
  color: #333;
  margin: 20px 0 10px 0;
}

.welcome ul {
  margin: 10px 0;
  padding-left: 20px;
}

.welcome li {
  margin: 5px 0;
}

.info-item {
  text-align: center;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.info-value {
  font-size: 24px;
  font-weight: bold;
  color: #409EFF;
  margin-bottom: 5px;
}

.info-label {
  font-size: 14px;
  color: #666;
}
</style>