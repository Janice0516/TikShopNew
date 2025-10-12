const axios = require('axios');

const API_BASE = 'https://tiktokshop-api.onrender.com/api';

async function debugEnvironment() {
  console.log('🔍 调试环境变量配置\n');

  try {
    // 1. 测试健康检查端点（如果存在）
    console.log('1. 测试API健康状态...');
    try {
      const healthResponse = await axios.get(`${API_BASE}/health`);
      console.log('   ✅ 健康检查:', healthResponse.data);
    } catch (error) {
      console.log('   ❌ 健康检查失败:', error.response?.status || error.message);
    }

    // 2. 测试公开分类API（不依赖数据库连接）
    console.log('\n2. 测试公开分类API...');
    try {
      const categoryResponse = await axios.get(`${API_BASE}/public-categories`);
      console.log('   ✅ 分类API正常');
      console.log('   分类数量:', categoryResponse.data.data.data.length);
    } catch (error) {
      console.log('   ❌ 分类API失败:', error.response?.data?.message || error.message);
    }

    // 3. 测试商家登录（依赖数据库）
    console.log('\n3. 测试商家登录...');
    try {
      const loginResponse = await axios.post(`${API_BASE}/merchant/login`, {
        username: 'merchant001',
        password: 'password123'
      });
      console.log('   ✅ 商家登录成功');
      console.log('   响应:', loginResponse.data);
    } catch (error) {
      console.log('   ❌ 商家登录失败:', error.response?.data?.message || error.message);
    }

    // 4. 测试提现列表API
    console.log('\n4. 测试提现列表API...');
    try {
      const withdrawalResponse = await axios.get(`${API_BASE}/withdrawal/list`);
      console.log('   ✅ 提现列表API正常');
      console.log('   数据:', withdrawalResponse.data);
    } catch (error) {
      console.log('   ❌ 提现列表API失败:', error.response?.data?.message || error.message);
    }

  } catch (error) {
    console.error('❌ 调试过程中出错:', error.message);
  }

  console.log('\n📋 环境变量检查清单:');
  console.log('请在Render Dashboard中确认以下环境变量已正确设置:');
  console.log('');
  console.log('DB_TYPE=postgres');
  console.log('DB_HOST=dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com');
  console.log('DB_PORT=5432');
  console.log('DB_USERNAME=tiktokshop_slkz_user');
  console.log('DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn');
  console.log('DB_DATABASE=tiktokshop_slkz');
  console.log('NODE_ENV=production');
  console.log('');
  console.log('⚠️  特别注意: DB_DATABASE 必须是 tiktokshop_slkz (不是 tikshop_slkz)');
}

debugEnvironment();
