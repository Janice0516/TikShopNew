const axios = require('axios');

const API_BASE = 'https://tiktokshop-api.onrender.com/api';

async function debugLogin() {
  try {
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const response = await axios.post(`${API_BASE}/merchant/login`, loginData);
    console.log('登录响应结构:');
    console.log(JSON.stringify(response.data, null, 2));
    
  } catch (error) {
    console.error('登录失败:', error.response?.data || error.message);
  }
}

debugLogin();
