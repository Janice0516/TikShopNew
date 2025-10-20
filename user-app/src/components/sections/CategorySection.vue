<template>
  <section class="categories-section">
    <h2 class="section-title">Categories</h2>
    <div class="categories-container">
      <div class="categories-scroll" ref="scrollContainer">
        <div 
          v-for="category in categories" 
          :key="category.id"
          class="category-item"
          @click="handleCategoryClick(category)"
        >
          <div class="category-icon">
            <img :src="category.icon" :alt="category.name" />
          </div>
          <span class="category-name">{{ category.name }}</span>
        </div>
      </div>
      <button 
        class="scroll-arrow scroll-right" 
        @click="scrollRight"
        v-show="canScrollRight"
      >
        →
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Category {
  id: string | number
  name: string
  icon: string
}

interface Props {
  categories: Category[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  categoryClick: [category: Category]
}>()

const scrollContainer = ref<HTMLElement>()
const scrollPosition = ref(0)
const containerWidth = ref(0)
const scrollWidth = ref(0)

const canScrollRight = computed(() => {
  return scrollPosition.value < scrollWidth.value - containerWidth.value
})

const handleCategoryClick = (category: Category) => {
  emit('categoryClick', category)
}

const scrollRight = () => {
  if (scrollContainer.value) {
    const scrollAmount = 200
    scrollContainer.value.scrollBy({
      left: scrollAmount,
      behavior: 'smooth'
    })
  }
}

const updateScrollInfo = () => {
  if (scrollContainer.value) {
    scrollPosition.value = scrollContainer.value.scrollLeft
    containerWidth.value = scrollContainer.value.clientWidth
    scrollWidth.value = scrollContainer.value.scrollWidth
  }
}

const handleScroll = () => {
  updateScrollInfo()
}

onMounted(() => {
  if (scrollContainer.value) {
    scrollContainer.value.addEventListener('scroll', handleScroll)
    updateScrollInfo()
  }
})

onUnmounted(() => {
  if (scrollContainer.value) {
    scrollContainer.value.removeEventListener('scroll', handleScroll)
  }
})
</script>

<style scoped lang="scss">
.categories-section {
  margin-bottom: 30px;
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin-bottom: 16px;
}

.categories-container {
  position: relative;
  display: flex;
  align-items: center;
}

.categories-scroll {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
  padding: 8px 0;
  scrollbar-width: none;
  -ms-overflow-style: none;

  &::-webkit-scrollbar {
    display: none;
  }
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 80px;
  cursor: pointer;
  transition: transform 0.2s;
  padding: 8px;

  &:hover {
    transform: translateY(-2px);
  }
}

.category-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 8px;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.category-name {
  font-size: 12px;
  font-weight: 500;
  color: #333;
  text-align: center;
  line-height: 1.2;
  max-width: 80px;
  word-wrap: break-word;
}

.scroll-arrow {
  position: absolute;
  right: -10px;
  top: 50%;
  transform: translateY(-50%);
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #e5e5e5;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 16px;
  color: #666;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
  z-index: 10;

  &:hover {
    background: white;
    color: #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
}
</style>
