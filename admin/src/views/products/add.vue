<template>
  <div class="add-product-page">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>{{ isEdit ? '编辑商品' : '添加商品' }}</span>
          <el-button @click="goBack">返回</el-button>
        </div>
      </template>

      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="商品名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入商品名称" />
        </el-form-item>

        <el-form-item label="商品分类" prop="categoryId">
          <el-cascader
            v-model="form.categoryId"
            :options="categoryOptions"
            :props="{ value: 'id', label: 'name', children: 'children', emitPath: false }"
            placeholder="请选择商品分类"
            clearable
          />
        </el-form-item>

        <el-form-item label="品牌" prop="brand">
          <el-input v-model="form.brand" placeholder="请输入品牌" />
        </el-form-item>

        <el-form-item label="主图" prop="mainImage">
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
                v-if="form.mainImage"
                :src="form.mainImage"
                class="uploaded-image"
                fit="cover"
                @error="handleImageError"
                @load="handleImageLoad"
              />
              <el-icon v-else class="image-uploader-icon">
                <Plus />
              </el-icon>
            </el-upload>
            <div class="upload-tips">
              <p>点击上传主图，支持 JPG、PNG、GIF 格式</p>
              <p>图片大小不超过 5MB</p>
            </div>
          </div>
        </el-form-item>

        <el-form-item label="附图">
          <div class="images-upload-container">
            <el-upload
              class="images-uploader"
              :action="uploadImagesUrl"
              :headers="uploadHeaders"
              :file-list="additionalImages"
              :on-success="handleAdditionalImagesSuccess"
              :on-remove="handleRemoveImage"
              :before-upload="beforeImageUpload"
              multiple
              accept="image/*"
              list-type="picture-card"
            >
              <el-icon class="image-uploader-icon">
                <Plus />
              </el-icon>
            </el-upload>
            <div class="upload-tips">
              <p>点击上传附图，支持 JPG、PNG、GIF 格式</p>
              <p>最多上传 9 张图片，每张不超过 5MB</p>
            </div>
          </div>
        </el-form-item>

        <el-form-item label="产品视频">
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
              <div v-if="form.video" class="video-preview">
                <video :src="form.video" controls class="uploaded-video" />
                <div class="video-overlay">
                  <el-icon class="video-icon"><VideoPlay /></el-icon>
                </div>
              </div>
              <div v-else class="video-upload-placeholder">
                <el-icon class="video-uploader-icon">
                  <VideoCamera />
                </el-icon>
                <div class="upload-text">点击上传视频</div>
              </div>
            </el-upload>
            <div class="upload-tips">
              <p>点击上传产品视频，支持 MP4、AVI、MOV 等格式</p>
              <p>视频大小不超过 100MB</p>
            </div>
          </div>
        </el-form-item>

        <el-form-item label="Cost Price (RM)" prop="costPrice">
          <el-input-number
            v-model="form.costPrice"
            :min="0"
            :precision="2"
            placeholder="Enter cost price in RM"
          />
        </el-form-item>

        <el-form-item label="Suggested Price (RM)" prop="suggestPrice">
          <el-input-number
            v-model="form.suggestPrice"
            :min="0"
            :precision="2"
            placeholder="Enter suggested price in RM"
          />
        </el-form-item>

        <el-form-item label="库存" prop="stock">
          <el-input-number
            v-model="form.stock"
            :min="0"
            placeholder="请输入库存"
          />
        </el-form-item>

        <el-form-item label="商品详情" prop="description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="5"
            placeholder="请输入商品详情"
          />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSubmit" :loading="loading">
            {{ isEdit ? '更新' : '创建' }}
          </el-button>
          <el-button @click="goBack">取消</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { Plus, VideoPlay, VideoCamera } from '@element-plus/icons-vue'
import { getCategoryList, createProduct, updateProduct, getProductDetail } from '@/api/product'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()

const isEdit = ref(false)
const productId = ref<number>()
const formRef = ref<FormInstance>()
const loading = ref(false)

// 图片上传相关
const uploadUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/image`)
const uploadImagesUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/images`)
const uploadVideoUrl = ref(`${import.meta.env.VITE_API_BASE_URL || '/api'}/upload/video`)
const uploadHeaders = ref({
  Authorization: `Bearer ${userStore.token}`
})

// 附图列表
const additionalImages = ref([])

const form = reactive({
  name: '',
  categoryId: null as number | null,
  brand: '',
  mainImage: '',
  images: '',
  video: '',
  costPrice: 0,
  suggestPrice: 0,
  stock: 0,
  description: ''
})

const rules: FormRules = {
  name: [{ required: true, message: '请输入商品名称', trigger: 'blur' }],
  categoryId: [{ required: true, message: '请选择商品分类', trigger: 'change' }],
  mainImage: [{ required: true, message: '请输入主图URL', trigger: 'blur' }],
  costPrice: [{ required: true, message: '请输入成本价', trigger: 'blur' }],
  stock: [{ required: true, message: '请输入库存', trigger: 'blur' }]
}

const categoryOptions = ref([])

// 获取分类列表
const fetchCategories = async () => {
  try {
    const res = await getCategoryList()
    categoryOptions.value = res.data
  } catch (error) {
    console.error('获取分类失败：', error)
  }
}

// 获取商品详情
const fetchProductDetail = async (id: number) => {
  try {
    const res = await getProductDetail(id)
    const product = res.data
    Object.assign(form, {
      name: product.name,
      categoryId: product.categoryId,
      brand: product.brand,
      mainImage: product.mainImage,
      images: product.images,
      video: product.video,
      costPrice: product.costPrice,
      suggestPrice: product.suggestPrice,
      stock: product.stock,
      description: product.description
    })
    
    // 处理附图
    if (product.images) {
      try {
        const imageUrls = JSON.parse(product.images)
        additionalImages.value = imageUrls.map((url: string, index: number) => ({
          name: `image-${index}`,
          url: url,
          uid: Date.now() + index
        }))
      } catch (error) {
        console.error('解析附图失败:', error)
        additionalImages.value = []
      }
    }
  } catch (error) {
    console.error('获取商品详情失败：', error)
  }
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        // 处理附图数据
        const imageUrls = additionalImages.value.map(img => img.url)
        form.images = JSON.stringify(imageUrls)
        
        if (isEdit.value && productId.value) {
          await updateProduct(productId.value, form)
          ElMessage.success('更新成功')
        } else {
          await createProduct(form)
          ElMessage.success('创建成功')
        }
        router.back()
      } catch (error: any) {
        ElMessage.error(error.message || '操作失败')
      } finally {
        loading.value = false
      }
    }
  })
}

// 主图上传成功
const handleMainImageSuccess = (response: any) => {
  console.log('主图上传响应:', response)
  if (response && response.url) {
    let imageUrl = response.url
    console.log('原始URL:', imageUrl)
    
    if (!imageUrl.startsWith('http')) {
      const currentDomain = window.location.origin
      const path = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`
      imageUrl = `${currentDomain}${path}`
    }
    console.log('最终图片URL:', imageUrl)
    form.mainImage = imageUrl
    ElMessage.success('主图上传成功')
  } else {
    ElMessage.error('主图上传失败')
  }
}

// 附图上传成功
const handleAdditionalImagesSuccess = (response: any, file: any, fileList: any[]) => {
  console.log('附图上传响应:', response)
  if (response && response.url) {
    let imageUrl = response.url
    if (!imageUrl.startsWith('http')) {
      const currentDomain = window.location.origin
      const path = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`
      imageUrl = `${currentDomain}${path}`
    }
    
    // 更新附图列表
    additionalImages.value = fileList.map(item => ({
      name: item.name,
      url: item.response?.url ? 
        (item.response.url.startsWith('http') ? item.response.url : `${window.location.origin}${item.response.url.startsWith('/') ? item.response.url : `/${item.response.url}`}`) : 
        item.url,
      uid: item.uid
    }))
    
    ElMessage.success('附图上传成功')
  } else {
    ElMessage.error('附图上传失败')
  }
}

// 视频上传成功
const handleVideoSuccess = (response: any) => {
  console.log('视频上传响应:', response)
  if (response && response.url) {
    let videoUrl = response.url
    console.log('原始视频URL:', videoUrl)
    
    if (!videoUrl.startsWith('http')) {
      const currentDomain = window.location.origin
      const path = videoUrl.startsWith('/') ? videoUrl : `/${videoUrl}`
      videoUrl = `${currentDomain}${path}`
    }
    console.log('最终视频URL:', videoUrl)
    form.video = videoUrl
    ElMessage.success('视频上传成功')
  } else {
    ElMessage.error('视频上传失败')
  }
}

// 移除图片
const handleRemoveImage = (file: any, fileList: any[]) => {
  additionalImages.value = fileList.map(item => ({
    name: item.name,
    url: item.url,
    uid: item.uid
  }))
  ElMessage.success('图片已移除')
}

// 图片加载成功
const handleImageLoad = () => {
  console.log('图片加载成功')
  ElMessage.success('图片加载成功')
}

// 图片加载失败
const handleImageError = (error: any) => {
  console.error('图片加载失败:', error)
  console.log('当前图片URL:', form.mainImage)
  ElMessage.error('图片加载失败，请检查图片URL')
}

// 图片上传前验证
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

// 视频上传前验证
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

// 返回
const goBack = () => {
  router.back()
}

onMounted(async () => {
  await fetchCategories()

  // 检查是否是编辑模式
  const id = route.query.id as string
  if (id) {
    isEdit.value = true
    productId.value = Number(id)
    await fetchProductDetail(productId.value)
  }
})
</script>

<style scoped>
.add-product-page {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.image-upload-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.image-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: border-color 0.3s;
  width: 200px;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-uploader:hover {
  border-color: #409eff;
}

.image-uploader-icon {
  font-size: 28px;
  color: #8c939d;
}

.uploaded-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-tips {
  font-size: 12px;
  color: #999;
  line-height: 1.4;
}

.upload-tips p {
  margin: 0;
}

/* 附图上传样式 */
.images-upload-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.images-uploader {
  :deep(.el-upload-list--picture-card) {
    .el-upload-list__item {
      width: 100px;
      height: 100px;
    }
  }
}

/* 视频上传样式 */
.video-upload-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.video-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: border-color 0.3s;
  width: 300px;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.video-uploader:hover {
  border-color: #409eff;
}

.video-preview {
  position: relative;
  width: 100%;
  height: 100%;
}

.uploaded-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.video-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.video-icon {
  font-size: 24px;
  color: white;
}

.video-upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #8c939d;
}

.video-uploader-icon {
  font-size: 48px;
  margin-bottom: 10px;
}

.upload-text {
  font-size: 14px;
}
</style>

