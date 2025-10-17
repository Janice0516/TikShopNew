const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function testSettingsAPI() {
  try {
    console.log('🔍 测试设置API\n');

    // 1. 管理员登录
    console.log('1. 管理员登录...');
    const loginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });

    if (loginResponse.data.code !== 200) {
      console.log('❌ 管理员登录失败:', loginResponse.data.message);
      return;
    }

    const adminToken = loginResponse.data.data.token;
    console.log('✅ 管理员登录成功');

    // 2. 测试设置API
    console.log('\n2. 测试设置API...');
    try {
      const settingsResponse = await axios.get(`${API_BASE_URL}/settings`, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });

      console.log('📊 设置API响应状态:', settingsResponse.status);
      console.log('📊 设置API响应数据:', JSON.stringify(settingsResponse.data, null, 2));

      if (settingsResponse.data.code === 200) {
        console.log('✅ 设置API正常工作');
        const settings = settingsResponse.data.data;
        console.log('   基本设置:', settings.basic);
        console.log('   业务设置:', settings.business);
        console.log('   安全设置:', settings.security);
        console.log('   通知设置:', settings.notification);
      } else {
        console.log('❌ 设置API返回错误:', settingsResponse.data.message);
      }

    } catch (error) {
      console.log('❌ 设置API调用失败:');
      console.log('   错误信息:', error.message);
      if (error.response) {
        console.log('   响应状态:', error.response.status);
        console.log('   响应数据:', JSON.stringify(error.response.data, null, 2));
      }
    }

    // 3. 测试更新基本设置
    console.log('\n3. 测试更新基本设置...');
    try {
      const updateResponse = await axios.put(`${API_BASE_URL}/settings/basic`, {
        siteName: 'TikShop 电商平台 - 测试',
        siteDescription: '测试更新',
        defaultCurrency: 'MYR'
      }, {
        headers: { 
          'Authorization': `Bearer ${adminToken}`,
          'Content-Type': 'application/json'
        }
      });

      console.log('📊 更新设置响应:', JSON.stringify(updateResponse.data, null, 2));

      if (updateResponse.data.code === 200) {
        console.log('✅ 更新设置成功');
      } else {
        console.log('❌ 更新设置失败:', updateResponse.data.message);
      }

    } catch (error) {
      console.log('❌ 更新设置失败:');
      console.log('   错误信息:', error.message);
      if (error.response) {
        console.log('   响应状态:', error.response.status);
        console.log('   响应数据:', JSON.stringify(error.response.data, null, 2));
      }
    }

  } catch (error) {
    console.error('❌ 测试失败:', error.message);
  }
}

testSettingsAPI();
