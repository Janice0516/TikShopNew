<template>
  <div class="my-products">
    <div class="page-header">
      <h1>{{ $t('products.title') }}</h1>
      <button class="btn-primary" @click="selectFromPlatform">
        + {{ $t('products.selectFromPlatform') }}
      </button>
    </div>

    <!-- 统计卡片 -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value">{{ totalProducts }}</div>
        <div class="stat-label">{{ $t('products.totalProducts') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ onShelfProducts }}</div>
        <div class="stat-label">{{ $t('products.onShelf') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ offShelfProducts }}</div>
        <div class="stat-label">{{ $t('products.offShelf') }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">RM{{ todaysSales }}</div>
        <div class="stat-label">{{ $t('products.todaysSales') }}</div>
      </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="filters">
      <div class="filter-group">
        <label>{{ $t('common.status') }}:</label>
        <select v-model="filters.status">
          <option value="">{{ $t('common.all') }}</option>
          <option value="active">{{ $t('common.active') }}</option>
          <option value="inactive">{{ $t('common.inactive') }}</option>
        </select>
      </div>
      <div class="filter-group">
        <label>{{ $t('products.productName') }}:</label>
        <input v-model="filters.productName" :placeholder="$t('common.search')" />
      </div>
      <div class="filter-actions">
        <button @click="searchProducts" class="btn-secondary">{{ $t('common.search') }}</button>
        <button @click="resetFilters" class="btn-outline">{{ $t('common.reset') }}</button>
      </div>
    </div>

    <!-- 商品列表 -->
    <div class="products-table">
      <table>
        <thead>
          <tr>
            <th>{{ $t('products.id') }}</th>
            <th>{{ $t('products.productName') }}</th>
            <th>{{ $t('products.costPriceRM') }}</th>
            <th>{{ $t('products.salePriceRM') }}</th>
            <th>{{ $t('products.profit') }}</th>
            <th>{{ $t('products.stock') }}</th>
            <th>{{ $t('products.sales') }}</th>
            <th>{{ $t('products.stat') }}</th>
            <th>{{ $t('products.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td>
              <div class="product-info">
                <img :src="product.image" :alt="product.name" class="product-image" />
                <span>{{ product.name }}</span>
              </div>
            </td>
            <td>RM{{ product.costPrice }}</td>
            <td>RM{{ product.salePrice }}</td>
            <td>RM{{ product.profit }}</td>
            <td>{{ product.stock }}</td>
            <td>{{ product.sales }}</td>
            <td>
              <span :class="['status-tag', product.status]">
                {{ $t(`common.${product.status}`) }}
              </span>
            </td>
            <td>
              <button @click="editProduct(product)" class="btn-small">{{ $t('common.edit') }}</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 响应式数据
const totalProducts = ref(10)
const onShelfProducts = ref(10)
const offShelfProducts = ref(0)
const todaysSales = ref(0)

const filters = reactive({
  status: '',
  productName: ''
})

const products = ref([
  {
    id: 1069,
    name: '[deevoka] 12 Piece Nativity Collection with Family and Animal Figures for Decoration',
    image: '/uploads/images/nativity.jpg',
    costPrice: '368.12',
    salePrice: '1035.20',
    profit: '667.08',
    stock: 150,
    sales: 0,
    status: 'inactive'
  },
  {
    id: 1068,
    name: 'Charlotte Tilbury Cheek To Chic Two-tone Powder Blush #PILLOW TALK 8g',
    image: '/uploads/images/blush.jpg',
    costPrice: '193.40',
    salePrice: '326.70',
    profit: '133.30',
    stock: 456,
    sales: 0,
    status: 'inactive'
  },
  {
    id: 1067,
    name: 'DRIXTA Pet Nest, Waterproof Anti-slip Pet Mat, Removable',
    image: '/uploads/images/pet-bed.jpg',
    costPrice: '24.43',
    salePrice: '78.98',
    profit: '54.55',
    stock: 299,
    sales: 0,
    status: 'active'
  }
])

// 方法
const selectFromPlatform = () => {
  router.push('/products/select-products')
}

const searchProducts = () => {
  // 实现搜索逻辑
  console.log('搜索商品:', filters)
}

const resetFilters = () => {
  filters.status = ''
  filters.productName = ''
}

const editProduct = (product: any) => {
  console.log('编辑商品:', product)
}

onMounted(() => {
  // 加载商品数据
})
</script>

<style scoped>
.my-products {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.page-header h1 {
  margin: 0;
  font-size: 28px;
  color: #2c3e50;
}

.btn-primary {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary:hover {
  background-color: #2980b9;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.filters {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  display: flex;
  gap: 20px;
  align-items: center;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-group label {
  font-weight: 500;
  color: #2c3e50;
}

.filter-group select,
.filter-group input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.filter-actions {
  display: flex;
  gap: 10px;
}

.btn-secondary {
  background-color: #95a5a6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-outline {
  background-color: transparent;
  color: #3498db;
  border: 1px solid #3498db;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
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
  background-color: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.product-info {
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

.status-tag {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.status-tag.active {
  background-color: #d4edda;
  color: #155724;
}

.status-tag.inactive {
  background-color: #f8d7da;
  color: #721c24;
}

.btn-small {
  background-color: #17a2b8;
  color: white;
  border: none;
  padding: 4px 8px;
  border-radius: 3px;
  cursor: pointer;
  font-size: 12px;
}

.btn-small:hover {
  background-color: #138496;
}
</style>
