#!/usr/bin/env node

// 测试商品API端点
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function testProductAPI() {
  try {
    console.log('🔍 测试商品API端点...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 测试商品列表API
    console.log('📋 测试商品列表API...');
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/products`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 商品列表API正常:', productsResponse.data);
    } catch (error) {
      console.log('❌ 商品列表API失败:', error.response?.data?.message || error.message);
    }
    
    // 3. 测试分类列表API
    console.log('📂 测试分类列表API...');
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category/list`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 分类列表API正常:', categoriesResponse.data);
    } catch (error) {
      console.log('❌ 分类列表API失败:', error.response?.data?.message || error.message);
    }
    
    // 4. 测试创建分类API
    console.log('📝 测试创建分类API...');
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
    
    // 5. 测试创建商品API（简单商品）
    console.log('🛍️ 测试创建商品API...');
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
      console.log('   响应数据:', error.response?.data);
    }
    
    // 6. 测试其他可能的商品端点
    console.log('🔍 测试其他商品端点...');
    const productEndpoints = [
      '/product/list',
      '/product',
      '/admin/products',
      '/admin/product'
    ];
    
    for (const endpoint of productEndpoints) {
      try {
        const response = await axios.get(`${API_BASE_URL}${endpoint}`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ ${endpoint} 正常:`, response.data);
      } catch (error) {
        console.log(`❌ ${endpoint} 失败:`, error.response?.data?.message || error.message);
      }
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

testProductAPI();
