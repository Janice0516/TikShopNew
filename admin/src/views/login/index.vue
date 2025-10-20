<template>
  <div class="login-container">
    <!-- 语言切换 -->
    <div class="language-switcher">
      <el-dropdown @command="handleLanguageChange">
        <span class="language-selector">
          <el-icon><LanguageIcon /></el-icon>
          <span>{{ currentLanguage }}</span>
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item command="zh-CN">中文</el-dropdown-item>
            <el-dropdown-item command="en-US">English</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
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
        {{ $t('login.login') }}
      </el-button>
    </el-form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { Setting as LanguageIcon } from '@element-plus/icons-vue'
import { useUserStore } from '@/stores/user'
import { testConnection } from '@/api/user'
import { setLocale } from '@/i18n'

const router = useRouter()
const userStore = useUserStore()
const { t, locale } = useI18n()

const loginForm = reactive({
  username: '',
  password: ''
})

const loginRules: FormRules = {
  username: [{ required: true, message: t('login.usernamePlaceholder'), trigger: 'blur' }],
  password: [{ required: true, message: t('login.passwordPlaceholder'), trigger: 'blur' }]
}

// 当前语言显示
const currentLanguage = computed(() => {
  return locale.value === 'zh-CN' ? '中文' : 'English'
})

// 处理语言切换
const handleLanguageChange = (lang: 'zh-CN' | 'en-US') => {
  setLocale(lang)
  ElMessage.success(t('common.success'))
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
        ElMessage.success(t('login.loginSuccess'))
        // 使用replace而不是push，避免返回按钮问题
        await router.replace('/')
      } catch (error: any) {
        console.error('登录错误:', error)
        if (error.code === 'ECONNABORTED') {
          ElMessage.error('登录超时，请检查网络连接或稍后重试')
        } else if (error.message?.includes('timeout')) {
          ElMessage.error('请求超时，请检查网络连接')
        } else {
          ElMessage.error(error.message || t('login.loginFailed'))
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
  position: relative;
}

.language-switcher {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 1000;
}

.language-selector {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 8px 12px;
  border-radius: 4px;
  background-color: rgba(255, 255, 255, 0.9);
  color: #606266;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.language-selector:hover {
  background-color: rgba(255, 255, 255, 1);
  color: #409eff;
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

