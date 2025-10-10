<template>
  <div class="fund-management">
    <el-card class="box-card">
      <template #header>
        <div class="card-header">
          <span>商户资金管理</span>
          <el-button type="primary" @click="handleAddOperation">资金操作</el-button>
        </div>
      </template>

      <!-- 搜索表单 -->
      <el-form :inline="true" :model="queryForm" class="query-form">
        <el-form-item label="商户ID">
          <el-input v-model="queryForm.merchantId" placeholder="请输入商户ID" clearable />
        </el-form-item>
        <el-form-item label="操作类型">
          <el-select v-model="queryForm.operationType" placeholder="请选择操作类型" clearable style="width: 120px;">
            <el-option label="全部" value="" />
            <el-option label="充值" value="1" />
            <el-option label="提现" value="2" />
            <el-option label="冻结" value="3" />
            <el-option label="解冻" value="4" />
            <el-option label="扣款" value="5" />
            <el-option label="退款" value="6" />
          </el-select>
        </el-form-item>
        <el-form-item label="操作日期">
          <el-date-picker
            v-model="queryForm.dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            value-format="YYYY-MM-DD HH:mm:ss"
            :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 1, 1, 23, 59, 59)]"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 操作记录表格 -->
      <el-table :data="tableData" v-loading="loading" border stripe style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="merchant.merchantName" label="商户名称" width="150" />
        <el-table-column prop="operationType" label="操作类型" width="100">
          <template #default="scope">
            <el-tag :type="getOperationTypeTagType(scope.row.operationType)">
              {{ getOperationTypeName(scope.row.operationType) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="amount" label="操作金额" width="120">
          <template #default="scope">
            <span :class="getAmountClass(scope.row.operationType)">
              {{ scope.row.operationType === 3 || scope.row.operationType === 5 ? '-' : '+' }}{{ scope.row.amount }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="balanceAfter" label="操作后余额" width="120" />
        <el-table-column prop="frozenAfter" label="操作后冻结" width="120" />
        <el-table-column prop="adminName" label="操作管理员" width="120" />
        <el-table-column prop="reason" label="操作原因" min-width="200" show-overflow-tooltip />
        <el-table-column prop="createTime" label="操作时间" width="180">
          <template #default="scope">
            {{ formatTime(scope.row.createTime) }}
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

    <!-- 资金操作弹窗 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="40%"
      :before-close="() => dialogVisible = false"
    >
      <el-form :model="operationForm" ref="operationFormRef" :rules="operationRules" label-width="100px">
        <el-form-item label="商户ID" prop="merchantId">
          <el-input v-model.number="operationForm.merchantId" placeholder="请输入商户ID" />
        </el-form-item>
        <el-form-item label="操作类型" prop="operationType">
          <el-select v-model="operationForm.operationType" placeholder="请选择操作类型" @change="handleOperationTypeChange">
            <el-option label="增加资金" value="increase" />
            <el-option label="冻结资金" value="freeze" />
            <el-option label="解冻资金" value="unfreeze" />
            <el-option label="扣除资金" value="deduct" />
            <el-option label="退还资金" value="refund" />
          </el-select>
        </el-form-item>
        <el-form-item label="操作金额" prop="amount">
          <el-input-number v-model="operationForm.amount" :min="0.01" :precision="2" style="width: 100%;" />
        </el-form-item>
        <el-form-item label="操作原因" prop="reason">
          <el-input v-model="operationForm.reason" type="textarea" :rows="3" placeholder="请输入操作原因" />
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="operationForm.remark" type="textarea" :rows="2" placeholder="请输入备注信息" />
        </el-form-item>
        <el-form-item v-if="operationForm.operationType === 'refund'" label="关联订单ID" prop="orderId">
          <el-input v-model.number="operationForm.orderId" placeholder="请输入关联订单ID（可选）" />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" @click="handleSubmit">确定</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import type { FormInstance } from 'element-plus';
import { 
  increaseFund,
  freezeFund, 
  unfreezeFund, 
  deductFund, 
  refundFund, 
  getFundOperationList 
} from '@/api/fund-management';
import { formatTime } from '@/utils/format';

// 响应式数据
const loading = ref(false);
const tableData = ref<any[]>([]);

const queryForm = reactive({
  merchantId: '' as string | number,
  operationType: '' as string,
  dateRange: null as [string, string] | null,
});

const pagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0,
});

const dialogVisible = ref(false);
const dialogTitle = ref('');
const operationForm = reactive({
  merchantId: null as number | null,
  operationType: '' as string,
  amount: 0,
  reason: '',
  remark: '',
  orderId: null as number | null,
});
const operationFormRef = ref<FormInstance>();

// 验证规则
const operationRules = {
  merchantId: [
    { required: true, message: '请输入商户ID', trigger: 'blur' },
    { type: 'number', message: '商户ID必须为数字', trigger: 'blur' },
  ],
  operationType: [
    { required: true, message: '请选择操作类型', trigger: 'change' },
  ],
  amount: [
    { required: true, message: '请输入操作金额', trigger: 'blur' },
    { type: 'number', message: '操作金额必须为数字', trigger: 'blur' },
  ],
  reason: [
    { required: true, message: '请输入操作原因', trigger: 'blur' },
  ],
};

// 获取操作记录列表
const getOperationListData = async () => {
  loading.value = true;
  try {
    const params: any = {
      page: pagination.page,
      pageSize: pagination.pageSize,
    };

    if (queryForm.merchantId) {
      params.merchantId = queryForm.merchantId;
    }
    if (queryForm.operationType) {
      params.operationType = queryForm.operationType;
    }
    if (queryForm.dateRange && queryForm.dateRange.length === 2) {
      params.startDate = queryForm.dateRange[0];
      params.endDate = queryForm.dateRange[1];
    }

    const res = await getFundOperationList(params);

    if (res.data && res.data.data) {
      tableData.value = res.data.data.list || [];
      pagination.total = res.data.data.total || 0;
    } else {
      tableData.value = [];
      pagination.total = 0;
    }
  } catch (error) {
    console.error('获取操作记录失败:', error);
    ElMessage.error('获取操作记录失败');
    tableData.value = [];
    pagination.total = 0;
  } finally {
    loading.value = false;
  }
};

// 查询
const handleQuery = () => {
  pagination.page = 1;
  getOperationListData();
};

// 重置
const handleReset = () => {
  queryForm.merchantId = '';
  queryForm.operationType = '';
  queryForm.dateRange = null;
  pagination.page = 1;
  getOperationListData();
};

// 分页大小改变
const handleSizeChange = (val: number) => {
  pagination.pageSize = val;
  pagination.page = 1;
  getOperationListData();
};

// 当前页改变
const handleCurrentChange = (val: number) => {
  pagination.page = val;
  getOperationListData();
};

// 添加操作
const handleAddOperation = () => {
  dialogTitle.value = '资金操作';
  operationForm.merchantId = null;
  operationForm.operationType = '';
  operationForm.amount = 0;
  operationForm.reason = '';
  operationForm.remark = '';
  operationForm.orderId = null;
  dialogVisible.value = true;
  if (operationFormRef.value) {
    operationFormRef.value.resetFields();
  }
};

// 操作类型改变
const handleOperationTypeChange = (value: string) => {
  // 可以根据操作类型设置不同的默认值或验证规则
};

// 提交表单
const handleSubmit = async () => {
  if (!operationFormRef.value) return;

  try {
    await operationFormRef.value.validate();

    const data = {
      merchantId: operationForm.merchantId,
      amount: operationForm.amount,
      reason: operationForm.reason,
      remark: operationForm.remark,
    };

    if (operationForm.operationType === 'refund' && operationForm.orderId) {
      (data as any).orderId = operationForm.orderId;
    }

    let apiCall;
    switch (operationForm.operationType) {
      case 'increase':
        apiCall = increaseFund(data);
        break;
      case 'freeze':
        apiCall = freezeFund(data);
        break;
      case 'unfreeze':
        apiCall = unfreezeFund(data);
        break;
      case 'deduct':
        apiCall = deductFund(data);
        break;
      case 'refund':
        apiCall = refundFund(data);
        break;
      default:
        throw new Error('未知的操作类型');
    }

    await apiCall;
    ElMessage.success('操作成功');
    dialogVisible.value = false;
    getOperationListData();
  } catch (error) {
    console.error('操作失败:', error);
    ElMessage.error('操作失败');
  }
};

// 获取操作类型名称
const getOperationTypeName = (type: number) => {
  const typeMap: Record<number, string> = {
    1: '充值',
    2: '提现',
    3: '冻结',
    4: '解冻',
    5: '扣款',
    6: '退款',
  };
  return typeMap[type] || '未知';
};

// 获取操作类型标签类型
const getOperationTypeTagType = (type: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'warning',
    3: 'danger',
    4: 'success',
    5: 'danger',
    6: 'success',
  };
  return typeMap[type] || 'info';
};

// 获取金额样式类
const getAmountClass = (type: number) => {
  if (type === 3 || type === 5) {
    return 'amount-negative';
  }
  return 'amount-positive';
};

// 组件挂载时获取数据
onMounted(() => {
  getOperationListData();
});
</script>

<style scoped>
.fund-management {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.query-form {
  margin-bottom: 20px;
}

.amount-positive {
  color: #67c23a;
  font-weight: bold;
}

.amount-negative {
  color: #f56c6c;
  font-weight: bold;
}
</style>
