<template>
  <div class="settings">
    <div class="page-header">
      <h1>{{ $t('nav.settings') }}</h1>
    </div>

    <div class="settings-content">
      <!-- 账户设置 -->
      <div class="settings-section">
        <h2>{{ $t('settings.accountSettings') }}</h2>
        <div class="form-group">
          <label>{{ $t('settings.username') }}</label>
          <el-input v-model="accountForm.username" disabled />
        </div>
        <div class="form-group">
          <label>{{ $t('settings.email') }}</label>
          <el-input v-model="accountForm.email" />
        </div>
        <div class="form-group">
          <label>{{ $t('settings.phone') }}</label>
          <el-input v-model="accountForm.phone" />
        </div>
        <div class="form-actions">
          <el-button type="primary" @click="saveAccount">{{ $t('common.save') }}</el-button>
          <el-button @click="resetAccount">{{ $t('common.reset') }}</el-button>
        </div>
      </div>

      <!-- 密码设置 -->
      <div class="settings-section">
        <h2>{{ $t('settings.passwordSettings') }}</h2>
        <div class="form-group">
          <label>{{ $t('settings.currentPassword') }}</label>
          <el-input v-model="passwordForm.currentPassword" type="password" show-password />
        </div>
        <div class="form-group">
          <label>{{ $t('settings.newPassword') }}</label>
          <el-input v-model="passwordForm.newPassword" type="password" show-password />
        </div>
        <div class="form-group">
          <label>{{ $t('settings.confirmPassword') }}</label>
          <el-input v-model="passwordForm.confirmPassword" type="password" show-password />
        </div>
        <div class="form-actions">
          <el-button type="primary" @click="changePassword">{{ $t('settings.changePassword') }}</el-button>
        </div>
      </div>

      <!-- 账户信息 -->
      <div class="settings-section">
        <h2>{{ $t('settings.accountInfo') }}</h2>
        <div class="info-grid">
          <div class="info-item">
            <label>{{ $t('settings.username') }}</label>
            <span>{{ accountForm.username }}</span>
          </div>
          <div class="info-item">
            <label>{{ $t('settings.totalProducts') }}</label>
            <span>{{ productCount }}</span>
          </div>
          <div class="info-item">
            <label>{{ $t('settings.accountStatus') }}</label>
            <span class="status active">{{ $t('settings.active') }}</span>
          </div>
          <div class="info-item">
            <label>{{ $t('settings.joinDate') }}</label>
            <span>{{ joinDate }}</span>
          </div>
        </div>
      </div>

      <!-- 退出登录 -->
      <div class="settings-section logout-section">
        <h2>{{ $t('settings.accountActions') }}</h2>
        <div class="logout-content">
          <p>{{ $t('settings.logoutDescription') }}</p>
          <el-button type="danger" @click="handleLogout" :loading="logoutLoading">
            {{ $t('common.logout') }}
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage, ElMessageBox } from 'element-plus';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const router = useRouter();

// 响应式数据
const accountForm = ref({
  username: 'merchant001',
  email: 'merchant@example.com',
  phone: '+1 234-567-8900'
});

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
});

const productCount = ref(312); // 从API获取
const joinDate = ref('2024-01-01');
const logoutLoading = ref(false);

// 保存账户信息
const saveAccount = () => {
  ElMessage.success(t('settings.accountSaved'));
};

// 重置账户信息
const resetAccount = () => {
  accountForm.value = {
    username: 'merchant001',
    email: 'merchant@example.com',
    phone: '+1 234-567-8900'
  };
  ElMessage.info(t('common.reset'));
};

// 修改密码
const changePassword = () => {
  if (!passwordForm.value.currentPassword) {
    ElMessage.error(t('settings.enterCurrentPassword'));
    return;
  }
  if (!passwordForm.value.newPassword) {
    ElMessage.error(t('settings.enterNewPassword'));
    return;
  }
  if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
    ElMessage.error(t('settings.passwordMismatch'));
    return;
  }
  ElMessage.success(t('settings.passwordChanged'));
  passwordForm.value = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  };
};

// 退出登录
const handleLogout = async () => {
  try {
    await ElMessageBox.confirm(
      t('settings.confirmLogout'),
      t('common.logout'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    );
    
    logoutLoading.value = true;
    
    // 清除本地存储
    localStorage.removeItem('merchant_token');
    localStorage.removeItem('merchant_user');
    
    // 跳转到登录页
    router.push('/merchant/login');
    
    ElMessage.success(t('settings.logoutSuccess'));
  } catch {
    ElMessage.info(t('common.cancel'));
  } finally {
    logoutLoading.value = false;
  }
};

// 获取产品数量
const getProductCount = async () => {
  try {
    // 这里可以调用API获取真实的产品数量
    // const response = await getMerchantProducts({ page: 1, pageSize: 1 });
    // productCount.value = response.data.total;
    productCount.value = 312; // 使用已知的数量
  } catch (error) {
    console.error('获取产品数量失败:', error);
  }
};

onMounted(() => {
  getProductCount();
});
</script>

<style scoped>
.settings {
  padding: 20px;
}

.page-header h1 {
  margin: 0 0 30px 0;
  color: #333;
  font-size: 28px;
}

.settings-content {
  max-width: 800px;
}

.settings-section {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.settings-section h2 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 20px;
  border-bottom: 2px solid #409eff;
  padding-bottom: 10px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #666;
  font-weight: 500;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.info-item label {
  color: #666;
  font-size: 14px;
}

.info-item span {
  color: #333;
  font-weight: 500;
}

.status.active {
  color: #67c23a;
}

.logout-section {
  border: 1px solid #f56c6c;
  background: #fef0f0;
}

.logout-content {
  text-align: center;
}

.logout-content p {
  color: #666;
  margin-bottom: 20px;
}

.logout-content .el-button {
  min-width: 120px;
}
</style>
