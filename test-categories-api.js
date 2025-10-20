const axios = require('axios');

async function testCategoriesAPI() {
  try {
    console.log('🧪 测试分类API调用...');
    
    // 模拟前端请求
    const response = await axios.get('http://localhost:3000/api/public-categories', {
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    console.log('✅ API响应状态:', response.status);
    console.log('📋 响应数据:', JSON.stringify(response.data, null, 2));
    
    // 模拟前端处理逻辑
    const res = response.data;
    console.log('\n🔍 前端处理逻辑测试:');
    console.log('res:', typeof res, res);
    console.log('res.data:', typeof res.data, res.data);
    console.log('Array.isArray(res.data):', Array.isArray(res.data));
    
    if (res && res.data && Array.isArray(res.data)) {
      console.log('✅ 格式1匹配: res.data 是数组');
      console.log('分类数量:', res.data.length);
      res.data.forEach(cat => {
        console.log(`  - ${cat.id}: ${cat.name}`);
      });
    } else if (res && Array.isArray(res)) {
      console.log('✅ 格式2匹配: res 直接是数组');
      console.log('分类数量:', res.length);
    } else {
      console.log('❌ 格式不匹配');
    }
    
  } catch (error) {
    console.error('❌ 测试失败:', error.message);
    if (error.response) {
      console.error('响应状态:', error.response.status);
      console.error('响应数据:', error.response.data);
    }
  }
}

testCategoriesAPI();
