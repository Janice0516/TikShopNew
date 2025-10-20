const axios = require('axios');

const API_BASE = 'http://localhost:3000/api';

async function finalTest() {
  console.log('🎉 最终API测试\n');

  try {
    // 1. 测试商家登录
    console.log('1. 测试商家登录...');
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post(`${API_BASE}/merchant/login`, loginData);
    console.log('   ✅ 登录成功');
    console.log('   响应:', loginResponse.data);

    // 2. 测试提现列表API
    console.log('\n2. 测试提现列表API...');
    const withdrawalResponse = await axios.get(`${API_BASE}/withdrawal/list`);
    console.log('   ✅ 提现列表API正常');
    console.log('   数据:', withdrawalResponse.data);

    // 3. 测试管理员登录
    console.log('\n3. 测试管理员登录...');
    const adminLoginData = {
      username: 'admin',
      password: 'admin123'
    };
    
    try {
      const adminResponse = await axios.post(`${API_BASE}/admin/login`, adminLoginData);
      console.log('   ✅ 管理员登录成功');
      console.log('   响应:', adminResponse.data);
    } catch (error) {
      console.log('   ❌ 管理员登录失败:', error.response?.data?.message);
    }

    // 4. 测试商品API
    console.log('\n4. 测试商品API...');
    try {
      const productResponse = await axios.get(`${API_BASE}/products?page=1&pageSize=5`);
      console.log('   ✅ 商品API正常');
      console.log('   商品数量:', productResponse.data.data?.list?.length || 0);
    } catch (error) {
      console.log('   ❌ 商品API失败:', error.response?.data?.message);
    }

    console.log('\n🎉 所有测试完成！');

  } catch (error) {
    console.error('❌ 测试失败:', error.response?.data?.message || error.message);
  }
}

finalTest();
