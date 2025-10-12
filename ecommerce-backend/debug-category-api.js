const axios = require('axios');
require('dotenv').config({ path: './.env.local' });

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function debugCategoryAPI() {
  try {
    console.log('🔍 调试分类API问题...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const loginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    if (loginResponse.data.code !== 200) {
      console.log('❌ 登录失败:', loginResponse.data);
      return;
    }
    
    const token = loginResponse.data.data.data.token;
    console.log('✅ 登录成功，获取到token');
    
    // 2. 测试分类API
    console.log('📂 测试分类API...');
    try {
      const categoryResponse = await axios.get(`${API_BASE_URL}/category`, {
        headers: { 
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      console.log('✅ 分类API成功:', categoryResponse.data);
    } catch (error) {
      console.log('❌ 分类API失败:', error.response?.status, error.response?.data);
      
      // 检查是否是500错误
      if (error.response?.status === 500) {
        console.log('🔍 500错误详情:', error.response.data);
      }
    }
    
    // 3. 测试分类树API
    console.log('🌳 测试分类树API...');
    try {
      const treeResponse = await axios.get(`${API_BASE_URL}/category/tree`, {
        headers: { 
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      console.log('✅ 分类树API成功:', treeResponse.data);
    } catch (error) {
      console.log('❌ 分类树API失败:', error.response?.status, error.response?.data);
    }
    
  } catch (error) {
    console.error('❌ 调试过程出错:', error.message);
  }
}

debugCategoryAPI();
