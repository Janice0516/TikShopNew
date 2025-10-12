#!/usr/bin/env node

// 完整数据上传脚本 - 上传所有本地数据到Render数据库
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function uploadAllLocalData() {
  try {
    console.log('🚀 开始上传所有本地数据到Render数据库...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 创建所有商家（使用正确的字段名）
    console.log('👥 创建所有商家...');
    const merchants = [
      {
        username: 'techstore_malaysia',
        password: '123456',
        merchantName: 'TechStore Malaysia',
        contactName: 'Ahmad Rahman',
        contactPhone: '012-3456789',
        shopName: 'TechStore Malaysia'
      },
      {
        username: 'fashion_hub_kl',
        password: '123456',
        merchantName: 'Fashion Hub KL',
        contactName: 'Siti Nurhaliza',
        contactPhone: '019-8765432',
        shopName: 'Fashion Hub KL'
      },
      {
        username: 'home_depot_my',
        password: '123456',
        merchantName: 'Home Depot Malaysia',
        contactName: 'Lim Wei Ming',
        contactPhone: '013-4567890',
        shopName: 'Home Depot Malaysia'
      },
      {
        username: 'sports_zone',
        password: '123456',
        merchantName: 'Sports Zone',
        contactName: 'Raj Kumar',
        contactPhone: '014-5678901',
        shopName: 'Sports Zone'
      },
      {
        username: 'beauty_paradise',
        password: '123456',
        merchantName: 'Beauty Paradise',
        contactName: 'Nurul Aisyah',
        contactPhone: '015-6789012',
        shopName: 'Beauty Paradise'
      },
      {
        username: 'book_world',
        password: '123456',
        merchantName: 'Book World Malaysia',
        contactName: 'Tan Mei Ling',
        contactPhone: '016-7890123',
        shopName: 'Book World Malaysia'
      },
      {
        username: 'toy_kingdom',
        password: '123456',
        merchantName: 'Toy Kingdom',
        contactName: 'Muhammad Ali',
        contactPhone: '017-8901234',
        shopName: 'Toy Kingdom'
      },
      {
        username: 'auto_parts_pro',
        password: '123456',
        merchantName: 'Auto Parts Pro',
        contactName: 'David Chen',
        contactPhone: '018-9012345',
        shopName: 'Auto Parts Pro'
      }
    ];
    
    for (const merchant of merchants) {
      try {
        const response = await axios.post(`${API_BASE_URL}/merchant/register`, merchant);
        console.log(`   ✅ 创建商家: ${merchant.merchantName} - ${response.data.message}`);
      } catch (error) {
        console.log(`   ⚠️ 商家 ${merchant.merchantName} 可能已存在: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 3. 创建商品分类
    console.log('📂 创建商品分类...');
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
    
    for (const category of categories) {
      try {
        await axios.post(`${API_BASE_URL}/category`, category, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建分类: ${category.name}`);
      } catch (error) {
        console.log(`   ⚠️ 分类 ${category.name} 可能已存在: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 4. 创建子分类
    console.log('📁 创建子分类...');
    const subCategories = [
      { name: 'Smartphones', description: 'Mobile phones and accessories', parentId: 1 },
      { name: 'Laptops', description: 'Laptop computers and accessories', parentId: 1 },
      { name: 'Audio', description: 'Headphones, speakers, and audio equipment', parentId: 1 },
      { name: 'Cameras', description: 'Digital cameras and photography equipment', parentId: 1 },
      { name: 'Men\'s Clothing', description: 'Men\'s apparel and accessories', parentId: 2 },
      { name: 'Women\'s Clothing', description: 'Women\'s apparel and accessories', parentId: 2 },
      { name: 'Shoes', description: 'Footwear for men and women', parentId: 2 },
      { name: 'Accessories', description: 'Fashion accessories and jewelry', parentId: 2 },
      { name: 'Furniture', description: 'Home and office furniture', parentId: 3 },
      { name: 'Kitchen & Dining', description: 'Kitchen appliances and dining accessories', parentId: 3 },
      { name: 'Garden Tools', description: 'Gardening equipment and tools', parentId: 3 },
      { name: 'Home Decor', description: 'Decorative items and home accessories', parentId: 3 }
    ];
    
    for (const subCategory of subCategories) {
      try {
        await axios.post(`${API_BASE_URL}/category`, subCategory, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建子分类: ${subCategory.name}`);
      } catch (error) {
        console.log(`   ⚠️ 子分类 ${subCategory.name} 可能已存在: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 5. 创建测试用户（使用测试端点）
    console.log('👤 创建测试用户...');
    const testUsers = [
      { phone: '13800138000', password: '123456', nickname: '测试用户1' },
      { phone: '13800138001', password: '123456', nickname: '测试用户2' },
      { phone: '13800138002', password: '123456', nickname: '测试用户3' },
      { phone: '13800138003', password: '123456', nickname: '测试用户4' },
      { phone: '13800138004', password: '123456', nickname: '测试用户5' }
    ];
    
    for (const user of testUsers) {
      try {
        // 使用测试端点创建用户
        await axios.post(`${API_BASE_URL}/test/user-login`, user);
        console.log(`   ✅ 创建测试用户: ${user.nickname}`);
      } catch (error) {
        console.log(`   ⚠️ 测试用户 ${user.nickname} 创建失败: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 6. 检查数据统计
    console.log('📊 检查数据统计...');
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
        
        // 显示最近订单
        if (statsResponse.data.data.recentOrders?.length > 0) {
          console.log('   📋 最近订单:');
          statsResponse.data.data.recentOrders.forEach((order, index) => {
            console.log(`      ${index + 1}. ${order.orderNo} - ${order.customerName} - RM${order.totalAmount}`);
          });
        }
        
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
    
    // 7. 测试商家登录
    console.log('🏪 测试商家登录...');
    const testMerchants = [
      { username: 'techstore_malaysia', password: '123456' },
      { username: 'fashion_hub_kl', password: '123456' },
      { username: 'home_depot_my', password: '123456' }
    ];
    
    for (const merchant of testMerchants) {
      try {
        const merchantLoginResponse = await axios.post(`${API_BASE_URL}/merchant/login`, merchant);
        
        if (merchantLoginResponse.data.code === 200) {
          console.log(`   ✅ 商家 ${merchant.username} 登录成功`);
        } else {
          console.log(`   ⚠️ 商家 ${merchant.username} 登录失败`);
        }
      } catch (error) {
        console.log(`   ⚠️ 商家 ${merchant.username} 登录测试失败: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 8. 测试用户登录
    console.log('👤 测试用户登录...');
    try {
      const userLoginResponse = await axios.post(`${API_BASE_URL}/test/user-login`, {
        phone: '13800138000',
        password: '123456'
      });
      
      if (userLoginResponse.data.code === 200) {
        console.log('✅ 测试用户登录成功');
      } else {
        console.log('⚠️ 测试用户登录失败');
      }
    } catch (error) {
      console.log('⚠️ 测试用户登录失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ 所有本地数据上传完成！');
    console.log('💡 提示：');
    console.log('   1. 商家注册后需要管理员审核才能正常使用');
    console.log('   2. 可以在管理后台查看和审核商家');
    console.log('   3. 测试用户可以通过测试端点登录');
    console.log('   4. 所有数据都已保存到Render数据库');
    
  } catch (error) {
    console.error('❌ 数据上传失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

uploadAllLocalData();
