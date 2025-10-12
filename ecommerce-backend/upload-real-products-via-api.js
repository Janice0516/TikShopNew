#!/usr/bin/env node

// 通过API上传真实商品数据（修复版本）
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function uploadRealProductsViaAPI() {
  try {
    console.log('🚀 通过API上传真实商品数据...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 先检查现有的分类和商品
    console.log('🔍 检查现有数据...');
    
    // 检查分类
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category/list`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 分类API正常:', categoriesResponse.data);
    } catch (error) {
      console.log('❌ 分类API失败:', error.response?.data?.message || error.message);
    }
    
    // 检查商品
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/products`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 商品API正常:', productsResponse.data);
    } catch (error) {
      console.log('❌ 商品API失败:', error.response?.data?.message || error.message);
    }
    
    // 3. 尝试创建分类
    console.log('📂 尝试创建分类...');
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
        categoryMap[category.name] = response.data.data.id;
        console.log(`   ✅ 创建分类: ${category.name} (ID: ${response.data.data.id})`);
      } catch (error) {
        console.log(`   ⚠️ 分类 ${category.name} 创建失败: ${error.response?.data?.message || error.message}`);
        // 尝试获取现有分类
        try {
          const listResponse = await axios.get(`${API_BASE_URL}/category/list`, {
            headers: { Authorization: `Bearer ${adminToken}` }
          });
          const existingCategory = listResponse.data.data.find(cat => cat.name === category.name);
          if (existingCategory) {
            categoryMap[category.name] = existingCategory.id;
            console.log(`   ✅ 使用现有分类: ${category.name} (ID: ${existingCategory.id})`);
          }
        } catch (listError) {
          console.log(`   ❌ 无法获取分类列表: ${listError.response?.data?.message || listError.message}`);
        }
      }
    }
    
    // 4. 尝试创建商品（使用最简单的数据）
    console.log('🛍️ 尝试创建商品...');
    const simpleProducts = [
      {
        name: 'iPhone 15 Pro Max 256GB',
        description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system',
        categoryId: categoryMap['Electronics'] || 1,
        brand: 'Apple',
        mainImage: '/static/products/iphone15pro.jpg',
        images: JSON.stringify(['/static/products/iphone15pro.jpg']),
        costPrice: 4500.00,
        suggestPrice: 4999.00,
        stock: 25
      },
      {
        name: 'Nike Air Max 270',
        description: 'Comfortable running shoes with Max Air cushioning',
        categoryId: categoryMap['Fashion'] || 2,
        brand: 'Nike',
        mainImage: '/static/products/nike-airmax.jpg',
        images: JSON.stringify(['/static/products/nike-airmax.jpg']),
        costPrice: 350.00,
        suggestPrice: 399.00,
        stock: 100
      },
      {
        name: 'IKEA MALM Bed Frame',
        description: 'Minimalist bed frame with storage drawers',
        categoryId: categoryMap['Home & Garden'] || 3,
        brand: 'IKEA',
        mainImage: '/static/products/ikea-malm.jpg',
        images: JSON.stringify(['/static/products/ikea-malm.jpg']),
        costPrice: 800.00,
        suggestPrice: 899.00,
        stock: 30
      }
    ];
    
    let successCount = 0;
    let failCount = 0;
    
    for (const product of simpleProducts) {
      try {
        const response = await axios.post(`${API_BASE_URL}/products`, product, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建商品: ${product.name} - ${product.brand}`);
        successCount++;
      } catch (error) {
        console.log(`   ❌ 商品 ${product.name} 创建失败: ${error.response?.data?.message || error.message}`);
        console.log(`       响应状态: ${error.response?.status}`);
        console.log(`       响应数据: ${JSON.stringify(error.response?.data)}`);
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
      } else {
        console.log('⚠️ 无法获取统计数据');
      }
    } catch (error) {
      console.log('⚠️ 统计数据获取失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ 真实商品数据上传尝试完成！');
    console.log(`📊 上传结果: 成功 ${successCount} 个，失败 ${failCount} 个`);
    
    if (failCount > 0) {
      console.log('💡 建议:');
      console.log('   1. 检查后端服务的错误日志');
      console.log('   2. 确认数据库表结构是否正确');
      console.log('   3. 检查商品API的权限配置');
      console.log('   4. 可以通过管理后台手动创建商品');
    }
    
  } catch (error) {
    console.error('❌ 真实商品数据上传失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

uploadRealProductsViaAPI();
