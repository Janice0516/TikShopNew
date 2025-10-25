<template>
  <div class="weight-variant-form">
    <el-row :gutter="20">
      <el-col :span="6">
        <el-form-item label="重量名称">
          <el-input 
            v-model="variantData.name" 
            placeholder="如：500g装、1kg装"
            @input="updateVariant"
          />
        </el-form-item>
      </el-col>
      <el-col :span="6">
        <el-form-item label="重量值">
          <el-input 
            v-model="variantData.weight" 
            placeholder="如：500、1"
            @input="updateVariant"
          />
        </el-form-item>
      </el-col>
      <el-col :span="6">
        <el-form-item label="单位">
          <div class="unit-buttons">
            <el-button 
              v-for="unit in weightUnits" 
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
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
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

const variantData = ref<ProductVariant>({ ...props.variant })

// 重量变体专用单位
const weightUnits: Array<{ label: string; value: 'g' | 'kg' | 'box' }> = [
  { label: '克', value: 'g' },
  { label: '千克', value: 'kg' },
  { label: '盒', value: 'box' }
]

// 监听props变化
watch(() => props.variant, (newVariant) => {
  variantData.value = { ...newVariant }
}, { deep: true })

const updateVariant = () => {
  emit('update:variant', { ...variantData.value })
}
</script>

<style scoped lang="scss">
.weight-variant-form {
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
</style>
