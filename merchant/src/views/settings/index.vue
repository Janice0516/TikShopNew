<template>
  <div class="settings">
    <!-- 账户设置 -->
    <el-card class="settings-card">
      <template #header>
        <div class="card-header">
          <span>👤 {{ $t('settings.accountSettings') }}</span>
        </div>
      </template>

      <el-form
        ref="accountFormRef"
        :model="accountForm"
        :rules="accountRules"
        label-width="150px"
        class="settings-form"
      >
        <el-form-item :label="$t('settings.username')" prop="username">
          <el-input
            v-model="accountForm.username"
            :placeholder="$t('settings.usernamePlaceholder')"
            style="width: 300px"
            disabled
          />
        </el-form-item>

        <el-form-item :label="$t('settings.email')" prop="email">
          <el-input
            v-model="accountForm.email"
            :placeholder="$t('settings.emailPlaceholder')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item :label="$t('settings.phone')" prop="phone">
          <el-input
            v-model="accountForm.phone"
            :placeholder="$t('settings.phonePlaceholder')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="saveAccountInfo" :loading="saving">
            {{ $t('common.save') }}
          </el-button>
          <el-button @click="resetAccountForm">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 密码设置 -->
    <el-card class="settings-card">
      <template #header>
        <div class="card-header">
          <span>🔒 {{ $t('settings.passwordSettings') }}</span>
        </div>
      </template>

      <el-form
        ref="passwordFormRef"
        :model="passwordForm"
        :rules="passwordRules"
        label-width="150px"
        class="settings-form"
      >
        <el-form-item :label="$t('settings.currentPassword')" prop="currentPassword">
          <el-input
            v-model="passwordForm.currentPassword"
            type="password"
            :placeholder="$t('settings.currentPasswordPlaceholder')"
            style="width: 300px"
            show-password
          />
        </el-form-item>

        <el-form-item :label="$t('settings.newPassword')" prop="newPassword">
          <el-input
            v-model="passwordForm.newPassword"
            type="password"
            :placeholder="$t('settings.newPasswordPlaceholder')"
            style="width: 300px"
            show-password
          />
        </el-form-item>

        <el-form-item :label="$t('settings.confirmPassword')" prop="confirmPassword">
          <el-input
            v-model="passwordForm.confirmPassword"
            type="password"
            :placeholder="$t('settings.confirmPasswordPlaceholder')"
            style="width: 300px"
            show-password
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="changePassword" :loading="saving">
            {{ $t('settings.changePassword') }}
          </el-button>
          <el-button @click="resetPasswordForm">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 通知设置 -->
    <el-card class="settings-card">
      <template #header>
        <div class="card-header">
          <span>🔔 {{ $t('settings.notificationSettings') }}</span>
        </div>
      </template>

      <el-form label-width="150px" class="settings-form">
        <el-form-item :label="$t('settings.emailNotifications')">
          <el-switch
            v-model="notificationSettings.emailNotifications"
            :active-text="$t('settings.enabled')"
            :inactive-text="$t('settings.disabled')"
          />
        </el-form-item>

        <el-form-item :label="$t('settings.smsNotifications')">
          <el-switch
            v-model="notificationSettings.smsNotifications"
            :active-text="$t('settings.enabled')"
            :inactive-text="$t('settings.disabled')"
          />
        </el-form-item>

        <el-form-item :label="$t('settings.orderNotifications')">
          <el-switch
            v-model="notificationSettings.orderNotifications"
            :active-text="$t('settings.enabled')"
            :inactive-text="$t('settings.disabled')"
          />
        </el-form-item>

        <el-form-item :label="$t('settings.paymentNotifications')">
          <el-switch
            v-model="notificationSettings.paymentNotifications"
            :active-text="$t('settings.enabled')"
            :inactive-text="$t('settings.disabled')"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="saveNotificationSettings" :loading="saving">
            {{ $t('common.save') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 系统设置 -->
    <el-card class="settings-card">
      <template #header>
        <div class="card-header">
          <span>⚙️ {{ $t('settings.systemSettings') }}</span>
        </div>
      </template>

      <el-form label-width="150px" class="settings-form">
        <el-form-item :label="$t('settings.language')">
          <LanguageSwitcher />
        </el-form-item>

        <el-form-item :label="$t('settings.timezone')">
          <el-select
            v-model="systemSettings.timezone"
            :placeholder="$t('settings.selectTimezone')"
            style="width: 300px"
          >
            <el-option label="UTC-8 (Pacific Time)" value="UTC-8" />
            <el-option label="UTC-5 (Eastern Time)" value="UTC-5" />
            <el-option label="UTC+0 (GMT)" value="UTC+0" />
            <el-option label="UTC+8 (China Standard Time)" value="UTC+8" />
            <el-option label="UTC+9 (Japan Standard Time)" value="UTC+9" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('settings.currency')">
          <el-select
            v-model="systemSettings.currency"
            :placeholder="$t('settings.selectCurrency')"
            style="width: 300px"
          >
            <el-option label="USD ($)" value="USD" />
            <el-option label="EUR (€)" value="EUR" />
            <el-option label="GBP (£)" value="GBP" />
            <el-option label="CNY (¥)" value="CNY" />
            <el-option label="JPY (¥)" value="JPY" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('settings.dateFormat')">
          <el-select
            v-model="systemSettings.dateFormat"
            :placeholder="$t('settings.selectDateFormat')"
            style="width: 300px"
          >
            <el-option label="YYYY-MM-DD" value="YYYY-MM-DD" />
            <el-option label="MM/DD/YYYY" value="MM/DD/YYYY" />
            <el-option label="DD/MM/YYYY" value="DD/MM/YYYY" />
            <el-option label="YYYY/MM/DD" value="YYYY/MM/DD" />
          </el-select>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="saveSystemSettings" :loading="saving">
            {{ $t('common.save') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 安全设置 -->
    <el-card class="settings-card">
      <template #header>
        <div class="card-header">
          <span>🛡️ {{ $t('settings.securitySettings') }}</span>
        </div>
      </template>

      <el-form label-width="150px" class="settings-form">
        <el-form-item :label="$t('settings.twoFactorAuth')">
          <el-switch
            v-model="securitySettings.twoFactorAuth"
            :active-text="$t('settings.enabled')"
            :inactive-text="$t('settings.disabled')"
          />
          <div class="setting-description">
            {{ $t('settings.twoFactorAuthDescription') }}
          </div>
        </el-form-item>

        <el-form-item :label="$t('settings.loginAlerts')">
          <el-switch
            v-model="securitySettings.loginAlerts"
            :active-text="$t('settings.enabled')"
            :inactive-text="$t('settings.disabled')"
          />
          <div class="setting-description">
            {{ $t('settings.loginAlertsDescription') }}
          </div>
        </el-form-item>

        <el-form-item :label="$t('settings.sessionTimeout')">
          <el-select
            v-model="securitySettings.sessionTimeout"
            :placeholder="$t('settings.selectSessionTimeout')"
            style="width: 300px"
          >
            <el-option label="15 minutes" value="15" />
            <el-option label="30 minutes" value="30" />
            <el-option label="1 hour" value="60" />
            <el-option label="2 hours" value="120" />
            <el-option label="Never" value="0" />
          </el-select>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="saveSecuritySettings" :loading="saving">
            {{ $t('common.save') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 数据管理 -->
    <el-card class="settings-card">
      <template #header>
        <div class="card-header">
          <span>📊 {{ $t('settings.dataManagement') }}</span>
        </div>
      </template>

      <el-form label-width="150px" class="settings-form">
        <el-form-item :label="$t('settings.exportData')">
          <el-button type="primary" @click="exportData">
            <el-icon><Download /></el-icon>
            {{ $t('settings.exportData') }}
          </el-button>
          <div class="setting-description">
            {{ $t('settings.exportDataDescription') }}
          </div>
        </el-form-item>

        <el-form-item :label="$t('settings.clearCache')">
          <el-button type="warning" @click="clearCache">
            <el-icon><Delete /></el-icon>
            {{ $t('settings.clearCache') }}
          </el-button>
          <div class="setting-description">
            {{ $t('settings.clearCacheDescription') }}
          </div>
        </el-form-item>

        <el-form-item :label="$t('settings.deleteAccount')">
          <el-button type="danger" @click="deleteAccount">
            <el-icon><Warning /></el-icon>
            {{ $t('settings.deleteAccount') }}
          </el-button>
          <div class="setting-description">
            {{ $t('settings.deleteAccountDescription') }}
          </div>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, FormInstance, FormRules } from 'element-plus'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

const { t } = useI18n()

const saving = ref(false)
const accountFormRef = ref<FormInstance>()
const passwordFormRef = ref<FormInstance>()

// 账户设置表单
const accountForm = reactive({
  username: 'merchant001',
  email: 'merchant@example.com',
  phone: '+1 234-567-8900'
})

const accountRules: FormRules = {
  email: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { type: 'email', message: () => t('validation.email'), trigger: 'blur' }
  ],
  phone: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ]
}

// 密码设置表单
const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const passwordRules: FormRules = {
  currentPassword: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  newPassword: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { min: 6, message: () => t('validation.minLength', { min: 6 }), trigger: 'blur' }
  ],
  confirmPassword: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    {
      validator: (rule, value, callback) => {
        if (value !== passwordForm.newPassword) {
          callback(new Error(t('validation.passwordMismatch')))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 通知设置
const notificationSettings = reactive({
  emailNotifications: true,
  smsNotifications: false,
  orderNotifications: true,
  paymentNotifications: true
})

// 系统设置
const systemSettings = reactive({
  timezone: 'UTC+8',
  currency: 'USD',
  dateFormat: 'YYYY-MM-DD'
})

// 安全设置
const securitySettings = reactive({
  twoFactorAuth: false,
  loginAlerts: true,
  sessionTimeout: '60'
})

// 保存账户信息
const saveAccountInfo = async () => {
  if (!accountFormRef.value) return
  
  await accountFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        // 实际API调用
        // await updateAccountInfo(accountForm)
        
        ElMessage.success(t('message.operationSuccess'))
      } catch (error) {
        ElMessage.error(t('message.operationFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

// 重置账户表单
const resetAccountForm = () => {
  accountForm.username = 'merchant001'
  accountForm.email = 'merchant@example.com'
  accountForm.phone = '+1 234-567-8900'
}

// 修改密码
const changePassword = async () => {
  if (!passwordFormRef.value) return
  
  await passwordFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        // 实际API调用
        // await changePassword(passwordForm)
        
        ElMessage.success(t('settings.passwordChanged'))
        resetPasswordForm()
      } catch (error) {
        ElMessage.error(t('settings.passwordChangeFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

// 重置密码表单
const resetPasswordForm = () => {
  passwordForm.currentPassword = ''
  passwordForm.newPassword = ''
  passwordForm.confirmPassword = ''
}

// 保存通知设置
const saveNotificationSettings = async () => {
  saving.value = true
  try {
    // 实际API调用
    // await updateNotificationSettings(notificationSettings)
    
    ElMessage.success(t('message.operationSuccess'))
  } catch (error) {
    ElMessage.error(t('message.operationFailed'))
  } finally {
    saving.value = false
  }
}

// 保存系统设置
const saveSystemSettings = async () => {
  saving.value = true
  try {
    // 实际API调用
    // await updateSystemSettings(systemSettings)
    
    ElMessage.success(t('message.operationSuccess'))
  } catch (error) {
    ElMessage.error(t('message.operationFailed'))
  } finally {
    saving.value = false
  }
}

// 保存安全设置
const saveSecuritySettings = async () => {
  saving.value = true
  try {
    // 实际API调用
    // await updateSecuritySettings(securitySettings)
    
    ElMessage.success(t('message.operationSuccess'))
  } catch (error) {
    ElMessage.error(t('message.operationFailed'))
  } finally {
    saving.value = false
  }
}

// 导出数据
const exportData = () => {
  ElMessage.info(t('settings.exportDataComingSoon'))
}

// 清除缓存
const clearCache = () => {
  ElMessageBox.confirm(
    t('settings.clearCacheConfirm'),
    t('common.warning'),
    {
      confirmButtonText: t('common.confirm'),
      cancelButtonText: t('common.cancel'),
      type: 'warning'
    }
  ).then(() => {
    // 实际API调用
    // await clearCache()
    
    ElMessage.success(t('settings.cacheCleared'))
  })
}

// 删除账户
const deleteAccount = () => {
  ElMessageBox.confirm(
    t('settings.deleteAccountConfirm'),
    t('common.warning'),
    {
      confirmButtonText: t('common.confirm'),
      cancelButtonText: t('common.cancel'),
      type: 'error'
    }
  ).then(() => {
    ElMessage.info(t('settings.deleteAccountComingSoon'))
  })
}

onMounted(() => {
  // 加载设置数据
  // loadSettings()
})
</script>

<style scoped>
.settings {
  padding: 20px;
}

.settings-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.settings-form {
  max-width: 600px;
}

.setting-description {
  font-size: 12px;
  color: #999;
  margin-top: 5px;
  line-height: 1.4;
}
</style>

