const axios = require('axios');

async function testMerchantLogin() {
  console.log('🔍 测试商家登录API\n');

  try {
    // 测试商家登录
    console.log('1. 测试商家登录API...');
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post('https://tiktokshop-api.onrender.com/api/merchant/login', loginData);
    console.log('   ✅ 商家登录API正常');
    console.log('   响应状态:', loginResponse.status);
    console.log('   响应数据:', JSON.stringify(loginResponse.data, null, 2));

    // 测试商家信息API
    console.log('\n2. 测试商家信息API...');
    const token = loginResponse.data.data.data.token;
    const profileResponse = await axios.get('https://tiktokshop-api.onrender.com/api/merchant/profile', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    console.log('   ✅ 商家信息API正常');
    console.log('   响应状态:', profileResponse.status);
    console.log('   商家信息:', JSON.stringify(profileResponse.data, null, 2));

  } catch (error) {
    console.error('❌ 测试失败:', error.response?.status, error.response?.data?.message || error.message);
    if (error.response?.data) {
      console.error('   详细错误:', JSON.stringify(error.response.data, null, 2));
    }
  }
}

testMerchantLogin();
