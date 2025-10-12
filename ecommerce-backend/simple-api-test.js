#!/usr/bin/env node

// 简单测试分类API
const axios = require('axios');

const API_BASE_URL = 'https://tiktokshop-api.onrender.com/api';

async function testCategoryAPI() {
  console.log('🔍 测试分类API...');
  
  try {
    // 1. 测试分类列表
    console.log('📂 测试分类列表...');
    const response = await axios.get(`${API_BASE_URL}/category`);
    console.log('✅ 分类列表成功:', response.data);
  } catch (error) {
    console.log('❌ 分类列表失败:', error.response?.status, error.response?.data);
  }
  
  try {
    // 2. 测试商品列表
    console.log('🛍️ 测试商品列表...');
    const response = await axios.get(`${API_BASE_URL}/products`);
    console.log('✅ 商品列表成功:', response.data);
  } catch (error) {
    console.log('❌ 商品列表失败:', error.response?.status, error.response?.data);
  }
  
  try {
    // 3. 测试公开分类列表
    console.log('📂 测试公开分类列表...');
    const response = await axios.get(`${API_BASE_URL}/public-categories`);
    console.log('✅ 公开分类列表成功:', response.data);
  } catch (error) {
    console.log('❌ 公开分类列表失败:', error.response?.status, error.response?.data);
  }
}

testCategoryAPI();
