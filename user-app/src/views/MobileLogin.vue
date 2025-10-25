<template>
  <div class="mobile-login">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="header-title">{{ t('login.title') }}</h1>
      <div class="header-spacer"></div>
    </div>

    <!-- Login Content -->
    <div class="login-content">
      <!-- Logo Section -->
      <div class="logo-section">
        <div class="logo-container">
          <img src="/logo.png" alt="TikTok Shop" class="logo" />
        </div>
        <h2 class="welcome-text">{{ t('login.welcome') }}</h2>
      </div>
      
      <!-- Loading State -->
      <MobileLoading 
        :loading="isLoading"
        type="spinner"
        message="登录中..."
      />
      
      <!-- Error State -->
      <MobileError 
        :show="hasError"
        type="server"
        :title="errorTitle"
        :message="errorMessage"
        @retry="handleRetry"
      />

      <!-- Login Form -->
      <div class="form-container">
        <form @submit.prevent="handleLogin" class="login-form">
          <!-- Phone Input -->
          <div class="input-group">
            <div class="input-label">{{ t('login.phoneLabel') }}</div>
            <div class="input-wrapper">
              <div class="input-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9844 21.5573 21.2136 21.3521 21.4019C21.1469 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27099 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36162C2.30512 3.09849 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.0498 2.28271 3.30379 2.17052C3.55777 2.05833 3.83233 2.00026 4.10999 2H7.10999C7.59531 1.99522 8.06607 2.16708 8.43376 2.48353C8.80145 2.79999 9.04207 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.89391 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5865 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 21.9999 16.92H22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <input
                v-model="loginForm.phone"
                type="tel"
                :placeholder="t('login.phonePlaceholder')"
                class="form-input"
                :class="{ 'error': phoneError }"
                @blur="validatePhone"
                @input="clearPhoneError"
              />
            </div>
            <div v-if="phoneError" class="error-message">{{ phoneError }}</div>
          </div>

          <!-- Password Input -->
          <div class="input-group">
            <div class="input-label">{{ t('login.passwordLabel') }}</div>
            <div class="input-wrapper">
              <div class="input-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="16" r="1" fill="currentColor"/>
                  <path d="M7 11V7A5 5 0 0 1 17 7V11" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <input
                v-model="loginForm.password"
                :type="showPassword ? 'text' : 'password'"
                :placeholder="t('login.passwordPlaceholder')"
                class="form-input"
                :class="{ 'error': passwordError }"
                @blur="validatePassword"
                @input="clearPasswordError"
              />
              <button
                type="button"
                class="password-toggle"
                @click="togglePassword"
              >
                <svg v-if="showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20C5 20 1 12 1 12A18.45 18.45 0 0 1 5.06 5.06L17.94 17.94Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4C19 4 23 12 23 12A18.5 18.5 0 0 1 19.1 19.76L9.9 4.24Z" stroke="currentColor" stroke-width="2"/>
                  <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
            </div>
            <div v-if="passwordError" class="error-message">{{ passwordError }}</div>
          </div>

          <!-- Login Button -->
          <button
            type="submit"
            class="login-btn"
            :class="{ 'loading': loading }"
            :disabled="loading"
          >
            <div v-if="loading" class="loading-spinner"></div>
            <span v-else>{{ t('navigation.login') }}</span>
          </button>
        </form>

        <!-- Footer Links -->
        <div class="login-footer">
          <p class="register-link">
            {{ t('login.noAccount') }}
            <router-link to="/mobile/register" class="link">{{ t('login.registerNow') }}</router-link>
          </p>
        </div>
      </div>
    </div>
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileLoading from '@/components/common/MobileLoading.vue'
import MobileError from '@/components/common/MobileError.vue'

const router = useRouter()
const { t } = useI18n()
const userStore = useUserStore()

// 状态管理
const loading = ref(false)
const isLoading = ref(false)
const hasError = ref(false)
const errorTitle = ref('')
const errorMessage = ref('')
const showPassword = ref(false)
const phoneError = ref('')
const passwordError = ref('')

const loginForm = reactive({
  phone: '',
  password: ''
})

// 验证手机号
const validatePhone = () => {
  if (!loginForm.phone) {
    phoneError.value = t('login.phoneRequired')
    return false
  }
  if (!/^1[3-9]\d{9}$/.test(loginForm.phone)) {
    phoneError.value = t('login.phoneInvalid')
    return false
  }
  phoneError.value = ''
  return true
}

// 验证密码
const validatePassword = () => {
  if (!loginForm.password) {
    passwordError.value = t('login.passwordRequired')
    return false
  }
  if (loginForm.password.length < 6) {
    passwordError.value = t('login.passwordMinLength')
    return false
  }
  passwordError.value = ''
  return true
}

// 清除错误信息
const clearPhoneError = () => {
  if (phoneError.value) phoneError.value = ''
}

const clearPasswordError = () => {
  if (passwordError.value) passwordError.value = ''
}

// 切换密码显示
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// 返回上一页
const goBack = () => {
  router.back()
}

// 处理登录
const handleLogin = async () => {
  const isPhoneValid = validatePhone()
  const isPasswordValid = validatePassword()
  
  if (!isPhoneValid || !isPasswordValid) {
    return
  }
  
  try {
    loading.value = true
    isLoading.value = true
    hasError.value = false
    
    await userStore.login(loginForm.phone, loginForm.password)
    ElMessage.success('登录成功')
    router.push('/mobile')
  } catch (error: any) {
    console.error('登录失败:', error)
    
    // 设置错误状态
    hasError.value = true
    errorTitle.value = '登录失败'
    
    if (error.response?.status === 401) {
      errorMessage.value = '手机号或密码错误，请检查后重试'
    } else if (error.response?.status === 429) {
      errorMessage.value = '登录尝试过于频繁，请稍后再试'
    } else if (error.code === 'NETWORK_ERROR') {
      errorMessage.value = '网络连接失败，请检查网络后重试'
    } else {
      errorMessage.value = error.message || '登录失败，请重试'
    }
    
    ElMessage.error(errorMessage.value)
  } finally {
    loading.value = false
    isLoading.value = false
  }
}

// 重试登录
const handleRetry = () => {
  hasError.value = false
  handleLogin()
}
</script>

<style scoped lang="scss">
.mobile-login {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 80px; // 为底部导航栏留出空间
}

.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #000;
  border-bottom: 1px solid #333;
  position: sticky;
  top: 0;
  z-index: 100;

  .back-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }

    &:active {
      background: #555;
    }
  }

  .header-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }

  .header-spacer {
    width: 40px;
  }
}

.login-content {
  padding: 20px;
  max-width: 400px;
  margin: 0 auto;
}

.logo-section {
  text-align: center;
  margin-bottom: 40px;

  .logo-container {
    margin-bottom: 20px;

    .logo {
      width: 80px;
      height: 80px;
      border-radius: 16px;
      object-fit: cover;
    }
  }

  .welcome-text {
    font-size: 16px;
    color: #ccc;
    margin: 0;
    font-weight: 400;
  }
}

.form-container {
  .login-form {
    .input-group {
      margin-bottom: 24px;

      .input-label {
        font-size: 14px;
        color: #fff;
        margin-bottom: 8px;
        font-weight: 500;
      }

      .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 0 16px;
        transition: border-color 0.2s;

        &:focus-within {
          border-color: #ff0050;
        }

        .input-icon {
          color: #666;
          margin-right: 12px;
          display: flex;
          align-items: center;
        }

        .form-input {
          flex: 1;
          background: none;
          border: none;
          color: #fff;
          font-size: 16px;
          padding: 16px 0;
          outline: none;

          &::placeholder {
            color: #666;
          }

          &.error {
            color: #ff0050;
          }
        }

        .password-toggle {
          background: none;
          border: none;
          color: #666;
          cursor: pointer;
          padding: 4px;
          border-radius: 4px;
          transition: color 0.2s;

          &:hover {
            color: #fff;
          }
        }

        &.error {
          border-color: #ff0050;
        }
      }

      .error-message {
        color: #ff0050;
        font-size: 12px;
        margin-top: 4px;
        padding-left: 4px;
      }
    }

    .login-btn {
      width: 100%;
      background: #ff0050;
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 16px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 52px;

      &:hover:not(:disabled) {
        background: #e6004a;
        transform: translateY(-1px);
      }

      &:active:not(:disabled) {
        transform: translateY(0);
      }

      &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }

      &.loading {
        background: #333;
      }

      .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid #fff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }
    }
  }
}

.login-footer {
  text-align: center;
  margin-top: 32px;

  .register-link {
    color: #ccc;
    font-size: 14px;
    margin: 0;

    .link {
      color: #ff0050;
      text-decoration: none;
      font-weight: 500;

      &:hover {
        text-decoration: underline;
      }
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// 响应式设计
@media (max-width: 480px) {
  .login-content {
    padding: 16px;
  }

  .logo-section {
    margin-bottom: 32px;

    .logo-container .logo {
      width: 64px;
      height: 64px;
    }
  }

  .form-container .login-form .input-group {
    margin-bottom: 20px;

    .input-wrapper {
      padding: 0 12px;

      .form-input {
        padding: 14px 0;
        font-size: 15px;
      }
    }
  }
}

// 全局移动端样式
:global(body) {
  background: #000;
  color: #fff;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}
</style>
