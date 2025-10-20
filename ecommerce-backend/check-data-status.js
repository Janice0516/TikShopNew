#!/usr/bin/env node

// 检查Render数据库中的数据状态
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function checkDataStatus() {
  try {
    console.log('🔍 检查Render数据库数据状态...');
    
    // 1. 测试API连接
    console.log('🔌 测试API连接...');
    const statusResponse = await axios.get(`${API_BASE_URL}/test/status`);
    console.log('✅ API连接正常:', statusResponse.data.message);
    
    // 2. 测试管理员登录
    console.log('🔐 测试管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    if (adminLoginResponse.data.code === 200) {
      console.log('✅ 管理员登录成功');
      const adminToken = adminLoginResponse.data.data.token;
      
      // 3. 测试管理员获取个人信息
      console.log('👤 测试管理员个人信息...');
      try {
        const profileResponse = await axios.get(`${API_BASE_URL}/admin/profile`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log('✅ 管理员个人信息获取成功:', profileResponse.data.data.username);
      } catch (error) {
        console.log('⚠️ 管理员个人信息获取失败:', error.response?.data?.message || error.message);
      }
      
      // 4. 测试获取统计数据
      console.log('📊 测试获取统计数据...');
      try {
        const statsResponse = await axios.get(`${API_BASE_URL}/admin/stats`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log('✅ 统计数据获取成功:');
        console.log(`   👥 商家数量: ${statsResponse.data.data.merchantCount || 0}`);
        console.log(`   🛍️ 商品数量: ${statsResponse.data.data.productCount || 0}`);
        console.log(`   📦 订单数量: ${statsResponse.data.data.orderCount || 0}`);
        console.log(`   👤 用户数量: ${statsResponse.data.data.userCount || 0}`);
      } catch (error) {
        console.log('⚠️ 统计数据获取失败:', error.response?.data?.message || error.message);
      }
      
    } else {
      console.log('❌ 管理员登录失败');
    }
    
    // 5. 测试商家登录
    console.log('🏪 测试商家登录...');
    try {
      const merchantLoginResponse = await axios.post(`${API_BASE_URL}/merchant/login`, {
        username: 'techstore_malaysia',
        password: '123456'
      });
      
      if (merchantLoginResponse.data.code === 200) {
        console.log('✅ 商家登录成功');
        const merchantToken = merchantLoginResponse.data.data.token;
        
        // 测试商家获取个人信息
        try {
          const merchantProfileResponse = await axios.get(`${API_BASE_URL}/merchant/profile`, {
            headers: { Authorization: `Bearer ${merchantToken}` }
          });
          console.log('✅ 商家个人信息获取成功:', merchantProfileResponse.data.data.merchant_name);
        } catch (error) {
          console.log('⚠️ 商家个人信息获取失败:', error.response?.data?.message || error.message);
        }
        
      } else {
        console.log('⚠️ 商家登录失败，可能需要先创建商家');
      }
    } catch (error) {
      console.log('⚠️ 商家登录测试失败:', error.response?.data?.message || error.message);
    }
    
    // 6. 测试用户登录
    console.log('👤 测试用户登录...');
    try {
      const userLoginResponse = await axios.post(`${API_BASE_URL}/user/login`, {
        phone: '13800138000',
        password: '123456'
      });
      
      if (userLoginResponse.data.code === 200) {
        console.log('✅ 用户登录成功');
      } else {
        console.log('⚠️ 用户登录失败，可能需要先创建用户');
      }
    } catch (error) {
      console.log('⚠️ 用户登录测试失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ 数据状态检查完成！');
    
  } catch (error) {
    console.error('❌ 数据状态检查失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

checkDataStatus();
