<template>
  <div class="file-upload">
    <el-upload
      ref="uploadRef"
      :action="uploadUrl"
      :headers="headers"
      :data="uploadData"
      :file-list="fileList"
      :before-upload="beforeUpload"
      :on-success="handleSuccess"
      :on-error="handleError"
      :on-progress="handleProgress"
      :on-remove="handleRemove"
      :on-preview="handlePreview"
      :multiple="multiple"
      :accept="accept"
      :limit="limit"
      :on-exceed="handleExceed"
      :drag="drag"
      :show-file-list="showFileList"
      :auto-upload="autoUpload"
      :disabled="disabled"
      class="upload-component"
    >
      <template #trigger v-if="!drag">
        <el-button type="primary" :disabled="disabled">
          <el-icon><Upload /></el-icon>
          {{ buttonText }}
        </el-button>
      </template>
      
      <template #default v-if="drag">
        <div class="upload-dragger">
          <el-icon class="el-icon--upload"><Upload /></el-icon>
          <div class="el-upload__text">
            {{ dragText }}
          </div>
          <div class="el-upload__tip">
            {{ uploadTip }}
          </div>
        </div>
      </template>

      <template #tip>
        <div class="el-upload__tip" v-if="showTip">
          {{ uploadTip }}
        </div>
      </template>
    </el-upload>

    <!-- 图片预览对话框 -->
    <el-dialog
      v-model="previewVisible"
      title="Image Preview"
      width="80%"
      center
    >
      <div class="preview-container">
        <el-image
          :src="previewUrl"
          style="width: 100%; max-height: 500px"
          fit="contain"
        />
      </div>
    </el-dialog>

    <!-- 上传进度对话框 -->
    <el-dialog
      v-model="progressVisible"
      title="Upload Progress"
      width="400px"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
    >
      <div class="progress-container">
        <el-progress
          :percentage="uploadProgress"
          :status="uploadStatus"
        />
        <div class="progress-text">
          {{ progressText }}
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Upload } from '@element-plus/icons-vue'
import { useUserStore } from '@/stores/user'

interface Props {
  // 上传配置
  uploadUrl?: string
  multiple?: boolean
  accept?: string
  limit?: number
  drag?: boolean
  autoUpload?: boolean
  disabled?: boolean
  showFileList?: boolean
  
  // 显示文本
  buttonText?: string
  dragText?: string
  uploadTip?: string
  showTip?: boolean
  
  // 文件类型限制
  maxSize?: number // MB
  allowedTypes?: string[]
  
  // 业务配置
  businessType?: 'product' | 'avatar' | 'banner' | 'general'
}

const props = withDefaults(defineProps<Props>(), {
  uploadUrl: '/upload/image',
  multiple: false,
  accept: 'image/*',
  limit: 5,
  drag: false,
  autoUpload: true,
  disabled: false,
  showFileList: true,
  buttonText: 'Select Files',
  dragText: 'Drop files here or click to upload',
  uploadTip: 'Support JPG/PNG files, max 2MB each',
  showTip: true,
  maxSize: 2,
  allowedTypes: () => ['image/jpeg', 'image/png', 'image/gif'],
  businessType: 'general'
})

const emit = defineEmits<{
  success: [response: any, file: any, fileList: any[]]
  error: [error: Error, file: any, fileList: any[]]
  progress: [event: any, file: any, fileList: any[]]
  remove: [file: any, fileList: any[]]
  preview: [file: any]
  exceed: [files: File[], fileList: any[]]
}>()

const userStore = useUserStore()
const uploadRef = ref()
const fileList = ref<any[]>([])
const previewVisible = ref(false)
const previewUrl = ref('')
const progressVisible = ref(false)
const uploadProgress = ref(0)
const uploadStatus = ref<'success' | 'exception' | undefined>()
const progressText = ref('')

// 计算属性
const headers = computed(() => {
  const token = userStore.token
  return token ? { Authorization: `Bearer ${token}` } : {}
})

const uploadData = computed(() => {
  return {
    businessType: props.businessType,
    timestamp: Date.now()
  }
})

// 上传前验证
const beforeUpload = (file: File) => {
  // 文件类型验证
  if (props.allowedTypes.length > 0 && !props.allowedTypes.includes(file.type)) {
    ElMessage.error(`File type not allowed. Allowed types: ${props.allowedTypes.join(', ')}`)
    return false
  }

  // 文件大小验证
  const isLtMaxSize = file.size / 1024 / 1024 < props.maxSize
  if (!isLtMaxSize) {
    ElMessage.error(`File size must be less than ${props.maxSize}MB`)
    return false
  }

  // 特殊业务验证
  if (props.businessType === 'avatar') {
    // 头像必须是正方形图片
    return new Promise((resolve) => {
      const img = new Image()
      img.onload = () => {
        if (img.width !== img.height) {
          ElMessage.error('Avatar image must be square')
          resolve(false)
        } else {
          resolve(true)
        }
      }
      img.src = URL.createObjectURL(file)
    })
  }

  return true
}

// 上传成功
const handleSuccess = (response: any, file: any, fileList: any[]) => {
  ElMessage.success('File uploaded successfully')
  uploadProgress.value = 100
  uploadStatus.value = 'success'
  progressText.value = 'Upload completed'
  
  setTimeout(() => {
    progressVisible.value = false
  }, 1000)
  
  emit('success', response, file, fileList)
}

// 上传失败
const handleError = (error: Error, file: any, fileList: any[]) => {
  ElMessage.error('File upload failed')
  uploadStatus.value = 'exception'
  progressText.value = 'Upload failed'
  
  setTimeout(() => {
    progressVisible.value = false
  }, 2000)
  
  emit('error', error, file, fileList)
}

// 上传进度
const handleProgress = (event: any, file: any, fileList: any[]) => {
  uploadProgress.value = Math.round(event.percent)
  progressText.value = `Uploading: ${file.name}`
  
  if (!progressVisible.value) {
    progressVisible.value = true
  }
  
  emit('progress', event, file, fileList)
}

// 删除文件
const handleRemove = (file: any, fileList: any[]) => {
  emit('remove', file, fileList)
}

// 预览文件
const handlePreview = (file: any) => {
  if (file.raw) {
    previewUrl.value = URL.createObjectURL(file.raw)
  } else {
    previewUrl.value = file.url
  }
  previewVisible.value = true
  emit('preview', file)
}

// 超出限制
const handleExceed = (files: File[], fileList: any[]) => {
  ElMessage.warning(`Only ${props.limit} files can be uploaded at a time`)
  emit('exceed', files, fileList)
}

// 手动上传
const submitUpload = () => {
  uploadRef.value?.submit()
}

// 清空文件列表
const clearFiles = () => {
  uploadRef.value?.clearFiles()
}

// 暴露方法给父组件
defineExpose({
  submitUpload,
  clearFiles
})

// 监听文件列表变化
watch(fileList, (newList) => {
  // 可以在这里处理文件列表变化
}, { deep: true })
</script>

<style scoped>
.file-upload {
  width: 100%;
}

.upload-component {
  width: 100%;
}

.upload-dragger {
  padding: 40px;
  text-align: center;
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: all 0.3s;
}

.upload-dragger:hover {
  border-color: #409EFF;
}

.el-icon--upload {
  font-size: 67px;
  color: #c0c4cc;
  margin-bottom: 16px;
}

.el-upload__text {
  color: #606266;
  font-size: 14px;
  text-align: center;
  margin-bottom: 10px;
}

.el-upload__tip {
  color: #909399;
  font-size: 12px;
  text-align: center;
}

.preview-container {
  text-align: center;
}

.progress-container {
  text-align: center;
}

.progress-text {
  margin-top: 10px;
  color: #606266;
  font-size: 14px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .upload-dragger {
    padding: 20px;
  }
  
  .el-icon--upload {
    font-size: 40px;
  }
}
</style>
