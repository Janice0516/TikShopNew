<template>
  <view class="register-page">
    <!-- 背景装饰 -->
    <view class="bg-decoration">
      <view class="circle circle-1"></view>
      <view class="circle circle-2"></view>
      <view class="circle circle-3"></view>
    </view>

    <!-- 注册表单 -->
    <view class="register-container">
      <!-- 标题 -->
      <view class="header">
        <text class="title">{{ $t('register.title') }}</text>
        <text class="subtitle">{{ $t('register.subtitle') }}</text>
      </view>

      <!-- 表单 -->
      <view class="form">
        <!-- 手机号 -->
        <view class="form-item">
          <view class="input-wrapper">
            <uni-icons type="phone" size="20" color="#999"></uni-icons>
            <input 
              v-model="form.phone"
              type="number"
              :placeholder="$t('register.phonePlaceholder')"
              class="input"
              maxlength="11"
            />
          </view>
        </view>

        <!-- 密码 -->
        <view class="form-item">
          <view class="input-wrapper">
            <uni-icons type="locked" size="20" color="#999"></uni-icons>
            <input 
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              :placeholder="$t('register.passwordPlaceholder')"
              class="input"
            />
            <uni-icons 
              :type="showPassword ? 'eye-slash' : 'eye'"
              size="20" 
              color="#999"
              @click="togglePassword"
            ></uni-icons>
          </view>
        </view>

        <!-- 确认密码 -->
        <view class="form-item">
          <view class="input-wrapper">
            <uni-icons type="locked" size="20" color="#999"></uni-icons>
            <input 
              v-model="form.confirmPassword"
              :type="showConfirmPassword ? 'text' : 'password'"
              :placeholder="$t('register.confirmPasswordPlaceholder')"
              class="input"
            />
            <uni-icons 
              :type="showConfirmPassword ? 'eye-slash' : 'eye'"
              size="20" 
              color="#999"
              @click="toggleConfirmPassword"
            ></uni-icons>
          </view>
        </view>

        <!-- 验证码 -->
        <view class="form-item">
          <view class="input-wrapper">
            <uni-icons type="chat" size="20" color="#999"></uni-icons>
            <input 
              v-model="form.verifyCode"
              type="number"
              :placeholder="$t('register.verifyCodePlaceholder')"
              class="input"
              maxlength="6"
            />
            <button 
              class="code-btn"
              :disabled="codeCountdown > 0"
              @click="getVerifyCode"
            >
              {{ codeCountdown > 0 ? `${codeCountdown}s` : $t('register.getCode') }}
            </button>
          </view>
        </view>

        <!-- 协议同意 -->
        <view class="agreement">
          <checkbox 
            v-model="agreeToTerms"
            color="#007aff"
          />
          <text class="agreement-text">
            {{ $t('register.agreeTo') }}
            <text class="link" @click="showUserAgreement">{{ $t('register.userAgreement') }}</text>
            {{ $t('register.and') }}
            <text class="link" @click="showPrivacyPolicy">{{ $t('register.privacyPolicy') }}</text>
          </text>
        </view>

        <!-- 注册按钮 -->
        <button 
          class="register-btn"
          :disabled="!canRegister"
          :class="{ disabled: !canRegister }"
          @click="handleRegister"
        >
          {{ loading ? $t('common.loading') : $t('register.registerBtn') }}
        </button>

        <!-- 登录链接 -->
        <view class="login-link">
          <text class="login-text">{{ $t('register.hasAccount') }}</text>
          <text class="login-btn" @click="goToLogin">{{ $t('register.loginNow') }}</text>
        </view>
      </view>
    </view>

    <!-- 语言切换器 -->
    <view class="language-switcher" @click="toggleLanguage">
      <uni-icons type="globe" size="20" color="#fff"></uni-icons>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

// 响应式数据
const form = ref({
  phone: '',
  password: '',
  confirmPassword: '',
  verifyCode: ''
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const agreeToTerms = ref(false)
const loading = ref(false)
const codeCountdown = ref(0)

// 计算属性
const canRegister = computed(() => {
  return form.value.phone.length === 11 &&
         form.value.password.length >= 6 &&
         form.value.password === form.value.confirmPassword &&
         form.value.verifyCode.length === 6 &&
         agreeToTerms.value &&
         !loading.value
})

// 方法
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPassword = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}

const getVerifyCode = async () => {
  if (form.value.phone.length !== 11) {
    uni.showToast({
      title: t('register.phoneRequired'),
      icon: 'none'
    })
    return
  }

  try {
    // 使用真实API调用
    const { sendCode } = await import('@/api/index')
    
    const result = await sendCode(form.value.phone)
    
    if (result.code === 200) {
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
    } else {
      throw new Error(result.message || '发送验证码失败')
    }
  } catch (error) {
    console.error('发送验证码失败:', error)
    uni.showToast({
      title: error.message || '发送验证码失败',
      icon: 'none'
    })
  }
}

const handleRegister = async () => {
  if (!canRegister.value) return

  loading.value = true

  try {
    // 使用真实API调用
    const { register } = await import('@/api/index')
    
    const result = await register({
      phone: form.value.phone,
      password: form.value.password,
      code: form.value.verifyCode
    })

    if (result.code === 200) {
      // 保存token和用户信息
      uni.setStorageSync('token', result.data.token)
      uni.setStorageSync('userInfo', result.data.userInfo)

      uni.showToast({
        title: t('message.registerSuccess'),
        icon: 'success'
      })

      // 注册成功后跳转到首页
      setTimeout(() => {
        uni.switchTab({
          url: '/pages/index/index'
        })
      }, 1500)
    } else {
      throw new Error(result.message || '注册失败')
    }

  } catch (error) {
    console.error('注册失败:', error)
    uni.showToast({
      title: error.message || t('message.registerFailed'),
      icon: 'none'
    })
  } finally {
    loading.value = false
  }
}

const goToLogin = () => {
  uni.navigateTo({
    url: '/pages/login/login'
  })
}

const showUserAgreement = () => {
  uni.showModal({
    title: t('register.userAgreement'),
    content: 'This is the user agreement content...',
    showCancel: false
  })
}

const showPrivacyPolicy = () => {
  uni.showModal({
    title: t('register.privacyPolicy'),
    content: 'This is the privacy policy content...',
    showCancel: false
  })
}

const toggleLanguage = () => {
  const languages = ['en', 'zh', 'ms']
  const currentIndex = languages.indexOf(locale.value)
  const nextIndex = (currentIndex + 1) % languages.length
  locale.value = languages[nextIndex]
  
  // 保存语言偏好
  uni.setStorageSync('language', languages[nextIndex])
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
}

/* 背景装饰 */
.bg-decoration {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
}

.circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
}

.circle-1 {
  width: 200rpx;
  height: 200rpx;
  top: 10%;
  right: -50rpx;
}

.circle-2 {
  width: 150rpx;
  height: 150rpx;
  top: 60%;
  left: -30rpx;
}

.circle-3 {
  width: 100rpx;
  height: 100rpx;
  top: 30%;
  left: 20%;
}

/* 注册容器 */
.register-container {
  position: relative;
  z-index: 1;
  padding: 100rpx 60rpx 60rpx;
}

/* 标题 */
.header {
  text-align: center;
  margin-bottom: 80rpx;
}

.title {
  display: block;
  font-size: 48rpx;
  font-weight: bold;
  color: #fff;
  margin-bottom: 20rpx;
}

.subtitle {
  display: block;
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.8);
}

/* 表单 */
.form {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 30rpx;
  padding: 60rpx 40rpx;
  backdrop-filter: blur(10rpx);
}

.form-item {
  margin-bottom: 40rpx;
}

.input-wrapper {
  display: flex;
  align-items: center;
  background: #f8f9fa;
  border-radius: 25rpx;
  padding: 25rpx 30rpx;
  border: 2rpx solid transparent;
  transition: all 0.3s;
}

.input-wrapper:focus-within {
  border-color: #007aff;
  background: #fff;
}

.input {
  flex: 1;
  margin: 0 20rpx;
  font-size: 28rpx;
  color: #333;
}

.code-btn {
  background: #007aff;
  color: #fff;
  border: none;
  border-radius: 20rpx;
  padding: 15rpx 25rpx;
  font-size: 24rpx;
  min-width: 120rpx;
}

.code-btn:disabled {
  background: #ccc;
}

/* 协议同意 */
.agreement {
  display: flex;
  align-items: flex-start;
  margin-bottom: 40rpx;
}

.agreement-text {
  font-size: 24rpx;
  color: #666;
  line-height: 1.5;
  margin-left: 15rpx;
}

.link {
  color: #007aff;
}

/* 注册按钮 */
.register-btn {
  width: 100%;
  background: linear-gradient(135deg, #007aff, #0056cc);
  color: #fff;
  border: none;
  border-radius: 25rpx;
  padding: 30rpx;
  font-size: 32rpx;
  font-weight: bold;
  margin-bottom: 40rpx;
  transition: all 0.3s;
}

.register-btn:not(.disabled):active {
  transform: scale(0.98);
}

.register-btn.disabled {
  background: #ccc;
}

/* 登录链接 */
.login-link {
  text-align: center;
}

.login-text {
  font-size: 28rpx;
  color: #666;
}

.login-btn {
  font-size: 28rpx;
  color: #007aff;
  margin-left: 10rpx;
}

/* 语言切换器 */
.language-switcher {
  position: fixed;
  right: 30rpx;
  bottom: 30rpx;
  width: 80rpx;
  height: 80rpx;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10rpx);
  z-index: 999;
}

.language-switcher:active {
  transform: scale(0.9);
}
</style>
