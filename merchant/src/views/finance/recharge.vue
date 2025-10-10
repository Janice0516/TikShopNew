<template>
  <div class="recharge">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>💰 {{ $t('recharge.title') }}</span>
          <el-button @click="goBack">
            <el-icon><ArrowLeft /></el-icon>
            {{ $t('common.back') }}
          </el-button>
        </div>
      </template>

      <!-- 当前余额 -->
      <div class="balance-info">
        <el-alert
          type="info"
          :closable="false"
          show-icon
        >
          <template #title>
            <strong>{{ $t('recharge.currentBalance') }}</strong>
          </template>
          <div class="balance-amount">
            <span class="currency">$</span>
            <span class="amount">{{ currentBalance }}</span>
          </div>
        </el-alert>
      </div>

      <!-- 充值表单 -->
      <el-form
        ref="rechargeFormRef"
        :model="rechargeForm"
        :rules="rechargeRules"
        label-width="120px"
        class="recharge-form"
      >
        <el-form-item :label="$t('recharge.amount')" prop="amount">
          <el-input
            v-model="rechargeForm.amount"
            :placeholder="$t('recharge.amountPlaceholder')"
            type="number"
            step="0.01"
            min="1"
          >
            <template #prepend>$</template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('recharge.paymentMethod')" prop="paymentMethod">
          <el-select
            v-model="rechargeForm.paymentMethod"
            :placeholder="$t('recharge.selectPaymentMethod')"
            style="width: 100%"
          >
            <el-option
              v-for="method in paymentMethods"
              :key="method.value"
              :label="method.label"
              :value="method.value"
            >
              <div class="payment-option">
                <el-icon :size="20" :color="method.color">
                  <component :is="method.icon" />
                </el-icon>
                <span>{{ method.label }}</span>
              </div>
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('recharge.remark')" prop="remark">
          <el-input
            v-model="rechargeForm.remark"
            type="textarea"
            :rows="3"
            :placeholder="$t('recharge.remarkPlaceholder')"
          />
        </el-form-item>

        <el-form-item>
          <el-button
            type="primary"
            size="large"
            :loading="loading"
            @click="handleRecharge"
            style="width: 200px"
          >
            <el-icon><Money /></el-icon>
            {{ $t('recharge.submit') }}
          </el-button>
          <el-button size="large" @click="handleReset" style="margin-left: 20px">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>

      <!-- 充值说明 -->
      <el-card class="info-card">
        <template #header>
          <span>ℹ️ {{ $t('recharge.instructions') }}</span>
        </template>
        <ul class="instruction-list">
          <li>{{ $t('recharge.instruction1') }}</li>
          <li>{{ $t('recharge.instruction2') }}</li>
          <li>{{ $t('recharge.instruction3') }}</li>
          <li>{{ $t('recharge.instruction4') }}</li>
        </ul>
      </el-card>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { useMerchantStore } from '@/stores/merchant'
import { rechargeAccount } from '@/api/finance'

const { t } = useI18n()
const router = useRouter()
const merchantStore = useMerchantStore()

const rechargeFormRef = ref<FormInstance>()
const loading = ref(false)
const currentBalance = ref('0.00')

const rechargeForm = reactive({
  amount: '',
  paymentMethod: '',
  remark: ''
})

const rechargeRules: FormRules = {
  amount: [
    { required: true, message: () => t('recharge.amountRequired'), trigger: 'blur' },
    { 
      validator: (rule, value, callback) => {
        if (value && parseFloat(value) < 1) {
          callback(new Error(t('recharge.amountMin')))
        } else {
          callback()
        }
      }, 
      trigger: 'blur' 
    }
  ],
  paymentMethod: [
    { required: true, message: () => t('recharge.paymentMethodRequired'), trigger: 'change' }
  ]
}

const paymentMethods = ref([
  {
    value: 'bank_transfer',
    label: 'Bank Transfer',
    icon: 'CreditCard',
    color: '#409EFF'
  },
  {
    value: 'credit_card',
    label: 'Credit Card',
    icon: 'CreditCard',
    color: '#67C23A'
  },
  {
    value: 'paypal',
    label: 'PayPal',
    icon: 'CreditCard',
    color: '#E6A23C'
  },
  {
    value: 'alipay',
    label: 'Alipay',
    icon: 'CreditCard',
    color: '#409EFF'
  },
  {
    value: 'wechat_pay',
    label: 'WeChat Pay',
    icon: 'CreditCard',
    color: '#67C23A'
  }
])

// 返回
const goBack = () => {
  router.go(-1)
}

// 充值
const handleRecharge = async () => {
  if (!rechargeFormRef.value) return

  await rechargeFormRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        const result = await rechargeAccount({
          amount: parseFloat(rechargeForm.amount),
          paymentMethod: rechargeForm.paymentMethod,
          remark: rechargeForm.remark
        })
        
        ElMessage.success(t('recharge.success'))
        
        // 刷新商家信息
        await merchantStore.getMerchantInfo()
        
        // 重置表单
        handleReset()
        
        // 返回仪表盘
        router.push('/dashboard')
      } catch (error: any) {
        ElMessage.error(error.message || t('recharge.failed'))
      } finally {
        loading.value = false
      }
    }
  })
}

// 重置表单
const handleReset = () => {
  rechargeForm.amount = ''
  rechargeForm.paymentMethod = ''
  rechargeForm.remark = ''
  rechargeFormRef.value?.resetFields()
}

onMounted(() => {
  // 获取当前余额
  if (merchantStore.merchantInfo) {
    currentBalance.value = merchantStore.merchantInfo.balance || '0.00'
  }
})
</script>

<style scoped>
.recharge {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.balance-info {
  margin-bottom: 30px;
}

.balance-amount {
  display: flex;
  align-items: baseline;
  margin-top: 10px;
}

.currency {
  font-size: 24px;
  font-weight: bold;
  color: #409EFF;
}

.amount {
  font-size: 32px;
  font-weight: bold;
  color: #409EFF;
  margin-left: 5px;
}

.recharge-form {
  margin: 30px 0;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 10px;
}

.info-card {
  margin-top: 30px;
}

.instruction-list {
  list-style: none;
  padding-left: 0;
}

.instruction-list li {
  padding: 8px 0;
  padding-left: 20px;
  position: relative;
  color: #666;
}

.instruction-list li::before {
  content: '•';
  position: absolute;
  left: 0;
  color: #409EFF;
  font-weight: bold;
}
</style>
