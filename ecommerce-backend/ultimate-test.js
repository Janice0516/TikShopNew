const axios = require('axios');

const API_BASE = 'https://tiktokshop-api.onrender.com/api';

async function ultimateTest() {
  console.log('🚀 终极测试 - 验证所有功能\n');

  try {
    // 1. 测试商家登录
    console.log('1. 测试商家登录...');
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post(`${API_BASE}/merchant/login`, loginData);
    console.log('   ✅ 商家登录成功');
    console.log('   Token:', loginResponse.data.data.data.token.substring(0, 20) + '...');

    // 2. 测试提现列表API
    console.log('\n2. 测试提现列表API...');
    const withdrawalResponse = await axios.get(`${API_BASE}/withdrawal/list`);
    console.log('   ✅ 提现列表API正常');
    console.log('   提现记录数量:', withdrawalResponse.data.data.data.total);

    // 3. 测试管理员登录
    console.log('\n3. 测试管理员登录...');
    const adminLoginData = {
      username: 'admin',
      password: 'admin123'
    };
    
    const adminResponse = await axios.post(`${API_BASE}/admin/login`, adminLoginData);
    console.log('   ✅ 管理员登录成功');
    console.log('   Token:', adminResponse.data.data.data.token.substring(0, 20) + '...');

    // 4. 测试商品API
    console.log('\n4. 测试商品API...');
    const productResponse = await axios.get(`${API_BASE}/products?page=1&pageSize=5`);
    console.log('   ✅ 商品API正常');
    console.log('   商品数量:', productResponse.data.data.list.length);

    // 5. 测试分类API
    console.log('\n5. 测试分类API...');
    const categoryResponse = await axios.get(`${API_BASE}/public-categories`);
    console.log('   ✅ 分类API正常');
    console.log('   分类数量:', categoryResponse.data.data.data.length);

    console.log('\n🎉 所有API测试通过！');
    console.log('\n📋 测试账号信息:');
    console.log('   商家账号: merchant001 / password123');
    console.log('   管理员账号: admin / admin123');
    console.log('\n🌐 访问地址:');
    console.log('   管理后台: https://tikshop-admin.onrender.com');
    console.log('   商家后台: https://tikshop-merchant.onrender.com');
    console.log('   用户前端: https://tikshop-user.onrender.com');

  } catch (error) {
    console.error('❌ 测试失败:', error.response?.data?.message || error.message);
  }
}

ultimateTest();
