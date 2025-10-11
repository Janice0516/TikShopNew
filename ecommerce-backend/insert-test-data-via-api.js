#!/usr/bin/env node

// 通过API插入测试数据到Render数据库
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function insertTestDataViaAPI() {
  try {
    console.log('🚀 开始通过API插入测试数据...');
    
    // 1. 管理员登录获取token
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 创建测试商家
    console.log('👥 创建测试商家...');
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
        console.log(`   ⚠️ 商家 ${merchant.merchant_name} 可能已存在`);
      }
    }
    
    // 3. 创建测试用户
    console.log('👤 创建测试用户...');
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
        console.log(`   ⚠️ 用户 ${user.username} 可能已存在`);
      }
    }
    
    // 4. 创建商品分类
    console.log('📂 创建商品分类...');
    const categories = [
      { name: 'Electronics', description: 'Electronic devices and gadgets' },
      { name: 'Fashion', description: 'Clothing and accessories' },
      { name: 'Home & Garden', description: 'Home improvement and garden supplies' },
      { name: 'Sports & Outdoors', description: 'Sports equipment and outdoor gear' },
      { name: 'Beauty & Health', description: 'Beauty products and health supplements' }
    ];
    
    for (const category of categories) {
      try {
        await axios.post(`${API_BASE_URL}/category`, category, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建分类: ${category.name}`);
      } catch (error) {
        console.log(`   ⚠️ 分类 ${category.name} 可能已存在`);
      }
    }
    
    // 5. 检查数据
    console.log('📊 检查数据...');
    
    try {
      const merchantsResponse = await axios.get(`${API_BASE_URL}/merchant/list`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log(`   👥 商家数量: ${merchantsResponse.data.data?.length || 0}`);
    } catch (error) {
      console.log('   ⚠️ 无法获取商家数据');
    }
    
    try {
      const usersResponse = await axios.get(`${API_BASE_URL}/user/list`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log(`   👤 用户数量: ${usersResponse.data.data?.length || 0}`);
    } catch (error) {
      console.log('   ⚠️ 无法获取用户数据');
    }
    
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category/list`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log(`   📂 分类数量: ${categoriesResponse.data.data?.length || 0}`);
    } catch (error) {
      console.log('   ⚠️ 无法获取分类数据');
    }
    
    console.log('✅ 测试数据插入完成！');
    
  } catch (error) {
    console.error('❌ 数据插入失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

insertTestDataViaAPI();
