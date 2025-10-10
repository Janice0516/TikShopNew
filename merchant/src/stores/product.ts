import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useProductStore = defineStore('product', () => {
  const categories = ref<any[]>([])
  const selectedCategory = ref<number | null>(null)

  const setCategories = (data: any[]) => {
    categories.value = data
  }

  const setSelectedCategory = (id: number | null) => {
    selectedCategory.value = id
  }

  return {
    categories,
    selectedCategory,
    setCategories,
    setSelectedCategory
  }
})

