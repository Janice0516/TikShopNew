#!/usr/bin/env node

// 检查数据库连接和分类表状态
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function checkDatabaseAndCategoryTable() {
  try {
    console.log('🔍 检查数据库连接和分类表状态...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 检查健康状态
    console.log('🏥 检查健康状态...');
    try {
      const healthResponse = await axios.get(`${API_BASE_URL}/health`);
      console.log('✅ 健康检查正常:', healthResponse.data);
    } catch (error) {
      console.log('❌ 健康检查失败:', error.response?.data?.message || error.message);
    }
    
    // 3. 尝试获取分类列表（不认证）
    console.log('📂 尝试获取分类列表（无认证）...');
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category`);
      console.log('✅ 分类列表API正常（无认证）:', categoriesResponse.data);
    } catch (error) {
      console.log('❌ 分类列表API失败（无认证）:', error.response?.status, error.response?.data?.message || error.message);
    }
    
    // 4. 尝试获取分类列表（认证）
    console.log('📂 尝试获取分类列表（认证）...');
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 分类列表API正常（认证）:', categoriesResponse.data);
    } catch (error) {
      console.log('❌ 分类列表API失败（认证）:', error.response?.status, error.response?.data?.message || error.message);
      console.log('   完整错误响应:', JSON.stringify(error.response?.data));
    }
    
    // 5. 尝试创建分类
    console.log('📂 尝试创建分类...');
    try {
      const createCategoryResponse = await axios.post(`${API_BASE_URL}/category`, {
        name: 'Test Category',
        parentId: 0,
        level: 1,
        sort: 0,
        status: 1
      }, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 创建分类API正常:', createCategoryResponse.data);
    } catch (error) {
      console.log('❌ 创建分类API失败:', error.response?.status, error.response?.data?.message || error.message);
      console.log('   完整错误响应:', JSON.stringify(error.response?.data));
    }
    
    // 6. 检查商品分类端点
    console.log('🛍️ 检查商品分类端点...');
    try {
      const productCategoriesResponse = await axios.get(`${API_BASE_URL}/products/categories`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 商品分类API正常:', productCategoriesResponse.data);
    } catch (error) {
      console.log('❌ 商品分类API失败:', error.response?.status, error.response?.data?.message || error.message);
      console.log('   完整错误响应:', JSON.stringify(error.response?.data));
    }
    
    // 7. 检查商品列表端点
    console.log('🛍️ 检查商品列表端点...');
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/products`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 商品列表API正常:', productsResponse.data);
    } catch (error) {
      console.log('❌ 商品列表API失败:', error.response?.status, error.response?.data?.message || error.message);
      console.log('   完整错误响应:', JSON.stringify(error.response?.data));
    }
    
    console.log('✅ 数据库和分类表检查完成！');
    
  } catch (error) {
    console.error('❌ 检查失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

checkDatabaseAndCategoryTable();
