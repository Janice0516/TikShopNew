<template>
  <div class="credit-rating-management">
    <!-- 统计卡片 -->
    <el-row :gutter="20" class="stats-row">
      <el-col :span="6">
        <el-card class="stats-card">
          <div class="stats-item">
            <div class="stats-value">{{ stats.totalRatings }}</div>
            <div class="stats-label">总评级数</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="stats-card">
          <div class="stats-item">
            <div class="stats-value">{{ stats.activeRatings }}</div>
            <div class="stats-label">有效评级</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="stats-card">
          <div class="stats-item">
            <div class="stats-value">{{ stats.avgScore.toFixed(1) }}</div>
            <div class="stats-label">平均分数</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="stats-card">
          <div class="stats-item">
            <div class="stats-value">{{ getTopLevelCount() }}</div>
            <div class="stats-label">AAA级商户</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 等级分布图表 -->
    <el-card class="chart-card">
      <template #header>
        <div class="card-header">
          <span>信用等级分布</span>
        </div>
      </template>
      <div ref="chartRef" style="height: 300px;"></div>
    </el-card>

    <el-card class="box-card">
      <template #header>
        <div class="card-header">
          <span>信用评级管理</span>
          <div>
            <el-button type="success" @click="handleRecalculateAll" :loading="recalculating">
              批量重新计算
            </el-button>
            <el-button type="primary" @click="handleAdd">添加评级</el-button>
          </div>
        </div>
      </template>

      <!-- 搜索表单 -->
      <el-form :inline="true" :model="queryForm" class="query-form">
        <el-form-item label="商户ID">
          <el-input v-model="queryForm.merchantId" placeholder="请输入商户ID" clearable />
        </el-form-item>
        <el-form-item label="信用等级">
          <el-select v-model="queryForm.level" placeholder="请选择等级" clearable style="width: 120px;">
            <el-option label="全部" value="" />
            <el-option label="AAA" value="AAA" />
            <el-option label="AA" value="AA" />
            <el-option label="A" value="A" />
            <el-option label="BBB" value="BBB" />
            <el-option label="BB" value="BB" />
            <el-option label="B" value="B" />
            <el-option label="C" value="C" />
          </el-select>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="queryForm.status" placeholder="请选择状态" clearable style="width: 120px;">
            <el-option label="全部" value="" />
            <el-option label="有效" :value="1" />
            <el-option label="无效" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 数据表格 -->
      <el-table :data="tableData" v-loading="loading" border stripe style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="merchantId" label="商户ID" width="100" />
        <el-table-column prop="merchant.merchantName" label="商户名称" width="150" />
        <el-table-column prop="rating" label="星级" width="80">
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
        <el-table-column label="操作" width="280" fixed="right">
          <template #default="scope">
            <el-button type="success" link @click="handleCalculate(scope.row)" :loading="scope.row.calculating">
              重新计算
            </el-button>
            <el-button type="primary" link @click="handleEdit(scope.row)">编辑</el-button>
            <el-button type="info" link @click="handleView(scope.row)">详情</el-button>
            <el-button type="danger" link @click="handleDelete(scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pagination.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="pagination.pageSize"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pagination.total"
        background
        style="margin-top: 20px; text-align: right;"
      />
    </el-card>

    <!-- 添加/编辑弹窗 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="600px"
      :before-close="handleDialogClose"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="商户ID" prop="merchantId">
          <el-input-number
            v-model="form.merchantId"
            :min="1"
            placeholder="请输入商户ID"
            style="width: 200px;"
          />
        </el-form-item>
        <el-form-item label="信用评级" prop="rating">
          <el-rate
            v-model="form.rating"
            :max="5"
            show-text
            :texts="['极差', '较差', '一般', '良好', '优秀']"
          />
        </el-form-item>
        <el-form-item label="信用分数" prop="score">
          <el-input-number
            v-model="form.score"
            :min="0"
            :max="100"
            :precision="2"
            placeholder="请输入信用分数"
            style="width: 200px;"
          />
          <span style="margin-left: 10px; color: #909399;">0-100分</span>
        </el-form-item>
        <el-form-item label="信用等级" prop="level">
          <el-select v-model="form.level" placeholder="请选择信用等级" style="width: 200px;">
            <el-option label="AAA" value="AAA" />
            <el-option label="AA" value="AA" />
            <el-option label="A" value="A" />
            <el-option label="BBB" value="BBB" />
            <el-option label="BB" value="BB" />
            <el-option label="B" value="B" />
            <el-option label="C" value="C" />
          </el-select>
        </el-form-item>
        <el-form-item label="评级日期" prop="evaluationDate">
          <el-date-picker
            v-model="form.evaluationDate"
            type="date"
            placeholder="请选择评级日期"
            value-format="YYYY-MM-DD"
            style="width: 200px;"
          />
        </el-form-item>
        <el-form-item label="有效期至" prop="validUntil">
          <el-date-picker
            v-model="form.validUntil"
            type="date"
            placeholder="请选择有效期"
            value-format="YYYY-MM-DD"
            style="width: 200px;"
          />
        </el-form-item>
        <el-form-item label="评级原因" prop="evaluationReason">
          <el-input
            v-model="form.evaluationReason"
            type="textarea"
            :rows="3"
            placeholder="请输入评级原因"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="handleDialogClose">取消</el-button>
          <el-button type="primary" @click="handleSubmit" :loading="submitting">
            {{ isEdit ? '更新' : '创建' }}
          </el-button>
        </span>
      </template>
    </el-dialog>

    <!-- 详情弹窗 -->
    <el-dialog
      v-model="detailDialogVisible"
      title="信用评级详情"
      width="500px"
    >
      <el-descriptions :column="1" border v-if="currentRating">
        <el-descriptions-item label="ID">{{ currentRating.id }}</el-descriptions-item>
        <el-descriptions-item label="商户ID">{{ currentRating.merchantId }}</el-descriptions-item>
        <el-descriptions-item label="商户名称">{{ currentRating.merchant?.merchantName || 'N/A' }}</el-descriptions-item>
        <el-descriptions-item label="信用评级">
          <el-rate v-model="currentRating.rating" disabled show-score />
        </el-descriptions-item>
        <el-descriptions-item label="信用分数">
          <el-tag :type="getScoreType(currentRating.score)">
            {{ currentRating.score }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="信用等级">
          <el-tag :type="getLevelType(currentRating.level)">
            {{ currentRating.level }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="评级日期">{{ formatDate(currentRating.evaluationDate) }}</el-descriptions-item>
        <el-descriptions-item label="有效期至">{{ formatDate(currentRating.validUntil) }}</el-descriptions-item>
        <el-descriptions-item label="状态">
          <el-tag :type="currentRating.status === 1 ? 'success' : 'danger'">
            {{ currentRating.status === 1 ? '有效' : '无效' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="评级原因">{{ currentRating.evaluationReason || '无' }}</el-descriptions-item>
        <el-descriptions-item label="创建时间">{{ formatTime(currentRating.createTime) }}</el-descriptions-item>
        <el-descriptions-item label="更新时间">{{ formatTime(currentRating.updateTime) }}</el-descriptions-item>
      </el-descriptions>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="detailDialogVisible = false">关闭</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed, nextTick } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import type { FormInstance } from 'element-plus'
import * as echarts from 'echarts'
import {
  getCreditRatingList,
  createCreditRating,
  updateCreditRating,
  deleteCreditRating,
  getCreditRatingDetail,
  calculateMerchantRating,
  getCreditRatingStats,
  recalculateAllMerchantRatings
} from '@/api/credit-rating'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const recalculating = ref(false)
const tableData = ref([])
const dialogVisible = ref(false)
const detailDialogVisible = ref(false)
const isEdit = ref(false)
const currentRating = ref(null)
const formRef = ref<FormInstance>()
const chartRef = ref<HTMLElement>()

// 统计数据
const stats = reactive({
  totalRatings: 0,
  activeRatings: 0,
  avgScore: 0,
  levelDistribution: {
    AAA: 0,
    AA: 0,
    A: 0,
    BBB: 0,
    BB: 0,
    B: 0,
    C: 0
  }
})

// 查询表单
const queryForm = reactive({
  merchantId: '',
  level: '',
  status: ''
})

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

// 表单数据
const form = reactive({
  merchantId: null,
  rating: 5,
  score: 95,
  level: 'AAA',
  evaluationDate: '',
  validUntil: '',
  evaluationReason: ''
})

// 表单验证规则
const rules = {
  merchantId: [
    { required: true, message: '请输入商户ID', trigger: 'blur' }
  ],
  rating: [
    { required: true, message: '请选择信用评级', trigger: 'change' }
  ],
  score: [
    { required: true, message: '请输入信用分数', trigger: 'blur' },
    { type: 'number', min: 0, max: 100, message: '分数必须在0-100之间', trigger: 'blur' }
  ],
  level: [
    { required: true, message: '请选择信用等级', trigger: 'change' }
  ],
  evaluationDate: [
    { required: true, message: '请选择评级日期', trigger: 'change' }
  ],
  validUntil: [
    { required: true, message: '请选择有效期', trigger: 'change' }
  ]
}

// 计算属性
const dialogTitle = computed(() => isEdit.value ? '编辑信用评级' : '添加信用评级')

// 获取信用评级列表
const getCreditRatingListData = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.page,
      pageSize: pagination.pageSize,
    }
    
    if (queryForm.merchantId) {
      params.merchantId = queryForm.merchantId
    }
    if (queryForm.level) {
      params.level = queryForm.level
    }
    if (queryForm.status !== '') {
      params.status = queryForm.status
    }

    const res = await getCreditRatingList(params)
    
    if (res.data && res.data.data) {
      tableData.value = res.data.data.list || []
      pagination.total = res.data.data.total || 0
    } else {
      tableData.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('获取信用评级列表失败:', error)
    ElMessage.error('获取信用评级列表失败')
    tableData.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 查询
const handleQuery = () => {
  pagination.page = 1
  getCreditRatingListData()
}

// 重置
const handleReset = () => {
  queryForm.merchantId = ''
  queryForm.level = ''
  queryForm.status = ''
  pagination.page = 1
  getCreditRatingListData()
}

// 分页大小改变
const handleSizeChange = (val: number) => {
  pagination.pageSize = val
  pagination.page = 1
  getCreditRatingListData()
}

// 当前页改变
const handleCurrentChange = (val: number) => {
  pagination.page = val
  getCreditRatingListData()
}

// 添加
const handleAdd = () => {
  isEdit.value = false
  resetForm()
  dialogVisible.value = true
}

// 编辑
const handleEdit = (row: any) => {
  isEdit.value = true
  form.merchantId = row.merchantId
  form.rating = row.rating
  form.score = row.score
  form.level = row.level
  form.evaluationDate = row.evaluationDate
  form.validUntil = row.validUntil
  form.evaluationReason = row.evaluationReason
  dialogVisible.value = true
}

// 查看详情
const handleView = async (row: any) => {
  try {
    const res = await getCreditRatingDetail(row.id)
    if (res.data && res.data.data) {
      currentRating.value = res.data.data
    } else {
      currentRating.value = row
    }
    detailDialogVisible.value = true
  } catch (error) {
    console.error('获取详情失败:', error)
    currentRating.value = row
    detailDialogVisible.value = true
  }
}

// 删除
const handleDelete = async (row: any) => {
  try {
    await ElMessageBox.confirm(
      `确认删除商户 ${row.merchantId} 的信用评级记录？`,
      '确认删除',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
    )

    await deleteCreditRating(row.id)
    ElMessage.success('删除成功')
    getCreditRatingListData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('删除失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    
    submitting.value = true
    
    if (isEdit.value) {
      await updateCreditRating(currentRating.value.id, form)
      ElMessage.success('更新成功')
    } else {
      await createCreditRating(form)
      ElMessage.success('创建成功')
    }
    
    dialogVisible.value = false
    getCreditRatingListData()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error('提交失败')
  } finally {
    submitting.value = false
  }
}

// 关闭弹窗
const handleDialogClose = () => {
  dialogVisible.value = false
  resetForm()
}

// 重置表单
const resetForm = () => {
  form.merchantId = null
  form.rating = 5
  form.score = 95
  form.level = 'AAA'
  form.evaluationDate = ''
  form.validUntil = ''
  form.evaluationReason = ''
  formRef.value?.clearValidate()
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

// 格式化时间
const formatTime = (time: string) => {
  if (!time) return '-'
  return new Date(time).toLocaleString('zh-CN')
}

// 获取统计数据
const getStatsData = async () => {
  try {
    const res = await getCreditRatingStats()
    if (res.data && res.data.data) {
      Object.assign(stats, res.data.data)
    }
  } catch (error) {
    console.error('获取统计数据失败:', error)
  }
}

// 初始化图表
const initChart = async () => {
  await nextTick()
  if (!chartRef.value) return
  
  const chart = echarts.init(chartRef.value)
  const option = {
    title: {
      text: '信用等级分布',
      left: 'center'
    },
    tooltip: {
      trigger: 'item',
      formatter: '{a} <br/>{b}: {c} ({d}%)'
    },
    legend: {
      orient: 'vertical',
      left: 'left',
      data: ['AAA', 'AA', 'A', 'BBB', 'BB', 'B', 'C']
    },
    series: [
      {
        name: '信用等级',
        type: 'pie',
        radius: '50%',
        data: [
          { value: stats.levelDistribution.AAA, name: 'AAA' },
          { value: stats.levelDistribution.AA, name: 'AA' },
          { value: stats.levelDistribution.A, name: 'A' },
          { value: stats.levelDistribution.BBB, name: 'BBB' },
          { value: stats.levelDistribution.BB, name: 'BB' },
          { value: stats.levelDistribution.B, name: 'B' },
          { value: stats.levelDistribution.C, name: 'C' }
        ],
        emphasis: {
          itemStyle: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: 'rgba(0, 0, 0, 0.5)'
          }
        }
      }
    ]
  }
  
  chart.setOption(option)
  
  // 监听窗口大小变化
  window.addEventListener('resize', () => {
    chart.resize()
  })
}

// 获取AAA级商户数量
const getTopLevelCount = () => {
  return stats.levelDistribution.AAA
}

// 重新计算单个商户信用评级
const handleCalculate = async (row: any) => {
  try {
    row.calculating = true
    await calculateMerchantRating(row.merchantId)
    ElMessage.success('重新计算成功')
    getCreditRatingListData()
    getStatsData()
  } catch (error) {
    console.error('重新计算失败:', error)
    ElMessage.error('重新计算失败')
  } finally {
    row.calculating = false
  }
}

// 批量重新计算所有商户信用评级
const handleRecalculateAll = async () => {
  try {
    await ElMessageBox.confirm(
      '确认要重新计算所有商户的信用评级吗？此操作可能需要较长时间。',
      '确认操作',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
    )

    recalculating.value = true
    const res = await recalculateAllMerchantRatings()
    
    if (res.data && res.data.data) {
      const { total, success, failed } = res.data.data
      ElMessage.success(`批量重新计算完成！总计：${total}，成功：${success}，失败：${failed}`)
    } else {
      ElMessage.success('批量重新计算完成')
    }
    
    getCreditRatingListData()
    getStatsData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('批量重新计算失败:', error)
      ElMessage.error('批量重新计算失败')
    }
  } finally {
    recalculating.value = false
  }
}

// 组件挂载时获取数据
onMounted(async () => {
  await getCreditRatingListData()
  await getStatsData()
  await initChart()
})
</script>

<style scoped>
.credit-rating-management {
  padding: 20px;
}

.stats-row {
  margin-bottom: 20px;
}

.stats-card {
  text-align: center;
}

.stats-item {
  padding: 20px;
}

.stats-value {
  font-size: 28px;
  font-weight: bold;
  color: #409EFF;
  margin-bottom: 8px;
}

.stats-label {
  font-size: 14px;
  color: #909399;
}

.chart-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.query-form {
  margin-bottom: 20px;
}
</style>
