<template>
  <div class="color-variant-form">
    <el-row :gutter="20">
      <el-col :span="8">
        <el-form-item label="颜色名称">
          <el-input 
            v-model="variantData.name" 
            placeholder="如：红色、蓝色、黑色"
            @input="updateVariant"
          />
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="单位">
          <div class="unit-buttons">
            <el-button 
              v-for="unit in colorUnits" 
              :key="unit.value"
              :type="variantData.unit === unit.value ? 'primary' : 'default'"
              @click="variantData.unit = unit.value; updateVariant()"
              size="small"
            >
              {{ unit.label }}
            </el-button>
          </div>
        </el-form-item>
      </el-col>
    </el-row>
    
    <el-row :gutter="20">
      <el-col :span="12">
        <el-form-item label="颜色图片">
          <el-upload
            class="color-image-uploader"
            :action="uploadUrl"
            :headers="uploadHeaders"
            :show-file-list="false"
            :on-success="handleImageSuccess"
            :before-upload="beforeImageUpload"
            accept="image/*"
          >
            <el-image
              v-if="variantData.image"
              :src="variantData.image"
              class="color-image"
              fit="cover"
            />
            <div v-else class="color-image-placeholder">
              <el-icon><Plus /></el-icon>
              <span>上传颜色图片</span>
            </div>
          </el-upload>
        </el-form-item>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Plus } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import { useUserStore } from '@/stores/user'
import type { ProductVariant } from '../types/product'

interface Props {
  variant: ProductVariant
  variantIndex: number
}

interface Emits {
  (e: 'update:variant', variant: ProductVariant): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const userStore = useUserStore()
const uploadUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/image`)

const uploadHeaders = computed(() => ({
  Authorization: `Bearer ${userStore.token}`
}))

const variantData = ref<ProductVariant>({ ...props.variant })

// 颜色变体专用单位
const colorUnits: Array<{ label: string; value: 'pcs' | 'piece' }> = [
  { label: '件', value: 'pcs' },
  { label: '个', value: 'piece' }
]

// 监听props变化
watch(() => props.variant, (newVariant) => {
  variantData.value = { ...newVariant }
}, { deep: true })

const updateVariant = () => {
  emit('update:variant', { ...variantData.value })
}

const beforeImageUpload = (file: File) => {
  const isImage = file.type.startsWith('image/')
  const isLt5M = file.size / 1024 / 1024 < 5

  if (!isImage) {
    ElMessage.error('只能上传图片文件!')
    return false
  }
  if (!isLt5M) {
    ElMessage.error('图片大小不能超过 5MB!')
    return false
  }
  return true
}

const handleImageSuccess = (response: any) => {
  if (response && response.url) {
    let imageUrl = response.url
    if (!imageUrl.startsWith('http')) {
      const currentDomain = window.location.origin
      const path = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`
      imageUrl = `${currentDomain}${path}`
    }
    
    variantData.value.image = imageUrl
    updateVariant()
    ElMessage.success('颜色图片上传成功')
  } else {
    ElMessage.error('图片上传失败')
  }
}
</script>

<style scoped lang="scss">
.color-variant-form {
  padding: 16px;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  background: #fafafa;
}

.unit-buttons {
  display: flex;
  gap: 8px;
  
  .el-button {
    margin: 0;
    min-width: 60px;
  }
}

.color-image-uploader {
  .color-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    border: 1px solid #e4e7ed;
  }
  
  .color-image-placeholder {
    width: 80px;
    height: 80px;
    border: 2px dashed #d9d9d9;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: border-color 0.3s;
    
    &:hover {
      border-color: #409eff;
    }
    
    .el-icon {
      font-size: 20px;
      color: #999;
      margin-bottom: 4px;
    }
    
    span {
      font-size: 12px;
      color: #999;
    }
  }
}
</style>
