<template>
  <div class="address-form">
    <!-- 头部 -->
    <div class="header">
      <button class="back-btn" @click="goBack">
        <i class="el-icon-arrow-left"></i>
      </button>
      <h1>{{ isEdit ? $t('address.editAddress') : $t('address.addAddress') }}</h1>
      <div class="placeholder"></div>
    </div>

    <!-- 表单 -->
    <div class="form-container">
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="80px"
        class="address-form"
      >
        <el-form-item :label="$t('address.receiverName')" prop="receiverName">
          <el-input
            v-model="form.receiverName"
            :placeholder="$t('address.receiverNamePlaceholder')"
            maxlength="50"
          />
        </el-form-item>

        <el-form-item :label="$t('address.phone')" prop="phone">
          <el-input
            v-model="form.phone"
            :placeholder="$t('address.phonePlaceholder')"
            maxlength="11"
          />
        </el-form-item>

        <el-form-item :label="$t('address.province')" prop="province">
          <el-input
            v-model="form.province"
            :placeholder="$t('address.provincePlaceholder')"
            maxlength="50"
          />
        </el-form-item>

        <el-form-item :label="$t('address.city')" prop="city">
          <el-input
            v-model="form.city"
            :placeholder="$t('address.cityPlaceholder')"
            maxlength="50"
          />
        </el-form-item>

        <el-form-item :label="$t('address.district')" prop="district">
          <el-input
            v-model="form.district"
            :placeholder="$t('address.districtPlaceholder')"
            maxlength="50"
          />
        </el-form-item>

        <el-form-item :label="$t('address.detailAddress')" prop="detailAddress">
          <el-input
            v-model="form.detailAddress"
            type="textarea"
            :placeholder="$t('address.detailAddressPlaceholder')"
            :rows="3"
            maxlength="255"
            show-word-limit
          />
        </el-form-item>

        <el-form-item>
          <el-checkbox v-model="form.isDefault">
            {{ $t('address.setAsDefault') }}
          </el-checkbox>
        </el-form-item>
      </el-form>

      <!-- 保存按钮 -->
      <div class="save-section">
        <button 
          class="save-btn"
          :disabled="saving"
          @click="saveAddress"
        >
          <div v-if="saving" class="loading-spinner"></div>
          {{ saving ? $t('common.saving') : $t('common.save') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, FormInstance, FormRules } from 'element-plus'
import { addressApi } from '@/api'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

// 状态管理
const isEdit = ref(false)
const addressId = ref('')
const saving = ref(false)
const formRef = ref<FormInstance>()

// 表单数据
const form = reactive({
  receiverName: '',
  phone: '',
  province: '',
  city: '',
  district: '',
  detailAddress: '',
  isDefault: false
})

// 表单验证规则
const rules: FormRules = {
  receiverName: [
    { required: true, message: t('address.receiverNameRequired'), trigger: 'blur' },
    { min: 1, max: 50, message: t('address.receiverNameLength'), trigger: 'blur' }
  ],
  phone: [
    { required: true, message: t('address.phoneRequired'), trigger: 'blur' },
    { pattern: /^1[3-9]\d{9}$/, message: t('address.phoneFormat'), trigger: 'blur' }
  ],
  province: [
    { required: true, message: t('address.provinceRequired'), trigger: 'blur' },
    { min: 1, max: 50, message: t('address.provinceLength'), trigger: 'blur' }
  ],
  city: [
    { required: true, message: t('address.cityRequired'), trigger: 'blur' },
    { min: 1, max: 50, message: t('address.cityLength'), trigger: 'blur' }
  ],
  district: [
    { required: true, message: t('address.districtRequired'), trigger: 'blur' },
    { min: 1, max: 50, message: t('address.districtLength'), trigger: 'blur' }
  ],
  detailAddress: [
    { required: true, message: t('address.detailAddressRequired'), trigger: 'blur' },
    { min: 1, max: 255, message: t('address.detailAddressLength'), trigger: 'blur' }
  ]
}

// 方法
const goBack = () => {
  router.go(-1)
}

const loadAddress = async () => {
  if (!isEdit.value) return

  try {
    const response = await addressApi.getAddressById(addressId.value)
    const address = response.data
    
    form.receiverName = address.receiverName
    form.phone = address.phone
    form.province = address.province
    form.city = address.city
    form.district = address.district
    form.detailAddress = address.detailAddress
    form.isDefault = address.isDefault === 1
  } catch (error: any) {
    console.error('加载地址失败:', error)
    ElMessage.error(error.message || t('address.loadFailed'))
  }
}

const saveAddress = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    saving.value = true

    const addressData = {
      ...form,
      isDefault: form.isDefault ? 1 : 0
    }

    if (isEdit.value) {
      await addressApi.updateAddress(addressId.value, addressData)
      ElMessage.success(t('address.updateSuccess'))
    } else {
      await addressApi.createAddress(addressData)
      ElMessage.success(t('address.createSuccess'))
    }

    // 返回地址列表页面
    router.push('/address/list')
  } catch (error: any) {
    console.error('保存地址失败:', error)
    ElMessage.error(error.message || t('address.saveFailed'))
  } finally {
    saving.value = false
  }
}

// 生命周期
onMounted(() => {
  // 检查是否是编辑模式
  if (route.path.includes('/edit/')) {
    isEdit.value = true
    addressId.value = route.params.id as string
    loadAddress()
  }
})
</script>

<style scoped lang="scss">
.address-form {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #fff;
  border-bottom: 1px solid #eee;

  .back-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #f0f0f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #666;

    &:hover {
      background: #e0e0e0;
    }
  }

  h1 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #333;
  }

  .placeholder {
    width: 40px;
  }
}

.form-container {
  padding: 20px;
}

.address-form {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.save-section {
  margin-top: 20px;
  text-align: center;

  .save-btn {
    width: 100%;
    height: 48px;
    background: #409eff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;

    &:hover:not(:disabled) {
      background: #337ecc;
    }

    &:disabled {
      background: #c0c4cc;
      cursor: not-allowed;
    }

    .loading-spinner {
      width: 16px;
      height: 16px;
      border: 2px solid #fff;
      border-top: 2px solid transparent;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

