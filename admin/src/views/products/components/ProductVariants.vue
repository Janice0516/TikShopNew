<template>
  <el-form-item :label="$t('products.variants')">
    <div class="variants-container">
      <div class="variants-header">
        <el-button type="primary" @click="showVariantDialog = true" size="small">
          {{ $t('products.addVariant') }}
        </el-button>
      </div>
      
      <!-- 变体列表 -->
      <div class="variants-list" v-if="variants.length > 0">
        <div 
          v-for="(variant, index) in variants" 
          :key="`variant-${variant.type}-${index}-${variant.name}`"
          class="variant-item"
        >
          <!-- 变体标题 -->
          <div class="variant-header">
            <span class="variant-name">{{ variant.name }} ({{ getVariantTypeLabel(variant.type) }})</span>
            <div class="variant-actions">
              <el-button 
                type="danger" 
                size="small" 
                @click="removeVariant(index)"
              >
                删除
              </el-button>
            </div>
          </div>
          
          <!-- 动态变体表单 -->
          <component 
            :is="getVariantFormComponent(variant.type)"
            :variant="variant"
            :variant-index="index"
            @update:variant="(updatedVariant) => updateVariant(updatedVariant, index)"
          />
        </div>
      </div>
      
      <!-- 空状态 -->
      <div class="empty-state" v-else>
        <p>暂无变体，点击"添加变体"开始添加</p>
      </div>
    </div>
    
    <!-- 变体类型选择对话框 -->
    <el-dialog
      v-model="showVariantDialog"
      title="选择变体类型"
      width="500px"
      :before-close="handleClose"
    >
      <div class="variant-type-selector">
        <div class="type-options">
          <div 
            v-for="type in variantTypes" 
            :key="type.value"
            class="type-option"
            @click="selectVariantType(type)"
          >
            <div class="type-icon">{{ type.icon }}</div>
            <div class="type-info">
              <h4>{{ type.label }}</h4>
              <p>{{ type.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </el-dialog>
  </el-form-item>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import ColorVariantForm from './ColorVariantForm.vue'
import VolumeVariantForm from './VolumeVariantForm.vue'
import WeightVariantForm from './WeightVariantForm.vue'
import SizeVariantForm from './SizeVariantForm.vue'
import StyleVariantForm from './StyleVariantForm.vue'
import type { ProductVariant } from '../types/product'

interface Props {
  variants: ProductVariant[]
}

interface Emits {
  (e: 'update:variants', variants: ProductVariant[]): void
  (e: 'variant-change', variant: ProductVariant, index: number): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 对话框状态
const showVariantDialog = ref(false)

// 变体类型选项
const variantTypes = [
  {
    value: 'color',
    label: '颜色变体',
    icon: '🎨',
    description: '添加不同颜色的产品变体，支持颜色图片'
  },
  {
    value: 'size',
    label: '尺寸变体',
    icon: '📏',
    description: '添加不同尺寸的产品变体'
  },
  {
    value: 'volume',
    label: '容量变体',
    icon: '🧴',
    description: '添加不同容量的产品变体'
  },
  {
    value: 'weight',
    label: '重量变体',
    icon: '⚖️',
    description: '添加不同重量的产品变体'
  },
  {
    value: 'style',
    label: '款式变体',
    icon: '👕',
    description: '添加不同款式的产品变体'
  }
]

// 动态获取变体表单组件
const getVariantFormComponent = (type?: string) => {
  const componentMap = {
    color: ColorVariantForm,
    size: SizeVariantForm,
    volume: VolumeVariantForm,
    weight: WeightVariantForm,
    style: StyleVariantForm
  }
  return componentMap[type as keyof typeof componentMap] || ColorVariantForm
}

// 选择变体类型
const selectVariantType = (type: any) => {
  const newVariant: ProductVariant = {
    name: getDefaultName(type.value),
    image: '',
    unit: getDefaultUnit(type.value),
    volume: '',
    weight: '',
    sizes: [],
    type: type.value
  }
  
  const updatedVariants = [...props.variants, newVariant]
  emit('update:variants', updatedVariants)
  showVariantDialog.value = false
  
  ElMessage.success(`已添加${type.label}`)
}

// 获取默认名称
const getDefaultName = (type: string) => {
  const defaults: Record<string, string> = {
    color: '红色',
    size: 'S码',
    volume: '50ml',
    weight: '500g',
    style: '经典款'
  }
  return defaults[type] || '新变体'
}

// 获取默认单位
const getDefaultUnit = (type: string): 'pcs' | 'ml' | 'l' | 'g' | 'kg' | 'bottle' | 'box' | 'piece' => {
  const defaults: Record<string, 'pcs' | 'ml' | 'l' | 'g' | 'kg' | 'bottle' | 'box' | 'piece'> = {
    color: 'pcs',
    size: 'pcs',
    volume: 'ml',
    weight: 'g',
    style: 'pcs'
  }
  return defaults[type] || 'pcs'
}

// 获取变体类型标签
const getVariantTypeLabel = (type?: string) => {
  const typeMap: Record<string, string> = {
    color: '颜色',
    size: '尺寸',
    volume: '容量',
    weight: '重量',
    style: '款式'
  }
  return typeMap[type || ''] || '变体'
}

// 关闭对话框
const handleClose = () => {
  showVariantDialog.value = false
}

const removeVariant = (index: number) => {
  const updatedVariants = props.variants.filter((_, i) => i !== index)
  emit('update:variants', updatedVariants)
}

const updateVariant = (variant: ProductVariant, index: number) => {
  const updatedVariants = [...props.variants]
  
  // 彻底清理变体数据，确保所有字段都是有效的
  const cleanVariant: ProductVariant = {
    name: String(variant.name || ''),
    image: String(variant.image || ''),
    unit: variant.unit || 'pcs',
    volume: String(variant.volume || ''),
    weight: String(variant.weight || ''),
    sizes: Array.isArray(variant.sizes) ? variant.sizes : [],
    type: variant.type || 'color'
  }
  
  updatedVariants[index] = cleanVariant
  emit('update:variants', updatedVariants)
  emit('variant-change', cleanVariant, index)
}
</script>

<style scoped lang="scss">
.variants-container {
  margin-bottom: 24px;
}

.variants-header {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
  align-items: center;
}

.variants-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.variant-item {
  padding: 16px;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  background: #fafafa;
  margin-bottom: 12px;
}

.variant-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  
  .variant-name {
    font-weight: bold;
    font-size: 16px;
  }
  
  .variant-actions {
    display: flex;
    gap: 8px;
  }
}

.variant-details {
  margin-top: 12px;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #999;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px dashed #ddd;
}

.variant-type-selector {
  .type-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
  
  .type-option {
    display: flex;
    align-items: center;
    padding: 16px;
    border: 1px solid #e4e7ed;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    
    &:hover {
      border-color: #409eff;
      background: #f0f9ff;
    }
    
    .type-icon {
      font-size: 24px;
      margin-right: 12px;
    }
    
    .type-info {
      h4 {
        margin: 0 0 4px 0;
        font-size: 14px;
        color: #333;
      }
      
      p {
        margin: 0;
        font-size: 12px;
        color: #666;
      }
    }
  }
}

.unit-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  
  .el-button {
    margin: 0;
    min-width: 50px;
    font-size: 12px;
  }
}
</style>
