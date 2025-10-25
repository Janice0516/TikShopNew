<template>
  <div class="mobile-search">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      
      <!-- Search Input -->
      <div class="search-input-container">
        <div class="search-input-wrapper">
          <div class="search-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
              <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            :placeholder="t('search.placeholder')"
            class="search-input"
            @input="handleSearchInput"
            @keyup.enter="performSearch"
            @focus="showSuggestions = true"
          />
          <button 
            v-if="searchQuery" 
            class="clear-btn"
            @click="clearSearch"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
      </div>
      
      <button class="search-btn" @click="performSearch" :disabled="!searchQuery.trim()">
        {{ t('search.search') }}
      </button>
    </div>

    <!-- Search Content -->
    <div class="search-content">
      <!-- Search Suggestions -->
      <div v-if="showSuggestions && !hasSearched" class="suggestions-section">
        <!-- Search History -->
        <div v-if="searchHistory.length > 0" class="history-section">
          <div class="section-header">
            <h3>{{ t('search.recentSearches') }}</h3>
            <button class="clear-history-btn" @click="clearHistory">
              {{ t('search.clearHistory') }}
            </button>
          </div>
          <div class="history-items">
            <div 
              v-for="(item, index) in searchHistory" 
              :key="index"
              class="history-item"
              @click="selectHistoryItem(item)"
            >
              <div class="history-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="history-text">{{ item }}</span>
              <button class="remove-history-btn" @click.stop="removeHistoryItem(index)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
                  <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Hot Searches -->
        <div class="hot-searches-section">
          <div class="section-header">
            <h3>{{ t('search.hotSearches') }}</h3>
          </div>
          <div class="hot-search-tags">
            <button 
              v-for="(tag, index) in hotSearches" 
              :key="index"
              class="hot-search-tag"
              @click="selectHotSearch(tag)"
            >
              {{ tag }}
            </button>
          </div>
        </div>

        <!-- Search Suggestions -->
        <div v-if="searchSuggestions.length > 0" class="suggestions-section">
          <div class="section-header">
            <h3>{{ t('search.suggestions') }}</h3>
          </div>
          <div class="suggestion-items">
            <div 
              v-for="(suggestion, index) in searchSuggestions" 
              :key="index"
              class="suggestion-item"
              @click="selectSuggestion(suggestion)"
            >
              <div class="suggestion-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                  <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="suggestion-text">{{ suggestion }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Results -->
      <div v-else-if="hasSearched" class="search-results">
        <!-- Results Header -->
        <div class="results-header">
          <div class="results-info">
            <h3>{{ t('search.resultsFor', { query: searchQuery }) }}</h3>
            <p>{{ t('search.resultsCount', { count: searchResults.length }) }}</p>
          </div>
          <div class="results-filters">
            <button class="filter-btn" @click="showFilters = true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              {{ t('search.filters') }}
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>{{ t('search.searching') }}</p>
        </div>

        <!-- Empty Results -->
        <div v-else-if="searchResults.length === 0" class="empty-results">
          <div class="empty-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
              <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
              <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3 class="empty-title">{{ t('search.noResults') }}</h3>
          <p class="empty-description">{{ t('search.noResultsDescription') }}</p>
          <div class="empty-suggestions">
            <p>{{ t('search.tryDifferentKeywords') }}</p>
            <div class="suggestion-keywords">
              <button 
                v-for="keyword in suggestedKeywords" 
                :key="keyword"
                class="keyword-btn"
                @click="searchKeyword(keyword)"
              >
                {{ keyword }}
              </button>
            </div>
          </div>
        </div>

        <!-- Results List -->
        <div v-else class="results-list">
          <div 
            v-for="product in searchResults" 
            :key="product.id"
            class="result-item"
            @click="goToProduct(product)"
          >
            <div class="product-image">
              <img :src="product.image" :alt="product.name" />
            </div>
            <div class="product-info">
              <h4 class="product-name">{{ product.name }}</h4>
              <p class="product-description">{{ product.description }}</p>
              <div class="product-meta">
                <div class="product-price">RM{{ formatPrice(product.price) }}</div>
                <div class="product-rating">
                  <span class="rating-stars">★★★★★</span>
                  <span class="rating-count">({{ product.ratingCount }})</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Default State -->
      <div v-else class="default-state">
        <div class="default-icon">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="default-title">{{ t('search.searchProducts') }}</h3>
        <p class="default-description">{{ t('search.searchDescription') }}</p>
        
        <!-- Popular Categories -->
        <div class="popular-categories">
          <h4>{{ t('search.popularCategories') }}</h4>
          <div class="category-tags">
            <button 
              v-for="category in popularCategories" 
              :key="category.id"
              class="category-tag"
              @click="searchCategory(category)"
            >
              <img :src="category.icon" :alt="category.name" class="category-icon" />
              <span>{{ category.name }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Modal -->
    <div v-if="showFilters" class="filter-modal" @click="showFilters = false">
      <div class="filter-content" @click.stop>
        <div class="filter-header">
          <h3>{{ t('search.filters') }}</h3>
          <button class="close-btn" @click="showFilters = false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        
        <div class="filter-options">
          <!-- Price Range -->
          <div class="filter-group">
            <h4>{{ t('search.priceRange') }}</h4>
            <div class="price-range">
              <input 
                v-model="filters.minPrice" 
                type="number" 
                :placeholder="t('search.minPrice')"
                class="price-input"
              />
              <span class="price-separator">-</span>
              <input 
                v-model="filters.maxPrice" 
                type="number" 
                :placeholder="t('search.maxPrice')"
                class="price-input"
              />
            </div>
          </div>

          <!-- Category -->
          <div class="filter-group">
            <h4>{{ t('search.category') }}</h4>
            <select v-model="filters.category" class="category-select">
              <option value="">{{ t('search.allCategories') }}</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- Sort By -->
          <div class="filter-group">
            <h4>{{ t('search.sortBy') }}</h4>
            <select v-model="filters.sortBy" class="sort-select">
              <option value="relevance">{{ t('search.relevance') }}</option>
              <option value="price_asc">{{ t('search.priceAsc') }}</option>
              <option value="price_desc">{{ t('search.priceDesc') }}</option>
              <option value="rating">{{ t('search.rating') }}</option>
              <option value="newest">{{ t('search.newest') }}</option>
            </select>
          </div>
        </div>

        <div class="filter-actions">
          <button class="reset-btn" @click="resetFilters">{{ t('search.reset') }}</button>
          <button class="apply-btn" @click="applyFilters">{{ t('search.apply') }}</button>
        </div>
      </div>
    </div>
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'

const router = useRouter()
const { t } = useI18n()

const searchInput = ref<HTMLInputElement>()
const searchQuery = ref('')
const showSuggestions = ref(false)
const hasSearched = ref(false)
const loading = ref(false)
const showFilters = ref(false)

// 搜索历史
const searchHistory = ref<string[]>([])
const searchSuggestions = ref<string[]>([])
const searchResults = ref<any[]>([])

// 热门搜索
const hotSearches = ref([
  'iPhone 15',
  'AirPods',
  'MacBook',
  'Samsung Galaxy',
  'Nike Shoes',
  'Adidas',
  'Laptop',
  'Headphones'
])

// 建议关键词
const suggestedKeywords = ref([
  'iPhone',
  'Samsung',
  'Nike',
  'Adidas',
  'Laptop'
])

// 热门分类
const popularCategories = ref([
  { id: 1, name: 'Electronics', icon: 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=60&h=60&fit=crop' },
  { id: 2, name: 'Fashion', icon: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=60&h=60&fit=crop' },
  { id: 3, name: 'Home & Garden', icon: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=60&h=60&fit=crop' },
  { id: 4, name: 'Sports', icon: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=60&h=60&fit=crop' }
])

// 分类数据
const categories = ref([
  { id: 1, name: 'Electronics' },
  { id: 2, name: 'Fashion' },
  { id: 3, name: 'Home & Garden' },
  { id: 4, name: 'Sports' }
])

// 过滤器
const filters = ref({
  minPrice: '',
  maxPrice: '',
  category: '',
  sortBy: 'relevance'
})

// 方法
const goBack = () => {
  router.back()
}

const handleSearchInput = () => {
  if (searchQuery.value.trim()) {
    // 模拟搜索建议
    searchSuggestions.value = [
      `${searchQuery.value} Pro`,
      `${searchQuery.value} Max`,
      `${searchQuery.value} Case`,
      `${searchQuery.value} Charger`
    ]
  } else {
    searchSuggestions.value = []
  }
}

const performSearch = async () => {
  if (!searchQuery.value.trim()) return
  
  hasSearched.value = true
  showSuggestions.value = false
  loading.value = true
  isLoading.value = true
  hasError.value = false
  
  // 添加到搜索历史
  addToHistory(searchQuery.value)
  
  try {
    // 调用搜索API
    const response = await productApi.searchProducts({
      keyword: searchQuery.value,
      page: 1,
      limit: 20
    })
    
    // 更新搜索结果
    searchResults.value = response.data || []
    
    console.log('搜索结果加载完成:', searchResults.value.length)
  } catch (error) {
    console.error('搜索失败:', error)
    hasError.value = true
    ElMessage.error('搜索失败，请重试')
  } finally {
    loading.value = false
    isLoading.value = false
  }
}

const clearSearch = () => {
  searchQuery.value = ''
  searchSuggestions.value = []
  hasSearched.value = false
  showSuggestions.value = true
  nextTick(() => {
    searchInput.value?.focus()
  })
}

const selectHistoryItem = (item: string) => {
  searchQuery.value = item
  performSearch()
}

const selectHotSearch = (tag: string) => {
  searchQuery.value = tag
  performSearch()
}

const selectSuggestion = (suggestion: string) => {
  searchQuery.value = suggestion
  performSearch()
}

const searchKeyword = (keyword: string) => {
  searchQuery.value = keyword
  performSearch()
}

const searchCategory = (category: any) => {
  router.push(`/mobile/category/${category.id}`)
}

const goToProduct = (product: any) => {
  router.push(`/mobile/product/${product.id}`)
}

const addToHistory = (query: string) => {
  const history = searchHistory.value.filter(item => item !== query)
  history.unshift(query)
  searchHistory.value = history.slice(0, 10) // 最多保存10条历史记录
}

const removeHistoryItem = (index: number) => {
  searchHistory.value.splice(index, 1)
}

const clearHistory = () => {
  searchHistory.value = []
}

const resetFilters = () => {
  filters.value = {
    minPrice: '',
    maxPrice: '',
    category: '',
    sortBy: 'relevance'
  }
}

const applyFilters = () => {
  showFilters.value = false
  // 这里可以重新执行搜索
  performSearch()
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

// 点击外部关闭建议
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.search-input-container') && !target.closest('.suggestions-section')) {
    showSuggestions.value = false
  }
}

onMounted(() => {
  // 从localStorage加载搜索历史
  const savedHistory = localStorage.getItem('searchHistory')
  if (savedHistory) {
    searchHistory.value = JSON.parse(savedHistory)
  }
  
  // 添加点击外部事件监听
  document.addEventListener('click', handleClickOutside)
  
  // 自动聚焦搜索框
  nextTick(() => {
    searchInput.value?.focus()
  })
})

// 保存搜索历史到localStorage
const saveHistory = () => {
  localStorage.setItem('searchHistory', JSON.stringify(searchHistory.value))
}

// 监听搜索历史变化
const unwatchHistory = computed(() => searchHistory.value)
unwatchHistory.value && saveHistory()
</script>

<style scoped lang="scss">
.mobile-search {
  min-height: 100vh;
  background: #000;
  color: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 80px; // 为底部导航栏留出空间
}

.mobile-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  background: #000;
  border-bottom: 1px solid #333;
  position: sticky;
  top: 0;
  z-index: 100;

  .back-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }

  .search-input-container {
    flex: 1;

    .search-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      background: #1a1a1a;
      border: 1px solid #333;
      border-radius: 12px;
      padding: 0 16px;
      transition: border-color 0.2s;

      &:focus-within {
        border-color: #ff0050;
      }

      .search-icon {
        color: #666;
        margin-right: 12px;
      }

      .search-input {
        flex: 1;
        background: none;
        border: none;
        color: #fff;
        font-size: 16px;
        padding: 14px 0;
        outline: none;

        &::placeholder {
          color: #666;
        }
      }

      .clear-btn {
        background: none;
        border: none;
        color: #666;
        padding: 4px;
        border-radius: 4px;
        cursor: pointer;
        transition: color 0.2s;

        &:hover {
          color: #fff;
        }
      }
    }
  }

  .search-btn {
    background: #ff0050;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;

    &:hover:not(:disabled) {
      background: #e6004a;
    }

    &:disabled {
      background: #333;
      color: #666;
      cursor: not-allowed;
    }
  }
}

.search-content {
  padding: 20px;
}

.suggestions-section {
  .history-section,
  .hot-searches-section,
  .suggestions-section {
    margin-bottom: 24px;

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;

      h3 {
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        margin: 0;
      }

      .clear-history-btn {
        background: none;
        border: none;
        color: #666;
        font-size: 14px;
        cursor: pointer;
        transition: color 0.2s;

        &:hover {
          color: #fff;
        }
      }
    }

    .history-items {
      .history-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        cursor: pointer;
        border-bottom: 1px solid #222;

        &:last-child {
          border-bottom: none;
        }

        &:hover {
          background: #222;
          margin: 0 -20px;
          padding: 12px 20px;
        }

        .history-icon {
          color: #666;
          margin-right: 12px;
        }

        .history-text {
          flex: 1;
          font-size: 16px;
          color: #fff;
        }

        .remove-history-btn {
          background: none;
          border: none;
          color: #666;
          padding: 4px;
          border-radius: 4px;
          cursor: pointer;
          transition: color 0.2s;

          &:hover {
            color: #fff;
          }
        }
      }
    }

    .hot-search-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;

      .hot-search-tag {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;

        &:hover {
          background: #ff0050;
          border-color: #ff0050;
        }
      }
    }

    .suggestion-items {
      .suggestion-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        cursor: pointer;
        border-bottom: 1px solid #222;

        &:last-child {
          border-bottom: none;
        }

        &:hover {
          background: #222;
          margin: 0 -20px;
          padding: 12px 20px;
        }

        .suggestion-icon {
          color: #666;
          margin-right: 12px;
        }

        .suggestion-text {
          font-size: 16px;
          color: #fff;
        }
      }
    }
  }
}

.search-results {
  .results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;

    .results-info {
      h3 {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        margin: 0 0 4px 0;
      }

      p {
        font-size: 14px;
        color: #ccc;
        margin: 0;
      }
    }

    .results-filters {
      .filter-btn {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;

        &:hover {
          background: #333;
        }
      }
    }
  }

  .loading-container {
    text-align: center;
    padding: 60px 20px;

    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 3px solid #333;
      border-top: 3px solid #ff0050;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 16px;
    }

    p {
      color: #ccc;
      margin: 0;
    }
  }

  .empty-results {
    text-align: center;
    padding: 60px 20px;

    .empty-icon {
      color: #666;
      margin-bottom: 24px;
    }

    .empty-title {
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 12px 0;
      color: #fff;
    }

    .empty-description {
      font-size: 14px;
      color: #ccc;
      margin: 0 0 24px 0;
      line-height: 1.5;
    }

    .empty-suggestions {
      p {
        font-size: 14px;
        color: #ccc;
        margin: 0 0 16px 0;
      }

      .suggestion-keywords {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;

        .keyword-btn {
          background: #1a1a1a;
          color: #fff;
          border: 1px solid #333;
          border-radius: 20px;
          padding: 8px 16px;
          font-size: 14px;
          cursor: pointer;
          transition: all 0.2s;

          &:hover {
            background: #ff0050;
            border-color: #ff0050;
          }
        }
      }
    }
  }

  .results-list {
    .result-item {
      display: flex;
      align-items: center;
      padding: 16px 0;
      border-bottom: 1px solid #222;
      cursor: pointer;
      transition: background-color 0.2s;

      &:last-child {
        border-bottom: none;
      }

      &:hover {
        background: #222;
        margin: 0 -20px;
        padding: 16px 20px;
      }

      .product-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        margin-right: 16px;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }

      .product-info {
        flex: 1;

        .product-name {
          font-size: 16px;
          font-weight: 500;
          color: #fff;
          margin: 0 0 4px 0;
          line-height: 1.3;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .product-description {
          font-size: 12px;
          color: #ccc;
          margin: 0 0 8px 0;
          line-height: 1.3;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .product-meta {
          display: flex;
          justify-content: space-between;
          align-items: center;

          .product-price {
            font-size: 16px;
            font-weight: 600;
            color: #ff0050;
          }

          .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;

            .rating-stars {
              color: #ffc107;
              font-size: 12px;
            }

            .rating-count {
              font-size: 12px;
              color: #ccc;
            }
          }
        }
      }
    }
  }
}

.default-state {
  text-align: center;
  padding: 60px 20px;

  .default-icon {
    color: #666;
    margin-bottom: 24px;
  }

  .default-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 12px 0;
    color: #fff;
  }

  .default-description {
    font-size: 14px;
    color: #ccc;
    margin: 0 0 32px 0;
    line-height: 1.5;
  }

  .popular-categories {
    h4 {
      font-size: 16px;
      font-weight: 600;
      color: #fff;
      margin: 0 0 16px 0;
    }

    .category-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      justify-content: center;

      .category-tag {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;

        &:hover {
          background: #ff0050;
          border-color: #ff0050;
        }

        .category-icon {
          width: 20px;
          height: 20px;
          border-radius: 4px;
          object-fit: cover;
        }
      }
    }
  }
}

.filter-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  z-index: 1000;
  display: flex;
  align-items: flex-end;

  .filter-content {
    background: #1a1a1a;
    border-radius: 16px 16px 0 0;
    width: 100%;
    max-height: 70vh;
    overflow: hidden;

    .filter-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 1px solid #333;

      h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: #fff;
      }

      .close-btn {
        background: none;
        border: none;
        color: #fff;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;

        &:hover {
          background: #333;
        }
      }
    }

    .filter-options {
      padding: 20px;
      max-height: 50vh;
      overflow-y: auto;

      .filter-group {
        margin-bottom: 24px;

        h4 {
          font-size: 16px;
          font-weight: 500;
          color: #fff;
          margin: 0 0 12px 0;
        }

        .price-range {
          display: flex;
          align-items: center;
          gap: 12px;

          .price-input {
            flex: 1;
            background: #333;
            border: 1px solid #555;
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 14px;

            &::placeholder {
              color: #999;
            }

            &:focus {
              outline: none;
              border-color: #ff0050;
            }
          }

          .price-separator {
            color: #ccc;
            font-size: 16px;
          }
        }

        .category-select,
        .sort-select {
          width: 100%;
          background: #333;
          border: 1px solid #555;
          border-radius: 8px;
          padding: 12px;
          color: #fff;
          font-size: 14px;
          cursor: pointer;

          &:focus {
            outline: none;
            border-color: #ff0050;
          }

          option {
            background: #333;
            color: #fff;
          }
        }
      }
    }

    .filter-actions {
      display: flex;
      gap: 12px;
      padding: 20px;
      border-top: 1px solid #333;

      .reset-btn {
        flex: 1;
        background: #333;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;

        &:hover {
          background: #555;
        }
      }

      .apply-btn {
        flex: 1;
        background: #ff0050;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;

        &:hover {
          background: #e6004a;
        }
      }
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// 响应式设计
@media (max-width: 480px) {
  .search-content {
    padding: 16px;
  }

  .mobile-header {
    padding: 12px 16px;
    gap: 8px;

    .search-btn {
      padding: 10px 12px;
      font-size: 13px;
    }
  }

  .results-list .result-item {
    .product-image {
      width: 70px;
      height: 70px;
    }

    .product-info .product-name {
      font-size: 15px;
    }
  }

  .popular-categories .category-tags {
    gap: 8px;

    .category-tag {
      padding: 10px 12px;
      font-size: 13px;
    }
  }
}

// 全局移动端样式
:global(body) {
  background: #000;
  color: #fff;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}
</style>
