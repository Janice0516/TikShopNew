<template>
  <div class="login-container">
    <el-form
      ref="loginFormRef"
      :model="loginForm"
      :rules="loginRules"
      class="login-form"
      auto-complete="on"
      label-position="left"
    >
      <div class="title-container">
        <h3 class="title">电商管理后台</h3>
      </div>

      <el-form-item prop="username">
        <el-input
          v-model="loginForm.username"
          placeholder="手机号"
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
          placeholder="密码"
          name="password"
          tabindex="2"
          auto-complete="on"
          prefix-icon="Lock"
          @keyup.enter="handleLogin"
        >
          <template #suffix>
            <el-icon @click="showPassword">
              <View v-if="passwordType === 'password'" />
              <Hide v-else />
            </el-icon>
          </template>
        </el-input>
      </el-form-item>

      <el-button
        :loading="loading"
        type="primary"
        style="width: 100%; margin-bottom: 30px"
        @click="handleLogin"
      >
        登录
      </el-button>

      <div class="tips">
        <span>测试账号：admin</span>
        <span>密码：admin123</span>
      </div>
    </el-form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { useUserStore } from '@/stores/user'
import { testConnection } from '@/api/user'

const router = useRouter()
const userStore = useUserStore()

const loginForm = reactive({
  username: '',
  password: ''
})

const loginRules: FormRules = {
  username: [{ required: true, message: '请输入手机号', trigger: 'blur' }],
  password: [{ required: true, message: '请输入密码', trigger: 'blur' }]
}

const loginFormRef = ref<FormInstance>()
const loading = ref(false)
const passwordType = ref('password')

const showPassword = () => {
  passwordType.value = passwordType.value === 'password' ? 'text' : 'password'
}

// 测试API连接
const testApiConnection = async () => {
  try {
    await testConnection()
    ElMessage.success('API连接正常')
  } catch (error: any) {
    ElMessage.error(`API连接失败: ${error.message}`)
  }
}

const handleLogin = async () => {
  if (!loginFormRef.value) return

  await loginFormRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        // 先测试连接
        await testConnection()
        
        // 执行登录
        const result = await userStore.login(loginForm)
        ElMessage.success('登录成功')
        // 使用replace而不是push，避免返回按钮问题
        await router.replace('/')
      } catch (error: any) {
        console.error('登录错误:', error)
        if (error.code === 'ECONNABORTED') {
          ElMessage.error('登录超时，请检查网络连接或稍后重试')
        } else if (error.message?.includes('timeout')) {
          ElMessage.error('请求超时，请检查网络连接')
        } else {
          ElMessage.error(error.message || '登录失败')
        }
      } finally {
        loading.value = false
      }
    }
  })
}

// 页面加载时测试连接
onMounted(() => {
  testApiConnection()
})
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
}

.login-form {
  position: relative;
  width: 520px;
  max-width: 100%;
  padding: 50px 35px 0;
  margin: 0 auto;
  overflow: hidden;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
}

.title-container {
  position: relative;

  .title {
    font-size: 26px;
    color: #333;
    margin: 0 auto 40px;
    text-align: center;
    font-weight: bold;
  }
}

.tips {
  font-size: 14px;
  color: #999;
  margin-bottom: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  text-align: center;
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

