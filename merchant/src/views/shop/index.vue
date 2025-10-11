<template>
  <div class="shop-management">
    <!-- 店铺概览 -->
    <el-card class="shop-overview">
      <template #header>
        <div class="card-header">
          <span>{{ $t('shop.shopOverview') }}</span>
          <el-button type="primary" @click="editShopInfo">
            <el-icon><Edit /></el-icon>
            {{ $t('common.edit') }}
          </el-button>
        </div>
      </template>

      <el-row :gutter="20">
        <el-col :span="8">
          <div class="shop-info">
            <div class="shop-logo">
              <el-image
                :src="shopInfo.logo || '/static/default-shop-logo.png'"
                style="width: 80px; height: 80px"
                fit="cover"
              />
            </div>
            <div class="shop-details">
              <h3 class="shop-name">{{ shopInfo.name }}</h3>
              <p class="shop-description">{{ shopInfo.description }}</p>
              <div class="shop-stats">
                <el-tag type="success" v-if="shopInfo.status === 1">{{ $t('shop.open') }}</el-tag>
                <el-tag type="info" v-else>{{ $t('shop.closed') }}</el-tag>
                <span class="shop-rating">
                  <el-icon><Star /></el-icon>
                  {{ shopInfo.rating || '4.8' }}
                </span>
              </div>
            </div>
          </div>
        </el-col>

        <el-col :span="16">
          <el-row :gutter="20">
            <el-col :span="6">
              <div class="stat-item">
                <div class="stat-value">{{ shopStats.totalProducts }}</div>
                <div class="stat-label">{{ $t('shop.totalProducts') }}</div>
              </div>
            </el-col>
            <el-col :span="6">
              <div class="stat-item">
                <div class="stat-value">{{ shopStats.totalOrders }}</div>
                <div class="stat-label">{{ $t('shop.totalOrders') }}</div>
              </div>
            </el-col>
            <el-col :span="6">
              <div class="stat-item">
                <div class="stat-value">${{ shopStats.totalSales }}</div>
                <div class="stat-label">{{ $t('shop.totalSales') }}</div>
              </div>
            </el-col>
            <el-col :span="6">
              <div class="stat-item">
                <div class="stat-value">{{ shopStats.totalCustomers }}</div>
                <div class="stat-label">{{ $t('shop.totalCustomers') }}</div>
              </div>
            </el-col>
          </el-row>
        </el-col>
      </el-row>
    </el-card>

    <!-- 店铺设置 -->
    <el-card>
      <template #header>
        <span>{{ $t('shop.shopSettings') }}</span>
      </template>

      <el-form
        ref="shopFormRef"
        :model="shopForm"
        :rules="shopRules"
        label-width="150px"
        class="shop-form"
      >
        <el-form-item :label="$t('shop.shopName')" prop="name">
          <el-input
            v-model="shopForm.name"
            :placeholder="$t('shop.shopNamePlaceholder')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item :label="$t('shop.shopLogo')" prop="logo">
          <el-upload
            class="logo-uploader"
            :show-file-list="false"
            :on-success="handleLogoSuccess"
            :before-upload="beforeLogoUpload"
            action="#"
          >
            <el-image
              v-if="shopForm.logo"
              :src="shopForm.logo"
              class="logo-preview"
            />
            <el-icon v-else class="logo-uploader-icon"><Plus /></el-icon>
          </el-upload>
          <div class="upload-tip">{{ $t('shop.logoUploadTip') }}</div>
        </el-form-item>

        <el-form-item :label="$t('shop.shopDescription')" prop="description">
          <el-input
            v-model="shopForm.description"
            type="textarea"
            :rows="4"
            :placeholder="$t('shop.descriptionPlaceholder')"
            style="width: 500px"
          />
        </el-form-item>

        <el-form-item :label="$t('shop.contactPhone')" prop="phone">
          <el-input
            v-model="shopForm.phone"
            :placeholder="$t('shop.phonePlaceholder')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item :label="$t('shop.contactEmail')" prop="email">
          <el-input
            v-model="shopForm.email"
            :placeholder="$t('shop.emailPlaceholder')"
            style="width: 300px"
          />
        </el-form-item>

        <el-form-item :label="$t('shop.businessHours')" prop="businessHours">
          <el-time-picker
            v-model="shopForm.businessHours"
            is-range
            :range-separator="$t('common.to')"
            :start-placeholder="$t('shop.startTime')"
            :end-placeholder="$t('shop.endTime')"
            format="HH:mm"
            value-format="HH:mm"
          />
        </el-form-item>

        <el-form-item :label="$t('shop.shopStatus')" prop="status">
          <el-radio-group v-model="shopForm.status">
            <el-radio :label="1">{{ $t('shop.open') }}</el-radio>
            <el-radio :label="0">{{ $t('shop.closed') }}</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="saveShopInfo" :loading="saving">
            {{ $t('common.save') }}
          </el-button>
          <el-button @click="resetForm">
            {{ $t('common.reset') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 店铺公告 -->
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ $t('shop.shopAnnouncement') }}</span>
          <el-button type="primary" @click="editAnnouncement">
            <el-icon><Edit /></el-icon>
            {{ $t('common.edit') }}
          </el-button>
        </div>
      </template>

      <div class="announcement-content">
        <div v-if="shopInfo.announcement" class="announcement-text">
          {{ shopInfo.announcement }}
        </div>
        <div v-else class="no-announcement">
          <el-icon><Bell /></el-icon>
          <span>{{ $t('shop.noAnnouncement') }}</span>
        </div>
      </div>
    </el-card>

    <!-- 店铺装修 -->
    <el-card>
      <template #header>
        <span>{{ $t('shop.shopDecoration') }}</span>
      </template>

      <el-row :gutter="20">
        <el-col :span="8">
          <div class="decoration-item">
            <div class="decoration-preview">
              <el-image
                :src="shopInfo.bannerImage || '/static/default-banner.jpg'"
                style="width: 100%; height: 120px"
                fit="cover"
              />
            </div>
            <div class="decoration-info">
              <h4>{{ $t('shop.bannerImage') }}</h4>
              <p>{{ $t('shop.bannerDescription') }}</p>
              <el-button size="small" @click="editBanner">
                {{ $t('common.edit') }}
              </el-button>
            </div>
          </div>
        </el-col>

        <el-col :span="8">
          <div class="decoration-item">
            <div class="decoration-preview">
              <el-image
                :src="shopInfo.welcomeImage || '/static/default-welcome.jpg'"
                style="width: 100%; height: 120px"
                fit="cover"
              />
            </div>
            <div class="decoration-info">
              <h4>{{ $t('shop.welcomeImage') }}</h4>
              <p>{{ $t('shop.welcomeDescription') }}</p>
              <el-button size="small" @click="editWelcome">
                {{ $t('common.edit') }}
              </el-button>
            </div>
          </div>
        </el-col>

        <el-col :span="8">
          <div class="decoration-item">
            <div class="decoration-preview">
              <el-image
                :src="shopInfo.categoryImage || '/static/default-category.jpg'"
                style="width: 100%; height: 120px"
                fit="cover"
              />
            </div>
            <div class="decoration-info">
              <h4>{{ $t('shop.categoryImage') }}</h4>
              <p>{{ $t('shop.categoryDescription') }}</p>
              <el-button size="small" @click="editCategory">
                {{ $t('common.edit') }}
              </el-button>
            </div>
          </div>
        </el-col>
      </el-row>
    </el-card>

    <!-- 编辑店铺信息对话框 -->
    <el-dialog
      v-model="shopDialogVisible"
      :title="$t('shop.editShopInfo')"
      width="600px"
    >
      <el-form
        ref="editFormRef"
        :model="editForm"
        :rules="editRules"
        label-width="120px"
      >
        <el-form-item :label="$t('shop.shopName')" prop="name">
          <el-input v-model="editForm.name" />
        </el-form-item>

        <el-form-item :label="$t('shop.shopDescription')" prop="description">
          <el-input
            v-model="editForm.description"
            type="textarea"
            :rows="3"
          />
        </el-form-item>

        <el-form-item :label="$t('shop.contactPhone')" prop="phone">
          <el-input v-model="editForm.phone" />
        </el-form-item>

        <el-form-item :label="$t('shop.contactEmail')" prop="email">
          <el-input v-model="editForm.email" />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="shopDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="saveEditForm" :loading="saving">
          {{ $t('common.save') }}
        </el-button>
      </template>
    </el-dialog>

    <!-- 编辑公告对话框 -->
    <el-dialog
      v-model="announcementDialogVisible"
      :title="$t('shop.editAnnouncement')"
      width="600px"
    >
      <el-form
        ref="announcementFormRef"
        :model="announcementForm"
        :rules="announcementRules"
        label-width="120px"
      >
        <el-form-item :label="$t('shop.announcement')" prop="announcement">
          <el-input
            v-model="announcementForm.announcement"
            type="textarea"
            :rows="4"
            :placeholder="$t('shop.announcementPlaceholder')"
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="announcementDialogVisible = false">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button type="primary" @click="saveAnnouncement" :loading="saving">
          {{ $t('common.save') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { getShopInfo, updateShopInfo, updateShopAnnouncement } from '@/api/shop'

const { t } = useI18n()

const saving = ref(false)
const shopDialogVisible = ref(false)
const announcementDialogVisible = ref(false)
const shopFormRef = ref()
const editFormRef = ref()
const announcementFormRef = ref()

const shopInfo = ref({
  id: 0,
  name: '',
  description: '',
  logo: '',
  phone: '',
  email: '',
  status: 1,
  rating: '0.0',
  announcement: '',
  bannerImage: '',
  welcomeImage: '',
  categoryImage: '',
  businessHours: ['09:00', '18:00']
})

const shopStats = ref({
  totalProducts: 0,
  totalOrders: 0,
  totalSales: '0.00',
  totalCustomers: 0
})

const shopForm = reactive({
  name: '',
  logo: '',
  description: '',
  phone: '',
  email: '',
  businessHours: null as any,
  status: 1
})

const editForm = reactive({
  name: '',
  description: '',
  phone: '',
  email: ''
})

const announcementForm = reactive({
  announcement: ''
})

const shopRules = {
  name: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  phone: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  email: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { type: 'email', message: () => t('validation.email'), trigger: 'blur' }
  ]
}

const editRules = {
  name: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  phone: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' }
  ],
  email: [
    { required: true, message: () => t('validation.required'), trigger: 'blur' },
    { type: 'email', message: () => t('validation.email'), trigger: 'blur' }
  ]
}

const announcementRules = {
  announcement: [
    { max: 500, message: () => t('validation.maxLength', { max: 500 }), trigger: 'blur' }
  ]
}

// 加载店铺信息
const loadShopInfo = async () => {
  try {
    const res = await getShopInfo()
    
    if (res.data && res.data.data) {
      shopInfo.value = res.data.data.shopInfo || shopInfo.value
      shopStats.value = res.data.data.shopStats || shopStats.value
      
      // 更新表单数据
      Object.assign(shopForm, {
        name: shopInfo.value.name,
        logo: shopInfo.value.logo,
        description: shopInfo.value.description,
        phone: shopInfo.value.phone,
        email: shopInfo.value.email,
        businessHours: shopInfo.value.businessHours,
        status: shopInfo.value.status
      })
    } else {
      // API返回空数据时保持默认值
      shopInfo.value = {
        id: 0,
        name: '',
        description: '',
        logo: '',
        phone: '',
        email: '',
        status: 1,
        rating: '0.0',
        announcement: '',
        bannerImage: '',
        welcomeImage: '',
        categoryImage: '',
        businessHours: ['09:00', '18:00']
      }
      
      shopStats.value = {
        totalProducts: 0,
        totalOrders: 0,
        totalSales: '0.00',
        totalCustomers: 0
      }
    }
  } catch (error) {
    console.error('Failed to load shop info:', error)
    ElMessage.error(t('message.loadFailed'))
    // 出错时保持默认值
    shopInfo.value = {
      id: 0,
      name: '',
      description: '',
      logo: '',
      phone: '',
      email: '',
      status: 1,
      rating: '0.0',
      announcement: '',
      bannerImage: '',
      welcomeImage: '',
      categoryImage: '',
      businessHours: ['09:00', '18:00']
    }
    
    shopStats.value = {
      totalProducts: 0,
      totalOrders: 0,
      totalSales: '0.00',
      totalCustomers: 0
    }
  }
}

// 保存店铺信息
const saveShopInfo = async () => {
  if (!shopFormRef.value) return
  
  await shopFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      saving.value = true
      try {
        const res = await updateShopInfo(shopForm)
        
        if (res.data && res.data.data) {
          Object.assign(shopInfo.value, res.data.data)
        } else {
          Object.assign(shopInfo.value, shopForm)
        }
        
        ElMessage.success(t('message.operationSuccess'))
      } catch (error) {
        console.error('Failed to update shop info:', error)
        ElMessage.error(t('message.operationFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

// 重置表单
const resetForm = () => {
  Object.assign(shopForm, {
    name: shopInfo.value.name,
    logo: shopInfo.value.logo,
    description: shopInfo.value.description,
    phone: shopInfo.value.phone,
    email: shopInfo.value.email,
    businessHours: shopInfo.value.businessHours,
    status: shopInfo.value.status
  })
}

// 编辑店铺信息
const editShopInfo = () => {
  Object.assign(editForm, {
    name: shopInfo.value.name,
    description: shopInfo.value.description,
    phone: shopInfo.value.phone,
    email: shopInfo.value.email
  })
  shopDialogVisible.value = true
}

// 保存编辑表单
const saveEditForm = async () => {
  if (!editFormRef.value) return
  
  await editFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      saving.value = true
      try {
        await updateShopInfo(editForm)
        
        if (res.data && res.data.data) {
          Object.assign(shopInfo.value, res.data.data)
        } else {
          Object.assign(shopInfo.value, editForm)
        }
        
        Object.assign(shopForm, editForm)
        ElMessage.success(t('message.operationSuccess'))
        shopDialogVisible.value = false
      } catch (error) {
        console.error('Failed to update shop info:', error)
        ElMessage.error(t('message.operationFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

// 编辑公告
const editAnnouncement = () => {
  announcementForm.announcement = shopInfo.value.announcement || ''
  announcementDialogVisible.value = true
}

// 保存公告
const saveAnnouncement = async () => {
  if (!announcementFormRef.value) return
  
  await announcementFormRef.value.validate(async (valid: boolean) => {
    if (valid) {
      saving.value = true
      try {
        const res = await updateShopAnnouncement(announcementForm)
        
        if (res.data && res.data.data) {
          shopInfo.value.announcement = res.data.data.announcement
        } else {
          shopInfo.value.announcement = announcementForm.announcement
        }
        
        ElMessage.success(t('message.operationSuccess'))
        announcementDialogVisible.value = false
      } catch (error) {
        console.error('Failed to update announcement:', error)
        ElMessage.error(t('message.operationFailed'))
      } finally {
        saving.value = false
      }
    }
  })
}

// Logo上传成功
const handleLogoSuccess = (response: any, file: any) => {
  shopForm.logo = URL.createObjectURL(file.raw)
  shopInfo.value.logo = shopForm.logo
}

// Logo上传前验证
const beforeLogoUpload = (file: any) => {
  const isJPG = file.type === 'image/jpeg' || file.type === 'image/png'
  const isLt2M = file.size / 1024 / 1024 < 2

  if (!isJPG) {
    ElMessage.error(t('shop.logoFormatError'))
  }
  if (!isLt2M) {
    ElMessage.error(t('shop.logoSizeError'))
  }
  return isJPG && isLt2M
}

// 编辑横幅
const editBanner = () => {
  ElMessage.info(t('shop.bannerEditComingSoon'))
}

// 编辑欢迎图
const editWelcome = () => {
  ElMessage.info(t('shop.welcomeEditComingSoon'))
}

// 编辑分类图
const editCategory = () => {
  ElMessage.info(t('shop.categoryEditComingSoon'))
}

onMounted(() => {
  loadShopInfo()
})
</script>

<style scoped>
.shop-overview {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.shop-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.shop-logo {
  flex-shrink: 0;
}

.shop-details {
  flex: 1;
}

.shop-name {
  margin: 0 0 10px 0;
  font-size: 24px;
  font-weight: bold;
  color: #333;
}

.shop-description {
  margin: 0 0 15px 0;
  color: #666;
  line-height: 1.4;
}

.shop-stats {
  display: flex;
  align-items: center;
  gap: 15px;
}

.shop-rating {
  display: flex;
  align-items: center;
  gap: 5px;
  color: #f39c12;
  font-weight: 500;
}

.stat-item {
  text-align: center;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #409EFF;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.shop-form {
  max-width: 600px;
}

.logo-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  width: 120px;
  height: 120px;
}

.logo-uploader:hover {
  border-color: #409EFF;
}

.logo-preview {
  width: 100%;
  height: 100%;
}

.logo-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 120px;
  height: 120px;
  line-height: 120px;
  text-align: center;
}

.upload-tip {
  font-size: 12px;
  color: #999;
  margin-top: 5px;
}

.announcement-content {
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  min-height: 100px;
}

.announcement-text {
  font-size: 14px;
  color: #333;
  line-height: 1.6;
}

.no-announcement {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #999;
  font-size: 14px;
}

.decoration-item {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  overflow: hidden;
}

.decoration-preview {
  background-color: #f8f9fa;
}

.decoration-info {
  padding: 15px;
}

.decoration-info h4 {
  margin: 0 0 8px 0;
  font-size: 16px;
  color: #333;
}

.decoration-info p {
  margin: 0 0 10px 0;
  font-size: 12px;
  color: #666;
  line-height: 1.4;
}
</style>

