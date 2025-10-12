const axios = require('axios');

const API_BASE = 'https://tiktokshop-api.onrender.com/api';

async function testSimple() {
  console.log('🧪 简单测试提现API\n');

  try {
    // 测试商家登录
    console.log('1. 测试商家登录...');
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post(`${API_BASE}/merchant/login`, loginData);
    console.log('   ✅ 商家登录成功');
    
    const token = loginResponse.data.data.token;
    console.log('   Token:', token.substring(0, 20) + '...');
    
    // 测试带认证的余额查询
    console.log('\n2. 测试余额查询...');
    const headers = { Authorization: `Bearer ${token}` };
    
    try {
      const balanceResponse = await axios.get(`${API_BASE}/withdrawal/balance`, { headers });
      console.log('   ✅ 余额查询成功:', balanceResponse.data);
    } catch (error) {
      console.log('   ❌ 余额查询失败:', error.response?.data?.message);
    }

  } catch (error) {
    console.error('❌ 测试失败:', error.response?.data?.message || error.message);
  }
}

testSimple();
