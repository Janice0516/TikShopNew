const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function debugCategoryService() {
  console.log('🔍 调试CategoryService问题...');

  const dbConfig = {
    host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
    port: 5432,
    user: 'tiktokshop_slkz_user',
    password: process.env.DB_PASSWORD,
    database: 'tiktokshop_slkz',
    ssl: {
      rejectUnauthorized: false,
    },
  };

  const client = new Client(dbConfig);

  try {
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 模拟CategoryService的findAll方法
    console.log('\n📊 模拟CategoryService.findAll...');
    const categories = await client.query('SELECT * FROM category ORDER BY sort ASC, id ASC');
    console.log(`查询到 ${categories.rows.length} 个分类`);

    // 检查数据类型
    console.log('\n🔍 检查数据类型...');
    if (categories.rows.length > 0) {
      const firstCategory = categories.rows[0];
      console.log('第一个分类的数据类型:');
      console.log(`  id: ${typeof firstCategory.id} (${firstCategory.id})`);
      console.log(`  parent_id: ${typeof firstCategory.parent_id} (${firstCategory.parent_id})`);
      console.log(`  name: ${typeof firstCategory.name} (${firstCategory.name})`);
      console.log(`  level: ${typeof firstCategory.level} (${firstCategory.level})`);
      console.log(`  sort: ${typeof firstCategory.sort} (${firstCategory.sort})`);
      console.log(`  status: ${typeof firstCategory.status} (${firstCategory.status})`);
    }

    // 模拟buildCategoryTree方法
    console.log('\n🌳 模拟buildCategoryTree...');
    const categoryMap = new Map();
    const tree = [];

    // 创建分类映射
    categories.rows.forEach(category => {
      categoryMap.set(category.id, {
        ...category,
        children: [],
      });
    });

    console.log(`创建了 ${categoryMap.size} 个分类映射`);

    // 构建树形结构
    categories.rows.forEach(category => {
      console.log(`处理分类: ${category.name}, parentId: ${category.parent_id}, type: ${typeof category.parent_id}`);
      
      if (Number(category.parent_id) === 0) {
        console.log(`  -> 添加到根节点`);
        tree.push(categoryMap.get(category.id));
      } else {
        console.log(`  -> 查找父节点: ${Number(category.parent_id)}`);
        const parent = categoryMap.get(Number(category.parent_id));
        if (parent) {
          console.log(`  -> 找到父节点: ${parent.name}`);
          parent.children.push(categoryMap.get(category.id));
        } else {
          console.log(`  -> 未找到父节点`);
        }
      }
    });

    console.log(`\n✅ 构建完成，根节点数量: ${tree.length}`);
    
    // 显示树结构
    tree.forEach(root => {
      console.log(`根分类: ${root.name} (${root.children.length} 个子分类)`);
    });

  } catch (error) {
    console.error('❌ 调试失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
  }
}

debugCategoryService();
