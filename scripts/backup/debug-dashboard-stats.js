const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function debugDashboardStats() {
  try {
    console.log('🔍 调试仪表盘统计数据API\n');

    // 1. 管理员登录
    console.log('1. 管理员登录...');
    const loginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });

    if (loginResponse.data.code !== 200) {
      console.log('❌ 管理员登录失败:', loginResponse.data.message);
      return;
    }

    const adminToken = loginResponse.data.data.token;
    console.log('✅ 管理员登录成功');

    // 2. 测试仪表盘统计数据API
    console.log('\n2. 测试仪表盘统计数据API...');
    try {
      const statsResponse = await axios.get(`${API_BASE_URL}/admin/dashboard/stats`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });

      console.log('📊 API响应状态:', statsResponse.status);
      console.log('📊 API响应数据:', JSON.stringify(statsResponse.data, null, 2));

      if (statsResponse.data.code === 200) {
        console.log('✅ 仪表盘统计数据获取成功');
        const stats = statsResponse.data.data.stats;
        console.log(`   👥 商家数量: ${stats.merchants}`);
        console.log(`   🛍️ 商品数量: ${stats.products}`);
        console.log(`   📦 订单数量: ${stats.orders}`);
        console.log(`   👤 用户数量: ${stats.users}`);
      } else {
        console.log('❌ 仪表盘统计数据获取失败:', statsResponse.data.message);
      }

    } catch (error) {
      console.log('❌ 仪表盘统计数据API调用失败:');
      console.log('   错误信息:', error.message);
      if (error.response) {
        console.log('   响应状态:', error.response.status);
        console.log('   响应数据:', JSON.stringify(error.response.data, null, 2));
      }
    }

    // 3. 测试各个单独的API
    console.log('\n3. 测试各个单独的API...');
    
    // 测试商品API
    try {
      const productsResponse = await axios.get(`${API_BASE_URL}/products`, {
        headers: { 'Authorization': `Bearer ${adminToken}` }
      });
      console.log('✅ 商品API正常，商品数量:', productsResponse.data.data?.length || 0);
    } catch (error) {
      console.log('❌ 商品API失败:', error.response?.data?.message || error.message);
    }

    // 测试商家API
    try {
      const merchantsResponse = await axios.get(`${API_BASE_URL}/merchants`, {
        headers: { 'Authorization': `Bearer ${adminToken}` }
      });
      console.log('✅ 商家API正常，商家数量:', merchantsResponse.data.data?.length || 0);
    } catch (error) {
      console.log('❌ 商家API失败:', error.response?.data?.message || error.message);
    }

    // 测试订单API
    try {
      const ordersResponse = await axios.get(`${API_BASE_URL}/orders`, {
        headers: { 'Authorization': `Bearer ${adminToken}` }
      });
      console.log('✅ 订单API正常，订单数量:', ordersResponse.data.data?.length || 0);
    } catch (error) {
      console.log('❌ 订单API失败:', error.response?.data?.message || error.message);
    }

    // 测试用户API
    try {
      const usersResponse = await axios.get(`${API_BASE_URL}/users`, {
        headers: { 'Authorization': `Bearer ${adminToken}` }
      });
      console.log('✅ 用户API正常，用户数量:', usersResponse.data.data?.length || 0);
    } catch (error) {
      console.log('❌ 用户API失败:', error.response?.data?.message || error.message);
    }

  } catch (error) {
    console.error('❌ 调试失败:', error.message);
  }
}

debugDashboardStats();
