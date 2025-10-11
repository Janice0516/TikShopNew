#!/usr/bin/env node

// 修复JWT认证问题 - 正确提取token
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function testCorrectedAuth() {
  try {
    console.log('🔍 测试修正后的JWT认证...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    if (adminLoginResponse.data.code === 200) {
      // 正确提取token - 从嵌套的data结构中
      const adminToken = adminLoginResponse.data.data.data.token;
      console.log('✅ 正确提取Token:', adminToken);
      
      // 2. 测试认证
      console.log('🔑 测试Token认证...');
      
      try {
        const profileResponse = await axios.get(`${API_BASE_URL}/admin/profile`, {
          headers: { 
            'Authorization': `Bearer ${adminToken}`,
            'Content-Type': 'application/json'
          }
        });
        console.log('✅ 管理员个人信息获取成功:', profileResponse.data.data.username);
      } catch (error) {
        console.log('❌ 管理员个人信息获取失败:', error.response?.data?.message || error.message);
      }
      
      // 3. 测试仪表盘统计数据
      console.log('📊 测试仪表盘统计数据...');
      try {
        const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
          headers: { 
            'Authorization': `Bearer ${adminToken}`,
            'Content-Type': 'application/json'
          }
        });
        console.log('✅ 仪表盘统计数据获取成功:');
        console.log(`   👥 商家数量: ${statsResponse.data.data.stats.merchants || 0}`);
        console.log(`   🛍️ 商品数量: ${statsResponse.data.data.stats.products || 0}`);
        console.log(`   📦 订单数量: ${statsResponse.data.data.stats.orders || 0}`);
        console.log(`   👤 用户数量: ${statsResponse.data.data.stats.users || 0}`);
        
        // 如果有数据，显示详细信息
        if (statsResponse.data.data.stats.merchants > 0) {
          console.log('   🎉 数据库中已有商家数据！');
        }
        if (statsResponse.data.data.stats.products > 0) {
          console.log('   🎉 数据库中已有商品数据！');
        }
        if (statsResponse.data.data.stats.users > 0) {
          console.log('   🎉 数据库中已有用户数据！');
        }
        if (statsResponse.data.data.stats.orders > 0) {
          console.log('   🎉 数据库中已有订单数据！');
        }
        
      } catch (error) {
        console.log('❌ 仪表盘统计数据获取失败:', error.response?.data?.message || error.message);
      }
      
      // 4. 测试商家登录
      console.log('🏪 测试商家登录...');
      try {
        const merchantLoginResponse = await axios.post(`${API_BASE_URL}/merchant/login`, {
          username: 'techstore_malaysia',
          password: '123456'
        });
        
        if (merchantLoginResponse.data.code === 200) {
          const merchantToken = merchantLoginResponse.data.data.token;
          console.log('✅ 商家登录成功');
          
          // 测试商家个人信息
          try {
            const merchantProfileResponse = await axios.get(`${API_BASE_URL}/merchant/profile`, {
              headers: { 
                'Authorization': `Bearer ${merchantToken}`,
                'Content-Type': 'application/json'
              }
            });
            console.log('✅ 商家个人信息获取成功:', merchantProfileResponse.data.data.merchant_name);
          } catch (error) {
            console.log('❌ 商家个人信息获取失败:', error.response?.data?.message || error.message);
          }
        } else {
          console.log('⚠️ 商家登录失败，可能需要先创建商家');
        }
      } catch (error) {
        console.log('⚠️ 商家登录测试失败:', error.response?.data?.message || error.message);
      }
      
    } else {
      console.log('❌ 管理员登录失败');
    }
    
    console.log('✅ 认证测试完成！');
    
  } catch (error) {
    console.error('❌ 测试失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

testCorrectedAuth();
