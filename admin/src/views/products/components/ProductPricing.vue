<template>
  <div class="product-pricing">
    <el-form-item :label="$t('products.costPrice')" prop="costPrice">
      <el-input-number
        v-model="costPrice"
        :min="0"
        :precision="2"
        :placeholder="$t('products.enterCostPrice')"
        @change="updateCostPrice"
      />
    </el-form-item>

    <el-form-item :label="$t('products.suggestedPrice')" prop="suggestPrice">
      <el-input-number
        v-model="suggestPrice"
        :min="0"
        :precision="2"
        :placeholder="$t('products.enterSuggestedPrice')"
        @change="updateSuggestPrice"
      />
    </el-form-item>

    <el-form-item :label="$t('products.stock')" prop="stock">
      <el-input-number
        key="main-stock"
        v-model="stock"
        :min="0"
        :placeholder="$t('products.enterStock')"
        @change="updateStock"
      />
    </el-form-item>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  costPrice: number
  suggestPrice: number
  stock: number
}

interface Emits {
  (e: 'update:costPrice', value: number): void
  (e: 'update:suggestPrice', value: number): void
  (e: 'update:stock', value: number): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const costPrice = computed({
  get: () => props.costPrice,
  set: (value) => emit('update:costPrice', value)
})

const suggestPrice = computed({
  get: () => props.suggestPrice,
  set: (value) => emit('update:suggestPrice', value)
})

const stock = computed({
  get: () => props.stock,
  set: (value) => emit('update:stock', value)
})

const updateCostPrice = (value: number) => {
  emit('update:costPrice', value)
}

const updateSuggestPrice = (value: number) => {
  emit('update:suggestPrice', value)
}

const updateStock = (value: number) => {
  emit('update:stock', value)
}
</script>

<style scoped lang="scss">
.product-pricing {
  margin-bottom: 24px;
}
</style>
