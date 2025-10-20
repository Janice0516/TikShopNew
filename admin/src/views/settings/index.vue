<template>
  <div class="settings-page">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>系统设置</span>
        </div>
      </template>

      <el-tabs v-model="activeTab" type="border-card">
        <!-- 基本设置 -->
        <el-tab-pane label="基本设置" name="basic">
          <el-form
            ref="basicFormRef"
            :model="basicSettings"
            :rules="basicRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="网站名称" prop="siteName">
              <el-input v-model="basicSettings.siteName" placeholder="请输入网站名称" />
            </el-form-item>
            <el-form-item label="网站Logo" prop="siteLogo">
              <div class="logo-upload">
                <el-upload
                  class="logo-uploader"
                  :show-file-list="false"
                  :on-success="handleLogoSuccess"
                  :before-upload="beforeLogoUpload"
                  action="#"
                >
                  <el-image
                    v-if="basicSettings.siteLogo"
                    :src="basicSettings.siteLogo"
                    class="logo-preview"
                  />
                  <el-icon v-else class="logo-uploader-icon"><Plus /></el-icon>
                </el-upload>
                <div class="upload-tip">
                  建议尺寸：200x60px，支持JPG/PNG格式
                </div>
              </div>
            </el-form-item>
            <el-form-item label="网站描述" prop="siteDescription">
              <el-input
                v-model="basicSettings.siteDescription"
                type="textarea"
                :rows="3"
                placeholder="请输入网站描述"
              />
            </el-form-item>
            <el-form-item label="客服电话" prop="customerServicePhone">
              <el-input v-model="basicSettings.customerServicePhone" placeholder="请输入客服电话" />
            </el-form-item>
            <el-form-item label="客服邮箱" prop="customerServiceEmail">
              <el-input v-model="basicSettings.customerServiceEmail" placeholder="请输入客服邮箱" />
            </el-form-item>
            <el-form-item label="默认货币" prop="defaultCurrency">
              <el-select v-model="basicSettings.defaultCurrency" style="width: 200px">
                <el-option label="人民币 (¥)" value="CNY" />
                <el-option label="美元 ($)" value="USD" />
                <el-option label="马来西亚林吉特 (RM)" value="MYR" />
                <el-option label="新加坡元 (S$)" value="SGD" />
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveBasicSettings" :loading="saving">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 业务设置 -->
        <el-tab-pane label="业务设置" name="business">
          <el-form
            ref="businessFormRef"
            :model="businessSettings"
            :rules="businessRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="自动审核订单" prop="autoApproveOrders">
              <el-switch v-model="businessSettings.autoApproveOrders" />
              <span class="form-tip">开启后，订单将自动审核通过</span>
            </el-form-item>
            <el-form-item label="订单超时时间" prop="orderTimeoutHours">
              <el-input-number
                v-model="businessSettings.orderTimeoutHours"
                :min="1"
                :max="168"
                controls-position="right"
              />
              <span class="form-tip">小时（1-168小时）</span>
            </el-form-item>
            <el-form-item label="最低提现金额" prop="minWithdrawalAmount">
              <el-input-number
                v-model="businessSettings.minWithdrawalAmount"
                :min="1"
                :precision="2"
                controls-position="right"
              />
              <span class="form-tip">元</span>
            </el-form-item>
            <el-form-item label="平台手续费率" prop="platformFeeRate">
              <el-input-number
                v-model="businessSettings.platformFeeRate"
                :min="0"
                :max="1"
                :precision="2"
                :step="0.01"
                controls-position="right"
              />
              <span class="form-tip">%（0-100%）</span>
            </el-form-item>
            <el-form-item label="商家入驻审核" prop="merchantApprovalRequired">
              <el-switch v-model="businessSettings.merchantApprovalRequired" />
              <span class="form-tip">开启后，新商家需要审核才能入驻</span>
            </el-form-item>
            <el-form-item label="商品上架审核" prop="productApprovalRequired">
              <el-switch v-model="businessSettings.productApprovalRequired" />
              <span class="form-tip">开启后，商品需要审核才能上架</span>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveBusinessSettings" :loading="saving">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 安全设置 -->
        <el-tab-pane label="安全设置" name="security">
          <el-form
            ref="securityFormRef"
            :model="securitySettings"
            :rules="securityRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="登录失败锁定" prop="loginLockEnabled">
              <el-switch v-model="securitySettings.loginLockEnabled" />
              <span class="form-tip">开启后，连续登录失败将锁定账户</span>
            </el-form-item>
            <el-form-item label="最大登录失败次数" prop="maxLoginAttempts">
              <el-input-number
                v-model="securitySettings.maxLoginAttempts"
                :min="3"
                :max="10"
                controls-position="right"
                :disabled="!securitySettings.loginLockEnabled"
              />
              <span class="form-tip">次</span>
            </el-form-item>
            <el-form-item label="锁定时间" prop="lockoutDuration">
              <el-input-number
                v-model="securitySettings.lockoutDuration"
                :min="5"
                :max="60"
                controls-position="right"
                :disabled="!securitySettings.loginLockEnabled"
              />
              <span class="form-tip">分钟</span>
            </el-form-item>
            <el-form-item label="密码最小长度" prop="minPasswordLength">
              <el-input-number
                v-model="securitySettings.minPasswordLength"
                :min="6"
                :max="20"
                controls-position="right"
              />
              <span class="form-tip">位</span>
            </el-form-item>
            <el-form-item label="强制密码复杂度" prop="requirePasswordComplexity">
              <el-switch v-model="securitySettings.requirePasswordComplexity" />
              <span class="form-tip">开启后，密码必须包含字母、数字和特殊字符</span>
            </el-form-item>
            <el-form-item label="会话超时时间" prop="sessionTimeout">
              <el-input-number
                v-model="securitySettings.sessionTimeout"
                :min="30"
                :max="480"
                controls-position="right"
              />
              <span class="form-tip">分钟</span>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveSecuritySettings" :loading="saving">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 通知设置 -->
        <el-tab-pane label="通知设置" name="notification">
          <el-form
            ref="notificationFormRef"
            :model="notificationSettings"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="邮件通知">
              <el-switch v-model="notificationSettings.emailEnabled" />
            </el-form-item>
            <el-form-item label="短信通知">
              <el-switch v-model="notificationSettings.smsEnabled" />
            </el-form-item>
            <el-form-item label="系统通知">
              <el-switch v-model="notificationSettings.systemEnabled" />
            </el-form-item>
            
            <el-divider content-position="left">邮件通知设置</el-divider>
            
            <el-form-item label="SMTP服务器" prop="smtpHost">
              <el-input v-model="notificationSettings.smtpHost" placeholder="smtp.example.com" />
            </el-form-item>
            <el-form-item label="SMTP端口" prop="smtpPort">
              <el-input-number v-model="notificationSettings.smtpPort" :min="1" :max="65535" />
            </el-form-item>
            <el-form-item label="发送邮箱" prop="smtpUsername">
              <el-input v-model="notificationSettings.smtpUsername" placeholder="noreply@example.com" />
            </el-form-item>
            <el-form-item label="邮箱密码" prop="smtpPassword">
              <el-input
                v-model="notificationSettings.smtpPassword"
                type="password"
                show-password
                placeholder="请输入邮箱密码或授权码"
              />
            </el-form-item>
            <el-form-item label="SSL加密">
              <el-switch v-model="notificationSettings.smtpSsl" />
            </el-form-item>
            
            <el-divider content-position="left">通知类型</el-divider>
            
            <el-form-item label="新订单通知">
              <el-switch v-model="notificationSettings.newOrderNotification" />
            </el-form-item>
            <el-form-item label="支付成功通知">
              <el-switch v-model="notificationSettings.paymentSuccessNotification" />
            </el-form-item>
            <el-form-item label="退款通知">
              <el-switch v-model="notificationSettings.refundNotification" />
            </el-form-item>
            <el-form-item label="系统维护通知">
              <el-switch v-model="notificationSettings.maintenanceNotification" />
            </el-form-item>
            
            <el-form-item>
              <el-button type="primary" @click="saveNotificationSettings" :loading="saving">
                保存设置
              </el-button>
              <el-button @click="testEmailConnection" :loading="testing">
                测试邮件连接
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 系统维护 -->
        <el-tab-pane label="系统维护" name="maintenance">
          <div class="maintenance-section">
            <el-card class="maintenance-card">
              <template #header>
                <span>缓存管理</span>
              </template>
              <div class="maintenance-actions">
                <el-button type="warning" @click="clearCache" :loading="clearing">
                  清理系统缓存
                </el-button>
                <el-button type="info" @click="clearLogs" :loading="clearing">
                  清理系统日志
                </el-button>
                <el-button type="success" @click="optimizeDatabase" :loading="optimizing">
                  优化数据库
                </el-button>
              </div>
            </el-card>

            <el-card class="maintenance-card">
              <template #header>
                <span>数据备份</span>
              </template>
              <div class="maintenance-actions">
                <el-button type="primary" @click="backupDatabase" :loading="backing">
                  备份数据库
                </el-button>
                <el-button type="success" @click="backupFiles" :loading="backing">
                  备份文件
                </el-button>
                <el-button type="warning" @click="restoreDatabase" :loading="restoring">
                  恢复数据库
                </el-button>
              </div>
            </el-card>

            <el-card class="maintenance-card">
              <template #header>
                <span>系统信息</span>
              </template>
              <el-descriptions :column="2" border>
                <el-descriptions-item label="系统版本">v1.0.0</el-descriptions-item>
                <el-descriptions-item label="数据库版本">PostgreSQL 15.0</el-descriptions-item>
                <el-descriptions-item label="Node.js版本">v18.17.0</el-descriptions-item>
                <el-descriptions-item label="运行时间">15天 8小时 32分钟</el-descriptions-item>
                <el-descriptions-item label="内存使用率">68.5%</el-descriptions-item>
                <el-descriptions-item label="磁盘使用率">45.2%</el-descriptions-item>
                <el-descriptions-item label="CPU使用率">23.1%</el-descriptions-item>
                <el-descriptions-item label="网络状态">正常</el-descriptions-item>
              </el-descriptions>
            </el-card>
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, FormInstance, FormRules } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import {
  getSystemSettings,
  updateBasicSettings,
  updateBusinessSettings,
  updateSecuritySettings,
  updateNotificationSettings,
  testEmailSettings,
  clearSystemCache,
  clearSystemLogs,
  optimizeDatabaseTables,
  createDatabaseBackup,
  createFileBackup,
  restoreDatabaseFromBackup,
  getSystemInfo
} from '@/api/settings'

const activeTab = ref('basic')
const saving = ref(false)
const testing = ref(false)
const clearing = ref(false)
const optimizing = ref(false)
const backing = ref(false)
const restoring = ref(false)

const basicFormRef = ref<FormInstance>()
const businessFormRef = ref<FormInstance>()
const securityFormRef = ref<FormInstance>()
const notificationFormRef = ref<FormInstance>()

// 基本设置
const basicSettings = reactive({
  siteName: 'TikTok Shop',
  siteLogo: '',
  siteDescription: '专业的电商平台，为商家和用户提供优质的服务',
  customerServicePhone: '+60 123-456-7890',
  customerServiceEmail: 'support@tiktokshop.com',
  defaultCurrency: 'MYR'
})

// 业务设置
const businessSettings = reactive({
  autoApproveOrders: false,
  orderTimeoutHours: 24,
  minWithdrawalAmount: 100,
  platformFeeRate: 0.05,
  merchantApprovalRequired: true,
  productApprovalRequired: false
})

// 安全设置
const securitySettings = reactive({
  loginLockEnabled: true,
  maxLoginAttempts: 5,
  lockoutDuration: 15,
  minPasswordLength: 8,
  requirePasswordComplexity: true,
  sessionTimeout: 120
})

// 通知设置
const notificationSettings = reactive({
  emailEnabled: true,
  smsEnabled: false,
  systemEnabled: true,
  smtpHost: 'smtp.gmail.com',
  smtpPort: 587,
  smtpUsername: '',
  smtpPassword: '',
  smtpSsl: true,
  newOrderNotification: true,
  paymentSuccessNotification: true,
  refundNotification: true,
  maintenanceNotification: true
})

// 表单验证规则
const basicRules: FormRules = {
  siteName: [
    { required: true, message: '请输入网站名称', trigger: 'blur' },
    { min: 2, max: 50, message: '网站名称长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  customerServicePhone: [
    { required: true, message: '请输入客服电话', trigger: 'blur' }
  ],
  customerServiceEmail: [
    { required: true, message: '请输入客服邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ]
}

const businessRules: FormRules = {
  orderTimeoutHours: [
    { required: true, message: '请输入订单超时时间', trigger: 'blur' }
  ],
  minWithdrawalAmount: [
    { required: true, message: '请输入最低提现金额', trigger: 'blur' }
  ],
  platformFeeRate: [
    { required: true, message: '请输入平台手续费率', trigger: 'blur' }
  ]
}

const securityRules: FormRules = {
  maxLoginAttempts: [
    { required: true, message: '请输入最大登录失败次数', trigger: 'blur' }
  ],
  lockoutDuration: [
    { required: true, message: '请输入锁定时间', trigger: 'blur' }
  ],
  minPasswordLength: [
    { required: true, message: '请输入密码最小长度', trigger: 'blur' }
  ],
  sessionTimeout: [
    { required: true, message: '请输入会话超时时间', trigger: 'blur' }
  ]
}

// 保存基本设置
const saveBasicSettings = async () => {
  if (!basicFormRef.value) return
  
  await basicFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        await updateBasicSettings(basicSettings)
        ElMessage.success('基本设置保存成功')
      } catch (error) {
        ElMessage.error('保存失败')
      } finally {
        saving.value = false
      }
    }
  })
}

// 保存业务设置
const saveBusinessSettings = async () => {
  if (!businessFormRef.value) return
  
  await businessFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        await updateBusinessSettings(businessSettings)
        ElMessage.success('业务设置保存成功')
      } catch (error) {
        ElMessage.error('保存失败')
      } finally {
        saving.value = false
      }
    }
  })
}

// 保存安全设置
const saveSecuritySettings = async () => {
  if (!securityFormRef.value) return
  
  await securityFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        await updateSecuritySettings(securitySettings)
        ElMessage.success('安全设置保存成功')
      } catch (error) {
        ElMessage.error('保存失败')
      } finally {
        saving.value = false
      }
    }
  })
}

// 保存通知设置
const saveNotificationSettings = async () => {
  saving.value = true
  try {
    await updateNotificationSettings(notificationSettings)
    ElMessage.success('通知设置保存成功')
  } catch (error) {
    ElMessage.error('保存失败')
  } finally {
    saving.value = false
  }
}

// Logo上传成功
const handleLogoSuccess = (_response: any, file: any) => {
  basicSettings.siteLogo = URL.createObjectURL(file.raw)
}

// Logo上传前验证
const beforeLogoUpload = (file: any) => {
  const isJPG = file.type === 'image/jpeg' || file.type === 'image/png'
  const isLt2M = file.size / 1024 / 1024 < 2

  if (!isJPG) {
    ElMessage.error('Logo必须是JPG或PNG格式')
  }
  if (!isLt2M) {
    ElMessage.error('Logo大小必须小于2MB')
  }
  return isJPG && isLt2M
}

// 测试邮件连接
const testEmailConnection = async () => {
  testing.value = true
  try {
    await testEmailSettings({ email: notificationSettings.smtpUsername })
    ElMessage.success('邮件连接测试成功')
  } catch (error) {
    ElMessage.error('邮件连接测试失败')
  } finally {
    testing.value = false
  }
}

// 清理缓存
const clearCache = async () => {
  try {
    await ElMessageBox.confirm('确定要清理系统缓存吗？', '确认操作', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    clearing.value = true
    await clearSystemCache()
    ElMessage.success('缓存清理成功')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('缓存清理失败')
    }
  } finally {
    clearing.value = false
  }
}

// 清理日志
const clearLogs = async () => {
  try {
    await ElMessageBox.confirm('确定要清理系统日志吗？', '确认操作', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    clearing.value = true
    await clearSystemLogs()
    ElMessage.success('日志清理成功')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('日志清理失败')
    }
  } finally {
    clearing.value = false
  }
}

// 优化数据库
const optimizeDatabase = async () => {
  try {
    await ElMessageBox.confirm('确定要优化数据库吗？此操作可能需要几分钟时间。', '确认操作', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    optimizing.value = true
    await optimizeDatabaseTables()
    ElMessage.success('数据库优化成功')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('数据库优化失败')
    }
  } finally {
    optimizing.value = false
  }
}

// 备份数据库
const backupDatabase = async () => {
  backing.value = true
  try {
    await createDatabaseBackup()
    ElMessage.success('数据库备份成功')
  } catch (error) {
    ElMessage.error('数据库备份失败')
  } finally {
    backing.value = false
  }
}

// 备份文件
const backupFiles = async () => {
  backing.value = true
  try {
    await createFileBackup()
    ElMessage.success('文件备份成功')
  } catch (error) {
    ElMessage.error('文件备份失败')
  } finally {
    backing.value = false
  }
}

// 恢复数据库
const restoreDatabase = async () => {
  try {
    await ElMessageBox.confirm('确定要恢复数据库吗？此操作将覆盖当前数据！', '危险操作', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'error'
    })
    
    restoring.value = true
    await restoreDatabaseFromBackup({ backupId: 'latest' })
    ElMessage.success('数据库恢复成功')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('数据库恢复失败')
    }
  } finally {
    restoring.value = false
  }
}

// 加载设置数据
const loadSettings = async () => {
  try {
    const res = await getSystemSettings()
    const actualData = res.data?.data || res.data
    
    if (actualData.basic) {
      Object.assign(basicSettings, actualData.basic)
    }
    if (actualData.business) {
      Object.assign(businessSettings, actualData.business)
    }
    if (actualData.security) {
      Object.assign(securitySettings, actualData.security)
    }
    if (actualData.notification) {
      Object.assign(notificationSettings, actualData.notification)
    }
  } catch (error) {
    console.error('加载设置失败:', error)
    ElMessage.warning('加载设置失败，使用默认配置')
  }
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
.settings-page {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.settings-form {
  max-width: 800px;
}

.form-tip {
  margin-left: 10px;
  color: #909399;
  font-size: 12px;
}

.logo-upload {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.logo-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: border-color 0.3s;
}

.logo-uploader:hover {
  border-color: #409eff;
}

.logo-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 200px;
  height: 60px;
  line-height: 60px;
  text-align: center;
}

.logo-preview {
  width: 200px;
  height: 60px;
  display: block;
}

.upload-tip {
  margin-top: 10px;
  color: #909399;
  font-size: 12px;
}

.maintenance-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.maintenance-card {
  margin-bottom: 20px;
}

.maintenance-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
</style>
