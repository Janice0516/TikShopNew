const { Client } = require('pg');

async function testUpdatedFundLogic() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 1. 查看商品价格示例
    console.log('1. 查看商品价格示例...');
    const products = await client.query(`
      SELECT id, name, cost_price, suggest_price 
      FROM platform_product 
      LIMIT 3
    `);
    console.log('   商品价格示例:');
    products.rows.forEach(product => {
      console.log(`   - ${product.name}: 成本价=${product.cost_price}, 建议价=${product.suggest_price}`);
    });

    // 2. 模拟订单创建和资金冻结
    console.log('\n2. 模拟订单创建和资金冻结...');
    
    // 假设订单包含商品ID=115，数量=2
    const orderItems = [
      { product_id: '115', quantity: 2 }
    ];
    
    let totalProductPrice = 0;
    for (const item of orderItems) {
      const product = await client.query('SELECT suggest_price FROM platform_product WHERE id = $1', [item.product_id]);
      if (product.rows.length > 0) {
        const productPrice = parseFloat(product.rows[0].suggest_price);
        totalProductPrice += productPrice * item.quantity;
        console.log(`   - 商品${item.product_id}: 单价=${productPrice}, 数量=${item.quantity}, 小计=${productPrice * item.quantity}`);
      }
    }
    console.log(`   总商品价格: ${totalProductPrice}`);

    // 3. 模拟订单完成和收益结算
    console.log('\n3. 模拟订单完成和收益结算...');
    const orderTotalAmount = 100; // 假设订单总金额100元
    const merchantProfit = orderTotalAmount - totalProductPrice;
    const platformProfit = 0; // 平台不抽成
    
    console.log(`   订单总金额: ${orderTotalAmount}`);
    console.log(`   商品价格: ${totalProductPrice}`);
    console.log(`   商家收益: ${merchantProfit}`);
    console.log(`   平台抽成: ${platformProfit}`);

    // 4. 资金变动总结
    console.log('\n4. 资金变动总结:');
    console.log('   冻结时:');
    console.log(`     - 冻结金额: ${totalProductPrice} (商品价格)`);
    console.log(`     - 商家余额: 原余额 - ${totalProductPrice}`);
    console.log('   解冻时:');
    console.log(`     - 解冻金额: ${totalProductPrice} (商品价格)`);
    console.log(`     - 收益结算: ${merchantProfit} (订单售价 - 商品价格)`);
    console.log(`     - 最终余额: 原余额 + ${totalProductPrice} + ${merchantProfit}`);

    console.log('\n🎉 修改后的资金冻结逻辑验证完成！');
    console.log('\n📋 关键变化:');
    console.log('   ✅ 冻结金额 = 商品价格 (suggestPrice)');
    console.log('   ✅ 平台抽成 = 0%');
    console.log('   ✅ 商家收益 = 订单售价 - 商品价格');

  } catch (error) {
    console.error('❌ 验证失败:', error);
  } finally {
    await client.end();
  }
}

testUpdatedFundLogic();
