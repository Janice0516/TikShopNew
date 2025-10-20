#!/usr/bin/env node

// 通过API插入数据到Render数据库
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function insertDataToRender() {
  try {
    console.log('🚀 开始插入数据到Render数据库...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 创建商家
    console.log('👥 创建商家...');
    const merchants = [
      {
        username: 'techstore_malaysia',
        password: '123456',
        email: 'contact@techstore.com.my',
        phone: '+60123456789',
        merchant_name: 'TechStore Malaysia',
        contact_name: 'Ahmad Rahman',
        business_license: 'BL2024001'
      },
      {
        username: 'fashion_hub_kl',
        password: '123456',
        email: 'info@fashionhub.com.my',
        phone: '+60198765432',
        merchant_name: 'Fashion Hub KL',
        contact_name: 'Siti Nurhaliza',
        business_license: 'BL2024002'
      },
      {
        username: 'home_depot_my',
        password: '123456',
        email: 'sales@homedepot.com.my',
        phone: '+60134567890',
        merchant_name: 'Home Depot Malaysia',
        contact_name: 'Lim Wei Ming',
        business_license: 'BL2024003'
      }
    ];
    
    for (const merchant of merchants) {
      try {
        await axios.post(`${API_BASE_URL}/merchant/register`, merchant);
        console.log(`   ✅ 创建商家: ${merchant.merchant_name}`);
      } catch (error) {
        console.log(`   ⚠️ 商家 ${merchant.merchant_name} 可能已存在: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 3. 创建用户
    console.log('👤 创建用户...');
    const users = [
      {
        username: 'john_doe',
        password: '123456',
        email: 'john.doe@email.com',
        phone: '+60123456788'
      },
      {
        username: 'sarah_wong',
        password: '123456',
        email: 'sarah.wong@email.com',
        phone: '+60123456787'
      },
      {
        username: 'ahmad_ali',
        password: '123456',
        email: 'ahmad.ali@email.com',
        phone: '+60123456786'
      }
    ];
    
    for (const user of users) {
      try {
        await axios.post(`${API_BASE_URL}/user/register`, user);
        console.log(`   ✅ 创建用户: ${user.username}`);
      } catch (error) {
        console.log(`   ⚠️ 用户 ${user.username} 可能已存在: ${error.response?.data?.message || error.message}`);
      }
    }
    
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
          console.log('✅ 商家个人信息获取成功:', merchantProfileResponse.data.data.merchant_name);
        } catch (error) {
          console.log('⚠️ 商家个人信息获取失败:', error.response?.data?.message || error.message);
        }
      } else {
        console.log('⚠️ 商家登录失败');
      }
    } catch (error) {
      console.log('⚠️ 商家登录测试失败:', error.response?.data?.message || error.message);
    }
    
    // 6. 测试用户登录
    console.log('👤 测试用户登录...');
    try {
      const userLoginResponse = await axios.post(`${API_BASE_URL}/user/login`, {
        phone: '+60123456788',
        password: '123456'
      });
      
      if (userLoginResponse.data.code === 200) {
        console.log('✅ 用户登录测试成功');
      } else {
        console.log('⚠️ 用户登录失败');
      }
    } catch (error) {
      console.log('⚠️ 用户登录测试失败:', error.response?.data?.message || error.message);
    }
    
    console.log('✅ 数据插入完成！');
    
  } catch (error) {
    console.error('❌ 数据插入失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

insertDataToRender();
