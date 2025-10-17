const { Client } = require('pg');

const client = new Client({
  host: 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
  port: 5432,
  database: 'tiktokshop_slkz',
  user: 'tiktokshop_slkz_user',
  password: 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
  ssl: { rejectUnauthorized: false }
});

async function checkDataCount() {
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 检查商家数量
    const merchantResult = await client.query('SELECT COUNT(*) FROM merchant');
    console.log(`📊 商家数量: ${merchantResult.rows[0].count}`);

    // 检查产品数量
    const productResult = await client.query('SELECT COUNT(*) FROM product');
    console.log(`📊 产品数量: ${productResult.rows[0].count}`);

    // 检查分类数量
    const categoryResult = await client.query('SELECT COUNT(*) FROM category');
    console.log(`📊 分类数量: ${categoryResult.rows[0].count}`);

    // 检查用户数量
    const userResult = await client.query('SELECT COUNT(*) FROM "user"');
    console.log(`📊 用户数量: ${userResult.rows[0].count}`);

    // 检查订单数量
    const orderResult = await client.query('SELECT COUNT(*) FROM "order"');
    console.log(`📊 订单数量: ${orderResult.rows[0].count}`);

    // 显示商家详情
    console.log('\n🏪 商家详情:');
    const merchants = await client.query('SELECT id, merchant_name, shop_name, status FROM merchant ORDER BY id');
    merchants.rows.forEach(merchant => {
      console.log(`  - ID: ${merchant.id}, 名称: ${merchant.merchant_name}, 店铺: ${merchant.shop_name}, 状态: ${merchant.status}`);
    });

    // 显示产品详情
    console.log('\n📦 产品详情:');
    const products = await client.query('SELECT id, name, brand, stock, sales, status FROM product ORDER BY id');
    products.rows.forEach(product => {
      console.log(`  - ID: ${product.id}, 名称: ${product.name}, 品牌: ${product.brand}, 库存: ${product.stock}, 销量: ${product.sales}, 状态: ${product.status}`);
    });

    // 显示分类详情
    console.log('\n📂 分类详情:');
    const categories = await client.query('SELECT id, name, parent_id, status FROM category ORDER BY id');
    categories.rows.forEach(category => {
      console.log(`  - ID: ${category.id}, 名称: ${category.name}, 父分类: ${category.parent_id}, 状态: ${category.status}`);
    });

  } catch (error) {
    console.error('❌ 查询失败:', error.message);
  } finally {
    await client.end();
  }
}

checkDataCount();
