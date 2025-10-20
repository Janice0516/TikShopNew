const axios = require('axios');

// 30个真实商家数据
const merchants = [
  { name: 'TechWorld Malaysia', shop: 'TechWorld MY', phone: '+60 12-345-6789', email: 'contact@techworld.my', category: 'Electronics' },
  { name: 'Fashion Forward KL', shop: 'Fashion Forward', phone: '+60 13-456-7890', email: 'info@fashionforward.kl', category: 'Fashion' },
  { name: 'Home & Living Store', shop: 'Home Living', phone: '+60 14-567-8901', email: 'sales@homeliving.my', category: 'Home & Garden' },
  { name: 'Sports Zone Malaysia', shop: 'Sports Zone', phone: '+60 15-678-9012', email: 'orders@sportszone.my', category: 'Sports' },
  { name: 'Beauty Paradise', shop: 'Beauty Paradise', phone: '+60 16-789-0123', email: 'beauty@paradise.my', category: 'Beauty' },
  { name: 'Book Haven KL', shop: 'Book Haven', phone: '+60 17-890-1234', email: 'books@haven.kl', category: 'Books' },
  { name: 'Auto Parts Pro', shop: 'Auto Parts Pro', phone: '+60 18-901-2345', email: 'parts@autopro.my', category: 'Automotive' },
  { name: 'Pet Care Center', shop: 'Pet Care', phone: '+60 19-012-3456', email: 'pets@carecenter.my', category: 'Pet Supplies' },
  { name: 'Gourmet Kitchen', shop: 'Gourmet Kitchen', phone: '+60 20-123-4567', email: 'kitchen@gourmet.my', category: 'Kitchen' },
  { name: 'Health & Wellness', shop: 'Health Wellness', phone: '+60 21-234-5678', email: 'health@wellness.my', category: 'Health' },
  { name: 'Kids World Store', shop: 'Kids World', phone: '+60 22-345-6789', email: 'kids@worldstore.my', category: 'Toys & Games' },
  { name: 'Office Supplies Plus', shop: 'Office Plus', phone: '+60 23-456-7890', email: 'office@supplies.my', category: 'Office Supplies' },
  { name: 'Jewelry & Watches', shop: 'Jewelry Watches', phone: '+60 24-567-8901', email: 'jewelry@watches.my', category: 'Jewelry' },
  { name: 'Garden & Outdoor', shop: 'Garden Outdoor', phone: '+60 25-678-9012', email: 'garden@outdoor.my', category: 'Garden' },
  { name: 'Music & Instruments', shop: 'Music Instruments', phone: '+60 26-789-0123', email: 'music@instruments.my', category: 'Music' },
  { name: 'Art & Crafts Store', shop: 'Art Crafts', phone: '+60 27-890-1234', email: 'art@crafts.my', category: 'Arts & Crafts' },
  { name: 'Travel Essentials', shop: 'Travel Essentials', phone: '+60 28-901-2345', email: 'travel@essentials.my', category: 'Travel' },
  { name: 'Baby & Toddler', shop: 'Baby Toddler', phone: '+60 29-012-3456', email: 'baby@toddler.my', category: 'Baby' },
  { name: 'Senior Care Shop', shop: 'Senior Care', phone: '+60 30-123-4567', email: 'senior@care.my', category: 'Senior Care' },
  { name: 'Gaming Hub Malaysia', shop: 'Gaming Hub', phone: '+60 31-234-5678', email: 'gaming@hub.my', category: 'Gaming' },
  { name: 'Photography Studio', shop: 'Photo Studio', phone: '+60 32-345-6789', email: 'photo@studio.my', category: 'Photography' },
  { name: 'Fitness Equipment', shop: 'Fitness Equipment', phone: '+60 33-456-7890', email: 'fitness@equipment.my', category: 'Fitness' },
  { name: 'Cooking & Baking', shop: 'Cooking Baking', phone: '+60 34-567-8901', email: 'cooking@baking.my', category: 'Cooking' },
  { name: 'DIY & Tools', shop: 'DIY Tools', phone: '+60 35-678-9012', email: 'diy@tools.my', category: 'Tools' },
  { name: 'Cleaning Supplies', shop: 'Cleaning Supplies', phone: '+60 36-789-0123', email: 'cleaning@supplies.my', category: 'Cleaning' },
  { name: 'Storage Solutions', shop: 'Storage Solutions', phone: '+60 37-890-1234', email: 'storage@solutions.my', category: 'Storage' },
  { name: 'Lighting & Decor', shop: 'Lighting Decor', phone: '+60 38-901-2345', email: 'lighting@decor.my', category: 'Lighting' },
  { name: 'Security Systems', shop: 'Security Systems', phone: '+60 39-012-3456', email: 'security@systems.my', category: 'Security' },
  { name: 'Smart Home Tech', shop: 'Smart Home', phone: '+60 40-123-4567', email: 'smarthome@tech.my', category: 'Smart Home' },
  { name: 'Renewable Energy', shop: 'Renewable Energy', phone: '+60 41-234-5678', email: 'renewable@energy.my', category: 'Energy' }
];

async function createMerchants() {
  try {
    console.log('🚀 开始创建30个商家...');

    for (let i = 0; i < merchants.length; i++) {
      const merchant = merchants[i];
      const merchantId = `merchant${String(i + 4).padStart(3, '0')}`;
      
      const merchantData = {
        merchantUid: merchantId,
        merchantName: merchant.name,
        phone: merchant.phone,
        email: merchant.email,
        password: 'password123',
        shopName: merchant.shop,
        shopDescription: `${merchant.category}专业店铺，提供优质商品和服务`,
        status: 1
      };

      try {
        const response = await axios.post('http://localhost:3000/api/merchant/register', merchantData);
        console.log(`✅ 商家 ${merchantId} (${merchant.name}) 创建成功`);
      } catch (error) {
        console.log(`⚠️  商家 ${merchantId} (${merchant.name}) 创建失败: ${error.response?.data?.message || error.message}`);
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
