#!/usr/bin/env node

// 调试JWT认证问题
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function debugJWTAuth() {
  try {
    console.log('🔍 调试JWT认证问题...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    console.log('登录响应:', JSON.stringify(adminLoginResponse.data, null, 2));
    
    if (adminLoginResponse.data.code === 200) {
      const adminToken = adminLoginResponse.data.data.token;
      console.log('获取到的Token:', adminToken);
      
      // 2. 测试不同的认证方式
      console.log('🔑 测试Token认证...');
      
      // 方式1: Bearer token
      try {
        const response1 = await axios.get(`${API_BASE_URL}/admin/profile`, {
          headers: { 
            'Authorization': `Bearer ${adminToken}`,
            'Content-Type': 'application/json'
          }
        });
        console.log('✅ Bearer token 认证成功:', response1.data);
      } catch (error) {
        console.log('❌ Bearer token 认证失败:', error.response?.data?.message || error.message);
      }
      
      // 方式2: 直接token
      try {
        const response2 = await axios.get(`${API_BASE_URL}/admin/profile`, {
          headers: { 
            'Authorization': adminToken,
            'Content-Type': 'application/json'
          }
        });
        console.log('✅ 直接token 认证成功:', response2.data);
      } catch (error) {
        console.log('❌ 直接token 认证失败:', error.response?.data?.message || error.message);
      }
      
      // 方式3: 使用test端点
      try {
        const response3 = await axios.post(`${API_BASE_URL}/test/admin-login`, {
          username: 'admin',
          password: 'admin123'
        });
        console.log('✅ 测试端点登录成功:', response3.data);
      } catch (error) {
        console.log('❌ 测试端点登录失败:', error.response?.data?.message || error.message);
      }
      
    } else {
      console.log('❌ 管理员登录失败');
    }
    
  } catch (error) {
    console.error('❌ 调试失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

debugJWTAuth();
