<template>
  <div class="mobile-register">
    <div class="register-header">
      <h1>TikTok Shop</h1>
      <p>创建新账户,开始购物之旅</p>
    </div>
    
    <form @submit.prevent="handleRegister" class="register-form">
      <!-- 手机号 -->
      <div class="form-group">
        <div class="input-wrapper">
          <i class="input-icon">📱</i>
          <input
            v-model="formData.phone"
            type="tel"
            placeholder="请输入手机号"
            class="form-input"
            required
          />
        </div>
      </div>

      <!-- 密码 -->
      <div class="form-group">
        <div class="input-wrapper">
          <i class="input-icon">🔒</i>
          <input
            v-model="formData.password"
            type="password"
            placeholder="请输入密码"
            class="form-input"
            required
          />
        </div>
      </div>

      <!-- 确认密码 -->
      <div class="form-group">
        <div class="input-wrapper">
          <i class="input-icon">🔒</i>
          <input
            v-model="formData.confirmPassword"
            type="password"
            placeholder="请确认密码"
            class="form-input"
            required
          />
        </div>
      </div>

      <!-- 注册按钮 -->
      <button type="submit" class="register-btn" :disabled="loading">
        {{ loading ? '注册中...' : '注册' }}
      </button>

      <!-- 登录链接 -->
      <div class="login-link">
        <span>已有账户?</span>
        <router-link to="/mobile/login" class="link">立即登录</router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { userApi } from '@/api'

const router = useRouter()
const loading = ref(false)

const formData = reactive({
  phone: '',
  password: '',
  confirmPassword: ''
})

const handleRegister = async () => {
  // 验证密码
  if (formData.password !== formData.confirmPassword) {
    ElMessage.error('两次输入的密码不一致')
    return
  }

  // 验证手机号格式
  const phoneRegex = /^1[3-9]\d{9}$/
  if (!phoneRegex.test(formData.phone)) {
    ElMessage.error('请输入正确的手机号')
    return
  }

  // 验证密码长度
  if (formData.password.length < 6) {
    ElMessage.error('密码长度不能少于6位')
    return
  }

  try {
    loading.value = true
    
    const response = await userApi.register({
      phone: formData.phone,
      password: formData.password
    })

    if (response) {
      ElMessage.success('注册成功！')
      router.push('/mobile/login')
    }
  } catch (error: any) {
    console.error('注册失败:', error)
    ElMessage.error(error.message || '注册失败，请重试')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.mobile-register {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.register-header {
  text-align: center;
  margin-bottom: 30px;
  color: white;
}

.register-header h1 {
  font-size: 28px;
  font-weight: bold;
  margin: 0 0 10px 0;
}

.register-header p {
  font-size: 14px;
  margin: 0;
  opacity: 0.9;
}

.register-form {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 350px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  position: relative;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 15px;
  font-size: 16px;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 15px 15px 15px 45px;
  border: 2px solid #e1e5e9;
  border-radius: 10px;
  font-size: 16px;
  transition: border-color 0.3s ease;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
}

.register-btn {
  background: #667eea;
  color: white;
  border: none;
  padding: 15px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 10px;
}

.register-btn:hover:not(:disabled) {
  background: #5a6fd8;
}

.register-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  margin-top: 20px;
  color: #666;
  font-size: 14px;
}

.login-link .link {
  color: #667eea;
  text-decoration: none;
  font-weight: bold;
}

.login-link .link:hover {
  text-decoration: underline;
}
</style>
