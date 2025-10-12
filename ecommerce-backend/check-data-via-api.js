const axios = require('axios');

const API_BASE = 'https://tiktokshop-api.onrender.com/api';

async function checkData() {
  console.log('🔍 通过API检查数据库数据情况\n');

  try {
    // 1. 检查公开分类API
    console.log('1. 检查分类数据:');
    const categories = await axios.get(`${API_BASE}/public-categories`);
    console.log(`   ✅ 分类数量: ${categories.data.data.data.length}`);
    console.log(`   分类列表: ${categories.data.data.data.map(c => c.name).join(', ')}\n`);

    // 2. 检查提现API测试
    console.log('2. 检查提现API:');
    try {
      const withdrawalTest = await axios.get(`${API_BASE}/withdrawal/test`);
      console.log(`   ✅ 提现API测试正常`);
    } catch (error) {
      console.log(`   ❌ 提现API测试错误: ${error.response?.data?.message || error.message}`);
    }

    // 3. 检查提现列表API
    console.log('\n3. 检查提现列表:');
    try {
      const withdrawalList = await axios.get(`${API_BASE}/withdrawal/list`);
      console.log(`   ✅ 提现列表API正常`);
      console.log(`   提现数据: ${JSON.stringify(withdrawalList.data, null, 2)}`);
    } catch (error) {
      console.log(`   ❌ 提现列表API错误: ${error.response?.data?.message || error.message}`);
    }

  } catch (error) {
    console.error('❌ 检查过程中出错:', error.message);
  }
}

checkData();
