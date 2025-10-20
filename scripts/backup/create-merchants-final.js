const axios = require('axios');

// 30个真实商家数据，使用正确的马来西亚手机号格式
const merchants = [
  { name: 'TechWorld Malaysia', contact: 'Ahmad Rahman', phone: '0123456789', shop: 'TechWorld MY' },
  { name: 'Fashion Forward KL', contact: 'Sarah Lim', phone: '0134567890', shop: 'Fashion Forward' },
  { name: 'Home & Living Store', contact: 'David Tan', phone: '0145678901', shop: 'Home Living' },
  { name: 'Sports Zone Malaysia', contact: 'Michael Wong', phone: '0156789012', shop: 'Sports Zone' },
  { name: 'Beauty Paradise', contact: 'Lisa Chen', phone: '0167890123', shop: 'Beauty Paradise' },
  { name: 'Book Haven KL', contact: 'John Lee', phone: '0178901234', shop: 'Book Haven' },
  { name: 'Auto Parts Pro', contact: 'Robert Kumar', phone: '0189012345', shop: 'Auto Parts Pro' },
  { name: 'Pet Care Center', contact: 'Emma Ng', phone: '0190123456', shop: 'Pet Care' },
  { name: 'Gourmet Kitchen', contact: 'James Ho', phone: '0101234567', shop: 'Gourmet Kitchen' },
  { name: 'Health & Wellness', contact: 'Maria Rodriguez', phone: '0112345678', shop: 'Health Wellness' },
  { name: 'Kids World Store', contact: 'Peter Singh', phone: '0123456789', shop: 'Kids World' },
  { name: 'Office Supplies Plus', contact: 'Anna Chong', phone: '0134567890', shop: 'Office Plus' },
  { name: 'Jewelry & Watches', contact: 'Kevin Lau', phone: '0145678901', shop: 'Jewelry Watches' },
  { name: 'Garden & Outdoor', contact: 'Susan Yeo', phone: '0156789012', shop: 'Garden Outdoor' },
  { name: 'Music & Instruments', contact: 'Daniel Lim', phone: '0167890123', shop: 'Music Instruments' },
  { name: 'Art & Crafts Store', contact: 'Michelle Tan', phone: '0178901234', shop: 'Art Crafts' },
  { name: 'Travel Essentials', contact: 'Andrew Ng', phone: '0189012345', shop: 'Travel Essentials' },
  { name: 'Baby & Toddler', contact: 'Jennifer Wong', phone: '0190123456', shop: 'Baby Toddler' },
  { name: 'Senior Care Shop', contact: 'Thomas Lee', phone: '0101234567', shop: 'Senior Care' },
  { name: 'Gaming Hub Malaysia', contact: 'Rachel Chua', phone: '0112345678', shop: 'Gaming Hub' },
  { name: 'Photography Studio', contact: 'Steven Goh', phone: '0123456789', shop: 'Photo Studio' },
  { name: 'Fitness Equipment', contact: 'Amanda Lim', phone: '0134567890', shop: 'Fitness Equipment' },
  { name: 'Cooking & Baking', contact: 'Nicholas Tan', phone: '0145678901', shop: 'Cooking Baking' },
  { name: 'DIY & Tools', contact: 'Grace Wong', phone: '0156789012', shop: 'DIY Tools' },
  { name: 'Cleaning Supplies', contact: 'Benjamin Lee', phone: '0167890123', shop: 'Cleaning Supplies' },
  { name: 'Storage Solutions', contact: 'Victoria Ng', phone: '0178901234', shop: 'Storage Solutions' },
  { name: 'Lighting & Decor', contact: 'Christopher Ho', phone: '0189012345', shop: 'Lighting Decor' },
  { name: 'Security Systems', contact: 'Isabella Chong', phone: '0190123456', shop: 'Security Systems' },
  { name: 'Smart Home Tech', contact: 'Alexander Lau', phone: '0101234567', shop: 'Smart Home' },
  { name: 'Renewable Energy', contact: 'Sophia Yeo', phone: '0112345678', shop: 'Renewable Energy' }
];

async function createMerchants() {
  try {
    console.log('🚀 开始创建30个商家...');

    for (let i = 0; i < merchants.length; i++) {
      const merchant = merchants[i];
      const username = `merchant${String(i + 4).padStart(3, '0')}`;
      
      const merchantData = {
        username: username,
        password: 'password123',
        merchantName: merchant.name,
        contactName: merchant.contact,
        contactPhone: merchant.phone,
        shopName: merchant.shop
      };

      try {
        const response = await axios.post('http://localhost:3000/api/merchant/register', merchantData);
        console.log(`✅ 商家 ${username} (${merchant.name}) 创建成功`);
      } catch (error) {
        console.log(`⚠️  商家 ${username} (${merchant.name}) 创建失败: ${error.response?.data?.message || error.message}`);
      }

      // 避免请求过快
      await new Promise(resolve => setTimeout(resolve, 500));
    }

    console.log('\n📊 商家创建完成！');

  } catch (error) {
    console.error('❌ 创建商家失败:', error.message);
  }
}

createMerchants();
