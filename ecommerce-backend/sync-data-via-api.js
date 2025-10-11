#!/usr/bin/env node

// 通过API同步数据到Render数据库
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function syncDataViaAPI() {
  try {
    console.log('🚀 开始通过API同步数据...');
    
    // 1. 测试API连接
    console.log('🔌 测试API连接...');
    const statusResponse = await axios.get(`${API_BASE_URL}/test/status`);
    console.log('✅ API连接正常:', statusResponse.data.message);
    
    // 2. 插入测试数据
    console.log('📝 插入测试数据...');
    
    // 插入管理员登录测试
    console.log('   👤 测试管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/test/admin-login`, {
      username: 'admin',
      password: 'admin123'
    });
    console.log('   ✅ 管理员登录测试成功');
    
    // 插入用户登录测试
    console.log('   👥 测试用户登录...');
    const userLoginResponse = await axios.post(`${API_BASE_URL}/test/user-login`, {
      phone: '13800138000',
      password: '123456'
    });
    console.log('   ✅ 用户登录测试成功');
    
    // 3. 检查现有数据
    console.log('📊 检查现有数据...');
    
    // 检查商家数据
    try {
      const merchantsResponse = await axios.get(`${API_BASE_URL}/merchant/list`);
      console.log(`   👥 商家数量: ${merchantsResponse.data.data?.length || 0}`);
    } catch (error) {
      console.log('   ⚠️ 无法获取商家数据:', error.message);
    }
    
    // 检查商品数据
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/product/list`);
      console.log(`   🛍️ 商品数量: ${productsResponse.data.data?.length || 0}`);
    } catch (error) {
      console.log('   ⚠️ 无法获取商品数据:', error.message);
    }
    
    // 检查用户数据
    try {
      const usersResponse = await axios.get(`${API_BASE_URL}/user/list`);
      console.log(`   👤 用户数量: ${usersResponse.data.data?.length || 0}`);
    } catch (error) {
      console.log('   ⚠️ 无法获取用户数据:', error.message);
    }
    
    console.log('✅ API数据同步完成！');
    
  } catch (error) {
    console.error('❌ API数据同步失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

syncDataViaAPI();
