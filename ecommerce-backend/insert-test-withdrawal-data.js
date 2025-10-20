const axios = require('axios');

const API_BASE = 'http://localhost:3000/api';

async function insertTestData() {
  console.log('📝 插入测试提现数据\n');

  try {
    // 首先检查是否有商家数据
    console.log('1. 检查商家数据...');
    
    // 尝试登录一个商家来获取token
    const loginData = {
      username: 'merchant001',
      password: 'password123'
    };
    
    const loginResponse = await axios.post(`${API_BASE}/merchant/login`, loginData);
    console.log('   ✅ 商家登录成功');
    
    const token = loginResponse.data.data.token;
    const headers = { Authorization: `Bearer ${token}` };
    
    // 检查商家余额
    console.log('\n2. 检查商家余额...');
    try {
      const balanceResponse = await axios.get(`${API_BASE}/withdrawal/balance`, { headers });
      console.log('   ✅ 商家余额:', balanceResponse.data.data);
    } catch (error) {
      console.log('   ❌ 获取余额失败:', error.response?.data?.message);
    }
    
    // 创建提现申请
    console.log('\n3. 创建提现申请...');
    const withdrawalData = {
      withdrawalAmount: 1000,
      bankName: 'Maybank',
      bankAccount: '1234567890',
      accountHolder: 'Test Merchant',
      remark: '测试提现申请'
    };
    
    try {
      const withdrawalResponse = await axios.post(`${API_BASE}/withdrawal`, withdrawalData, { headers });
      console.log('   ✅ 提现申请创建成功:', withdrawalResponse.data.data);
    } catch (error) {
      console.log('   ❌ 创建提现申请失败:', error.response?.data?.message);
    }
    
    // 再次测试提现列表API
    console.log('\n4. 测试提现列表API...');
    try {
      const listResponse = await axios.get(`${API_BASE}/withdrawal/list`);
      console.log('   ✅ 提现列表API正常:', listResponse.data);
    } catch (error) {
      console.log('   ❌ 提现列表API仍然错误:', error.response?.data?.message);
    }

  } catch (error) {
    console.error('❌ 插入测试数据失败:', error.response?.data?.message || error.message);
  }
}

insertTestData();
