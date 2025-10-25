<template>
  <div class="profile-edit">
    <!-- 头部 -->
    <div class="header">
      <button class="back-btn" @click="goBack">
        <i class="el-icon-arrow-left"></i>
      </button>
      <h1>{{ $t('profile.editProfile') }}</h1>
      <button class="save-btn" @click="saveProfile" :disabled="saving">
        {{ saving ? $t('common.saving') : $t('common.save') }}
      </button>
    </div>

    <!-- 头像部分 -->
    <div class="avatar-section">
      <div class="avatar-container">
        <img 
          :src="form.avatar || defaultAvatar" 
          :alt="$t('profile.avatar')"
          class="avatar-image"
        />
        <button class="change-avatar-btn" @click="changeAvatar">
          <i class="el-icon-camera"></i>
        </button>
      </div>
      <p class="avatar-tip">{{ $t('profile.changeAvatarTip') }}</p>
    </div>

    <!-- 表单 -->
    <div class="form-container">
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="80px"
        class="profile-form"
      >
        <el-form-item :label="$t('profile.name')" prop="name">
          <el-input
            v-model="form.name"
            :placeholder="$t('profile.namePlaceholder')"
            maxlength="50"
          />
        </el-form-item>

        <el-form-item :label="$t('profile.email')" prop="email">
          <el-input
            v-model="form.email"
            :placeholder="$t('profile.emailPlaceholder')"
            type="email"
          />
        </el-form-item>

        <el-form-item :label="$t('profile.phone')" prop="phone">
          <el-input
            v-model="form.phone"
            :placeholder="$t('profile.phonePlaceholder')"
            disabled
          />
          <p class="field-tip">{{ $t('profile.phoneCannotChange') }}</p>
        </el-form-item>

        <el-form-item :label="$t('profile.gender')" prop="gender">
          <el-radio-group v-model="form.gender">
            <el-radio :label="1">{{ $t('profile.male') }}</el-radio>
            <el-radio :label="2">{{ $t('profile.female') }}</el-radio>
            <el-radio :label="0">{{ $t('profile.other') }}</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item :label="$t('profile.birthday')" prop="birthday">
          <el-date-picker
            v-model="form.birthday"
            type="date"
            :placeholder="$t('profile.birthdayPlaceholder')"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            style="width: 100%"
          />
        </el-form-item>

        <el-form-item :label="$t('profile.bio')" prop="bio">
          <el-input
            v-model="form.bio"
            type="textarea"
            :placeholder="$t('profile.bioPlaceholder')"
            :rows="3"
            maxlength="200"
            show-word-limit
          />
        </el-form-item>
      </el-form>
    </div>

    <!-- 其他设置 -->
    <div class="settings-section">
      <div class="setting-item" @click="changePassword">
        <div class="setting-content">
          <i class="el-icon-lock"></i>
          <span>{{ $t('profile.changePassword') }}</span>
        </div>
        <i class="el-icon-arrow-right"></i>
      </div>

      <div class="setting-item" @click="privacySettings">
        <div class="setting-content">
          <i class="el-icon-view"></i>
          <span>{{ $t('profile.privacySettings') }}</span>
        </div>
        <i class="el-icon-arrow-right"></i>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { useUserStore } from '@/stores/user'
import { userApi } from '@/api'

const router = useRouter()
const { t } = useI18n()
const userStore = useUserStore()

// 状态管理
const saving = ref(false)
const formRef = ref<FormInstance>()
const defaultAvatar = 'https://via.placeholder.com/100x100/409EFF/ffffff?text=User'

// 表单数据
const form = reactive({
  name: '',
  email: '',
  phone: '',
  gender: 0,
  birthday: '',
  bio: '',
  avatar: ''
})

// 表单验证规则
const rules: FormRules = {
  name: [
    { required: true, message: t('profile.nameRequired'), trigger: 'blur' },
    { min: 1, max: 50, message: t('profile.nameLength'), trigger: 'blur' }
  ],
  email: [
    { type: 'email', message: t('profile.emailFormat'), trigger: 'blur' }
  ]
}

// 方法
const goBack = () => {
  router.go(-1)
}

const changeAvatar = () => {
  ElMessage.info(t('profile.avatarFeatureComingSoon'))
}

const changePassword = () => {
  ElMessage.info(t('profile.passwordFeatureComingSoon'))
}

const privacySettings = () => {
  ElMessage.info(t('profile.privacyFeatureComingSoon'))
}

const loadUserInfo = async () => {
  try {
    await userStore.fetchUserInfo()
    const userInfo = userStore.userInfo
    
    if (userInfo) {
      form.name = userInfo.name || ''
      form.email = userInfo.email || ''
      form.phone = userInfo.phone || ''
      form.gender = userInfo.gender || 0
      form.birthday = userInfo.birthday || ''
      form.bio = userInfo.bio || ''
      form.avatar = userInfo.avatar || ''
    }
  } catch (error: any) {
    console.error('加载用户信息失败:', error)
    ElMessage.error(error.message || t('profile.loadFailed'))
  }
}

const saveProfile = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    saving.value = true

    const updateData = {
      name: form.name,
      email: form.email,
      gender: form.gender,
      birthday: form.birthday,
      bio: form.bio,
      avatar: form.avatar
    }

    await userStore.updateUserInfo(updateData)
    ElMessage.success(t('profile.updateSuccess'))
    
    // 返回个人中心
    router.push('/mobile/profile')
  } catch (error: any) {
    console.error('保存资料失败:', error)
    ElMessage.error(error.message || t('profile.saveFailed'))
  } finally {
    saving.value = false
  }
}

// 生命周期
onMounted(() => {
  loadUserInfo()
})
</script>

<style scoped lang="scss">
.profile-edit {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #fff;
  border-bottom: 1px solid #eee;

  .back-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #f0f0f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #666;

    &:hover {
      background: #e0e0e0;
    }
  }

  h1 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #333;
  }

  .save-btn {
    background: #409eff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;

    &:hover:not(:disabled) {
      background: #337ecc;
    }

    &:disabled {
      background: #c0c4cc;
      cursor: not-allowed;
    }
  }
}

.avatar-section {
  background: #fff;
  padding: 30px 20px;
  text-align: center;
  margin-bottom: 12px;

  .avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 12px;

    .avatar-image {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #f0f0f0;
    }

    .change-avatar-btn {
      position: absolute;
      bottom: 0;
      right: 0;
      width: 32px;
      height: 32px;
      background: #409eff;
      color: white;
      border: none;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 14px;
      box-shadow: 0 2px 8px rgba(64, 158, 255, 0.3);

      &:hover {
        background: #337ecc;
      }
    }
  }

  .avatar-tip {
    color: #666;
    font-size: 14px;
    margin: 0;
  }
}

.form-container {
  background: #fff;
  margin-bottom: 12px;
}

.profile-form {
  padding: 20px;

  .field-tip {
    font-size: 12px;
    color: #999;
    margin: 4px 0 0;
  }
}

.settings-section {
  background: #fff;

  .setting-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #f8f9fa;
    }

    &:last-child {
      border-bottom: none;
    }

    .setting-content {
      display: flex;
      align-items: center;
      gap: 12px;

      i {
        font-size: 18px;
        color: #666;
      }

      span {
        font-size: 16px;
        color: #333;
      }
    }

    .el-icon-arrow-right {
      color: #ccc;
      font-size: 14px;
    }
  }
}
</style>

