#!/usr/bin/env node

// 商品数据上传脚本 - 上传所有商品到Render数据库
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function uploadAllProducts() {
  try {
    console.log('🚀 开始上传所有商品到Render数据库...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 先创建商品分类（如果还没有的话）
    console.log('📂 确保商品分类存在...');
    const categories = [
      { name: 'Electronics', description: 'Electronic devices and gadgets' },
      { name: 'Fashion', description: 'Clothing and accessories' },
      { name: 'Home & Garden', description: 'Home improvement and garden supplies' },
      { name: 'Sports & Outdoors', description: 'Sports equipment and outdoor gear' },
      { name: 'Beauty & Health', description: 'Beauty products and health supplements' },
      { name: 'Books & Media', description: 'Books, movies, and music' },
      { name: 'Toys & Games', description: 'Toys and gaming products' },
      { name: 'Automotive', description: 'Car parts and accessories' }
    ];
    
    let categoryMap = {};
    for (const category of categories) {
      try {
        const response = await axios.post(`${API_BASE_URL}/category`, category, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建分类: ${category.name}`);
        categoryMap[category.name] = response.data.data.id;
      } catch (error) {
        // 如果分类已存在，尝试获取现有分类
        try {
          const listResponse = await axios.get(`${API_BASE_URL}/category/list`, {
            headers: { Authorization: `Bearer ${adminToken}` }
          });
          const existingCategory = listResponse.data.data.find(cat => cat.name === category.name);
          if (existingCategory) {
            categoryMap[category.name] = existingCategory.id;
            console.log(`   ✅ 使用现有分类: ${category.name}`);
          }
        } catch (listError) {
          console.log(`   ⚠️ 分类 ${category.name} 处理失败: ${error.response?.data?.message || error.message}`);
        }
      }
    }
    
    // 3. 创建子分类
    console.log('📁 创建子分类...');
    const subCategories = [
      { name: 'Smartphones', description: 'Mobile phones and accessories', parentId: categoryMap['Electronics'] },
      { name: 'Laptops', description: 'Laptop computers and accessories', parentId: categoryMap['Electronics'] },
      { name: 'Audio', description: 'Headphones, speakers, and audio equipment', parentId: categoryMap['Electronics'] },
      { name: 'Cameras', description: 'Digital cameras and photography equipment', parentId: categoryMap['Electronics'] },
      { name: 'Men\'s Clothing', description: 'Men\'s apparel and accessories', parentId: categoryMap['Fashion'] },
      { name: 'Women\'s Clothing', description: 'Women\'s apparel and accessories', parentId: categoryMap['Fashion'] },
      { name: 'Shoes', description: 'Footwear for men and women', parentId: categoryMap['Fashion'] },
      { name: 'Accessories', description: 'Fashion accessories and jewelry', parentId: categoryMap['Fashion'] },
      { name: 'Furniture', description: 'Home and office furniture', parentId: categoryMap['Home & Garden'] },
      { name: 'Kitchen & Dining', description: 'Kitchen appliances and dining accessories', parentId: categoryMap['Home & Garden'] },
      { name: 'Garden Tools', description: 'Gardening equipment and tools', parentId: categoryMap['Home & Garden'] },
      { name: 'Home Decor', description: 'Decorative items and home accessories', parentId: categoryMap['Home & Garden'] }
    ];
    
    for (const subCategory of subCategories) {
      if (subCategory.parentId) {
        try {
          const response = await axios.post(`${API_BASE_URL}/category`, subCategory, {
            headers: { Authorization: `Bearer ${adminToken}` }
          });
          console.log(`   ✅ 创建子分类: ${subCategory.name}`);
          categoryMap[subCategory.name] = response.data.data.id;
        } catch (error) {
          console.log(`   ⚠️ 子分类 ${subCategory.name} 可能已存在: ${error.response?.data?.message || error.message}`);
        }
      }
    }
    
    // 4. 创建商品
    console.log('🛍️ 创建商品...');
    const products = [
      // TechStore Malaysia 商品
      {
        name: 'iPhone 15 Pro Max 256GB',
        description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system',
        categoryId: categoryMap['Smartphones'] || 1,
        brand: 'Apple',
        mainImage: '/static/products/iphone15pro.jpg',
        images: JSON.stringify(['/static/products/iphone15pro.jpg']),
        costPrice: 4500.00,
        suggestPrice: 4999.00,
        stock: 25
      },
      {
        name: 'MacBook Pro M3 14-inch',
        description: 'Powerful laptop with M3 chip, perfect for professionals and creators',
        categoryId: categoryMap['Laptops'] || 2,
        brand: 'Apple',
        mainImage: '/static/products/macbook-m3.jpg',
        images: JSON.stringify(['/static/products/macbook-m3.jpg']),
        costPrice: 7500.00,
        suggestPrice: 7999.00,
        stock: 15
      },
      {
        name: 'AirPods Pro 2nd Gen',
        description: 'Wireless earbuds with active noise cancellation and spatial audio',
        categoryId: categoryMap['Audio'] || 3,
        brand: 'Apple',
        mainImage: '/static/products/airpods-pro2.jpg',
        images: JSON.stringify(['/static/products/airpods-pro2.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 50
      },
      {
        name: 'Samsung Galaxy S24 Ultra',
        description: 'Premium Android smartphone with S Pen and advanced AI features',
        categoryId: categoryMap['Smartphones'] || 1,
        brand: 'Samsung',
        mainImage: '/static/products/galaxy-s24.jpg',
        images: JSON.stringify(['/static/products/galaxy-s24.jpg']),
        costPrice: 4000.00,
        suggestPrice: 4299.00,
        stock: 20
      },
      
      // Fashion Hub KL 商品
      {
        name: 'Nike Air Max 270',
        description: 'Comfortable running shoes with Max Air cushioning',
        categoryId: categoryMap['Shoes'] || 7,
        brand: 'Nike',
        mainImage: '/static/products/nike-airmax.jpg',
        images: JSON.stringify(['/static/products/nike-airmax.jpg']),
        costPrice: 350.00,
        suggestPrice: 399.00,
        stock: 100
      },
      {
        name: 'Adidas Ultraboost 22',
        description: 'High-performance running shoes with Boost midsole',
        categoryId: categoryMap['Shoes'] || 7,
        brand: 'Adidas',
        mainImage: '/static/products/adidas-ultraboost.jpg',
        images: JSON.stringify(['/static/products/adidas-ultraboost.jpg']),
        costPrice: 550.00,
        suggestPrice: 599.00,
        stock: 80
      },
      {
        name: 'Uniqlo Heattech Long Sleeve',
        description: 'Thermal base layer for cold weather',
        categoryId: categoryMap['Men\'s Clothing'] || 5,
        brand: 'Uniqlo',
        mainImage: '/static/products/uniqlo-heattech.jpg',
        images: JSON.stringify(['/static/products/uniqlo-heattech.jpg']),
        costPrice: 40.00,
        suggestPrice: 49.90,
        stock: 200
      },
      {
        name: 'Zara Denim Jacket',
        description: 'Classic denim jacket with modern fit',
        categoryId: categoryMap['Men\'s Clothing'] || 5,
        brand: 'Zara',
        mainImage: '/static/products/zara-denim.jpg',
        images: JSON.stringify(['/static/products/zara-denim.jpg']),
        costPrice: 180.00,
        suggestPrice: 199.00,
        stock: 60
      },
      
      // Home Depot Malaysia 商品
      {
        name: 'IKEA MALM Bed Frame',
        description: 'Minimalist bed frame with storage drawers',
        categoryId: categoryMap['Furniture'] || 9,
        brand: 'IKEA',
        mainImage: '/static/products/ikea-malm.jpg',
        images: JSON.stringify(['/static/products/ikea-malm.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 30
      },
      {
        name: 'KitchenAid Stand Mixer',
        description: 'Professional stand mixer for baking enthusiasts',
        categoryId: categoryMap['Kitchen & Dining'] || 10,
        brand: 'KitchenAid',
        mainImage: '/static/products/kitchenaid-mixer.jpg',
        images: JSON.stringify(['/static/products/kitchenaid-mixer.jpg']),
        costPrice: 1200.00,
        suggestPrice: 1299.00,
        stock: 15
      },
      {
        name: 'Philips Air Fryer XXL',
        description: 'Large capacity air fryer for healthy cooking',
        categoryId: categoryMap['Kitchen & Dining'] || 10,
        brand: 'Philips',
        mainImage: '/static/products/philips-airfryer.jpg',
        images: JSON.stringify(['/static/products/philips-airfryer.jpg']),
        costPrice: 350.00,
        suggestPrice: 399.00,
        stock: 40
      },
      {
        name: 'Dyson V15 Detect Vacuum',
        description: 'Cordless vacuum with laser dust detection',
        categoryId: categoryMap['Furniture'] || 9,
        brand: 'Dyson',
        mainImage: '/static/products/dyson-v15.jpg',
        images: JSON.stringify(['/static/products/dyson-v15.jpg']),
        costPrice: 1800.00,
        suggestPrice: 1999.00,
        stock: 20
      },
      
      // Sports Zone 商品
      {
        name: 'Wilson Pro Staff Tennis Racket',
        description: 'Professional tennis racket for advanced players',
        categoryId: categoryMap['Sports & Outdoors'] || 4,
        brand: 'Wilson',
        mainImage: '/static/products/wilson-prostaff.jpg',
        images: JSON.stringify(['/static/products/wilson-prostaff.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 25
      },
      {
        name: 'Nike Dri-FIT Training Shorts',
        description: 'Moisture-wicking training shorts for workouts',
        categoryId: categoryMap['Sports & Outdoors'] || 4,
        brand: 'Nike',
        mainImage: '/static/products/nike-shorts.jpg',
        images: JSON.stringify(['/static/products/nike-shorts.jpg']),
        costPrice: 80.00,
        suggestPrice: 89.00,
        stock: 150
      },
      {
        name: 'Garmin Forerunner 255',
        description: 'GPS running watch with advanced training metrics',
        categoryId: categoryMap['Sports & Outdoors'] || 4,
        brand: 'Garmin',
        mainImage: '/static/products/garmin-255.jpg',
        images: JSON.stringify(['/static/products/garmin-255.jpg']),
        costPrice: 1200.00,
        suggestPrice: 1299.00,
        stock: 35
      },
      {
        name: 'Yoga Mat Premium',
        description: 'Non-slip yoga mat with carrying strap',
        categoryId: categoryMap['Sports & Outdoors'] || 4,
        brand: 'Generic',
        mainImage: '/static/products/yoga-mat.jpg',
        images: JSON.stringify(['/static/products/yoga-mat.jpg']),
        costPrice: 70.00,
        suggestPrice: 79.00,
        stock: 80
      },
      
      // Beauty Paradise 商品
      {
        name: 'SK-II Facial Treatment Essence',
        description: 'Premium skincare essence for radiant skin',
        categoryId: categoryMap['Beauty & Health'] || 5,
        brand: 'SK-II',
        mainImage: '/static/products/sk2-essence.jpg',
        images: JSON.stringify(['/static/products/sk2-essence.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 40
      },
      {
        name: 'MAC Lipstick Ruby Woo',
        description: 'Classic red lipstick with matte finish',
        categoryId: categoryMap['Beauty & Health'] || 5,
        brand: 'MAC',
        mainImage: '/static/products/mac-rubywoo.jpg',
        images: JSON.stringify(['/static/products/mac-rubywoo.jpg']),
        costPrice: 80.00,
        suggestPrice: 89.00,
        stock: 100
      },
      {
        name: 'La Mer The Moisturizing Cream',
        description: 'Luxury moisturizing cream for all skin types',
        categoryId: categoryMap['Beauty & Health'] || 5,
        brand: 'La Mer',
        mainImage: '/static/products/lamer-cream.jpg',
        images: JSON.stringify(['/static/products/lamer-cream.jpg']),
        costPrice: 1200.00,
        suggestPrice: 1299.00,
        stock: 20
      },
      {
        name: 'Dyson Supersonic Hair Dryer',
        description: 'Professional hair dryer with intelligent heat control',
        categoryId: categoryMap['Beauty & Health'] || 5,
        brand: 'Dyson',
        mainImage: '/static/products/dyson-hairdryer.jpg',
        images: JSON.stringify(['/static/products/dyson-hairdryer.jpg']),
        costPrice: 1200.00,
        suggestPrice: 1299.00,
        stock: 25
      },
      
      // Book World Malaysia 商品
      {
        name: 'Atomic Habits by James Clear',
        description: 'Bestselling book on building good habits and breaking bad ones',
        categoryId: categoryMap['Books & Media'] || 6,
        brand: 'Random House',
        mainImage: '/static/products/atomic-habits.jpg',
        images: JSON.stringify(['/static/products/atomic-habits.jpg']),
        costPrice: 40.00,
        suggestPrice: 49.90,
        stock: 200
      },
      {
        name: 'The Psychology of Money',
        description: 'Timeless lessons on wealth, greed, and happiness',
        categoryId: categoryMap['Books & Media'] || 6,
        brand: 'Harriman House',
        mainImage: '/static/products/psychology-money.jpg',
        images: JSON.stringify(['/static/products/psychology-money.jpg']),
        costPrice: 50.00,
        suggestPrice: 59.90,
        stock: 150
      },
      {
        name: 'Malaysian Cookbook',
        description: 'Authentic Malaysian recipes and cooking techniques',
        categoryId: categoryMap['Books & Media'] || 6,
        brand: 'Local Publisher',
        mainImage: '/static/products/malaysian-cookbook.jpg',
        images: JSON.stringify(['/static/products/malaysian-cookbook.jpg']),
        costPrice: 80.00,
        suggestPrice: 89.90,
        stock: 80
      },
      {
        name: 'Harry Potter Complete Set',
        description: 'All 7 books in the Harry Potter series',
        categoryId: categoryMap['Books & Media'] || 6,
        brand: 'Bloomsbury',
        mainImage: '/static/products/harry-potter-set.jpg',
        images: JSON.stringify(['/static/products/harry-potter-set.jpg']),
        costPrice: 280.00,
        suggestPrice: 299.90,
        stock: 50
      },
      
      // Toy Kingdom 商品
      {
        name: 'LEGO Creator Expert Modular Building',
        description: 'Detailed modular building set for adults',
        categoryId: categoryMap['Toys & Games'] || 7,
        brand: 'LEGO',
        mainImage: '/static/products/lego-modular.jpg',
        images: JSON.stringify(['/static/products/lego-modular.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 30
      },
      {
        name: 'Barbie Dreamhouse',
        description: '3-story dollhouse with furniture and accessories',
        categoryId: categoryMap['Toys & Games'] || 7,
        brand: 'Mattel',
        mainImage: '/static/products/barbie-dreamhouse.jpg',
        images: JSON.stringify(['/static/products/barbie-dreamhouse.jpg']),
        costPrice: 280.00,
        suggestPrice: 299.00,
        stock: 40
      },
      {
        name: 'Nintendo Switch OLED',
        description: 'Gaming console with OLED screen and Joy-Con controllers',
        categoryId: categoryMap['Toys & Games'] || 7,
        brand: 'Nintendo',
        mainImage: '/static/products/nintendo-switch.jpg',
        images: JSON.stringify(['/static/products/nintendo-switch.jpg']),
        costPrice: 1200.00,
        suggestPrice: 1299.00,
        stock: 25
      },
      {
        name: 'Hot Wheels Track Set',
        description: 'Racing track set with multiple cars and loops',
        categoryId: categoryMap['Toys & Games'] || 7,
        brand: 'Mattel',
        mainImage: '/static/products/hotwheels-track.jpg',
        images: JSON.stringify(['/static/products/hotwheels-track.jpg']),
        costPrice: 180.00,
        suggestPrice: 199.00,
        stock: 60
      },
      
      // Auto Parts Pro 商品
      {
        name: 'Michelin Pilot Sport 4',
        description: 'High-performance summer tires for sports cars',
        categoryId: categoryMap['Automotive'] || 8,
        brand: 'Michelin',
        mainImage: '/static/products/michelin-pilot.jpg',
        images: JSON.stringify(['/static/products/michelin-pilot.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 20
      },
      {
        name: 'Bosch Icon Wiper Blades',
        description: 'Premium windshield wiper blades for all weather',
        categoryId: categoryMap['Automotive'] || 8,
        brand: 'Bosch',
        mainImage: '/static/products/bosch-wipers.jpg',
        images: JSON.stringify(['/static/products/bosch-wipers.jpg']),
        costPrice: 80.00,
        suggestPrice: 89.00,
        stock: 100
      },
      {
        name: 'K&N Air Filter',
        description: 'High-flow air filter for improved engine performance',
        categoryId: categoryMap['Automotive'] || 8,
        brand: 'K&N',
        mainImage: '/static/products/kn-airfilter.jpg',
        images: JSON.stringify(['/static/products/kn-airfilter.jpg']),
        costPrice: 180.00,
        suggestPrice: 199.00,
        stock: 50
      },
      {
        name: 'Mobil 1 Engine Oil 5W-30',
        description: 'Full synthetic engine oil for all vehicles',
        categoryId: categoryMap['Automotive'] || 8,
        brand: 'Mobil',
        mainImage: '/static/products/mobil1-oil.jpg',
        images: JSON.stringify(['/static/products/mobil1-oil.jpg']),
        costPrice: 80.00,
        suggestPrice: 89.00,
        stock: 80
      }
    ];
    
    let successCount = 0;
    let failCount = 0;
    
    for (const product of products) {
      try {
        const response = await axios.post(`${API_BASE_URL}/products`, product, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建商品: ${product.name} - ${product.brand}`);
        successCount++;
      } catch (error) {
        console.log(`   ⚠️ 商品 ${product.name} 创建失败: ${error.response?.data?.message || error.message}`);
        failCount++;
      }
    }
    
    // 5. 检查最终数据统计
    console.log('📊 检查最终数据统计...');
    try {
      const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (statsResponse.data.data && statsResponse.data.data.stats) {
        console.log('✅ 最终数据统计:');
        console.log(`   👥 商家数量: ${statsResponse.data.data.stats.merchants || 0}`);
        console.log(`   🛍️ 商品数量: ${statsResponse.data.data.stats.products || 0}`);
        console.log(`   📦 订单数量: ${statsResponse.data.data.stats.orders || 0}`);
        console.log(`   👤 用户数量: ${statsResponse.data.data.stats.users || 0}`);
        
        // 显示热销商品
        if (statsResponse.data.data.topProducts?.length > 0) {
          console.log('   🔥 热销商品:');
          statsResponse.data.data.topProducts.forEach((product, index) => {
            console.log(`      ${index + 1}. ${product.name} - 销量: ${product.sales} - 库存: ${product.stock}`);
          });
        }
      } else {
        console.log('⚠️ 无法获取统计数据');
      }
    } catch (error) {
      console.log('⚠️ 统计数据获取失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ 商品上传完成！');
    console.log(`📊 上传结果: 成功 ${successCount} 个，失败 ${failCount} 个`);
    console.log('💡 提示：');
    console.log('   1. 商品已创建，可以在管理后台查看');
    console.log('   2. 商家可以选择这些商品进行销售');
    console.log('   3. 用户可以在前端看到这些商品');
    
  } catch (error) {
    console.error('❌ 商品上传失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

uploadAllProducts();
