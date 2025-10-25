<template>
  <div class="register-container">
    <div class="language-switcher-top">
      <LanguageSwitcher />
    </div>
    
    <el-form
      ref="registerFormRef"
      :model="registerForm"
      :rules="registerRules"
      class="register-form"
      label-width="140px"
    >
      <div class="title-container">
        <h3 class="title">{{ $t('register.title') }}</h3>
        <p class="subtitle">{{ $t('login.subtitle') }}</p>
      </div>

      <el-form-item :label="$t('login.username')" prop="username">
        <el-input v-model="registerForm.username" />
      </el-form-item>

      <el-form-item :label="$t('login.password')" prop="password">
        <el-input v-model="registerForm.password" type="password" />
      </el-form-item>

      <el-form-item :label="$t('register.merchantName')" prop="merchantName">
        <el-input v-model="registerForm.merchantName" />
      </el-form-item>

      <el-form-item :label="$t('register.contactName')" prop="contactName">
        <el-input v-model="registerForm.contactName" />
      </el-form-item>

      <el-form-item :label="$t('register.contactPhone')" prop="contactPhone">
        <el-input v-model="registerForm.contactPhone" />
      </el-form-item>

      <el-form-item :label="$t('register.shopName')" prop="shopName">
        <el-input v-model="registerForm.shopName" />
      </el-form-item>

      <el-form-item :label="$t('register.inviteCode')" prop="inviteCode" required>
        <el-input 
          v-model="registerForm.inviteCode" 
          :placeholder="$t('register.inviteCodePlaceholder')"
        />
        <div class="invite-code-tip">
          {{ $t('register.inviteCodeTip') }}
        </div>
      </el-form-item>

      <el-form-item>
        <el-button
          :loading="loading"
          type="primary"
          style="width: 100%"
          @click="handleRegister"
        >
          {{ $t('register.submitApplication') }}
        </el-button>
      </el-form-item>

      <div class="login-link">
        {{ $t('register.alreadyHaveAccount') }}
        <router-link to="/login">{{ $t('register.loginNow') }}</router-link>
      </div>
    </el-form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'
import { register } from '../../api/merchant'

const { t } = useI18n()
const router = useRouter()

const registerForm = reactive({
  username: '',
  password: '',
  merchantName: '',
  contactName: '',
  contactPhone: '',
  shopName: '',
  inviteCode: ''
})

const registerRules: FormRules = {
  username: [{ required: true, message: () => t('validation.required'), trigger: 'blur' }],
  password: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { min: 6, message: () => t('validation.minLength', { min: 6 }), trigger: 'blur' }
  ],
  merchantName: [{ required: true, message: () => t('validation.required'), trigger: 'blur' }],
  contactName: [{ required: true, message: () => t('validation.required'), trigger: 'blur' }],
  contactPhone: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { pattern: /^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/, message: () => t('validation.phone'), trigger: 'blur' }
  ],
  inviteCode: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { min: 6, message: () => t('validation.minLength', { min: 6 }), trigger: 'blur' }
  ]
}

const registerFormRef = ref<FormInstance>()
const loading = ref(false)

const handleRegister = async () => {
  if (!registerFormRef.value) return

  await registerFormRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        // 调用注册API
        const response = await register(registerForm)
        if (response.code === 200) {
          ElMessage.success(response.data.message || t('register.registerSuccess'))
          router.push('/login')
        } else {
          ElMessage.error(response.message || 'Registration failed')
        }
      } catch (error: any) {
        ElMessage.error(error.message || 'Registration failed')
      } finally {
        loading.value = false
      }
    }
  })
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  width: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  padding: 40px 20px;
}

.language-switcher-top {
  position: absolute;
  top: 20px;
  right: 20px;
  color: white;
}

.register-form {
  position: relative;
  width: 600px;
  max-width: 100%;
  padding: 50px 35px 30px;
  margin: 0 auto;
  overflow: hidden;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
}

.title-container {
  position: relative;
  margin-bottom: 30px;
  text-align: center;

  .title {
    font-size: 28px;
    color: #333;
    margin: 0 0 10px;
    font-weight: bold;
  }

  .subtitle {
    color: #999;
    font-size: 14px;
    margin: 0;
  }
}

.login-link {
  text-align: center;
  font-size: 14px;
  color: #666;
  margin-top: 20px;
}

.login-link a {
  color: #409EFF;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}

.invite-code-tip {
  font-size: 12px;
  color: #666;
  margin-top: 5px;
  line-height: 1.4;
}
</style>

