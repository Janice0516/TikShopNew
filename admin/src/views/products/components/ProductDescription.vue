<template>
  <div class="product-description">
    <el-form-item :label="$t('products.productDescription')" prop="description">
      <el-input
        v-model="description"
        type="textarea"
        :rows="5"
        :placeholder="$t('products.productDescription')"
        @input="updateDescription"
      />
    </el-form-item>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  description: string
}

interface Emits {
  (e: 'update:description', value: string): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const description = computed({
  get: () => props.description,
  set: (value) => emit('update:description', value)
})

const updateDescription = (value: string) => {
  emit('update:description', value)
}
</script>

<style scoped lang="scss">
.product-description {
  margin-bottom: 24px;
}
</style>
