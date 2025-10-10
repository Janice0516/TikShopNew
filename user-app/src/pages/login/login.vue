<template>
  <view class="login">
    <view class="login-container">
      <!-- Logo和标题 -->
      <view class="header">
        <image src="/static/logo.png" class="logo" />
        <text class="title">{{ $t('login.title') }}</text>
        <text class="subtitle">{{ $t('login.subtitle') }}</text>
      </view>

      <!-- 登录表单 -->
      <view class="form-container">
        <view class="form-group">
          <view class="input-wrapper">
            <uni-icons type="phone" size="20" color="#999"></uni-icons>
            <input 
              v-model="loginForm.phone"
              class="form-input"
              type="number"
              :placeholder="$t('login.phonePlaceholder')"
              maxlength="11"
            />
          </view>
        </view>

        <view class="form-group">
          <view class="input-wrapper">
            <uni-icons type="locked" size="20" color="#999"></uni-icons>
            <input 
              v-model="loginForm.password"
              class="form-input"
              :type="showPassword ? 'text' : 'password'"
              :placeholder="$t('login.passwordPlaceholder')"
            />
            <uni-icons 
              :type="showPassword ? 'eye-slash' : 'eye'" 
              size="20" 
              color="#999"
              @click="togglePassword"
            ></uni-icons>
          </view>
        </view>

        <!-- 验证码登录 -->
        <view class="form-group" v-if="loginType === 'code'">
          <view class="input-wrapper">
            <uni-icons type="chat" size="20" color="#999"></uni-icons>
            <input 
              v-model="loginForm.verifyCode"
              class="form-input"
              type="number"
              :placeholder="$t('login.verifyCode')"
              maxlength="6"
            />
            <view 
              class="code-btn" 
              :class="{ disabled: codeCountdown > 0 }"
              @click="sendVerifyCode"
            >
              {{ codeCountdown > 0 ? `${codeCountdown}s` : $t('login.getCode') }}
            </view>
          </view>
        </view>

        <!-- 登录方式切换 -->
        <view class="login-type-switch">
          <text 
            class="switch-text" 
            :class="{ active: loginType === 'password' }"
            @click="switchLoginType('password')"
          >
            {{ $t('login.passwordLogin') }}
          </text>
          <text class="divider">|</text>
          <text 
            class="switch-text" 
            :class="{ active: loginType === 'code' }"
            @click="switchLoginType('code')"
          >
            {{ $t('login.codeLogin') }}
          </text>
        </view>

        <!-- 记住密码和忘记密码 -->
        <view class="form-options">
          <view class="remember-me" @click="toggleRememberMe">
            <uni-icons 
              :type="rememberMe ? 'checkbox-filled' : 'checkbox'" 
              :color="rememberMe ? '#409EFF' : '#999'"
              size="16"
            ></uni-icons>
            <text class="remember-text">{{ $t('login.rememberMe') }}</text>
          </view>
          <text class="forgot-password" @click="goToForgotPassword">
            {{ $t('login.forgotPassword') }}
          </text>
        </view>

        <!-- 登录按钮 -->
        <view 
          class="login-btn" 
          :class="{ disabled: !canLogin }"
          @click="handleLogin"
        >
          {{ $t('login.loginBtn') }}
        </view>

        <!-- 第三方登录 -->
        <view class="third-party-login">
          <view class="divider-line">
            <text class="divider-text">{{ $t('login.orLoginWith') }}</text>
          </view>
          
          <view class="third-party-buttons">
            <view class="third-party-btn" @click="thirdPartyLogin('wechat')">
              <image src="/static/wechat.png" class="third-party-icon" />
              <text class="third-party-text">WeChat</text>
            </view>
            <view class="third-party-btn" @click="thirdPartyLogin('google')">
              <image src="/static/google.png" class="third-party-icon" />
              <text class="third-party-text">Google</text>
            </view>
            <view class="third-party-btn" @click="thirdPartyLogin('facebook')">
              <image src="/static/facebook.png" class="third-party-icon" />
              <text class="third-party-text">Facebook</text>
            </view>
          </view>
        </view>

        <!-- 注册提示 -->
        <view class="register-tip">
          <text class="tip-text">{{ $t('login.noAccount') }}</text>
          <text class="register-link" @click="goToRegister">
            {{ $t('login.registerNow') }}
          </text>
        </view>
      </view>
    </view>

    <!-- 加载遮罩 -->
    <view class="loading-mask" v-if="loading">
      <view class="loading-content">
        <uni-icons type="spinner-cycle" size="24" color="#409EFF"></uni-icons>
        <text class="loading-text">{{ $t('common.loading') }}</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const loading = ref(false)
const loginType = ref('password') // password | code
const showPassword = ref(false)
const rememberMe = ref(false)
const codeCountdown = ref(0)

const loginForm = ref({
  phone: '',
  password: '',
  verifyCode: ''
})

// 计算属性
const canLogin = computed(() => {
  if (loginType.value === 'password') {
    return loginForm.value.phone && loginForm.value.password
  } else {
    return loginForm.value.phone && loginForm.value.verifyCode
  }
})

// 切换密码显示
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// 切换登录方式
const switchLoginType = (type: string) => {
  loginType.value = type
  // 清空表单
  loginForm.value.password = ''
  loginForm.value.verifyCode = ''
}

// 切换记住密码
const toggleRememberMe = () => {
  rememberMe.value = !rememberMe.value
}

// 发送验证码
const sendVerifyCode = async () => {
  if (codeCountdown.value > 0) return
  
  if (!loginForm.value.phone) {
    uni.showToast({
      title: t('login.phoneRequired'),
      icon: 'none'
    })
    return
  }

  try {
    // 实际API调用
    // await sendVerifyCodeAPI(loginForm.value.phone)
    
    // 开始倒计时
    codeCountdown.value = 60
    const timer = setInterval(() => {
      codeCountdown.value--
      if (codeCountdown.value <= 0) {
        clearInterval(timer)
      }
    }, 1000)
    
    uni.showToast({
      title: t('message.codeSent'),
      icon: 'success'
    })
    
  } catch (error) {
    console.error('Failed to send verify code:', error)
    uni.showToast({
      title: t('message.operationFailed'),
      icon: 'error'
    })
  }
}

// 处理登录
const handleLogin = async () => {
  if (!canLogin.value) {
    uni.showToast({
      title: t('login.fillRequired'),
      icon: 'none'
    })
    return
  }

  loading.value = true
  
  try {
    const loginData = {
      phone: loginForm.value.phone,
      password: loginForm.value.password,
      verifyCode: loginForm.value.verifyCode,
      loginType: loginType.value,
      rememberMe: rememberMe.value
    }

    // 实际API调用
    // const res = await loginAPI(loginData)
    
    // 模拟登录成功
    const mockUser = {
      id: 1,
      name: 'John Smith',
      phone: loginForm.value.phone,
      avatar: '/static/avatar.jpg',
      token: 'mock_token_' + Date.now()
    }
    
    // 保存用户信息
    uni.setStorageSync('userInfo', mockUser)
    uni.setStorageSync('token', mockUser.token)
    
    uni.showToast({
      title: t('message.loginSuccess'),
      icon: 'success'
    })
    
    // 跳转到首页
    setTimeout(() => {
      uni.switchTab({
        url: '/pages/index/index'
      })
    }, 1500)
    
  } catch (error) {
    console.error('Login failed:', error)
    uni.showToast({
      title: t('message.loginFailed'),
      icon: 'error'
    })
  } finally {
    loading.value = false
  }
}

// 第三方登录
const thirdPartyLogin = (platform: string) => {
  uni.showToast({
    title: `${platform} login coming soon`,
    icon: 'none'
  })
}

// 跳转到注册页
const goToRegister = () => {
  uni.navigateTo({
    url: '/pages/register/register'
  })
}

// 跳转到忘记密码
const goToForgotPassword = () => {
  uni.navigateTo({
    url: '/pages/forgot-password/forgot-password'
  })
}

onMounted(() => {
  // 检查是否已登录
  const userInfo = uni.getStorageSync('userInfo')
  if (userInfo) {
    uni.switchTab({
      url: '/pages/index/index'
    })
  }
})
</script>

<style scoped>
.login {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.login-container {
  background-color: #fff;
  border-radius: 20px;
  padding: 40px 30px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* 头部 */
.header {
  text-align: center;
  margin-bottom: 40px;
}

.logo {
  width: 80px;
  height: 80px;
  margin-bottom: 20px;
}

.title {
  display: block;
  font-size: 28px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.subtitle {
  display: block;
  font-size: 14px;
  color: #666;
}

/* 表单容器 */
.form-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input-wrapper {
  display: flex;
  align-items: center;
  background-color: #f8f9fa;
  border-radius: 12px;
  padding: 0 15px;
  height: 50px;
  border: 1px solid #e9ecef;
}

.input-wrapper:focus-within {
  border-color: #409EFF;
  background-color: #fff;
}

.form-input {
  flex: 1;
  height: 100%;
  font-size: 16px;
  color: #333;
  background-color: transparent;
  border: none;
  outline: none;
}

.code-btn {
  background-color: #409EFF;
  color: #fff;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  white-space: nowrap;
}

.code-btn.disabled {
  background-color: #ccc;
}

/* 登录方式切换 */
.login-type-switch {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  margin: 10px 0;
}

.switch-text {
  font-size: 14px;
  color: #666;
  padding: 5px 10px;
  border-radius: 6px;
}

.switch-text.active {
  color: #409EFF;
  background-color: #e6f7ff;
}

.divider {
  color: #ddd;
}

/* 表单选项 */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 0;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 8px;
}

.remember-text {
  font-size: 14px;
  color: #666;
}

.forgot-password {
  font-size: 14px;
  color: #409EFF;
}

/* 登录按钮 */
.login-btn {
  background-color: #409EFF;
  color: #fff;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: bold;
  margin: 20px 0;
}

.login-btn.disabled {
  background-color: #ccc;
}

/* 第三方登录 */
.third-party-login {
  margin: 30px 0 20px;
}

.divider-line {
  position: relative;
  text-align: center;
  margin-bottom: 20px;
}

.divider-line::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #e9ecef;
}

.divider-text {
  background-color: #fff;
  padding: 0 15px;
  font-size: 14px;
  color: #999;
}

.third-party-buttons {
  display: flex;
  justify-content: space-around;
  gap: 15px;
}

.third-party-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 15px;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  background-color: #fafafa;
}

.third-party-icon {
  width: 24px;
  height: 24px;
}

.third-party-text {
  font-size: 12px;
  color: #666;
}

/* 注册提示 */
.register-tip {
  text-align: center;
  margin-top: 20px;
}

.tip-text {
  font-size: 14px;
  color: #666;
}

.register-link {
  font-size: 14px;
  color: #409EFF;
  margin-left: 5px;
}

/* 加载遮罩 */
.loading-mask {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.loading-text {
  font-size: 14px;
  color: #666;
}
</style>
