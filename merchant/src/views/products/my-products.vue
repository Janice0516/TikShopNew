<template>
  <div class="my-products">
    <div class="page-header">
      <h1>{{ $t('products.myProducts') }}</h1>
    </div>

    <!-- 统计卡片 -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-value">{{ stats.totalProducts }}</div>
        <div class="stat-label">{{ $t('products.totalProducts') }}</div>
      </div>
      <div class="stat-card active">
        <div class="stat-value">{{ stats.onShelfProducts }}</div>
        <div class="stat-label">{{ $t('products.onShelf') }}</div>
      </div>
      <div class="stat-card inactive">
        <div class="stat-value">{{ stats.offShelfProducts }}</div>
        <div class="stat-label">{{ $t('products.offShelf') }}</div>
      </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="filter-section">
      <div class="filter-group">
        <label>{{ $t('products.status') }}:</label>
        <select v-model="filters.status" @change="loadProducts">
          <option value="">{{ $t('common.all') }}</option>
          <option value="active">{{ $t('products.onShelf') }}</option>
          <option value="inactive">{{ $t('products.offShelf') }}</option>
        </select>
      </div>
      <div class="filter-group">
        <label>{{ $t('products.productName') }}:</label>
        <input 
          v-model="filters.keyword" 
          :placeholder="$t('products.searchPlaceholder')"
          @keyup.enter="loadProducts"
        />
        <button @click="loadProducts" class="search-btn">{{ $t('common.search') }}</button>
      </div>
    </div>

    <!-- 商品列表 -->
    <div class="products-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>{{ $t('products.productName') }}</th>
            <th>{{ $t('products.costPrice') }}</th>
            <th>{{ $t('products.salePrice') }}</th>
            <th>{{ $t('products.profit') }}</th>
            <th>{{ $t('products.stock') }}</th>
            <th>{{ $t('common.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="loading">{{ $t('common.loading') }}...</td>
          </tr>
          <tr v-else-if="products.length === 0">
            <td colspan="7" class="no-data">{{ $t('common.noData') }}</td>
          </tr>
          <tr v-else v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td class="product-name">
              <img :src="product.image" :alt="product.name" class="product-image" />
              {{ product.name }}
            </td>
            <td>RM{{ product.costPrice.toFixed(2) }}</td>
            <td>RM{{ product.salePrice.toFixed(2) }}</td>
            <td class="profit">RM{{ product.profit.toFixed(2) }}</td>
            <td>{{ product.stock }}</td>
            <td class="actions">
              <button 
                @click="toggleStatus(product)" 
                :class="product.status === 'active' ? 'btn-inactive' : 'btn-active'"
              >
                {{ product.status === 'active' ? $t('products.offShelf') : $t('products.onShelf') }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 分页 -->
    <div class="pagination" v-if="pagination.totalPages > 1">
      <div class="pagination-info">
        Total {{ pagination.total }} items
      </div>
      <div class="pagination-controls">
        <button 
          @click="changePage(pagination.page - 1)" 
          :disabled="pagination.page <= 1"
        >
          &lt;
        </button>
        <span>{{ pagination.page }} / {{ pagination.totalPages }}</span>
        <button 
          @click="changePage(pagination.page + 1)" 
          :disabled="pagination.page >= pagination.totalPages"
        >
          &gt;
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { getMerchantProducts } from '@/api/product';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// 响应式数据
const products = ref([]);
const loading = ref(false);
const filters = ref({
  status: '',
  keyword: ''
});

const pagination = ref({
  page: 1,
  pageSize: 10,
  total: 0,
  totalPages: 0
});

const stats = ref({
  totalProducts: 0,
  onShelfProducts: 0,
  offShelfProducts: 0
});

// 加载商品列表
const loadProducts = async () => {
  try {
    loading.value = true;
    console.log('商家商品列表API调用参数:', {
      page: pagination.value.page,
      pageSize: pagination.value.pageSize,
      status: filters.value.status,
      keyword: filters.value.keyword
    });

    const response = await getMerchantProducts({
      page: pagination.value.page,
      pageSize: pagination.value.pageSize,
      status: filters.value.status,
      keyword: filters.value.keyword
    });

    console.log('商家商品列表API响应:', response);

    // 检查响应数据结构 - 现在response直接就是数据对象
    if (response && response.list) {
      products.value = response.list || [];
      pagination.value.total = response.total || 0;
      pagination.value.totalPages = response.totalPages || 0;
      
      // 更新统计信息
      stats.value.totalProducts = response.total || 0;
      stats.value.onShelfProducts = products.value.filter(p => p.status === 'active').length;
      stats.value.offShelfProducts = products.value.filter(p => p.status === 'inactive').length;
      
      console.log('商品列表加载成功:', products.value.length, '个商品');
    } else {
      console.error('API响应格式错误:', response);
      products.value = [];
      pagination.value.total = 0;
      pagination.value.totalPages = 0;
    }
  } catch (error) {
    console.error('加载商品列表失败:', error);
    products.value = [];
    pagination.value.total = 0;
    pagination.value.totalPages = 0;
  } finally {
    loading.value = false;
  }
};

// 切换商品状态
const toggleStatus = async (product: any) => {
  try {
    // 这里可以添加更新状态的API调用
    product.status = product.status === 'active' ? 'inactive' : 'active';
    await loadProducts(); // 重新加载数据
  } catch (error) {
    console.error('更新商品状态失败:', error);
  }
};

// 切换页面
const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.totalPages) {
    pagination.value.page = page;
    loadProducts();
  }
};

// 组件挂载时加载数据
onMounted(() => {
  loadProducts();
});
</script>

<style scoped>
.my-products {
  padding: 20px;
}

.page-header h1 {
  margin: 0 0 20px 0;
  color: #333;
}

.stats-cards {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  flex: 1;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
}

.stat-card.active {
  border-left: 4px solid #67c23a;
}

.stat-card.inactive {
  border-left: 4px solid #f56c6c;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  margin-bottom: 5px;
}

.stat-label {
  color: #666;
  font-size: 14px;
}

.filter-section {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-group label {
  font-weight: 500;
  color: #333;
}

.filter-group select,
.filter-group input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.search-btn {
  padding: 8px 16px;
  background: #409eff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.search-btn:hover {
  background: #66b1ff;
}

.products-table {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  overflow: hidden;
}

.products-table table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th,
.products-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.products-table th {
  background: #f5f7fa;
  font-weight: 500;
  color: #333;
}

.product-name {
  display: flex;
  align-items: center;
  gap: 10px;
}

.product-image {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 4px;
}

.profit {
  color: #67c23a;
  font-weight: 500;
}

.actions button {
  padding: 4px 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.btn-active {
  background: #67c23a;
  color: white;
}

.btn-inactive {
  background: #f56c6c;
  color: white;
}

.loading,
.no-data {
  text-align: center;
  color: #999;
  font-style: italic;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.pagination-controls button {
  padding: 8px 12px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
  border-radius: 4px;
}

.pagination-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-controls button:not(:disabled):hover {
  background: #f5f7fa;
}
</style>
