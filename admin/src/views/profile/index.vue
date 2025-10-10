<template>
  <div class="profile">
    <el-card>
      <template #header>
        <span>Personal Center</span>
      </template>

      <el-row :gutter="20">
        <el-col :span="8">
          <div class="profile-info">
            <div class="avatar-section">
              <el-avatar :size="120" :src="userInfo.avatar" class="avatar">
                <el-icon :size="60"><User /></el-icon>
              </el-avatar>
              <el-button type="primary" @click="changeAvatar" class="change-avatar-btn">
                Change Avatar
              </el-button>
            </div>
            <div class="user-details">
              <h3>{{ userInfo.name }}</h3>
              <p class="role">{{ userInfo.role }}</p>
              <p class="email">{{ userInfo.email }}</p>
              <p class="phone">{{ userInfo.phone }}</p>
              <p class="join-date">Joined: {{ userInfo.joinDate }}</p>
            </div>
          </div>
        </el-col>

        <el-col :span="16">
          <el-tabs v-model="activeTab" class="profile-tabs">
            <el-tab-pane label="Profile Information" name="profile">
              <el-form
                ref="profileFormRef"
                :model="profileForm"
                :rules="profileRules"
                label-width="120px"
                class="profile-form"
              >
                <el-form-item label="Full Name" prop="name">
                  <el-input v-model="profileForm.name" />
                </el-form-item>
                <el-form-item label="Email" prop="email">
                  <el-input v-model="profileForm.email" />
                </el-form-item>
                <el-form-item label="Phone" prop="phone">
                  <el-input v-model="profileForm.phone" />
                </el-form-item>
                <el-form-item label="Department" prop="department">
                  <el-input v-model="profileForm.department" />
                </el-form-item>
                <el-form-item label="Position" prop="position">
                  <el-input v-model="profileForm.position" />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="updateProfile" :loading="updating">
                    Update Profile
                  </el-button>
                  <el-button @click="resetForm">Reset</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>

            <el-tab-pane label="Change Password" name="password">
              <el-form
                ref="passwordFormRef"
                :model="passwordForm"
                :rules="passwordRules"
                label-width="120px"
                class="password-form"
              >
                <el-form-item label="Current Password" prop="currentPassword">
                  <el-input
                    v-model="passwordForm.currentPassword"
                    type="password"
                    show-password
                  />
                </el-form-item>
                <el-form-item label="New Password" prop="newPassword">
                  <el-input
                    v-model="passwordForm.newPassword"
                    type="password"
                    show-password
                  />
                </el-form-item>
                <el-form-item label="Confirm Password" prop="confirmPassword">
                  <el-input
                    v-model="passwordForm.confirmPassword"
                    type="password"
                    show-password
                  />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="changePassword" :loading="changing">
                    Change Password
                  </el-button>
                  <el-button @click="resetPasswordForm">Reset</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>

            <el-tab-pane label="System Settings" name="settings">
              <el-form label-width="120px" class="settings-form">
                <el-form-item label="Language">
                  <el-select v-model="settings.language" style="width: 200px">
                    <el-option label="English" value="en" />
                    <el-option label="中文" value="zh" />
                    <el-option label="Bahasa Melayu" value="ms" />
                  </el-select>
                </el-form-item>
                <el-form-item label="Theme">
                  <el-radio-group v-model="settings.theme">
                    <el-radio label="light">Light</el-radio>
                    <el-radio label="dark">Dark</el-radio>
                    <el-radio label="auto">Auto</el-radio>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="Notifications">
                  <el-switch v-model="settings.notifications" />
                </el-form-item>
                <el-form-item label="Email Notifications">
                  <el-switch v-model="settings.emailNotifications" />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="saveSettings" :loading="saving">
                    Save Settings
                  </el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>

            <el-tab-pane label="Activity Log" name="activity">
              <el-table :data="activityLog" style="width: 100%" max-height="400">
                <el-table-column prop="action" label="Action" width="150" />
                <el-table-column prop="description" label="Description" min-width="200" />
                <el-table-column prop="ip" label="IP Address" width="120" />
                <el-table-column prop="timestamp" label="Time" width="180" />
                <el-table-column prop="status" label="Status" width="100">
                  <template #default="{ row }">
                    <el-tag :type="row.status === 'success' ? 'success' : 'danger'">
                      {{ row.status }}
                    </el-tag>
                  </template>
                </el-table-column>
              </el-table>
              <el-pagination
                v-model:current-page="activityPage"
                v-model:page-size="activityPageSize"
                :total="activityTotal"
                :page-sizes="[10, 20, 50]"
                layout="total, sizes, prev, pager, next"
                @size-change="loadActivityLog"
                @current-change="loadActivityLog"
                style="margin-top: 20px; justify-content: flex-end"
              />
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
    </el-card>

    <!-- 头像上传对话框 -->
    <el-dialog
      v-model="avatarDialogVisible"
      title="Change Avatar"
      width="400px"
    >
      <div class="avatar-upload">
        <el-upload
          class="avatar-uploader"
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
          :before-upload="beforeAvatarUpload"
          action="#"
        >
          <el-image
            v-if="avatarUrl"
            :src="avatarUrl"
            class="avatar-preview"
          />
          <el-icon v-else class="avatar-uploader-icon"><Plus /></el-icon>
        </el-upload>
        <div class="upload-tip">
          Upload avatar image (JPG/PNG, max 2MB)
        </div>
      </div>
      <template #footer>
        <el-button @click="avatarDialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="confirmAvatar" :loading="uploading">
          Confirm
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { User, Plus } from '@element-plus/icons-vue'

const activeTab = ref('profile')
const updating = ref(false)
const changing = ref(false)
const saving = ref(false)
const uploading = ref(false)
const avatarDialogVisible = ref(false)
const avatarUrl = ref('')

const profileFormRef = ref()
const passwordFormRef = ref()

const userInfo = ref({
  id: 1,
  name: 'Admin User',
  role: 'Platform Administrator',
  email: 'admin@ecommerce.com',
  phone: '+1 234-567-8900',
  avatar: '/static/admin-avatar.jpg',
  joinDate: '2025-01-01'
})

const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  department: '',
  position: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const settings = reactive({
  language: 'en',
  theme: 'light',
  notifications: true,
  emailNotifications: true
})

const activityLog = ref([
  {
    action: 'Login',
    description: 'User logged in successfully',
    ip: '192.168.1.100',
    timestamp: '2025-01-04 14:30:00',
    status: 'success'
  },
  {
    action: 'Product Update',
    description: 'Updated product: Premium Wireless Headphones',
    ip: '192.168.1.100',
    timestamp: '2025-01-04 14:25:00',
    status: 'success'
  },
  {
    action: 'Merchant Audit',
    description: 'Approved merchant: TechPro Store',
    ip: '192.168.1.100',
    timestamp: '2025-01-04 14:20:00',
    status: 'success'
  }
])

const activityPage = ref(1)
const activityPageSize = ref(10)
const activityTotal = ref(25)

const profileRules = {
  name: [
    { required: true, message: 'Please enter full name', trigger: 'blur' }
  ],
  email: [
    { required: true, message: 'Please enter email', trigger: 'blur' },
    { type: 'email', message: 'Please enter valid email', trigger: 'blur' }
  ],
  phone: [
    { required: true, message: 'Please enter phone number', trigger: 'blur' }
  ]
}

const passwordRules = {
  currentPassword: [
    { required: true, message: 'Please enter current password', trigger: 'blur' }
  ],
  newPassword: [
    { required: true, message: 'Please enter new password', trigger: 'blur' },
    { min: 6, message: 'Password must be at least 6 characters', trigger: 'blur' }
  ],
  confirmPassword: [
    { required: true, message: 'Please confirm password', trigger: 'blur' },
    {
      validator: (rule: any, value: string, callback: Function) => {
        if (value !== passwordForm.newPassword) {
          callback(new Error('Passwords do not match'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 加载用户数据
const loadUserData = async () => {
  try {
    // 模拟数据
    Object.assign(profileForm, {
      name: userInfo.value.name,
      email: userInfo.value.email,
      phone: userInfo.value.phone,
      department: 'IT Department',
      position: 'Platform Administrator'
    })

    // 实际API调用
    // const res = await getUserProfile()
    // userInfo.value = res.userInfo
    // Object.assign(profileForm, res.profile)
  } catch (error) {
    console.error('Failed to load user data:', error)
  }
}

// 更新个人资料
const updateProfile = async () => {
  if (!profileFormRef.value) return
  
  await profileFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      updating.value = true
      try {
        // 实际API调用
        // await updateUserProfile(profileForm)
        
        Object.assign(userInfo.value, profileForm)
        ElMessage.success('Profile updated successfully')
      } catch (error) {
        ElMessage.error('Failed to update profile')
      } finally {
        updating.value = false
      }
    }
  })
}

// 重置表单
const resetForm = () => {
  Object.assign(profileForm, {
    name: userInfo.value.name,
    email: userInfo.value.email,
    phone: userInfo.value.phone,
    department: 'IT Department',
    position: 'Platform Administrator'
  })
}

// 修改密码
const changePassword = async () => {
  if (!passwordFormRef.value) return
  
  await passwordFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      changing.value = true
      try {
        // 实际API调用
        // await changeUserPassword(passwordForm)
        
        ElMessage.success('Password changed successfully')
        resetPasswordForm()
      } catch (error) {
        ElMessage.error('Failed to change password')
      } finally {
        changing.value = false
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

// 保存设置
const saveSettings = async () => {
  saving.value = true
  try {
    // 实际API调用
    // await saveUserSettings(settings)
    
    ElMessage.success('Settings saved successfully')
  } catch (error) {
    ElMessage.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

// 加载活动日志
const loadActivityLog = async () => {
  try {
    // 实际API调用
    // const res = await getActivityLog({
    //   page: activityPage.value,
    //   pageSize: activityPageSize.value
    // })
    // activityLog.value = res.list
    // activityTotal.value = res.total
  } catch (error) {
    console.error('Failed to load activity log:', error)
  }
}

// 更换头像
const changeAvatar = () => {
  avatarUrl.value = userInfo.value.avatar
  avatarDialogVisible.value = true
}

// 头像上传成功
const handleAvatarSuccess = (response: any, file: any) => {
  avatarUrl.value = URL.createObjectURL(file.raw)
}

// 头像上传前验证
const beforeAvatarUpload = (file: any) => {
  const isJPG = file.type === 'image/jpeg' || file.type === 'image/png'
  const isLt2M = file.size / 1024 / 1024 < 2

  if (!isJPG) {
    ElMessage.error('Avatar must be JPG or PNG format')
  }
  if (!isLt2M) {
    ElMessage.error('Avatar size must be less than 2MB')
  }
  return isJPG && isLt2M
}

// 确认头像
const confirmAvatar = async () => {
  uploading.value = true
  try {
    // 实际API调用
    // await updateUserAvatar(avatarUrl.value)
    
    userInfo.value.avatar = avatarUrl.value
    ElMessage.success('Avatar updated successfully')
    avatarDialogVisible.value = false
  } catch (error) {
    ElMessage.error('Failed to update avatar')
  } finally {
    uploading.value = false
  }
}

onMounted(() => {
  loadUserData()
  loadActivityLog()
})
</script>

<style scoped>
.profile {
  padding: 20px;
}

.profile-info {
  text-align: center;
}

.avatar-section {
  margin-bottom: 30px;
}

.avatar {
  margin-bottom: 15px;
}

.change-avatar-btn {
  margin-top: 10px;
}

.user-details h3 {
  margin: 0 0 10px 0;
  font-size: 24px;
  color: #333;
}

.user-details p {
  margin: 8px 0;
  color: #666;
  font-size: 14px;
}

.role {
  color: #409EFF !important;
  font-weight: 500;
}

.profile-tabs {
  margin-top: 20px;
}

.profile-form,
.password-form,
.settings-form {
  max-width: 500px;
}

.avatar-upload {
  text-align: center;
}

.avatar-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  width: 200px;
  height: 200px;
  margin: 0 auto 20px;
}

.avatar-uploader:hover {
  border-color: #409EFF;
}

.avatar-preview {
  width: 100%;
  height: 100%;
}

.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 200px;
  height: 200px;
  line-height: 200px;
  text-align: center;
}

.upload-tip {
  font-size: 12px;
  color: #999;
}
</style>
