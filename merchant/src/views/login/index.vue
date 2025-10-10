<template>
  <div class="login-container">
    <div class="language-switcher-top">
      <LanguageSwitcher />
    </div>
    
    <el-form
      ref="loginFormRef"
      :model="loginForm"
      :rules="loginRules"
      class="login-form"
      auto-complete="on"
      label-position="left"
    >
      <div class="title-container">
        <h3 class="title">{{ $t('login.title') }}</h3>
        <p class="subtitle">{{ $t('login.subtitle') }}</p>
      </div>

      <el-form-item prop="username">
        <el-input
          v-model="loginForm.username"
          :placeholder="$t('login.usernamePlaceholder')"
          name="username"
          type="text"
          tabindex="1"
          auto-complete="on"
          prefix-icon="User"
        />
      </el-form-item>

      <el-form-item prop="password">
        <el-input
          v-model="loginForm.password"
          :type="passwordType"
          :placeholder="$t('login.passwordPlaceholder')"
          name="password"
          tabindex="2"
          auto-complete="on"
          prefix-icon="Lock"
          @keyup.enter="handleLogin"
        >
          <template #suffix>
            <el-icon @click="showPassword" style="cursor: pointer;">
              <View v-if="passwordType === 'password'" />
              <Hide v-else />
            </el-icon>
          </template>
        </el-input>
      </el-form-item>

      <div class="remember-forgot">
        <el-checkbox v-model="rememberMe">
          {{ $t('login.rememberMe') }}
        </el-checkbox>
        <a href="#" class="forgot-link">{{ $t('login.forgotPassword') }}</a>
      </div>

      <el-button
        :loading="loading"
        type="primary"
        style="width: 100%; margin-bottom: 20px"
        @click="handleLogin"
      >
        {{ $t('common.login') }}
      </el-button>

      <div class="register-link">
        {{ $t('login.noAccount') }}
        <router-link to="/register">{{ $t('login.registerNow') }}</router-link>
      </div>
    </el-form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { useMerchantStore } from '@/stores/merchant'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

const { t } = useI18n()
const router = useRouter()
const merchantStore = useMerchantStore()

const loginForm = reactive({
  username: 'merchant001',
  password: '123456'
})

const loginRules: FormRules = {
  username: [{ required: true, message: () => t('login.usernameRequired'), trigger: 'blur' }],
  password: [{ required: true, message: () => t('login.passwordRequired'), trigger: 'blur' }]
}

const loginFormRef = ref<FormInstance>()
const loading = ref(false)
const passwordType = ref('password')
const rememberMe = ref(false)

const showPassword = () => {
  passwordType.value = passwordType.value === 'password' ? 'text' : 'password'
}

const handleLogin = async () => {
  if (!loginFormRef.value) return

  await loginFormRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        await merchantStore.login(loginForm)
        ElMessage.success(t('login.loginSuccess'))
        router.push('/')
      } catch (error: any) {
        ElMessage.error(t('login.loginFailed'))
      } finally {
        loading.value = false
      }
    }
  })
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  width: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.language-switcher-top {
  position: absolute;
  top: 20px;
  right: 20px;
  color: white;
}

.login-form {
  position: relative;
  width: 520px;
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
  margin-bottom: 40px;
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

.remember-forgot {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.forgot-link {
  color: #409EFF;
  text-decoration: none;
  font-size: 14px;
}

.forgot-link:hover {
  text-decoration: underline;
}

.register-link {
  text-align: center;
  font-size: 14px;
  color: #666;
}

.register-link a {
  color: #409EFF;
  text-decoration: none;
}

.register-link a:hover {
  text-decoration: underline;
}

:deep(.el-input__wrapper) {
  padding: 12px 15px;
}

:deep(.el-form-item) {
  border: 1px solid rgba(0, 0, 0, 0.1);
  background: rgba(0, 0, 0, 0.02);
  border-radius: 5px;
  color: #454545;
}
</style>

