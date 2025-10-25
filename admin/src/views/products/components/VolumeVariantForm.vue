<template>
  <div class="volume-variant-form">
    <el-row :gutter="20">
      <el-col :span="6">
        <el-form-item label="容量名称">
          <el-input 
            v-model="variantData.name" 
            placeholder="如：50ml装、100ml装"
            @input="updateVariant"
          />
        </el-form-item>
      </el-col>
      <el-col :span="6">
        <el-form-item label="容量值">
          <el-input 
            v-model="variantData.volume" 
            placeholder="如：50、100"
            @input="updateVariant"
          />
        </el-form-item>
      </el-col>
      <el-col :span="6">
        <el-form-item label="单位">
          <div class="unit-buttons">
            <el-button 
              v-for="unit in volumeUnits" 
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

// 容量变体专用单位
const volumeUnits: Array<{ label: string; value: 'ml' | 'l' | 'bottle' }> = [
  { label: '毫升', value: 'ml' },
  { label: '升', value: 'l' },
  { label: '瓶', value: 'bottle' }
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
.volume-variant-form {
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
