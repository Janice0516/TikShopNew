const axios = require('axios');

const API_BASE = 'https://tiktokshop-api.onrender.com/api';

async function testRegister() {
  console.log('📝 测试商家注册API\n');

  try {
    const registerData = {
      username: 'merchant001',
      password: 'password123',
      merchantName: '测试商家001',
      contactPerson: '张三',
      phone: '13800138001',
      email: 'merchant001@test.com'
    };
    
    console.log('1. 尝试注册商家...');
    const response = await axios.post(`${API_BASE}/merchant/register`, registerData);
    console.log('   ✅ 注册成功:', response.data);
    
    // 尝试登录
    console.log('\n2. 尝试登录...');
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post(`${API_BASE}/merchant/login`, loginData);
    console.log('   ✅ 登录成功:', loginResponse.data);
    
    // 测试提现列表
    console.log('\n3. 测试提现列表...');
    const withdrawalResponse = await axios.get(`${API_BASE}/withdrawal/list`);
    console.log('   ✅ 提现列表:', withdrawalResponse.data);

  } catch (error) {
    console.error('❌ 错误:', error.response?.data?.message || error.message);
  }
}

testRegister();
