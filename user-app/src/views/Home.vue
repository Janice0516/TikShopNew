<template>
  <div class="home">
    <!-- 轮播图 -->
    <div class="banner-section">
      <el-carousel height="400px" indicator-position="outside">
        <el-carousel-item v-for="(banner, index) in banners" :key="index">
          <div class="banner-item" :style="{ backgroundImage: `url(${banner.image})` }">
            <div class="banner-content">
              <h2>{{ banner.title }}</h2>
              <p>{{ banner.description }}</p>
              <el-button type="primary" size="large">{{ banner.buttonText }}</el-button>
            </div>
          </div>
        </el-carousel-item>
      </el-carousel>
    </div>

    <!-- 分类导航 -->
    <div class="category-section">
      <div class="section-title">
        <h2>{{ $t('home.categories') }}</h2>
        <p>{{ $t('home.categoryDescription') }}</p>
      </div>
      <div class="category-grid">
        <div 
          v-for="category in categories" 
          :key="category.id" 
          class="category-item"
          @click="goToCategory(category.id)"
        >
          <div class="category-image">
            <img :src="getCategoryImage(category)" :alt="category.name" />
          </div>
          <div class="category-name">{{ category.name }}</div>
        </div>
      </div>
    </div>

    <!-- 热门商品 -->
    <div class="products-section">
      <div class="section-title">
        <h2>{{ $t('home.hotProducts') }}</h2>
        <p>{{ $t('home.hotProductsDescription') }}</p>
      </div>
      <div class="products-grid">
        <div 
          v-for="product in hotProducts" 
          :key="product.id" 
          class="product-card"
          @click="goToProduct(product.id)"
        >
          <div class="product-image">
            <img :src="product.mainImage" :alt="product.name" />
            <div class="product-badge" v-if="product.isHot">HOT</div>
          </div>
          <div class="product-info">
            <h3 class="product-name">{{ product.name }}</h3>
            <div class="product-price">
              <span class="current-price">RM{{ product.suggestPrice }}</span>
              <span class="original-price" v-if="product.costPrice !== product.suggestPrice">RM{{ product.costPrice }}</span>
            </div>
            <div class="product-rating">
              <el-rate v-model="product.rating" disabled show-score />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 特价商品 -->
    <div class="sale-section">
      <div class="section-title">
        <h2>{{ $t('home.saleProducts') }}</h2>
        <p>{{ $t('home.saleProductsDescription') }}</p>
      </div>
      <div class="products-grid">
        <div 
          v-for="product in saleProducts" 
          :key="product.id" 
          class="product-card sale-card"
          @click="goToProduct(product.id)"
        >
          <div class="product-image">
            <img :src="product.mainImage" :alt="product.name" />
            <div class="sale-badge">SALE</div>
          </div>
          <div class="product-info">
            <h3 class="product-name">{{ product.name }}</h3>
            <div class="product-price">
              <span class="current-price">RM{{ product.suggestPrice }}</span>
              <span class="original-price">RM{{ product.costPrice }}</span>
            </div>
            <div class="discount-info">
              <span class="discount-percent">{{ getDiscountPercent(product.costPrice, product.suggestPrice) }}% OFF</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 品牌推荐 -->
    <div class="brand-section">
      <div class="section-title">
        <h2>{{ $t('home.brands') }}</h2>
        <p>{{ $t('home.brandsDescription') }}</p>
      </div>
      <div class="brand-grid">
        <div 
          v-for="brand in brands" 
          :key="brand.id" 
          class="brand-item"
          @click="goToBrand(brand.id)"
        >
          <img :src="brand.logo" :alt="brand.name" />
          <div class="brand-name">{{ brand.name }}</div>
        </div>
      </div>
    </div>

    <!-- 优惠信息 -->
    <div class="savings-section">
      <div class="savings-content">
        <div class="savings-text">
          <h2>{{ $t('home.savingsTitle') }}</h2>
          <p>{{ $t('home.savingsDescription') }}</p>
        </div>
        <div class="savings-actions">
          <el-button type="primary" size="large">{{ $t('home.shopNow') }}</el-button>
          <el-button size="large">{{ $t('home.learnMore') }}</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { getCategories } from '@/api/category';
import { getProducts } from '@/api/product';

const { t } = useI18n();
const router = useRouter();

// 响应式数据
const banners = ref([
  {
    image: '/images/banner1.jpg',
    title: t('home.banner1Title'),
    description: t('home.banner1Description'),
    buttonText: t('home.shopNow')
  },
  {
    image: '/images/banner2.jpg',
    title: t('home.banner2Title'),
    description: t('home.banner2Description'),
    buttonText: t('home.shopNow')
  }
]);

const categories = ref([]);
const hotProducts = ref([]);
const saleProducts = ref([]);
const brands = ref([]);

// 获取分类图片 - 使用API数据
const getCategoryImage = (category: any) => {
  console.log('分类数据:', category); // 调试日志
  
  // 优先使用API返回的imageUrl
  if (category.imageUrl) {
    console.log('使用API图片:', category.imageUrl);
    return category.imageUrl;
  }
  
  // 如果没有imageUrl，使用映射
  const imageMap = {
    // 英文名称
    'Electronics & Gadgets': '/images/categories/electronics.jpg',
    'Personal Care': '/images/categories/beauty.jpg',
    'Cleaning Supplies': '/images/categories/home.jpg',
    'Womenswear & Underwear': '/images/categories/fashion.jpg',
    'Phones & Electronics': '/images/categories/electronics.jpg',
    'Fashion Accessories': '/images/categories/fashion.jpg',
    'Menswear & Underwear': '/images/categories/fashion.jpg',
    'Home Supplies': '/images/categories/home.jpg',
    'Beauty & Personal Care': '/images/categories/beauty.jpg',
    'Shoes': '/images/categories/shoes.jpg',
    'Sports & Outdoor': '/images/categories/sports.jpg',
    'Luggage & Bags': '/images/categories/luggage.jpg',
    'Toys & Hobbies': '/images/categories/toys.jpg',
    'Automotive & Motorcycle': '/images/categories/automotive.jpg',
    'Kids Fashion': '/images/categories/kids-fashion.jpg',
    'Kitchenware': '/images/categories/kitchen.jpg',
    'Computers & Office Equipment': '/images/categories/office.jpg',
    'Baby & Maternity': '/images/categories/baby.jpg',
    'Tools & Hardware': '/images/categories/tools.jpg',
    'Textiles & Soft Furnishings': '/images/categories/textiles.jpg',
    'Pet Supplies': '/images/categories/pets.jpg',
    'Home Improvement': '/images/categories/home.jpg',
    'Food & Beverages': '/images/categories/food.jpg',
    'Muslim Fashion': '/images/categories/fashion.jpg',
    'Books, Magazines & Audio': '/images/categories/books.jpg',
    'Household Appliances': '/images/categories/appliances.jpg',
    'Health': '/images/categories/health.jpg',
    'Furniture': '/images/categories/furniture.jpg',
    'Jewelry Accessories & Derivatives': '/images/categories/jewelry.jpg',
    'Collectibles': '/images/categories/collectibles.jpg',
    'Pre-Owned': '/images/categories/preowned.jpg',
    
    // 中文名称
    '收藏品': '/images/categories/collectibles.jpg',
    '二手商品': '/images/categories/preowned.jpg',
    '工具五金': '/images/categories/tools.jpg',
    '摩托车用品': '/images/categories/motorcycle.jpg',
    '玩具': '/images/categories/toys.jpg',
    '电子产品': '/images/categories/electronics.jpg',
    '服装': '/images/categories/fashion.jpg',
    '家居': '/images/categories/home.jpg',
    '运动': '/images/categories/sports.jpg',
    '美妆': '/images/categories/beauty.jpg',
    '食品': '/images/categories/food.jpg',
    '图书': '/images/categories/books.jpg',
    '汽车': '/images/categories/automotive.jpg',
    '宠物': '/images/categories/pets.jpg',
    '珠宝': '/images/categories/jewelry.jpg',
    '乐器': '/images/categories/music.jpg',
    '园艺': '/images/categories/garden.jpg',
    '办公': '/images/categories/office.jpg',
    '旅行': '/images/categories/travel.jpg',
    '健康': '/images/categories/health.jpg'
  };
  
  const imagePath = imageMap[category.name] || '/images/categories/default.jpg';
  console.log('使用映射图片:', imagePath);
  return imagePath;
};

// 计算折扣百分比
const getDiscountPercent = (originalPrice: number, salePrice: number) => {
  return Math.round(((originalPrice - salePrice) / originalPrice) * 100);
};

// 跳转到分类页面
const goToCategory = (categoryId: string) => {
  router.push(`/category/${categoryId}`);
};

// 跳转到商品详情
const goToProduct = (productId: string) => {
  router.push(`/product/${productId}`);
};

// 跳转到品牌页面
const goToBrand = (brandId: string) => {
  router.push(`/brand/${brandId}`);
};

// 加载数据
const loadData = async () => {
  try {
    console.log('开始加载分类数据...'); // 调试日志
    
    // 加载分类
    const categoriesResponse = await getCategories();
    console.log('分类API响应:', categoriesResponse); // 调试日志
    
    categories.value = categoriesResponse.data || categoriesResponse || [];
    console.log('处理后的分类数据:', categories.value); // 调试日志
    
    // 加载热门商品
    const hotProductsResponse = await getProducts({ 
      page: 1, 
      pageSize: 8, 
      sort: 'sales',
      order: 'desc'
    });
    hotProducts.value = hotProductsResponse.data?.list || [];
    
    // 加载特价商品
    const saleProductsResponse = await getProducts({ 
      page: 1, 
      pageSize: 8, 
      sort: 'discount',
      order: 'desc'
    });
    saleProducts.value = saleProductsResponse.data?.list || [];
    
  } catch (error) {
    console.error('加载数据失败:', error);
    
    // 如果API失败，使用默认分类数据
    categories.value = [
      { id: 1, name: 'Electronics & Gadgets', imageUrl: '/images/categories/electronics.jpg' },
      { id: 2, name: 'Fashion & Accessories', imageUrl: '/images/categories/fashion.jpg' },
      { id: 3, name: 'Home & Living', imageUrl: '/images/categories/home.jpg' },
      { id: 4, name: 'Beauty & Personal Care', imageUrl: '/images/categories/beauty.jpg' },
      { id: 5, name: 'Sports & Outdoor', imageUrl: '/images/categories/sports.jpg' },
      { id: 6, name: 'Food & Beverages', imageUrl: '/images/categories/food.jpg' },
      { id: 7, name: 'Books & Media', imageUrl: '/images/categories/books.jpg' },
      { id: 8, name: 'Automotive', imageUrl: '/images/categories/automotive.jpg' }
    ];
  }
};

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.home {
  min-height: 100vh;
}

.banner-section {
  margin-bottom: 40px;
}

.banner-item {
  height: 400px;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.banner-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
}

.banner-content {
  text-align: center;
  color: white;
  z-index: 1;
  position: relative;
}

.banner-content h2 {
  font-size: 48px;
  margin-bottom: 20px;
  font-weight: bold;
}

.banner-content p {
  font-size: 18px;
  margin-bottom: 30px;
}

.section-title {
  text-align: center;
  margin-bottom: 40px;
}

.section-title h2 {
  font-size: 32px;
  color: #333;
  margin-bottom: 10px;
}

.section-title p {
  font-size: 16px;
  color: #666;
}

.category-section {
  padding: 60px 0;
  background: #f8f9fa;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.category-item {
  text-align: center;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.category-item:hover {
  transform: translateY(-5px);
}

.category-image {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto 15px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.category-name {
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

.products-section,
.sale-section {
  padding: 60px 0;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-badge,
.sale-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #ff4757;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.sale-badge {
  background: #2ed573;
}

.product-info {
  padding: 20px;
}

.product-name {
  font-size: 16px;
  color: #333;
  margin-bottom: 10px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  margin-bottom: 10px;
}

.current-price {
  font-size: 18px;
  color: #ff4757;
  font-weight: bold;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
  margin-left: 8px;
}

.product-rating {
  margin-bottom: 10px;
}

.discount-info {
  margin-top: 10px;
}

.discount-percent {
  background: #ff4757;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.brand-section {
  padding: 60px 0;
  background: #f8f9fa;
}

.brand-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.brand-item {
  text-align: center;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.brand-item:hover {
  transform: translateY(-5px);
}

.brand-item img {
  width: 80px;
  height: 80px;
  object-fit: contain;
  margin-bottom: 15px;
}

.brand-name {
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

.savings-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 80px 0;
}

.savings-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  text-align: center;
}

.savings-text h2 {
  font-size: 36px;
  margin-bottom: 20px;
}

.savings-text p {
  font-size: 18px;
  margin-bottom: 40px;
  opacity: 0.9;
}

.savings-actions {
  display: flex;
  gap: 20px;
  justify-content: center;
}

.savings-actions .el-button {
  min-width: 150px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .banner-content h2 {
    font-size: 32px;
  }
  
  .banner-content p {
    font-size: 16px;
  }
  
  .section-title h2 {
    font-size: 24px;
  }
  
  .category-grid {
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 20px;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
  }
  
  .savings-actions {
    flex-direction: column;
    align-items: center;
  }
}
</style>
