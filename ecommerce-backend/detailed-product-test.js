#!/usr/bin/env node

// 详细测试商品API
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function testProductAPI() {
  console.log('🔍 详细测试商品API...');
  
  try {
    // 测试不同的参数组合
    console.log('\n1. 测试无参数的商品列表...');
    const response1 = await axios.get(`${API_BASE_URL}/products`);
    console.log('✅ 无参数成功:', response1.data);
  } catch (error) {
    console.log('❌ 无参数失败:', error.response?.status, error.response?.data);
  }
  
  try {
    console.log('\n2. 测试带分页参数的商品列表...');
    const response2 = await axios.get(`${API_BASE_URL}/products?page=1&pageSize=10`);
    console.log('✅ 带分页参数成功:', response2.data);
  } catch (error) {
    console.log('❌ 带分页参数失败:', error.response?.status, error.response?.data);
  }
  
  try {
    console.log('\n3. 测试商品分类API...');
    const response3 = await axios.get(`${API_BASE_URL}/products/categories`);
    console.log('✅ 商品分类成功:', response3.data);
  } catch (error) {
    console.log('❌ 商品分类失败:', error.response?.status, error.response?.data);
  }
  
  try {
    console.log('\n4. 测试健康检查...');
    const response4 = await axios.get(`${API_BASE_URL}/health`);
    console.log('✅ 健康检查成功:', response4.data);
  } catch (error) {
    console.log('❌ 健康检查失败:', error.response?.status, error.response?.data);
  }
}

testProductAPI();
