<template>
  <div class="product-basic-info">
    <el-form-item :label="$t('products.productName')" prop="name">
      <el-input v-model="form.name" :placeholder="$t('products.enterProductName')" />
    </el-form-item>

    <el-form-item label="商品分类" prop="categoryId">
      <el-select 
        v-model="form.categoryId" 
        placeholder="请选择商品分类"
        style="width: 100%"
        @change="updateForm('categoryId', form.categoryId)"
      >
        <!-- 先显示所有分类，不分组 -->
        <el-option 
          v-for="category in allCategories" 
          :key="category.id"
          :label="category.name"
          :value="category.id"
        />
      </el-select>
    </el-form-item>

    <el-form-item :label="$t('products.brand')" prop="brand">
      <el-input v-model="form.brand" :placeholder="$t('products.brand')" />
    </el-form-item>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { ProductForm, CategoryOption } from '../types/product'

interface Props {
  form: ProductForm
  categories: CategoryOption[]
}

interface Emits {
  (e: 'update:form', form: ProductForm): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 直接显示所有分类
const allCategories = computed(() => {
  console.log('ProductBasicInfo - allCategories 计算属性被调用:', props.categories?.length)
  
  if (!Array.isArray(props.categories) || props.categories.length === 0) {
    console.log('ProductBasicInfo - allCategories 返回空数组')
    return []
  }
  
  console.log('ProductBasicInfo - allCategories 返回分类数量:', props.categories.length)
  console.log('ProductBasicInfo - allCategories 前3个分类:', props.categories.slice(0, 3))
  
  return props.categories
})

// 将平铺的分类数据转换为分组结构
const groupedCategories = computed(() => {
  console.log('ProductBasicInfo - 开始处理分类数据:', props.categories?.length)
  
  // 确保 categories 是数组
  if (!Array.isArray(props.categories) || props.categories.length === 0) {
    console.log('ProductBasicInfo - 分类数据为空或不是数组')
    return []
  }
  
  // 获取一级分类（parentId为"0"或0的分类）
  const parentCategories = props.categories.filter(cat => 
    String(cat.parentId) === "0" || cat.parentId === 0
  )
  
  console.log('ProductBasicInfo - 找到一级分类数量:', parentCategories.length)
  console.log('ProductBasicInfo - 一级分类示例:', parentCategories.slice(0, 3))
  
  const result = parentCategories.map(parent => {
    const children = props.categories.filter(cat => 
      String(cat.parentId) === String(parent.id)
    )
    
    console.log(`ProductBasicInfo - 分类"${parent.name}"的子分类数量:`, children.length)
    
    return {
      ...parent,
      children
    }
  })
  
  console.log('ProductBasicInfo - 最终分组结果:', result.length, '个分组')
  return result
})

const updateForm = (field: keyof ProductForm, value: any) => {
  const updatedForm = { ...props.form, [field]: value }
  emit('update:form', updatedForm)
}
</script>

<style scoped lang="scss">
.product-basic-info {
  margin-bottom: 24px;
}
</style>
