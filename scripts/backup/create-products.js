const axios = require('axios');

// 40个真实产品数据
const products = [
  // 电子产品
  { name: 'Samsung Galaxy S24 Ultra 512GB', brand: 'Samsung', categoryId: '9', costPrice: '4200.00', suggestPrice: '4599.00', stock: 15, description: '旗舰智能手机，配备S Pen和AI功能' },
  { name: 'iPad Pro 12.9-inch M4', brand: 'Apple', categoryId: '10', costPrice: '3800.00', suggestPrice: '4199.00', stock: 20, description: '专业级平板电脑，M4芯片，Liquid Retina XDR显示屏' },
  { name: 'Dell XPS 15 9530', brand: 'Dell', categoryId: '10', costPrice: '4500.00', suggestPrice: '4999.00', stock: 12, description: '高端笔记本电脑，13代Intel处理器，4K OLED屏幕' },
  { name: 'Sony WH-1000XM5', brand: 'Sony', categoryId: '11', costPrice: '1200.00', suggestPrice: '1299.00', stock: 25, description: '顶级降噪耳机，30小时续航，高解析度音频' },
  { name: 'Bose QuietComfort 45', brand: 'Bose', categoryId: '11', costPrice: '1100.00', suggestPrice: '1199.00', stock: 18, description: '舒适降噪耳机，24小时续航，快速充电' },
  { name: 'Canon EOS R6 Mark II', brand: 'Canon', categoryId: '12', costPrice: '6800.00', suggestPrice: '7299.00', stock: 8, description: '全画幅无反相机，24.2MP，4K视频录制' },
  { name: 'Nikon Z7 II', brand: 'Nikon', categoryId: '12', costPrice: '7200.00', suggestPrice: '7799.00', stock: 6, description: '专业全画幅相机，45.7MP，双卡槽设计' },
  { name: 'DJI Mini 4 Pro', brand: 'DJI', categoryId: '12', costPrice: '2800.00', suggestPrice: '3099.00', stock: 30, description: '便携无人机，4K HDR视频，智能跟随' },

  // 运动用品
  { name: 'Nike Air Jordan 1 Retro', brand: 'Nike', categoryId: '15', costPrice: '450.00', suggestPrice: '499.00', stock: 50, description: '经典篮球鞋，复古设计，优质皮革' },
  { name: 'Adidas Ultraboost 22', brand: 'Adidas', categoryId: '15', costPrice: '280.00', suggestPrice: '320.00', stock: 40, description: '跑步鞋，Boost中底技术，舒适透气' },
  { name: 'Wilson Pro Staff RF97', brand: 'Wilson', categoryId: '15', costPrice: '380.00', suggestPrice: '429.00', stock: 15, description: '专业网球拍，费德勒签名款，精准控制' },
  { name: 'Yonex Voltric Z-Force II', brand: 'Yonex', categoryId: '15', costPrice: '320.00', suggestPrice: '369.00', stock: 20, description: '羽毛球拍，进攻型设计，高弹性' },
  { name: 'Under Armour Curry 9', brand: 'Under Armour', categoryId: '15', costPrice: '420.00', suggestPrice: '469.00', stock: 25, description: '篮球鞋，库里签名款，轻量化设计' },

  // 时尚服装
  { name: 'Uniqlo Heattech Ultra Warm', brand: 'Uniqlo', categoryId: '13', costPrice: '45.00', suggestPrice: '59.00', stock: 100, description: '保暖内衣，Heattech技术，舒适贴身' },
  { name: 'Zara Wool Blend Coat', brand: 'Zara', categoryId: '13', costPrice: '180.00', suggestPrice: '229.00', stock: 30, description: '羊毛混纺大衣，经典剪裁，保暖时尚' },
  { name: 'H&M Cotton T-Shirt', brand: 'H&M', categoryId: '13', costPrice: '15.00', suggestPrice: '25.00', stock: 200, description: '纯棉T恤，多种颜色，舒适透气' },
  { name: 'Levi\'s 501 Original Jeans', brand: 'Levi\'s', categoryId: '13', costPrice: '120.00', suggestPrice: '159.00', stock: 60, description: '经典牛仔裤，直筒剪裁，耐穿舒适' },

  // 家居用品
  { name: 'IKEA MALM Bed Frame', brand: 'IKEA', categoryId: '14', costPrice: '280.00', suggestPrice: '349.00', stock: 25, description: '简约床架，实木贴面，现代设计' },
  { name: 'Dyson V15 Detect', brand: 'Dyson', categoryId: '14', costPrice: '1200.00', suggestPrice: '1299.00', stock: 15, description: '无线吸尘器，激光探测，60分钟续航' },
  { name: 'Philips Hue Starter Kit', brand: 'Philips', categoryId: '14', costPrice: '180.00', suggestPrice: '229.00', stock: 40, description: '智能灯泡套装，1600万色彩，语音控制' },
  { name: 'KitchenAid Stand Mixer', brand: 'KitchenAid', categoryId: '14', costPrice: '450.00', suggestPrice: '529.00', stock: 12, description: '立式搅拌机，5夸脱容量，多种配件' },

  // 美妆护肤
  { name: 'SK-II Facial Treatment Essence', brand: 'SK-II', categoryId: '16', costPrice: '280.00', suggestPrice: '329.00', stock: 20, description: '神仙水，Pitera精华，改善肌肤质地' },
  { name: 'La Mer Crème de la Mer', brand: 'La Mer', categoryId: '16', costPrice: '450.00', suggestPrice: '529.00', stock: 8, description: '海蓝之谜面霜，深海巨藻精华，奢华护肤' },
  { name: 'MAC Ruby Woo Lipstick', brand: 'MAC', categoryId: '16', costPrice: '35.00', suggestPrice: '45.00', stock: 50, description: '经典红色唇膏，哑光质地，持久显色' },
  { name: 'Chanel No.5 Eau de Parfum', brand: 'Chanel', categoryId: '16', costPrice: '180.00', suggestPrice: '229.00', stock: 15, description: '经典香水，花香调，优雅持久' },

  // 食品饮料
  { name: 'Nespresso Vertuo Next', brand: 'Nespresso', categoryId: '17', costPrice: '180.00', suggestPrice: '229.00', stock: 30, description: '胶囊咖啡机，多种杯型，一键制作' },
  { name: 'Vitamix A3500', brand: 'Vitamix', categoryId: '17', costPrice: '650.00', suggestPrice: '729.00', stock: 10, description: '高速搅拌机，预设程序，自清洁功能' },
  { name: 'Breville Barista Express', brand: 'Breville', categoryId: '17', costPrice: '380.00', suggestPrice: '429.00', stock: 18, description: '意式咖啡机，内置磨豆机，专业萃取' },
  { name: 'Tea Forté Pyramid Tea', brand: 'Tea Forté', categoryId: '17', costPrice: '25.00', suggestPrice: '35.00', stock: 80, description: '金字塔茶包，多种口味，优质茶叶' },

  // 图书文具
  { name: 'Kindle Paperwhite 11th Gen', brand: 'Amazon', categoryId: '18', costPrice: '180.00', suggestPrice: '229.00', stock: 40, description: '电子阅读器，6.8英寸屏幕，防水设计' },
  { name: 'Moleskine Classic Notebook', brand: 'Moleskine', categoryId: '18', costPrice: '25.00', suggestPrice: '35.00', stock: 100, description: '经典笔记本，优质纸张，多种尺寸' },
  { name: 'Pilot G2 Gel Pen Set', brand: 'Pilot', categoryId: '18', costPrice: '15.00', suggestPrice: '25.00', stock: 150, description: '凝胶笔套装，多种颜色，流畅书写' },

  // 汽车用品
  { name: 'Michelin Pilot Sport 4', brand: 'Michelin', categoryId: '19', costPrice: '180.00', suggestPrice: '229.00', stock: 25, description: '高性能轮胎，湿地抓地力强，运动驾驶' },
  { name: 'Bosch Icon Wiper Blades', brand: 'Bosch', categoryId: '19', costPrice: '35.00', suggestPrice: '45.00', stock: 60, description: '雨刷片，双曲面设计，清晰视野' },
  { name: 'Mobil 1 Synthetic Oil', brand: 'Mobil', categoryId: '19', costPrice: '45.00', suggestPrice: '59.00', stock: 80, description: '全合成机油，5W-30粘度，发动机保护' },

  // 宠物用品
  { name: 'Royal Canin Adult Cat Food', brand: 'Royal Canin', categoryId: '20', costPrice: '35.00', suggestPrice: '45.00', stock: 120, description: '成猫专用粮，均衡营养，易消化' },
  { name: 'Pedigree Adult Dog Food', brand: 'Pedigree', categoryId: '20', costPrice: '25.00', suggestPrice: '35.00', stock: 100, description: '成犬专用粮，蛋白质丰富，健康配方' },
  { name: 'Furminator Deshedding Tool', brand: 'Furminator', categoryId: '20', costPrice: '45.00', suggestPrice: '59.00', stock: 30, description: '宠物梳毛器，减少掉毛，健康毛发' }
];

async function createProducts() {
  try {
    console.log('🚀 开始创建40个产品...');

    for (let i = 0; i < products.length; i++) {
      const product = products[i];
      
      const productData = {
        name: product.name,
        brand: product.brand,
        categoryId: product.categoryId,
        costPrice: product.costPrice,
        suggestPrice: product.suggestPrice,
        stock: product.stock,
        description: product.description,
        status: 1
      };

      try {
        const response = await axios.post('https://tiktokshop-api.onrender.com/api/public-products', productData);
        console.log(`✅ 产品 ${product.name} 创建成功`);
      } catch (error) {
        console.log(`⚠️  产品 ${product.name} 创建失败: ${error.response?.data?.message || error.message}`);
      }

      // 避免请求过快
      await new Promise(resolve => setTimeout(resolve, 300));
    }

    console.log('\n📊 产品创建完成！');

  } catch (error) {
    console.error('❌ 创建产品失败:', error.message);
  }
}

createProducts();
