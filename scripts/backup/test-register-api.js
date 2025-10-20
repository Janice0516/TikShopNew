const axios = require('axios');

async function testRegisterAPI() {
  try {
    console.log('🧪 测试用户注册API...');
    
    // 测试数据
    const testData = {
      phone: '13800138004',
      password: 'password123',
      code: '123456'
    };
    
    console.log('📤 发送注册请求:', testData);
    
    const response = await axios.post('http://localhost:3000/api/user/register', testData, {
      headers: {
        'Content-Type': 'application/json'
      },
      timeout: 10000
    });
    
    console.log('✅ 注册成功!');
    console.log('响应状态:', response.status);
    console.log('响应数据:', JSON.stringify(response.data, null, 2));
    
  } catch (error) {
    console.error('❌ 注册失败:');
    if (error.response) {
      console.error('状态码:', error.response.status);
      console.error('响应数据:', JSON.stringify(error.response.data, null, 2));
    } else if (error.request) {
      console.error('请求失败:', error.message);
    } else {
      console.error('错误:', error.message);
    }
  }
}

testRegisterAPI();
