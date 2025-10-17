<template>
  <div class="profile-page">
    <div class="container">
      <div class="profile-header">
        <h1>个人中心</h1>
      </div>
      
      <div class="profile-content">
        <!-- 用户信息卡片 -->
        <div class="user-card">
          <div class="user-avatar">
            <el-avatar :size="80" :src="userInfo.avatar">
              {{ userInfo.name?.charAt(0) || 'U' }}
            </el-avatar>
          </div>
          <div class="user-info">
            <h2 class="user-name">{{ userInfo.name || '用户' }}</h2>
            <p class="user-phone">{{ userInfo.phone }}</p>
            <p class="user-email">{{ userInfo.email || '未设置邮箱' }}</p>
          </div>
          <div class="user-actions">
            <el-button type="primary" @click="showEditDialog = true">编辑资料</el-button>
          </div>
        </div>
        
        <!-- 功能菜单 -->
        <div class="menu-grid">
          <div class="menu-item" @click="$router.push('/orders')">
            <div class="menu-icon">
              <el-icon size="24"><Document /></el-icon>
            </div>
            <div class="menu-content">
              <h3>我的订单</h3>
              <p>查看订单状态</p>
            </div>
            <div class="menu-arrow">
              <el-icon><ArrowRight /></el-icon>
            </div>
          </div>
          
          <div class="menu-item" @click="$router.push('/cart')">
            <div class="menu-icon">
              <el-icon size="24"><ShoppingCart /></el-icon>
            </div>
            <div class="menu-content">
              <h3>购物车</h3>
              <p>{{ cartStore.cartCount }}件商品</p>
            </div>
            <div class="menu-arrow">
              <el-icon><ArrowRight /></el-icon>
            </div>
          </div>
          
          <div class="menu-item" @click="showAddressDialog = true">
            <div class="menu-icon">
              <el-icon size="24"><Location /></el-icon>
            </div>
            <div class="menu-content">
              <h3>收货地址</h3>
              <p>管理收货地址</p>
            </div>
            <div class="menu-arrow">
              <el-icon><ArrowRight /></el-icon>
            </div>
          </div>
          
          <div class="menu-item" @click="showSettingsDialog = true">
            <div class="menu-icon">
              <el-icon size="24"><Setting /></el-icon>
            </div>
            <div class="menu-content">
              <h3>设置</h3>
              <p>账户设置</p>
            </div>
            <div class="menu-arrow">
              <el-icon><ArrowRight /></el-icon>
            </div>
          </div>
          
          <div class="menu-item" @click="showAboutDialog = true">
            <div class="menu-icon">
              <el-icon size="24"><InfoFilled /></el-icon>
            </div>
            <div class="menu-content">
              <h3>关于我们</h3>
              <p>了解更多信息</p>
            </div>
            <div class="menu-arrow">
              <el-icon><ArrowRight /></el-icon>
            </div>
          </div>
          
          <div class="menu-item logout" @click="handleLogout">
            <div class="menu-icon">
              <el-icon size="24"><SwitchButton /></el-icon>
            </div>
            <div class="menu-content">
              <h3>退出登录</h3>
              <p>安全退出账户</p>
            </div>
            <div class="menu-arrow">
              <el-icon><ArrowRight /></el-icon>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 编辑资料对话框 -->
    <el-dialog v-model="showEditDialog" title="编辑资料" width="500px">
      <el-form :model="editForm" :rules="editRules" ref="editFormRef" label-width="80px">
        <el-form-item label="姓名" prop="name">
          <el-input v-model="editForm.name" placeholder="请输入姓名" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="editForm.email" placeholder="请输入邮箱" />
        </el-form-item>
        <el-form-item label="头像" prop="avatar">
          <el-input v-model="editForm.avatar" placeholder="请输入头像URL" />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="showEditDialog = false">取消</el-button>
        <el-button type="primary" @click="saveProfile" :loading="saving">保存</el-button>
      </template>
    </el-dialog>
    
    <!-- 收货地址对话框 -->
    <el-dialog v-model="showAddressDialog" title="收货地址" width="600px">
      <div class="address-list">
        <div class="address-item" v-for="address in addresses" :key="address.id">
          <div class="address-info">
            <div class="receiver">
              <span class="name">{{ address.name }}</span>
              <span class="phone">{{ address.phone }}</span>
            </div>
            <div class="address">{{ address.address }}</div>
          </div>
          <div class="address-actions">
            <el-button type="text" size="small">编辑</el-button>
            <el-button type="text" size="small" @click="deleteAddress(address)">删除</el-button>
          </div>
        </div>
        
        <div class="add-address">
          <el-button type="primary" @click="addAddress">添加新地址</el-button>
        </div>
      </div>
      
      <template #footer>
        <el-button @click="showAddressDialog = false">关闭</el-button>
      </template>
    </el-dialog>
    
    <!-- 设置对话框 -->
    <el-dialog v-model="showSettingsDialog" title="设置" width="500px">
      <el-form label-width="100px">
        <el-form-item label="语言">
          <el-select v-model="settings.language" placeholder="选择语言">
            <el-option label="简体中文" value="zh-CN" />
            <el-option label="English" value="en-US" />
          </el-select>
        </el-form-item>
        <el-form-item label="主题">
          <el-select v-model="settings.theme" placeholder="选择主题">
            <el-option label="浅色" value="light" />
            <el-option label="深色" value="dark" />
          </el-select>
        </el-form-item>
        <el-form-item label="通知">
          <el-switch v-model="settings.notifications" />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="showSettingsDialog = false">取消</el-button>
        <el-button type="primary" @click="saveSettings">保存</el-button>
      </template>
    </el-dialog>
    
    <!-- 关于我们对话框 -->
    <el-dialog v-model="showAboutDialog" title="关于我们" width="500px">
      <div class="about-content">
        <div class="app-info">
          <h3>TikTok Shop</h3>
          <p>版本: 1.0.0</p>
          <p>您的购物首选平台</p>
        </div>
        
        <div class="contact-info">
          <h4>联系我们</h4>
          <p>邮箱: support@tikshop.com</p>
          <p>电话: 400-123-4567</p>
        </div>
      </div>
      
      <template #footer>
        <el-button @click="showAboutDialog = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useCartStore } from '@/stores/cart'
import { 
  Document, 
  ShoppingCart, 
  Location, 
  Setting, 
  InfoFilled, 
  SwitchButton,
  ArrowRight
} from '@element-plus/icons-vue'
import { ElMessage, ElMessageBox, type FormInstance } from 'element-plus'

const router = useRouter()
const userStore = useUserStore()
const cartStore = useCartStore()

const showEditDialog = ref(false)
const showAddressDialog = ref(false)
const showSettingsDialog = ref(false)
const showAboutDialog = ref(false)
const saving = ref(false)

const userInfo = ref<any>({})
const addresses = ref<any[]>([])
const editFormRef = ref<FormInstance>()

const editForm = reactive({
  name: '',
  email: '',
  avatar: ''
})

const editRules = {
  name: [
    { required: true, message: '请输入姓名', trigger: 'blur' }
  ],
  email: [
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
  ]
}

const settings = reactive({
  language: 'zh-CN',
  theme: 'light',
  notifications: true
})

// 加载用户信息
const loadUserInfo = async () => {
  try {
    await userStore.fetchUserInfo()
    userInfo.value = userStore.userInfo || {}
    
    // 初始化编辑表单
    editForm.name = userInfo.value.name || ''
    editForm.email = userInfo.value.email || ''
    editForm.avatar = userInfo.value.avatar || ''
  } catch (error) {
    console.error('加载用户信息失败:', error)
    // 使用默认用户信息
    userInfo.value = {
      name: '用户',
      phone: '13800138000',
      email: 'user@example.com',
      avatar: ''
    }
  }
}

// 加载收货地址
const loadAddresses = () => {
  addresses.value = [
    {
      id: '1',
      name: '张三',
      phone: '13800138000',
      address: '北京市朝阳区某某街道某某小区1号楼1单元101室'
    },
    {
      id: '2',
      name: '李四',
      phone: '13900139000',
      address: '上海市浦东新区某某路某某大厦2楼'
    }
  ]
}

// 保存资料
const saveProfile = async () => {
  if (!editFormRef.value) return
  
  try {
    await editFormRef.value.validate()
    saving.value = true
    
    await userStore.updateUserInfo(editForm)
    userInfo.value = { ...userInfo.value, ...editForm }
    
    ElMessage.success('资料更新成功')
    showEditDialog.value = false
  } catch (error) {
    console.error('保存资料失败:', error)
    ElMessage.error('保存失败，请重试')
  } finally {
    saving.value = false
  }
}

// 添加地址
const addAddress = () => {
  ElMessage.info('添加地址功能待开发')
}

// 删除地址
const deleteAddress = (address: any) => {
  const index = addresses.value.findIndex(addr => addr.id === address.id)
  if (index > -1) {
    addresses.value.splice(index, 1)
    ElMessage.success('地址已删除')
  }
}

// 保存设置
const saveSettings = () => {
  ElMessage.success('设置已保存')
  showSettingsDialog.value = false
}

// 退出登录
const handleLogout = async () => {
  try {
    await ElMessageBox.confirm('确定要退出登录吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    userStore.logout()
    ElMessage.success('已退出登录')
    router.push('/')
  } catch (error) {
    // 用户取消
  }
}

onMounted(async () => {
  await Promise.all([
    loadUserInfo(),
    loadAddresses()
  ])
})
</script>

<style scoped lang="scss">
.profile-page {
  padding: 20px 0;
  background: $background-base;
  min-height: 100vh;
}

.profile-header {
  background: #fff;
  padding: 20px 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  
  h1 {
    font-size: 24px;
    font-weight: bold;
    color: $text-primary;
    margin: 0;
  }
}

.profile-content {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 20px;
}

.user-card {
  background: #fff;
  border-radius: 8px;
  padding: 30px;
  text-align: center;
  height: fit-content;
  
  .user-avatar {
    margin-bottom: 20px;
  }
  
  .user-info {
    margin-bottom: 20px;
    
    .user-name {
      font-size: 20px;
      font-weight: bold;
      color: $text-primary;
      margin: 0 0 10px 0;
    }
    
    .user-phone,
    .user-email {
      color: $text-secondary;
      margin: 5px 0;
    }
  }
}

.menu-grid {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 15px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 10px;
  
  &:hover {
    background: $background-base;
  }
  
  &.logout {
    color: $danger-color;
    
    &:hover {
      background: rgba(245, 108, 108, 0.1);
    }
  }
  
  .menu-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: $background-base;
    border-radius: 50%;
    margin-right: 15px;
  }
  
  .menu-content {
    flex: 1;
    
    h3 {
      font-size: 16px;
      font-weight: 500;
      color: $text-primary;
      margin: 0 0 5px 0;
    }
    
    p {
      font-size: 14px;
      color: $text-secondary;
      margin: 0;
    }
  }
  
  .menu-arrow {
    color: $text-secondary;
  }
}

.address-list {
  .address-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 1px solid $border-base;
    border-radius: 8px;
    margin-bottom: 10px;
    
    .address-info {
      .receiver {
        margin-bottom: 5px;
        
        .name {
          font-weight: 500;
          margin-right: 10px;
        }
        
        .phone {
          color: $text-secondary;
        }
      }
      
      .address {
        color: $text-regular;
      }
    }
    
    .address-actions {
      display: flex;
      gap: 10px;
    }
  }
  
  .add-address {
    text-align: center;
    padding: 20px;
    border: 2px dashed $border-base;
    border-radius: 8px;
  }
}

.about-content {
  .app-info {
    text-align: center;
    margin-bottom: 30px;
    
    h3 {
      font-size: 24px;
      font-weight: bold;
      color: $primary-color;
      margin-bottom: 10px;
    }
    
    p {
      color: $text-secondary;
      margin: 5px 0;
    }
  }
  
  .contact-info {
    h4 {
      font-size: 16px;
      font-weight: 500;
      color: $text-primary;
      margin-bottom: 10px;
    }
    
    p {
      color: $text-secondary;
      margin: 5px 0;
    }
  }
}

@media (max-width: 768px) {
  .profile-content {
    grid-template-columns: 1fr;
  }
  
  .user-card {
    padding: 20px;
  }
  
  .menu-item {
    padding: 12px;
    
    .menu-icon {
      width: 35px;
      height: 35px;
      margin-right: 12px;
    }
  }
}
</style>
