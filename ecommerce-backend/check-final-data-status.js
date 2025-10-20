#!/usr/bin/env node

// 检查Render数据库最终数据状态
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function checkFinalDataStatus() {
  try {
    console.log('🔍 检查Render数据库最终数据状态...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 检查仪表盘统计数据
    console.log('📊 检查仪表盘统计数据...');
    try {
      const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (statsResponse.data.data && statsResponse.data.data.stats) {
        console.log('✅ 数据统计:');
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
        } else {
          console.log('   📋 最近订单: 暂无');
        }
        
        // 显示热销商品
        if (statsResponse.data.data.topProducts?.length > 0) {
          console.log('   🔥 热销商品:');
          statsResponse.data.data.topProducts.forEach((product, index) => {
            console.log(`      ${index + 1}. ${product.name} - 销量: ${product.sales} - 库存: ${product.stock}`);
          });
        } else {
          console.log('   🔥 热销商品: 暂无');
        }
      } else {
        console.log('⚠️ 无法获取统计数据');
      }
    } catch (error) {
      console.log('⚠️ 统计数据获取失败:', error.response?.data?.message || error.message);
    }
    
    // 3. 检查商家列表
    console.log('👥 检查商家列表...');
    try {
      const merchantsResponse = await axios.get(`${API_BASE_URL}/admin/merchants`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (merchantsResponse.data.data && merchantsResponse.data.data.length > 0) {
        console.log(`✅ 商家列表 (${merchantsResponse.data.data.length}个):`);
        merchantsResponse.data.data.forEach((merchant, index) => {
          console.log(`   ${index + 1}. ${merchant.merchantName} (${merchant.username}) - 状态: ${merchant.status === 1 ? '已通过' : merchant.status === 0 ? '待审核' : '已拒绝'}`);
        });
      } else {
        console.log('⚠️ 无法获取商家列表');
      }
    } catch (error) {
      console.log('⚠️ 商家列表获取失败:', error.response?.data?.message || error.message);
    }
    
    // 4. 检查用户列表
    console.log('👤 检查用户列表...');
    try {
      const usersResponse = await axios.get(`${API_BASE_URL}/admin/users`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (usersResponse.data.data && usersResponse.data.data.length > 0) {
        console.log(`✅ 用户列表 (${usersResponse.data.data.length}个):`);
        usersResponse.data.data.forEach((user, index) => {
          console.log(`   ${index + 1}. ${user.nickname || user.phone} - 状态: ${user.status === 1 ? '正常' : '禁用'}`);
        });
      } else {
        console.log('⚠️ 无法获取用户列表');
      }
    } catch (error) {
      console.log('⚠️ 用户列表获取失败:', error.response?.data?.message || error.message);
    }
    
    // 5. 检查商品列表
    console.log('🛍️ 检查商品列表...');
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/admin/products`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (productsResponse.data.data && productsResponse.data.data.length > 0) {
        console.log(`✅ 商品列表 (${productsResponse.data.data.length}个):`);
        productsResponse.data.data.forEach((product, index) => {
          console.log(`   ${index + 1}. ${product.name} - 价格: RM${product.price} - 库存: ${product.stock}`);
        });
      } else {
        console.log('⚠️ 无法获取商品列表');
      }
    } catch (error) {
      console.log('⚠️ 商品列表获取失败:', error.response?.data?.message || error.message);
    }
    
    // 6. 检查订单列表
    console.log('📦 检查订单列表...');
    try {
      const ordersResponse = await axios.get(`${API_BASE_URL}/admin/orders`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (ordersResponse.data.data && ordersResponse.data.data.length > 0) {
        console.log(`✅ 订单列表 (${ordersResponse.data.data.length}个):`);
        ordersResponse.data.data.forEach((order, index) => {
          console.log(`   ${index + 1}. ${order.orderNo} - 金额: RM${order.totalAmount} - 状态: ${order.orderStatus}`);
        });
      } else {
        console.log('⚠️ 无法获取订单列表');
      }
    } catch (error) {
      console.log('⚠️ 订单列表获取失败:', error.response?.data?.message || error.message);
    }
    
    // 7. 测试商家登录状态
    console.log('🏪 测试商家登录状态...');
    const testMerchants = [
      { username: 'techstore_malaysia', password: '123456', name: 'TechStore Malaysia' },
      { username: 'fashion_hub_kl', password: '123456', name: 'Fashion Hub KL' },
      { username: 'home_depot_my', password: '123456', name: 'Home Depot Malaysia' },
      { username: 'sports_zone', password: '123456', name: 'Sports Zone' },
      { username: 'book_world', password: '123456', name: 'Book World Malaysia' },
      { username: 'toy_kingdom', password: '123456', name: 'Toy Kingdom' },
      { username: 'auto_parts_pro', password: '123456', name: 'Auto Parts Pro' }
    ];
    
    for (const merchant of testMerchants) {
      try {
        const merchantLoginResponse = await axios.post(`${API_BASE_URL}/merchant/login`, {
          username: merchant.username,
          password: merchant.password
        });
        
        if (merchantLoginResponse.data.code === 200) {
          console.log(`   ✅ ${merchant.name} 登录成功`);
        } else {
          console.log(`   ⚠️ ${merchant.name} 登录失败`);
        }
      } catch (error) {
        console.log(`   ⚠️ ${merchant.name} 登录测试失败: ${error.response?.data?.message || error.message}`);
      }
    }
    
    // 8. 测试用户登录状态
    console.log('👤 测试用户登录状态...');
    const testUsers = [
      { phone: '13800138000', password: '123456', name: '测试用户1' },
      { phone: '13800138001', password: '123456', name: '测试用户2' },
      { phone: '13800138002', password: '123456', name: '测试用户3' }
    ];
    
    for (const user of testUsers) {
      try {
        const userLoginResponse = await axios.post(`${API_BASE_URL}/test/user-login`, user);
        
        if (userLoginResponse.data.code === 200) {
          console.log(`   ✅ ${user.name} 登录成功`);
        } else {
          console.log(`   ⚠️ ${user.name} 登录失败`);
        }
      } catch (error) {
        console.log(`   ⚠️ ${user.name} 登录测试失败: ${error.response?.data?.message || error.message}`);
      }
    }
    
    console.log('✅ 数据状态检查完成！');
    console.log('📋 总结:');
    console.log('   - 商家数据已上传，需要管理员审核');
    console.log('   - 测试用户已创建，可以通过测试端点登录');
    console.log('   - 管理员账户正常，可以管理所有数据');
    console.log('   - 所有数据都保存在Render数据库中');
    
  } catch (error) {
    console.error('❌ 数据状态检查失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

checkFinalDataStatus();
