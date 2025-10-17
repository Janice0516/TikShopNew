const axios = require('axios');

// 40个真实产品数据
const productsToCreate = [
  // 电子产品
  { name: 'Samsung Galaxy S24 Ultra 512GB', categoryId: 2, brand: 'Samsung', mainImage: '/static/products/galaxy-s24-ultra-512.jpg', costPrice: 4500.00, suggestPrice: 4999.00, stock: 30, description: 'Latest Samsung flagship with advanced camera and AI features.' },
  { name: 'iPad Pro 12.9-inch M4', categoryId: 2, brand: 'Apple', mainImage: '/static/products/ipad-pro-12-9-m4.jpg', costPrice: 3500.00, suggestPrice: 3999.00, stock: 25, description: 'Professional tablet with M4 chip and Liquid Retina XDR display.' },
  { name: 'Dell XPS 15 9530', categoryId: 2, brand: 'Dell', mainImage: '/static/products/dell-xps-15-9530.jpg', costPrice: 2800.00, suggestPrice: 3299.00, stock: 20, description: 'Premium laptop with Intel i7 processor and OLED display.' },
  { name: 'Sony WH-1000XM5', categoryId: 2, brand: 'Sony', mainImage: '/static/products/sony-wh-1000xm5.jpg', costPrice: 1200.00, suggestPrice: 1399.00, stock: 50, description: 'Industry-leading noise canceling wireless headphones.' },
  { name: 'Bose QuietComfort 45', categoryId: 2, brand: 'Bose', mainImage: '/static/products/bose-qc45.jpg', costPrice: 1100.00, suggestPrice: 1299.00, stock: 40, description: 'Premium noise canceling headphones with superior comfort.' },
  { name: 'Canon EOS R6 Mark II', categoryId: 2, brand: 'Canon', mainImage: '/static/products/canon-eos-r6-mark-ii.jpg', costPrice: 8500.00, suggestPrice: 9999.00, stock: 15, description: 'Professional mirrorless camera with advanced autofocus.' },
  { name: 'Nikon Z7 II', categoryId: 2, brand: 'Nikon', mainImage: '/static/products/nikon-z7-ii.jpg', costPrice: 7800.00, suggestPrice: 8999.00, stock: 12, description: 'High-resolution mirrorless camera for professionals.' },
  { name: 'DJI Mini 4 Pro', categoryId: 2, brand: 'DJI', mainImage: '/static/products/dji-mini-4-pro.jpg', costPrice: 2500.00, suggestPrice: 2999.00, stock: 35, description: 'Compact drone with 4K video and obstacle avoidance.' },

  // 运动鞋服
  { name: 'Nike Air Jordan 1 Retro', categoryId: 1, brand: 'Nike', mainImage: '/static/products/nike-air-jordan-1-retro.jpg', costPrice: 350.00, suggestPrice: 450.00, stock: 100, description: 'Classic basketball sneaker with premium leather construction.' },
  { name: 'Adidas Ultraboost 22', categoryId: 1, brand: 'Adidas', mainImage: '/static/products/adidas-ultraboost-22.jpg', costPrice: 280.00, suggestPrice: 350.00, stock: 80, description: 'High-performance running shoe with Boost technology.' },
  { name: 'Wilson Pro Staff RF97', categoryId: 1, brand: 'Wilson', mainImage: '/static/products/wilson-pro-staff-rf97.jpg', costPrice: 450.00, suggestPrice: 550.00, stock: 30, description: 'Professional tennis racket used by Roger Federer.' },
  { name: 'Yonex Voltric Z-Force II', categoryId: 1, brand: 'Yonex', mainImage: '/static/products/yonex-voltric-z-force-ii.jpg', costPrice: 380.00, suggestPrice: 480.00, stock: 25, description: 'High-end badminton racket for power players.' },
  { name: 'Under Armour Curry 9', categoryId: 1, brand: 'Under Armour', mainImage: '/static/products/under-armour-curry-9.jpg', costPrice: 320.00, suggestPrice: 420.00, stock: 60, description: 'Basketball shoe designed for Stephen Curry.' },

  // 服装
  { name: 'Uniqlo Heattech Ultra Warm', categoryId: 1, brand: 'Uniqlo', mainImage: '/static/products/uniqlo-heattech-ultra-warm.jpg', costPrice: 45.00, suggestPrice: 65.00, stock: 200, description: 'Thermal underwear with advanced heat retention technology.' },
  { name: 'Zara Wool Blend Coat', categoryId: 1, brand: 'Zara', mainImage: '/static/products/zara-wool-blend-coat.jpg', costPrice: 180.00, suggestPrice: 250.00, stock: 50, description: 'Elegant winter coat with wool blend fabric.' },
  { name: 'H&M Cotton T-Shirt', categoryId: 1, brand: 'H&M', mainImage: '/static/products/hm-cotton-tshirt.jpg', costPrice: 15.00, suggestPrice: 25.00, stock: 500, description: 'Basic cotton t-shirt in various colors.' },
  { name: 'Levi\'s 501 Original Jeans', categoryId: 1, brand: 'Levi\'s', mainImage: '/static/products/levis-501-original.jpg', costPrice: 120.00, suggestPrice: 180.00, stock: 150, description: 'Classic straight-fit jeans with original 501 design.' },

  // 家居用品
  { name: 'IKEA MALM Bed Frame', categoryId: 5, brand: 'IKEA', mainImage: '/static/products/ikea-malm-bed-frame.jpg', costPrice: 200.00, suggestPrice: 280.00, stock: 30, description: 'Minimalist bed frame with storage drawers.' },
  { name: 'Dyson V15 Detect', categoryId: 5, brand: 'Dyson', mainImage: '/static/products/dyson-v15-detect.jpg', costPrice: 800.00, suggestPrice: 999.00, stock: 25, description: 'Cordless vacuum with laser dust detection.' },
  { name: 'Philips Hue Starter Kit', categoryId: 5, brand: 'Philips', mainImage: '/static/products/philips-hue-starter-kit.jpg', costPrice: 150.00, suggestPrice: 200.00, stock: 40, description: 'Smart lighting system with color control.' },
  { name: 'KitchenAid Stand Mixer', categoryId: 5, brand: 'KitchenAid', mainImage: '/static/products/kitchenaid-stand-mixer.jpg', costPrice: 450.00, suggestPrice: 599.00, stock: 20, description: 'Professional stand mixer for baking enthusiasts.' },

  // 美妆个护
  { name: 'SK-II Facial Treatment Essence', categoryId: 4, brand: 'SK-II', mainImage: '/static/products/sk-ii-facial-treatment-essence.jpg', costPrice: 280.00, suggestPrice: 350.00, stock: 60, description: 'Premium skincare essence with Pitera complex.' },
  { name: 'La Mer Crème de la Mer', categoryId: 4, brand: 'La Mer', mainImage: '/static/products/la-mer-creme-de-la-mer.jpg', costPrice: 450.00, suggestPrice: 580.00, stock: 30, description: 'Luxury moisturizing cream with sea kelp extract.' },
  { name: 'MAC Ruby Woo Lipstick', categoryId: 4, brand: 'MAC', mainImage: '/static/products/mac-ruby-woo-lipstick.jpg', costPrice: 35.00, suggestPrice: 45.00, stock: 100, description: 'Classic matte red lipstick with long-lasting formula.' },
  { name: 'Chanel No.5 Eau de Parfum', categoryId: 4, brand: 'Chanel', mainImage: '/static/products/chanel-no5-eau-de-parfum.jpg', costPrice: 180.00, suggestPrice: 250.00, stock: 40, description: 'Iconic fragrance with timeless elegance.' },

  // 食品饮料
  { name: 'Nespresso Vertuo Next', categoryId: 3, brand: 'Nespresso', mainImage: '/static/products/nespresso-vertuo-next.jpg', costPrice: 200.00, suggestPrice: 280.00, stock: 50, description: 'Coffee machine with capsule brewing technology.' },
  { name: 'Vitamix A3500', categoryId: 3, brand: 'Vitamix', mainImage: '/static/products/vitamix-a3500.jpg', costPrice: 600.00, suggestPrice: 750.00, stock: 15, description: 'High-performance blender for smoothies and soups.' },
  { name: 'Breville Barista Express', categoryId: 3, brand: 'Breville', mainImage: '/static/products/breville-barista-express.jpg', costPrice: 800.00, suggestPrice: 999.00, stock: 12, description: 'Espresso machine with built-in grinder.' },
  { name: 'Tea Forté Pyramid Tea', categoryId: 3, brand: 'Tea Forté', mainImage: '/static/products/tea-forte-pyramid-tea.jpg', costPrice: 25.00, suggestPrice: 35.00, stock: 200, description: 'Premium pyramid tea bags in elegant packaging.' },

  // 图书文具
  { name: 'Kindle Paperwhite 11th Gen', categoryId: 9, brand: 'Amazon', mainImage: '/static/products/kindle-paperwhite-11th-gen.jpg', costPrice: 180.00, suggestPrice: 250.00, stock: 80, description: 'E-reader with waterproof design and adjustable light.' },
  { name: 'Moleskine Classic Notebook', categoryId: 9, brand: 'Moleskine', mainImage: '/static/products/moleskine-classic-notebook.jpg', costPrice: 25.00, suggestPrice: 35.00, stock: 300, description: 'Premium notebook with acid-free paper.' },
  { name: 'Pilot G2 Gel Pen Set', categoryId: 9, brand: 'Pilot', mainImage: '/static/products/pilot-g2-gel-pen-set.jpg', costPrice: 15.00, suggestPrice: 25.00, stock: 500, description: 'Smooth-writing gel pen set in multiple colors.' },

  // 汽车用品
  { name: 'Michelin Pilot Sport 4', categoryId: 8, brand: 'Michelin', mainImage: '/static/products/michelin-pilot-sport-4.jpg', costPrice: 200.00, suggestPrice: 280.00, stock: 40, description: 'High-performance summer tire for sports cars.' },
  { name: 'Bosch Icon Wiper Blades', categoryId: 8, brand: 'Bosch', mainImage: '/static/products/bosch-icon-wiper-blades.jpg', costPrice: 35.00, suggestPrice: 50.00, stock: 100, description: 'Premium windshield wiper blades with advanced design.' },
  { name: 'Mobil 1 Synthetic Oil', categoryId: 8, brand: 'Mobil', mainImage: '/static/products/mobil-1-synthetic-oil.jpg', costPrice: 45.00, suggestPrice: 65.00, stock: 150, description: 'High-performance synthetic motor oil.' },

  // 宠物用品
  { name: 'Royal Canin Adult Cat Food', categoryId: 10, brand: 'Royal Canin', mainImage: '/static/products/royal-canin-adult-cat-food.jpg', costPrice: 25.00, suggestPrice: 35.00, stock: 200, description: 'Nutritionally balanced cat food for adult felines.' },
  { name: 'Pedigree Adult Dog Food', categoryId: 10, brand: 'Pedigree', mainImage: '/static/products/pedigree-adult-dog-food.jpg', costPrice: 30.00, suggestPrice: 45.00, stock: 180, description: 'Complete nutrition for adult dogs.' },
  { name: 'Furminator Deshedding Tool', categoryId: 10, brand: 'Furminator', mainImage: '/static/products/furminator-deshedding-tool.jpg', costPrice: 40.00, suggestPrice: 60.00, stock: 80, description: 'Professional grooming tool to reduce shedding.' },

  // 运动户外
  { name: 'Patagonia Down Sweater', categoryId: 6, brand: 'Patagonia', mainImage: '/static/products/patagonia-down-sweater.jpg', costPrice: 180.00, suggestPrice: 250.00, stock: 35, description: 'Lightweight down jacket for outdoor adventures.' },
  { name: 'The North Face Recon Backpack', categoryId: 6, brand: 'The North Face', mainImage: '/static/products/north-face-recon-backpack.jpg', costPrice: 120.00, suggestPrice: 180.00, stock: 50, description: 'Durable backpack for hiking and travel.' },
  { name: 'Coleman Sundome Tent', categoryId: 6, brand: 'Coleman', mainImage: '/static/products/coleman-sundome-tent.jpg', costPrice: 80.00, suggestPrice: 120.00, stock: 25, description: 'Easy-to-setup tent for camping trips.' },

  // 母婴用品
  { name: 'Philips Avent Baby Bottle', categoryId: 7, brand: 'Philips Avent', mainImage: '/static/products/philips-avent-baby-bottle.jpg', costPrice: 15.00, suggestPrice: 25.00, stock: 300, description: 'Anti-colic baby bottle with natural latch technology.' },
  { name: 'Fisher-Price Baby Swing', categoryId: 7, brand: 'Fisher-Price', mainImage: '/static/products/fisher-price-baby-swing.jpg', costPrice: 120.00, suggestPrice: 180.00, stock: 20, description: 'Comfortable baby swing with multiple speeds.' },
  { name: 'Graco 4Ever Car Seat', categoryId: 7, brand: 'Graco', mainImage: '/static/products/graco-4ever-car-seat.jpg', costPrice: 200.00, suggestPrice: 300.00, stock: 15, description: 'Convertible car seat that grows with your child.' }
];

async function createProductsViaAPI() {
  try {
    console.log('🚀 开始创建40个产品...');
    
    let successCount = 0;
    let failCount = 0;
    
    for (const productData of productsToCreate) {
      try {
        // 尝试使用公开API
        const response = await axios.post('https://tiktokshop-api.onrender.com/api/public-products', productData);
        
        if (response.data.code === 200) {
          successCount++;
          console.log(`✅ 产品创建成功: ${productData.name}`);
        } else {
          failCount++;
          console.log(`⚠️  产品创建失败: ${productData.name} - ${response.data.message}`);
        }
        
      } catch (error) {
        failCount++;
        console.log(`❌ 产品创建失败: ${productData.name} - ${error.response?.data?.message || error.message}`);
      }
      
      // 避免请求过快
      await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    console.log(`\n📊 产品创建完成！`);
    console.log(`✅ 成功: ${successCount} 个`);
    console.log(`❌ 失败: ${failCount} 个`);
    
    if (failCount > 0) {
      console.log('\n💡 如果API失败，可以手动执行SQL语句创建产品');
      console.log('📋 生成SQL文件...');
      
      const fs = require('fs');
      let sqlContent = `-- 创建40个产品\n`;
      
      for (const product of productsToCreate) {
        sqlContent += `INSERT INTO platform_product (name, category_id, brand, main_image, cost_price, suggest_price, stock, description, status) VALUES ('${product.name}', ${product.categoryId}, '${product.brand}', '${product.mainImage}', ${product.costPrice}, ${product.suggestPrice}, ${product.stock}, '${product.description}', 1);\n`;
      }
      
      fs.writeFileSync('create-40-products.sql', sqlContent);
      console.log('💾 已生成SQL文件: create-40-products.sql');
    }
    
  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

createProductsViaAPI();
