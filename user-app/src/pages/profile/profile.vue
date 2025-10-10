<template>
  <view class="profile">
    <!-- 用户信息卡片 -->
    <view class="user-card">
      <view class="user-info" v-if="isLoggedIn">
        <image :src="userInfo.avatar || '/static/default-avatar.png'" class="user-avatar" />
        <view class="user-details">
          <text class="user-name">{{ userInfo.name || userInfo.phone }}</text>
          <text class="user-phone">{{ userInfo.phone }}</text>
        </view>
        <view class="edit-profile" @click="editProfile">
          <uni-icons type="compose" size="16" color="#409EFF"></uni-icons>
        </view>
      </view>
      
      <view class="login-prompt" v-else>
        <image src="/static/default-avatar.png" class="default-avatar" />
        <view class="login-info">
          <text class="login-text">{{ $t('profile.welcome') }}</text>
          <view class="login-buttons">
            <view class="login-btn" @click="goToLogin">{{ $t('profile.login') }}</view>
            <view class="register-btn" @click="goToRegister">{{ $t('profile.register') }}</view>
          </view>
        </view>
      </view>
    </view>

    <!-- 订单统计 -->
    <view class="order-stats" v-if="isLoggedIn">
      <view class="stats-item" @click="goToOrders('all')">
        <text class="stats-number">{{ orderStats.total }}</text>
        <text class="stats-label">{{ $t('order.all') }}</text>
      </view>
      <view class="stats-item" @click="goToOrders('pending')">
        <text class="stats-number">{{ orderStats.pending }}</text>
        <text class="stats-label">{{ $t('order.pending') }}</text>
      </view>
      <view class="stats-item" @click="goToOrders('paid')">
        <text class="stats-number">{{ orderStats.paid }}</text>
        <text class="stats-label">{{ $t('order.paid') }}</text>
      </view>
      <view class="stats-item" @click="goToOrders('shipped')">
        <text class="stats-number">{{ orderStats.shipped }}</text>
        <text class="stats-label">{{ $t('order.shipped') }}</text>
      </view>
    </view>

    <!-- 功能菜单 -->
    <view class="menu-section">
      <view class="menu-group">
        <view class="menu-item" @click="goToOrders('all')">
          <uni-icons type="list" size="20" color="#409EFF"></uni-icons>
          <text class="menu-text">{{ $t('profile.myOrders') }}</text>
          <uni-icons type="right" size="16" color="#999"></uni-icons>
        </view>
        
        <view class="menu-item" @click="goToAddress">
          <uni-icons type="location" size="20" color="#67C23A"></uni-icons>
          <text class="menu-text">{{ $t('profile.myAddress') }}</text>
          <uni-icons type="right" size="16" color="#999"></uni-icons>
        </view>
        
        <view class="menu-item" @click="goToFavorites">
          <uni-icons type="heart" size="20" color="#F56C6C"></uni-icons>
          <text class="menu-text">{{ $t('profile.myFavorites') }}</text>
          <uni-icons type="right" size="16" color="#999"></uni-icons>
        </view>
      </view>

      <view class="menu-group">
        <view class="menu-item" @click="goToSettings">
          <uni-icons type="gear" size="20" color="#E6A23C"></uni-icons>
          <text class="menu-text">{{ $t('profile.settings') }}</text>
          <uni-icons type="right" size="16" color="#999"></uni-icons>
        </view>
        
        <view class="menu-item" @click="showLanguageModal = true">
          <uni-icons type="globe" size="20" color="#909399"></uni-icons>
          <text class="menu-text">{{ $t('profile.language') }}</text>
          <view class="menu-right">
            <text class="current-lang">{{ currentLanguage.name }}</text>
            <uni-icons type="right" size="16" color="#999"></uni-icons>
          </view>
        </view>
        
        <view class="menu-item" @click="goToAbout">
          <uni-icons type="info" size="20" color="#909399"></uni-icons>
          <text class="menu-text">{{ $t('profile.aboutUs') }}</text>
          <uni-icons type="right" size="16" color="#999"></uni-icons>
        </view>
      </view>

      <view class="menu-group" v-if="isLoggedIn">
        <view class="menu-item logout-item" @click="handleLogout">
          <uni-icons type="logout" size="20" color="#F56C6C"></uni-icons>
          <text class="menu-text">{{ $t('profile.logout') }}</text>
        </view>
      </view>
    </view>

    <!-- 语言选择弹窗 -->
    <uni-popup ref="languagePopup" type="bottom">
      <view class="language-modal">
        <view class="modal-header">
          <text class="modal-title">{{ $t('profile.language') }}</text>
          <uni-icons type="close" size="20" @click="showLanguageModal = false"></uni-icons>
        </view>
        <view class="language-list">
          <view 
            v-for="lang in languages" 
            :key="lang.code"
            class="language-item"
            :class="{ active: lang.code === currentLanguage.code }"
            @click="changeLanguage(lang.code)"
          >
            <text class="language-flag">{{ lang.flag }}</text>
            <text class="language-name">{{ lang.name }}</text>
            <uni-icons v-if="lang.code === currentLanguage.code" type="checkmarkempty" size="16" color="#409EFF"></uni-icons>
          </view>
        </view>
      </view>
    </uni-popup>

    <!-- 编辑资料弹窗 -->
    <uni-popup ref="profilePopup" type="center">
      <view class="profile-modal">
        <view class="modal-header">
          <text class="modal-title">{{ $t('profile.editProfile') }}</text>
          <uni-icons type="close" size="20" @click="closeProfileModal"></uni-icons>
        </view>
        
        <view class="profile-form">
          <view class="form-item">
            <text class="form-label">{{ $t('profile.nickname') }}</text>
            <input 
              v-model="editForm.nickname"
              class="form-input"
              :placeholder="$t('profile.nicknamePlaceholder')"
            />
          </view>
          
          <view class="form-item">
            <text class="form-label">{{ $t('profile.email') }}</text>
            <input 
              v-model="editForm.email"
              class="form-input"
              :placeholder="$t('profile.emailPlaceholder')"
            />
          </view>
          
          <view class="form-item">
            <text class="form-label">{{ $t('profile.birthday') }}</text>
            <picker mode="date" :value="editForm.birthday" @change="onBirthdayChange">
              <view class="picker-input">
                {{ editForm.birthday || $t('profile.birthdayPlaceholder') }}
              </view>
            </picker>
          </view>
        </view>

        <view class="modal-footer">
          <view class="btn-cancel" @click="closeProfileModal">
            {{ $t('common.cancel') }}
          </view>
          <view class="btn-confirm" @click="saveProfile">
            {{ $t('common.save') }}
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { languages, setLanguage } from '@/locale'

const { t, locale } = useI18n()

const isLoggedIn = ref(true)
const showLanguageModal = ref(false)

const userInfo = ref({
  id: 1,
  name: 'John Smith',
  phone: '+1 234-567-8900',
  email: 'john@example.com',
  avatar: '/static/avatar.jpg',
  birthday: '1990-01-01'
})

const orderStats = ref({
  total: 12,
  pending: 2,
  paid: 3,
  shipped: 5,
  completed: 2
})

const editForm = ref({
  nickname: '',
  email: '',
  birthday: ''
})

// 当前语言
const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === locale.value) || languages[0]
})

// 页面加载
onMounted(() => {
  loadUserData()
})

// 加载用户数据
const loadUserData = async () => {
  try {
    // 模拟数据
    editForm.value = {
      nickname: userInfo.value.name,
      email: userInfo.value.email,
      birthday: userInfo.value.birthday
    }

    // 实际API调用
    // const res = await getUserInfo()
    // userInfo.value = res.userInfo
    // orderStats.value = res.orderStats
    
  } catch (error) {
    console.error('Failed to load user data:', error)
  }
}

// 跳转到登录页
const goToLogin = () => {
  uni.navigateTo({
    url: '/pages/login/login'
  })
}

// 跳转到注册页
const goToRegister = () => {
  uni.navigateTo({
    url: '/pages/register/register'
  })
}

// 跳转到订单列表
const goToOrders = (status: string) => {
  uni.navigateTo({
    url: `/pages/order/list?status=${status}`
  })
}

// 跳转到地址管理
const goToAddress = () => {
  uni.navigateTo({
    url: '/pages/address/list'
  })
}

// 跳转到收藏夹
const goToFavorites = () => {
  uni.navigateTo({
    url: '/pages/favorites/list'
  })
}

// 跳转到设置
const goToSettings = () => {
  uni.navigateTo({
    url: '/pages/settings/index'
  })
}

// 跳转到关于我们
const goToAbout = () => {
  uni.navigateTo({
    url: '/pages/about/index'
  })
}

// 编辑资料
const editProfile = () => {
  uni.$refs.profilePopup?.open()
}

// 关闭资料弹窗
const closeProfileModal = () => {
  uni.$refs.profilePopup?.close()
}

// 生日选择
const onBirthdayChange = (e: any) => {
  editForm.value.birthday = e.detail.value
}

// 保存资料
const saveProfile = async () => {
  try {
    // 实际API调用
    // await updateUserInfo(editForm.value)
    
    userInfo.value.name = editForm.value.nickname
    userInfo.value.email = editForm.value.email
    userInfo.value.birthday = editForm.value.birthday
    
    uni.showToast({
      title: t('message.operationSuccess'),
      icon: 'success'
    })
    
    closeProfileModal()
    
  } catch (error) {
    console.error('Failed to save profile:', error)
    uni.showToast({
      title: t('message.operationFailed'),
      icon: 'error'
    })
  }
}

// 切换语言
const changeLanguage = (langCode: string) => {
  setLanguage(langCode)
  showLanguageModal.value = false
  uni.showToast({
    title: t('message.operationSuccess'),
    icon: 'success'
  })
}

// 退出登录
const handleLogout = () => {
  uni.showModal({
    title: t('common.warning'),
    content: t('profile.confirmLogout'),
    success: (res) => {
      if (res.confirm) {
        // 清除用户数据
        isLoggedIn.value = false
        userInfo.value = {
          id: 0,
          name: '',
          phone: '',
          email: '',
          avatar: '',
          birthday: ''
        }
        
        uni.showToast({
          title: t('message.logoutSuccess'),
          icon: 'success'
        })
      }
    }
  })
}
</script>

<style scoped>
.profile {
  background-color: #f5f5f5;
  min-height: 100vh;
}

/* 用户信息卡片 */
.user-card {
  background-color: #fff;
  margin-bottom: 10px;
}

.user-info {
  display: flex;
  align-items: center;
  padding: 20px;
}

.user-avatar {
  width: 60px;
  height: 60px;
  border-radius: 30px;
  margin-right: 15px;
}

.user-details {
  flex: 1;
}

.user-name {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 5px;
}

.user-phone {
  font-size: 14px;
  color: #666;
}

.edit-profile {
  padding: 8px;
}

.login-prompt {
  display: flex;
  align-items: center;
  padding: 20px;
}

.default-avatar {
  width: 60px;
  height: 60px;
  border-radius: 30px;
  margin-right: 15px;
}

.login-info {
  flex: 1;
}

.login-text {
  font-size: 16px;
  color: #333;
  margin-bottom: 10px;
}

.login-buttons {
  display: flex;
  gap: 10px;
}

.login-btn {
  background-color: #409EFF;
  color: #fff;
  padding: 8px 16px;
  border-radius: 4px;
  font-size: 14px;
}

.register-btn {
  background-color: #67C23A;
  color: #fff;
  padding: 8px 16px;
  border-radius: 4px;
  font-size: 14px;
}

/* 订单统计 */
.order-stats {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 20px;
  display: flex;
  justify-content: space-around;
}

.stats-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px;
}

.stats-number {
  font-size: 20px;
  font-weight: bold;
  color: #409EFF;
  margin-bottom: 5px;
}

.stats-label {
  font-size: 12px;
  color: #666;
}

/* 功能菜单 */
.menu-section {
  background-color: #fff;
}

.menu-group {
  margin-bottom: 10px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #f0f0f0;
}

.menu-item:last-child {
  border-bottom: none;
}

.menu-item.logout-item {
  justify-content: center;
}

.menu-text {
  flex: 1;
  font-size: 16px;
  color: #333;
  margin-left: 15px;
}

.menu-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.current-lang {
  font-size: 14px;
  color: #666;
}

/* 语言选择弹窗 */
.language-modal {
  background-color: #fff;
  border-radius: 12px 12px 0 0;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-title {
  font-size: 18px;
  font-weight: bold;
  color: #333;
}

.language-list {
  padding: 20px;
}

.language-item {
  display: flex;
  align-items: center;
  padding: 15px;
  border-radius: 8px;
  background-color: #f8f9fa;
  margin-bottom: 10px;
}

.language-item.active {
  background-color: #e6f7ff;
}

.language-flag {
  font-size: 20px;
  margin-right: 12px;
}

.language-name {
  flex: 1;
  font-size: 16px;
  color: #333;
}

/* 编辑资料弹窗 */
.profile-modal {
  background-color: #fff;
  border-radius: 12px;
  width: 320px;
  max-height: 80vh;
}

.profile-form {
  padding: 20px;
}

.form-item {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  color: #333;
  margin-bottom: 8px;
}

.form-input {
  width: 100%;
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 0 12px;
  font-size: 14px;
  color: #333;
  background-color: #fafafa;
}

.picker-input {
  width: 100%;
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 0 12px;
  font-size: 14px;
  color: #333;
  background-color: #fafafa;
  display: flex;
  align-items: center;
}

.modal-footer {
  display: flex;
  gap: 10px;
  padding: 20px;
  border-top: 1px solid #eee;
}

.btn-cancel {
  flex: 1;
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: #666;
}

.btn-confirm {
  flex: 1;
  height: 40px;
  background-color: #409EFF;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: #fff;
}
</style>
