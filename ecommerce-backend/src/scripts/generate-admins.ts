import { DataSource } from 'typeorm';
import * as bcrypt from 'bcrypt';
import { Admin } from '../modules/admin/entities/admin.entity';

// 数据库配置
const dataSource = new DataSource({
  type: 'postgres',
  host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
  port: 5432,
  username: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  entities: [Admin],
  ssl: { rejectUnauthorized: false },
});

// 生成随机密码
function generatePassword(): string {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < 12; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}

// 生成随机昵称
function generateNickname(): string {
  const names = ['Admin', 'Manager', 'Supervisor', 'Director', 'Coordinator', 'Lead', 'Chief', 'Head', 'Senior', 'Principal'];
  const adjectives = ['Smart', 'Quick', 'Bright', 'Sharp', 'Swift', 'Bold', 'Cool', 'Wise', 'Strong', 'Fast'];
  const randomName = names[Math.floor(Math.random() * names.length)];
  const randomAdj = adjectives[Math.floor(Math.random() * adjectives.length)];
  const number = Math.floor(Math.random() * 999) + 1;
  return `${randomAdj}${randomName}${number}`;
}

async function generateAdmins() {
  try {
    console.log('🔐 连接数据库...');
    await dataSource.initialize();
    
    const adminRepository = dataSource.getRepository(Admin);
    
    console.log('👥 生成10个管理员账户...');
    const admins = [];
    
    for (let i = 1; i <= 10; i++) {
      const username = `admin${i.toString().padStart(3, '0')}`;
      const password = generatePassword();
      const hashedPassword = await bcrypt.hash(password, 10);
      const nickname = generateNickname();
      
      // 检查是否已存在
      const existingAdmin = await adminRepository.findOne({ where: { username } });
      if (existingAdmin) {
        console.log(`⚠️  账户 ${username} 已存在，跳过`);
        continue;
      }
      
      const admin = new Admin();
      admin.username = username;
      admin.password = hashedPassword;
      admin.nickname = nickname;
      admin.role = 'admin';
      admin.status = 1;
      
      await adminRepository.save(admin);
      admins.push({ username, password, nickname });
      
      console.log(`✅ 生成账户 ${i}: ${username} / ${password} / ${nickname}`);
    }
    
    console.log('\n🎉 管理员账户生成完成！');
    console.log('================================');
    console.log('📝 账户信息汇总：');
    console.log('================================');
    
    for (let i = 0; i < admins.length; i++) {
      const admin = admins[i];
      console.log(`${i + 1}. 用户名: ${admin.username}`);
      console.log(`   密码: ${admin.password}`);
      console.log(`   昵称: ${admin.nickname}`);
      console.log(`   角色: admin`);
      console.log('   ---');
    }
    
    const totalAdmins = await adminRepository.count();
    console.log(`\n📊 数据库中共有 ${totalAdmins} 个管理员账户`);
    console.log('💡 请妥善保存这些账户信息');
    
  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    await dataSource.destroy();
  }
}

generateAdmins();
