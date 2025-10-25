<template>
  <div class="product-add-page">
    <el-card>
      <template #header>
        <h2>{{ isEdit ? $t('products.editProduct') : $t('products.addProduct') }}</h2>
      </template>
      
      <el-form ref="formRef" :model="form" :rules="rules" label-width="120px">
        <!-- 基本信息模块 -->
        <ProductBasicInfo 
          :form="form"
          :categories="categories"
          @update:form="updateForm"
        />
        
        <!-- 媒体上传模块 -->
        <ProductMediaUpload 
          v-model:mainImage="form.mainImage"
          v-model:images="form.images"
          v-model:video="form.video"
          @upload-success="handleUploadSuccess"
        />
        
        <!-- 价格管理模块 -->
        <ProductPricing 
          v-model:costPrice="form.costPrice"
          v-model:suggestPrice="form.suggestPrice"
          v-model:stock="form.stock"
        />
        
        <!-- 变体管理模块 -->
        <el-form-item :label="$t('products.hasVariants')">
          <el-switch 
            v-model="hasVariants" 
            :active-text="$t('products.enableVariants')"
            @change="toggleVariants"
          />
        </el-form-item>
        
        <ProductVariants 
          v-if="hasVariants"
          v-model:variants="form.variants"
          @variant-change="handleVariantChange"
        />
        
        <!-- 描述模块 -->
        <ProductDescription 
          v-model:description="form.description"
        />
        
        <el-form-item>
          <el-button type="primary" @click="handleSubmit" :loading="submitLoading">
            {{ isEdit ? $t('common.save') : $t('common.add') }}
          </el-button>
          <el-button @click="goBack">{{ $t('common.cancel') }}</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { ElMessage, type FormInstance } from 'element-plus'

// 导入模块组件
import ProductBasicInfo from './components/ProductBasicInfo.vue'
import ProductMediaUpload from './components/ProductMediaUpload.vue'
import ProductPricing from './components/ProductPricing.vue'
import ProductVariants from './components/ProductVariants.vue'
import ProductDescription from './components/ProductDescription.vue'

// 导入组合式函数
import { useProductForm } from './composables/useProductForm'
import { useProductValidation } from './composables/useProductValidation'
import { useProductSubmit } from './composables/useProductSubmit'
import type { ProductForm, ProductVariant } from './types/product'

const { t } = useI18n()

// 使用组合式函数
const { form, loading, isEdit, productId, categories, goBack } = useProductForm()
const { rules, validateForm } = useProductValidation()
const { loading: submitLoading, submitProduct } = useProductSubmit()

const formRef = ref<FormInstance>()
const hasVariants = ref(false)

// 监听form.variants变化，但不自动设置hasVariants
// 变体开关由用户手动控制，不自动启用
// watch(() => form.variants, (variants) => {
//   hasVariants.value = variants && variants.length > 0
// }, { immediate: true })

// 切换变体功能
const toggleVariants = (enabled: boolean) => {
  if (!enabled) {
    // 禁用变体时，清空变体数据
    form.variants = []
  } else {
    // 启用变体时，添加默认变体
    if (form.variants.length === 0) {
      form.variants = [
        {
          name: 'BURGUNDY',
          image: '',
          unit: 'pcs',
          volume: '',
          weight: '',
          sizes: [
            { name: 'S', stock: 0 },
            { name: 'M', stock: 0 },
            { name: 'L', stock: 0 },
            { name: 'XL', stock: 0 },
            { name: 'XXL', stock: 0 },
            { name: '3XL', stock: 0 }
          ]
        }
      ]
    }
  }
}

// 更新表单数据
const updateForm = (updatedForm: ProductForm) => {
  Object.assign(form, updatedForm)
}

// 处理上传成功
const handleUploadSuccess = (type: string, response: any) => {
  console.log(`${type} 上传成功:`, response)
}

// 处理变体变化
const handleVariantChange = (variant: ProductVariant, index: number) => {
  // 暂时禁用console.log以避免响应式代理问题
  // const cleanVariant = JSON.parse(JSON.stringify(variant))
  // console.log(`变体 ${index} 更新:`, cleanVariant)
}

// 表单提交
const handleSubmit = async () => {
  if (!formRef.value) return
  
  const isValid = await validateForm(formRef.value)
  if (isValid) {
    await submitProduct(form, isEdit.value, productId.value)
  }
}
</script>

<style scoped lang="scss">
.product-add-page {
  padding: 20px;
}

h2 {
  margin: 0;
  color: #303133;
  font-size: 20px;
  font-weight: 500;
}
</style>