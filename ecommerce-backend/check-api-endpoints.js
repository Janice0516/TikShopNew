#!/usr/bin/env node

// 检查API端点是否存在
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function checkAPIEndpoints() {
  try {
    console.log('🔍 检查API端点...');
    
    // 1. 检查基础API状态
    console.log('🔌 检查基础API状态...');
    try {
      const statusResponse = await axios.get(`${API_BASE_URL}/test/status`);
      console.log('✅ 基础API正常:', statusResponse.data);
    } catch (error) {
      console.log('❌ 基础API失败:', error.response?.data?.message || error.message);
    }
    
    // 2. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 3. 检查各种可能的分类端点
    console.log('📂 检查分类端点...');
    const categoryEndpoints = [
      '/category',
      '/categories',
      '/category/list',
      '/categories/list',
      '/admin/category',
      '/admin/categories'
    ];
    
    for (const endpoint of categoryEndpoints) {
      try {
        const response = await axios.get(`${API_BASE_URL}${endpoint}`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ ${endpoint} 正常:`, response.data);
      } catch (error) {
        console.log(`❌ ${endpoint} 失败:`, error.response?.status, error.response?.data?.message || error.message);
      }
    }
    
    // 4. 检查各种可能的商品端点
    console.log('🛍️ 检查商品端点...');
    const productEndpoints = [
      '/products',
      '/product',
      '/products/list',
      '/product/list',
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
        console.log(`❌ ${endpoint} 失败:`, error.response?.status, error.response?.data?.message || error.message);
      }
    }
    
    // 5. 检查管理端点
    console.log('👨‍💼 检查管理端点...');
    const adminEndpoints = [
      '/admin/dashboard/stats',
      '/admin/merchants',
      '/admin/users',
      '/admin/products',
      '/admin/orders'
    ];
    
    for (const endpoint of adminEndpoints) {
      try {
        const response = await axios.get(`${API_BASE_URL}${endpoint}`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ ${endpoint} 正常:`, response.data);
      } catch (error) {
        console.log(`❌ ${endpoint} 失败:`, error.response?.status, error.response?.data?.message || error.message);
      }
    }
    
    console.log('✅ API端点检查完成！');
    
  } catch (error) {
    console.error('❌ API端点检查失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

checkAPIEndpoints();
