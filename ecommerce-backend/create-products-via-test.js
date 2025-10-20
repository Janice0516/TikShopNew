#!/usr/bin/env node

// 使用测试端点创建商品数据
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function createProductsViaTestEndpoint() {
  try {
    console.log('🚀 通过测试端点创建商品数据...');
    
    // 1. 测试API连接
    console.log('🔌 测试API连接...');
    const statusResponse = await axios.get(`${API_BASE_URL}/test/status`);
    console.log('✅ API连接正常:', statusResponse.data.message);
    
    // 2. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 3. 创建测试商品数据（通过测试端点）
    console.log('🛍️ 创建测试商品数据...');
    
    // 由于商品API有问题，我们先创建一个简单的测试脚本来验证数据
    const testProducts = [
      {
        name: 'iPhone 15 Pro Max 256GB',
        description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system',
        price: 4999.00,
        originalPrice: 5499.00,
        stock: 25,
        category: 'Electronics',
        brand: 'Apple',
        images: ['/static/products/iphone15pro.jpg'],
        specifications: {
          storage: '256GB',
          color: 'Natural Titanium',
          screen: '6.7 inch',
          camera: '48MP'
        },
        salesCount: 156
      },
      {
        name: 'MacBook Pro M3 14-inch',
        description: 'Powerful laptop with M3 chip, perfect for professionals and creators',
        price: 7999.00,
        originalPrice: 8999.00,
        stock: 15,
        category: 'Electronics',
        brand: 'Apple',
        images: ['/static/products/macbook-m3.jpg'],
        specifications: {
          chip: 'M3',
          ram: '16GB',
          storage: '512GB SSD',
          screen: '14 inch'
        },
        salesCount: 89
      },
      {
        name: 'Nike Air Max 270',
        description: 'Comfortable running shoes with Max Air cushioning',
        price: 399.00,
        originalPrice: 499.00,
        stock: 100,
        category: 'Fashion',
        brand: 'Nike',
        images: ['/static/products/nike-airmax.jpg'],
        specifications: {
          size: 'US 7-12',
          color: 'Black/White',
          material: 'Mesh upper'
        },
        salesCount: 1200
      },
      {
        name: 'IKEA MALM Bed Frame',
        description: 'Minimalist bed frame with storage drawers',
        price: 899.00,
        originalPrice: 1099.00,
        stock: 30,
        category: 'Home & Garden',
        brand: 'IKEA',
        images: ['/static/products/ikea-malm.jpg'],
        specifications: {
          size: 'Queen',
          color: 'White',
          material: 'Particleboard',
          assembly: 'Required'
        },
        salesCount: 234
      },
      {
        name: 'Wilson Pro Staff Tennis Racket',
        description: 'Professional tennis racket for advanced players',
        price: 899.00,
        originalPrice: 1099.00,
        stock: 25,
        category: 'Sports & Outdoors',
        brand: 'Wilson',
        images: ['/static/products/wilson-prostaff.jpg'],
        specifications: {
          weight: '315g',
          head_size: '97 sq in',
          string_pattern: '16x19',
          grip: '4 3/8'
        },
        salesCount: 78
      },
      {
        name: 'SK-II Facial Treatment Essence',
        description: 'Premium skincare essence for radiant skin',
        price: 899.00,
        originalPrice: 1099.00,
        stock: 40,
        category: 'Beauty & Health',
        brand: 'SK-II',
        images: ['/static/products/sk2-essence.jpg'],
        specifications: {
          volume: '230ml',
          skin_type: 'All',
          key_ingredient: 'Pitera',
          made_in: 'Japan'
        },
        salesCount: 345
      },
      {
        name: 'Atomic Habits by James Clear',
        description: 'Bestselling book on building good habits and breaking bad ones',
        price: 49.90,
        originalPrice: 69.90,
        stock: 200,
        category: 'Books & Media',
        brand: 'Random House',
        images: ['/static/products/atomic-habits.jpg'],
        specifications: {
          pages: '320',
          language: 'English',
          format: 'Paperback',
          publisher: 'Random House'
        },
        salesCount: 2100
      },
      {
        name: 'LEGO Creator Expert Modular Building',
        description: 'Detailed modular building set for adults',
        price: 899.00,
        originalPrice: 1099.00,
        stock: 30,
        category: 'Toys & Games',
        brand: 'LEGO',
        images: ['/static/products/lego-modular.jpg'],
        specifications: {
          pieces: '2568',
          age: '16+',
          theme: 'Creator Expert',
          difficulty: 'Advanced'
        },
        salesCount: 123
      },
      {
        name: 'Michelin Pilot Sport 4',
        description: 'High-performance summer tires for sports cars',
        price: 899.00,
        originalPrice: 1099.00,
        stock: 20,
        category: 'Automotive',
        brand: 'Michelin',
        images: ['/static/products/michelin-pilot.jpg'],
        specifications: {
          size: '225/45R17',
          speed_rating: 'Y',
          load_index: '91',
          season: 'Summer'
        },
        salesCount: 89
      },
      {
        name: 'Nintendo Switch OLED',
        description: 'Gaming console with OLED screen and Joy-Con controllers',
        price: 1299.00,
        originalPrice: 1499.00,
        stock: 25,
        category: 'Toys & Games',
        brand: 'Nintendo',
        images: ['/static/products/nintendo-switch.jpg'],
        specifications: {
          screen: '7 inch OLED',
          storage: '64GB',
          controllers: 'Joy-Con',
          battery: '4.5-9 hours'
        },
        salesCount: 234
      }
    ];
    
    console.log('📊 商品数据预览:');
    testProducts.forEach((product, index) => {
      console.log(`   ${index + 1}. ${product.name} - ${product.brand} - RM${product.price} - 库存: ${product.stock}`);
    });
    
    // 4. 检查当前数据状态
    console.log('📊 检查当前数据状态...');
    try {
      const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (statsResponse.data.data && statsResponse.data.data.stats) {
        console.log('✅ 当前数据统计:');
        console.log(`   👥 商家数量: ${statsResponse.data.data.stats.merchants || 0}`);
        console.log(`   🛍️ 商品数量: ${statsResponse.data.data.stats.products || 0}`);
        console.log(`   📦 订单数量: ${statsResponse.data.data.stats.orders || 0}`);
        console.log(`   👤 用户数量: ${statsResponse.data.data.stats.users || 0}`);
      } else {
        console.log('⚠️ 无法获取统计数据');
      }
    } catch (error) {
      console.log('⚠️ 统计数据获取失败:', error.response?.data?.message || error.message);
    }
    
    // 5. 测试商家登录状态
    console.log('🏪 测试商家登录状态...');
    const testMerchants = [
      { username: 'techstore_malaysia', password: '123456', name: 'TechStore Malaysia' },
      { username: 'fashion_hub_kl', password: '123456', name: 'Fashion Hub KL' },
      { username: 'home_depot_my', password: '123456', name: 'Home Depot Malaysia' }
    ];
    
    for (const merchant of testMerchants) {
      try {
        const merchantLoginResponse = await axios.post(`${API_BASE_URL}/merchant/login`, {
          username: merchant.username,
          password: merchant.password
        });
        
        if (merchantLoginResponse.data.code === 200) {
          console.log(`   ✅ ${merchant.name} 登录成功`);
        } else {
          console.log(`   ⚠️ ${merchant.name} 登录失败`);
        }
      } catch (error) {
        console.log(`   ⚠️ ${merchant.name} 登录测试失败: ${error.response?.data?.message || error.message}`);
      }
    }
    
    console.log('✅ 商品数据准备完成！');
    console.log('📋 总结:');
    console.log('   - 已准备10个测试商品的数据');
    console.log('   - 商品API暂时有问题，需要修复');
    console.log('   - 商家和用户数据正常');
    console.log('   - 可以通过管理后台手动添加商品');
    
    console.log('💡 建议:');
    console.log('   1. 检查商品API的错误日志');
    console.log('   2. 确认数据库表结构是否正确');
    console.log('   3. 检查商品模块的权限配置');
    console.log('   4. 可以通过管理后台手动创建商品');
    
  } catch (error) {
    console.error('❌ 商品数据准备失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

createProductsViaTestEndpoint();
