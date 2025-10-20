const axios = require('axios');
require('dotenv').config({ path: './.env.local' });

const API_BASE_URL = 'http://localhost:3000/api';

async function debugCategoryServiceDetailed() {
  try {
    console.log('🔍 详细调试CategoryService...');
    
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
    console.log('✅ 登录成功');
    
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
      console.log('❌ 分类API失败:', error.response?.status);
      console.log('错误详情:', error.response?.data);
      
      // 如果是500错误，可能是CategoryService内部错误
      if (error.response?.status === 500) {
        console.log('🔍 500错误 - CategoryService内部错误');
        console.log('可能的原因:');
        console.log('1. 数据库连接问题');
        console.log('2. TypeORM查询问题');
        console.log('3. Category实体映射问题');
        console.log('4. 数据库表结构问题');
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
      console.log('❌ 分类树API失败:', error.response?.status);
      console.log('错误详情:', error.response?.data);
    }
    
    // 4. 测试公开分类API（不需要认证）
    console.log('📂 测试公开分类API...');
    try {
      const publicResponse = await axios.get(`${API_BASE_URL}/public-categories`);
      console.log('✅ 公开分类API成功:', publicResponse.data);
    } catch (error) {
      console.log('❌ 公开分类API失败:', error.response?.status);
      console.log('错误详情:', error.response?.data);
    }
    
  } catch (error) {
    console.error('❌ 调试过程出错:', error.message);
  }
}

debugCategoryServiceDetailed();
