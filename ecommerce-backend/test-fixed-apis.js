#!/usr/bin/env node

// 测试修复后的商品和分类API
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function testFixedAPIs() {
  try {
    console.log('🔧 测试修复后的API...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 测试分类API
    console.log('📂 测试分类API...');
    
    // 测试分类列表
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 分类列表API正常:', categoriesResponse.data);
    } catch (error) {
      console.log('❌ 分类列表API失败:', error.response?.data?.message || error.message);
    }
    
    // 测试创建分类
    try {
      const createCategoryResponse = await axios.post(`${API_BASE_URL}/category`, {
        name: 'Test Category',
        description: 'Test category description'
      }, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 创建分类API正常:', createCategoryResponse.data);
    } catch (error) {
      console.log('❌ 创建分类API失败:', error.response?.data?.message || error.message);
    }
    
    // 3. 测试商品API
    console.log('🛍️ 测试商品API...');
    
    // 测试商品列表
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/products`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 商品列表API正常:', productsResponse.data);
    } catch (error) {
      console.log('❌ 商品列表API失败:', error.response?.data?.message || error.message);
    }
    
    // 测试商品分类
    try {
      const productCategoriesResponse = await axios.get(`${API_BASE_URL}/products/categories`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 商品分类API正常:', productCategoriesResponse.data);
    } catch (error) {
      console.log('❌ 商品分类API失败:', error.response?.data?.message || error.message);
    }
    
    // 测试创建商品
    try {
      const createProductResponse = await axios.post(`${API_BASE_URL}/products`, {
        name: 'Test Product',
        description: 'Test product description',
        categoryId: 1,
        brand: 'Test Brand',
        mainImage: '/static/products/test.jpg',
        images: JSON.stringify(['/static/products/test.jpg']),
        costPrice: 100.00,
        suggestPrice: 150.00,
        stock: 10
      }, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 创建商品API正常:', createProductResponse.data);
    } catch (error) {
      console.log('❌ 创建商品API失败:', error.response?.data?.message || error.message);
      console.log('   响应状态:', error.response?.status);
      console.log('   响应数据:', JSON.stringify(error.response?.data));
    }
    
    // 4. 测试统计数据
    console.log('📊 测试统计数据...');
    try {
      const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (statsResponse.data.data && statsResponse.data.data.stats) {
        console.log('✅ 统计数据API正常:');
        console.log(`   👥 商家数量: ${statsResponse.data.data.stats.merchants || 0}`);
        console.log(`   🛍️ 商品数量: ${statsResponse.data.data.stats.products || 0}`);
        console.log(`   📦 订单数量: ${statsResponse.data.data.stats.orders || 0}`);
        console.log(`   👤 用户数量: ${statsResponse.data.data.stats.users || 0}`);
      } else {
        console.log('⚠️ 统计数据格式异常:', statsResponse.data);
      }
    } catch (error) {
      console.log('❌ 统计数据API失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ API测试完成！');
    
  } catch (error) {
    console.error('❌ API测试失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

testFixedAPIs();
