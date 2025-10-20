#!/usr/bin/env node

// 修正字段名后插入数据到Render数据库
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function insertDataWithCorrectFields() {
  try {
    console.log('🚀 使用正确字段名插入数据到Render数据库...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 创建商家（使用正确的字段名）
    console.log('👥 创建商家...');
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
      }
    ];
    
    for (const merchant of merchants) {
      try {
        const response = await axios.post(`${API_BASE_URL}/merchant/register`, merchant);
        console.log(`   ✅ 创建商家: ${merchant.merchantName} - ${response.data.message}`);
      } catch (error) {
        console.log(`   ⚠️ 商家 ${merchant.merchantName} 创建失败: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 3. 创建用户（需要验证码，先跳过）
    console.log('👤 跳过用户创建（需要验证码）...');
    
    // 4. 检查数据
    console.log('📊 检查插入的数据...');
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
    
    // 5. 测试商家登录
    console.log('🏪 测试商家登录...');
    try {
      const merchantLoginResponse = await axios.post(`${API_BASE_URL}/merchant/login`, {
        username: 'techstore_malaysia',
        password: '123456'
      });
      
      if (merchantLoginResponse.data.code === 200) {
        console.log('✅ 商家登录测试成功');
        const merchantToken = merchantLoginResponse.data.data.token;
        
        // 测试商家个人信息
        try {
          const merchantProfileResponse = await axios.get(`${API_BASE_URL}/merchant/profile`, {
            headers: { 
              'Authorization': `Bearer ${merchantToken}`,
              'Content-Type': 'application/json'
            }
          });
          console.log('✅ 商家个人信息获取成功:', merchantProfileResponse.data.data.merchantName);
        } catch (error) {
          console.log('⚠️ 商家个人信息获取失败:', error.response?.data?.message || error.message);
        }
      } else {
        console.log('⚠️ 商家登录失败');
      }
    } catch (error) {
      console.log('⚠️ 商家登录测试失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ 数据插入完成！');
    console.log('💡 提示：商家注册后需要管理员审核才能正常使用');
    
  } catch (error) {
    console.error('❌ 数据插入失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

insertDataWithCorrectFields();
