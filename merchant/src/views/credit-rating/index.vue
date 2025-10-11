<template>
  <div class="credit-rating-page">
    <!-- 当前信用评级卡片 -->
    <el-card class="current-rating-card">
      <template #header>
        <div class="card-header">
          <span>当前信用评级</span>
          <el-tag :type="getLevelType(currentRating?.level)" size="large">
            {{ currentRating?.level || '暂无评级' }}
          </el-tag>
        </div>
      </template>
      
      <div v-if="currentRating" class="rating-content">
        <el-row :gutter="20">
          <el-col :span="8">
            <div class="rating-item">
              <div class="rating-label">信用分数</div>
              <div class="rating-value score">{{ currentRating.score }}</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="rating-item">
              <div class="rating-label">信用等级</div>
              <div class="rating-value level">{{ currentRating.level }}</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="rating-item">
              <div class="rating-label">星级评价</div>
              <div class="rating-value">
                <el-rate v-model="currentRating.rating" disabled show-score />
              </div>
            </div>
          </el-col>
        </el-row>
        
        <div class="rating-details">
          <el-descriptions :column="2" border>
            <el-descriptions-item label="评级日期">
              {{ formatDate(currentRating.evaluationDate) }}
            </el-descriptions-item>
            <el-descriptions-item label="有效期至">
              {{ formatDate(currentRating.validUntil) }}
            </el-descriptions-item>
            <el-descriptions-item label="评级原因" :span="2">
              {{ currentRating.evaluationReason || '基于综合经营数据评估' }}
            </el-descriptions-item>
          </el-descriptions>
        </div>
      </div>
      
      <div v-else class="no-rating">
        <el-empty description="暂无信用评级记录">
          <el-button type="primary" @click="loadCurrentRating">刷新</el-button>
        </el-empty>
      </div>
    </el-card>

    <!-- 信用评级历史 -->
    <el-card class="history-card">
      <template #header>
        <div class="card-header">
          <span>信用评级历史</span>
          <el-button @click="loadHistory">刷新</el-button>
        </div>
      </template>
      
      <el-table :data="historyData" v-loading="historyLoading" border>
        <el-table-column prop="rating" label="星级" width="120">
          <template #default="scope">
            <el-rate v-model="scope.row.rating" disabled show-score />
          </template>
        </el-table-column>
        <el-table-column prop="score" label="分数" width="100">
          <template #default="scope">
            <el-tag :type="getScoreType(scope.row.score)">
              {{ scope.row.score }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="level" label="等级" width="100">
          <template #default="scope">
            <el-tag :type="getLevelType(scope.row.level)">
              {{ scope.row.level }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="evaluationDate" label="评级日期" width="120">
          <template #default="scope">
            {{ formatDate(scope.row.evaluationDate) }}
          </template>
        </el-table-column>
        <el-table-column prop="validUntil" label="有效期至" width="120">
          <template #default="scope">
            {{ formatDate(scope.row.validUntil) }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100">
          <template #default="scope">
            <el-tag :type="scope.row.status === 1 ? 'success' : 'danger'">
              {{ scope.row.status === 1 ? '有效' : '无效' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="evaluationReason" label="评级原因" min-width="200" show-overflow-tooltip />
      </el-table>
      
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pagination.page"
        :page-sizes="[10, 20, 50]"
        :page-size="pagination.pageSize"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pagination.total"
        background
        style="margin-top: 20px; text-align: right;"
      />
    </el-card>

    <!-- 信用评级说明 -->
    <el-card class="guide-card">
      <template #header>
        <span>信用评级说明</span>
      </template>
      
      <div class="guide-content">
        <el-row :gutter="20">
          <el-col :span="12">
            <h4>评级标准</h4>
            <el-table :data="levelGuide" border size="small">
              <el-table-column prop="level" label="等级" width="80" />
              <el-table-column prop="scoreRange" label="分数范围" width="120" />
              <el-table-column prop="rating" label="星级" width="80" />
              <el-table-column prop="description" label="说明" />
            </el-table>
          </el-col>
          <el-col :span="12">
            <h4>影响因素</h4>
            <ul class="factor-list">
              <li><strong>订单完成率 (40%)</strong> - 完成订单占总订单的比例</li>
              <li><strong>订单取消率 (30%)</strong> - 取消订单占总订单的比例</li>
              <li><strong>活跃度 (20%)</strong> - 近30天订单数量</li>
              <li><strong>订单金额 (10%)</strong> - 平均订单金额</li>
            </ul>
            
            <h4>提升建议</h4>
            <ul class="suggestion-list">
              <li>提高订单完成率，减少订单取消</li>
              <li>保持稳定的订单量</li>
              <li>提供优质的商品和服务</li>
              <li>及时处理客户问题</li>
            </ul>
          </el-col>
        </el-row>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { getMerchantCurrentRating, getMerchantRatingHistory } from '@/api/credit-rating'

// 响应式数据
const currentRating = ref(null)
const historyData = ref([])
const historyLoading = ref(false)

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 评级说明数据
const levelGuide = ref([
  { level: 'AAA', scoreRange: '95-100', rating: 5, description: '优秀' },
  { level: 'AA', scoreRange: '90-94', rating: 5, description: '良好' },
  { level: 'A', scoreRange: '85-89', rating: 4, description: '较好' },
  { level: 'BBB', scoreRange: '80-84', rating: 4, description: '一般' },
  { level: 'BB', scoreRange: '70-79', rating: 3, description: '较差' },
  { level: 'B', scoreRange: '60-69', rating: 3, description: '差' },
  { level: 'C', scoreRange: '0-59', rating: 1, description: '很差' }
])

// 获取当前信用评级
const loadCurrentRating = async () => {
  try {
    await getMerchantCurrentRating()
    if (res.data && res.data.data) {
      currentRating.value = res.data.data
    } else {
      currentRating.value = null
    }
  } catch (error) {
    console.error('获取当前信用评级失败:', error)
    currentRating.value = null
  }
}

// 获取信用评级历史
const loadHistory = async () => {
  historyLoading.value = true
  try {
    const params = {
      page: pagination.page,
      pageSize: pagination.pageSize
    }
    
    await getMerchantRatingHistory(params)
    if (res.data && res.data.data) {
      historyData.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      historyData.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('获取信用评级历史失败:', error)
    ElMessage.error('获取信用评级历史失败')
    historyData.value = []
    pagination.total = 0
  } finally {
    historyLoading.value = false
  }
}

// 分页大小改变
const handleSizeChange = (val: number) => {
  pagination.pageSize = val
  pagination.page = 1
  loadHistory()
}

// 当前页改变
const handleCurrentChange = (val: number) => {
  pagination.page = val
  loadHistory()
}

// 获取分数标签类型
const getScoreType = (score: number) => {
  if (score >= 90) return 'success'
  if (score >= 80) return ''
  if (score >= 70) return 'warning'
  return 'danger'
}

// 获取等级标签类型
const getLevelType = (level: string) => {
  const typeMap = {
    'AAA': 'success',
    'AA': '',
    'A': 'warning',
    'BBB': 'warning',
    'BB': 'danger',
    'B': 'danger',
    'C': 'danger'
  }
  return typeMap[level] || 'info'
}

// 格式化日期
const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('zh-CN')
}

// 组件挂载时获取数据
onMounted(() => {
  loadCurrentRating()
  loadHistory()
})
</script>

<style scoped>
.credit-rating-page {
  padding: 20px;
}

.current-rating-card {
  margin-bottom: 20px;
}

.history-card {
  margin-bottom: 20px;
}

.guide-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.rating-content {
  padding: 20px 0;
}

.rating-item {
  text-align: center;
  padding: 20px;
}

.rating-label {
  font-size: 14px;
  color: #909399;
  margin-bottom: 10px;
}

.rating-value {
  font-size: 24px;
  font-weight: bold;
}

.rating-value.score {
  color: #409EFF;
}

.rating-value.level {
  color: #67C23A;
}

.rating-details {
  margin-top: 30px;
}

.no-rating {
  padding: 40px 0;
  text-align: center;
}

.guide-content h4 {
  margin-bottom: 15px;
  color: #303133;
}

.factor-list,
.suggestion-list {
  padding-left: 20px;
}

.factor-list li,
.suggestion-list li {
  margin-bottom: 8px;
  line-height: 1.6;
}

.factor-list li strong {
  color: #409EFF;
}
</style>
