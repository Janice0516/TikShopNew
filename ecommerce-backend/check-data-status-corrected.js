#!/usr/bin/env node

// 检查Render数据库中的数据状态 - 修正版
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function checkDataStatusCorrected() {
  try {
    console.log('🔍 检查Render数据库数据状态（修正版）...');
    
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
      
      // 4. 测试获取仪表盘统计数据（正确的端点）
      console.log('📊 测试获取仪表盘统计数据...');
      try {
        const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log('✅ 仪表盘统计数据获取成功:');
        console.log(`   👥 商家数量: ${statsResponse.data.data.stats.merchants || 0}`);
        console.log(`   🛍️ 商品数量: ${statsResponse.data.data.stats.products || 0}`);
        console.log(`   📦 订单数量: ${statsResponse.data.data.stats.orders || 0}`);
        console.log(`   👤 用户数量: ${statsResponse.data.data.stats.users || 0}`);
        
        // 显示最近订单
        if (statsResponse.data.data.recentOrders?.length > 0) {
          console.log('   📋 最近订单:');
          statsResponse.data.data.recentOrders.forEach((order, index) => {
            console.log(`      ${index + 1}. ${order.orderNo} - ${order.customerName} - RM${order.totalAmount}`);
          });
        }
        
        // 显示热销商品
        if (statsResponse.data.data.topProducts?.length > 0) {
          console.log('   🔥 热销商品:');
          statsResponse.data.data.topProducts.forEach((product, index) => {
            console.log(`      ${index + 1}. ${product.name} - 销量: ${product.sales} - 库存: ${product.stock}`);
          });
        }
        
      } catch (error) {
        console.log('⚠️ 仪表盘统计数据获取失败:', error.response?.data?.message || error.message);
      }
      
      // 5. 测试获取用户列表
      console.log('👥 测试获取用户列表...');
      try {
        const usersResponse = await axios.get(`${API_BASE_URL}/admin/users`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ 用户列表获取成功，数量: ${usersResponse.data.data?.length || 0}`);
      } catch (error) {
        console.log('⚠️ 用户列表获取失败:', error.response?.data?.message || error.message);
      }
      
      // 6. 测试获取商家列表
      console.log('🏪 测试获取商家列表...');
      try {
        const merchantsResponse = await axios.get(`${API_BASE_URL}/admin/merchants`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ 商家列表获取成功，数量: ${merchantsResponse.data.data?.length || 0}`);
      } catch (error) {
        console.log('⚠️ 商家列表获取失败:', error.response?.data?.message || error.message);
      }
      
      // 7. 测试获取商品列表
      console.log('🛍️ 测试获取商品列表...');
      try {
        const productsResponse = await axios.get(`${API_BASE_URL}/admin/products`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ 商品列表获取成功，数量: ${productsResponse.data.data?.length || 0}`);
      } catch (error) {
        console.log('⚠️ 商品列表获取失败:', error.response?.data?.message || error.message);
      }
      
      // 8. 测试获取订单列表
      console.log('📦 测试获取订单列表...');
      try {
        const ordersResponse = await axios.get(`${API_BASE_URL}/admin/orders`, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`✅ 订单列表获取成功，数量: ${ordersResponse.data.data?.length || 0}`);
      } catch (error) {
        console.log('⚠️ 订单列表获取失败:', error.response?.data?.message || error.message);
      }
      
    } else {
      console.log('❌ 管理员登录失败');
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

checkDataStatusCorrected();
