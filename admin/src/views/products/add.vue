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
              :on-success="handleImageSuccess"
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
              <p>点击上传图片，支持 JPG、PNG、GIF 格式</p>
              <p>图片大小不超过 5MB</p>
            </div>
          </div>
        </el-form-item>

        <el-form-item label="Cost Price (USD)" prop="costPrice">
          <el-input-number
            v-model="form.costPrice"
            :min="0"
            :precision="2"
            placeholder="Enter cost price in USD"
          />
        </el-form-item>

        <el-form-item label="Suggested Price (USD)" prop="suggestPrice">
          <el-input-number
            v-model="form.suggestPrice"
            :min="0"
            :precision="2"
            placeholder="Enter suggested price in USD"
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
import { Plus } from '@element-plus/icons-vue'
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
const uploadHeaders = ref({
  Authorization: `Bearer ${userStore.token}`
})

const form = reactive({
  name: '',
  categoryId: null as number | null,
  brand: '',
  mainImage: '',
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
      costPrice: product.costPrice,
      suggestPrice: product.suggestPrice,
      stock: product.stock,
      description: product.description
    })
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

// 图片上传成功
const handleImageSuccess = (response: any) => {
  console.log('上传响应:', response)
  if (response && response.url) {
    // 确保图片URL是绝对路径，避免base路径问题
    let imageUrl = response.url
    console.log('原始URL:', imageUrl)
    console.log('VITE_API_BASE_URL:', import.meta.env.VITE_API_BASE_URL)
    
    if (!imageUrl.startsWith('http')) {
      // 使用当前页面的域名作为基础URL，确保静态文件能正确访问
      const currentDomain = window.location.origin // 例如: http://202.146.222.134
      console.log('当前域名:', currentDomain)
      
      // 确保路径以 / 开头
      const path = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`
      imageUrl = `${currentDomain}${path}`
    }
    console.log('最终图片URL:', imageUrl)
    form.mainImage = imageUrl
    ElMessage.success('图片上传成功')
  } else {
    ElMessage.error('图片上传失败')
  }
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
</style>

