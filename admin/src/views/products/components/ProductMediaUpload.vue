<template>
  <div class="product-media-upload">
    <!-- 主图上传 -->
    <el-form-item :label="$t('products.mainImage')" prop="mainImage">
      <div class="image-upload-container">
        <el-upload
          class="image-uploader"
          :action="uploadUrl"
          :headers="uploadHeaders"
          :show-file-list="false"
          :on-success="handleMainImageSuccess"
          :before-upload="beforeImageUpload"
          accept="image/*"
        >
          <el-image
            v-if="mainImage"
            :src="mainImage"
            class="uploaded-image"
            fit="cover"
          />
          <el-icon v-else class="image-uploader-icon">
            <Plus />
          </el-icon>
        </el-upload>
        <div class="upload-tips">
          <p>{{ $t('products.uploadMainImage') }}</p>
          <p>{{ $t('products.imageSizeLimit') }}</p>
        </div>
      </div>
    </el-form-item>

    <!-- 附图上传 -->
    <el-form-item :label="$t('products.attachedImages')">
      <div class="images-upload-container">
        <el-upload
          class="images-uploader"
          :action="uploadImagesUrl"
          :headers="uploadHeaders"
          :file-list="additionalImages"
          :on-success="handleAdditionalImagesSuccess"
          :on-remove="handleRemoveImage"
          :before-upload="beforeImageUpload"
          name="files"
          multiple
          accept="image/*"
          list-type="picture-card"
        >
          <el-icon class="image-uploader-icon">
            <Plus />
          </el-icon>
        </el-upload>
        <div class="upload-tips">
          <p>{{ $t('products.uploadAttachedImages') }}</p>
          <p>{{ $t('products.maxImages') }}</p>
        </div>
      </div>
    </el-form-item>

    <!-- 视频上传 -->
    <el-form-item :label="$t('products.productVideo')">
      <div class="video-upload-container">
        <el-upload
          class="video-uploader"
          :action="uploadVideoUrl"
          :headers="uploadHeaders"
          :show-file-list="false"
          :on-success="handleVideoSuccess"
          :before-upload="beforeVideoUpload"
          accept="video/*"
        >
          <div v-if="video" class="video-preview">
            <video :src="video" controls class="uploaded-video" />
            <div class="video-overlay">
              <el-icon class="video-icon"><VideoPlay /></el-icon>
            </div>
          </div>
          <div v-else class="video-upload-placeholder">
            <el-icon class="video-icon"><VideoCamera /></el-icon>
            <p>{{ $t('products.uploadVideo') }}</p>
          </div>
        </el-upload>
        <div class="upload-tips">
          <p>{{ $t('products.uploadProductVideo') }}</p>
          <p>{{ $t('products.videoSizeLimit') }}</p>
        </div>
      </div>
    </el-form-item>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { Plus, VideoPlay, VideoCamera } from '@element-plus/icons-vue'
import { useUserStore } from '@/stores/user'
import type { AdditionalImage, UploadResponse } from '../types/product'

// 新增：安全清洗 URL 中的反引号、中文引号与省略号
const sanitizeUrl = (url: string): string => {
  if (!url) return ''
  return url.replace(/[`\u201C\u201D\u2018\u2019\u2026]/g, '').trim()
}

interface Props {
  mainImage: string
  images: string
  video: string
}

interface Emits {
  (e: 'update:mainImage', value: string): void
  (e: 'update:images', value: string): void
  (e: 'update:video', value: string): void
  (e: 'upload-success', type: string, response: any): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const { t } = useI18n()
const userStore = useUserStore()

// 上传相关
const uploadUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/image`)
const uploadImagesUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/images`)
const uploadVideoUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/video`)

const uploadHeaders = computed(() => ({
  Authorization: `Bearer ${userStore.token}`
}))

// 附图列表
const additionalImages = ref<AdditionalImage[]>([])

// 监听 props.images 变化，更新 additionalImages
watch(() => props.images, (newImages) => {
  if (newImages) {
    try {
      const imageUrls = JSON.parse(newImages)
      if (Array.isArray(imageUrls)) {
        additionalImages.value = imageUrls.map((url, index) => ({
          name: `image-${index + 1}`,
          url: sanitizeUrl(url),
          uid: `${Date.now()}-${index}`
        }))
      }
    } catch (error) {
      console.error('解析图片数据失败:', error)
      additionalImages.value = []
    }
  } else {
    additionalImages.value = []
  }
}, { immediate: true })

// 上传前验证
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

const beforeVideoUpload = (file: File) => {
  const isVideo = file.type.startsWith('video/')
  const isLt100M = file.size / 1024 / 1024 < 100

  if (!isVideo) {
    ElMessage.error('只能上传视频文件!')
    return false
  }
  if (!isLt100M) {
    ElMessage.error('视频大小不能超过 100MB!')
    return false
  }
  return true
}

// 上传成功处理
const handleMainImageSuccess = (response: UploadResponse) => {
  if (response && response.url) {
    let imageUrl = response.url
    if (!imageUrl.startsWith('http')) {
      const currentDomain = window.location.origin
      const path = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`
      imageUrl = `${currentDomain}${path}`
    }
    // 清洗 URL
    imageUrl = sanitizeUrl(imageUrl)
    emit('update:mainImage', imageUrl)
    emit('upload-success', 'mainImage', response)
    ElMessage.success(t('common.success'))
  } else {
    ElMessage.error(t('common.error'))
  }
}

const handleAdditionalImagesSuccess = (response: any, file: any, fileList: any[]) => {
  if (Array.isArray(response) && response.length > 0) {
    const currentFileResponse = response.find(item => 
      item.filename === file.name || 
      file.name.includes(item.filename?.split('.')[0])
    ) || response[response.length - 1]
    
    if (currentFileResponse && currentFileResponse.url) {
      let imageUrl = currentFileResponse.url
      if (!imageUrl.startsWith('http')) {
        const currentDomain = window.location.origin
        const path = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`
        imageUrl = `${currentDomain}${path}`
      }
      // 清洗 URL
      imageUrl = sanitizeUrl(imageUrl)
      
      file.url = imageUrl
      file.response = currentFileResponse
      
      ElMessage.success(t('common.success'))
    } else {
      ElMessage.error(t('common.error'))
    }
  }
  
  // 更新附图列表（逐项清洗）
  additionalImages.value = fileList.map(item => ({
    name: item.name,
    url: sanitizeUrl(
      item.url || (item.response?.url ? 
        (item.response.url.startsWith('http') ? item.response.url : `${window.location.origin}${item.response.url.startsWith('/') ? item.response.url : `/${item.response.url}`}`) : 
        '')
    ),
    uid: item.uid
  }))
  
  // 更新images字段，逐项清洗
  const imageUrls = additionalImages.value.map(img => sanitizeUrl(img.url))
  emit('update:images', JSON.stringify(imageUrls))
}

const handleRemoveImage = (file: any, fileList: any[]) => {
  additionalImages.value = fileList.map(item => ({
    name: item.name,
    url: sanitizeUrl(
      item.url || (item.response?.url ? 
        (item.response.url.startsWith('http') ? item.response.url : `${window.location.origin}${item.response.url.startsWith('/') ? item.response.url : `/${item.response.url}`}`) : 
        '')
    ),
    uid: item.uid
  }))
  
  const imageUrls = additionalImages.value.map(img => sanitizeUrl(img.url))
  emit('update:images', JSON.stringify(imageUrls))
  ElMessage.success(t('common.success'))
}

const handleVideoSuccess = (response: UploadResponse) => {
  if (response && response.url) {
    let videoUrl = response.url
    if (!videoUrl.startsWith('http')) {
      const currentDomain = window.location.origin
      const path = videoUrl.startsWith('/') ? videoUrl : `/${videoUrl}`
      videoUrl = `${currentDomain}${path}`
    }
    // 清洗 URL
    videoUrl = sanitizeUrl(videoUrl)
    emit('update:video', videoUrl)
    emit('upload-success', 'video', response)
    ElMessage.success(t('common.success'))
  } else {
    ElMessage.error(t('common.error'))
  }
}
</script>

<style scoped lang="scss">
.product-media-upload {
  margin-bottom: 24px;
}

.image-upload-container,
.images-upload-container,
.video-upload-container {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.image-uploader,
.video-uploader {
  width: 200px;
  height: 200px;
  border: 2px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: border-color 0.3s;

  &:hover {
    border-color: #409eff;
  }
}

.uploaded-image,
.uploaded-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-uploader-icon,
.video-icon {
  font-size: 28px;
  color: #8c939d;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.video-preview {
  position: relative;
  width: 100%;
  height: 100%;
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;

  &:hover {
    opacity: 1;
  }
}

.video-upload-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #8c939d;

  p {
    margin-top: 8px;
    font-size: 14px;
  }
}

.images-uploader {
  :deep(.el-upload--picture-card) {
    width: 100px;
    height: 100px;
  }
}

.upload-tips {
  flex: 1;
  color: #999;
  font-size: 12px;
  line-height: 1.5;

  p {
    margin: 0 0 4px 0;
  }
}
</style>
