const axios = require('axios');

async function testMerchantBackend() {
  console.log('🔍 测试商家后台问题\n');

  try {
    // 1. 测试商家登录API
    console.log('1. 测试商家登录API...');
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post('http://localhost:3000/api/merchant/login', loginData);
    console.log('   ✅ 商家登录API正常');
    console.log('   响应:', loginResponse.data);

    // 2. 测试商家后台页面
    console.log('\n2. 测试商家后台页面...');
    const pageResponse = await axios.get('http://localhost:5176')
    console.log('   ✅ 商家后台页面可访问');
    console.log('   状态码:', pageResponse.status);
    console.log('   内容长度:', pageResponse.data.length);

    // 3. 检查页面中的API配置
    console.log('\n3. 检查API配置...');
    const apiConfigMatch = pageResponse.data.match(/VITE_API_BASE_URL[^>]*>/);
    if (apiConfigMatch) {
      console.log('   API配置:', apiConfigMatch[0]);
    } else {
      console.log('   ⚠️  未找到API配置');
    }

  } catch (error) {
    console.error('❌ 测试失败:', error.response?.status, error.response?.data?.message || error.message);
  }
}

testMerchantBackend();
