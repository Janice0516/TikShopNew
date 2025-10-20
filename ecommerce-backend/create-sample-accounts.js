const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');

async function createSampleSalespersonAccounts() {
    const dbConfig = {
        host: '127.0.0.1',
        user: 'tikshop',
        password: 'TikShop_MySQL_#2025!9pQwXz',
        database: 'ecommerce',
    };

    let connection;
    try {
        console.log('Connecting to database...');
        connection = await mysql.createConnection(dbConfig);
        console.log('Connected to database successfully!');

        // 获取业务员角色ID
        const [salespersonRole] = await connection.execute(
            'SELECT id FROM `roles` WHERE code = ?',
            ['SALES_PERSON']
        );

        if (salespersonRole.length === 0) {
            console.log('Salesperson role not found. Please run init-system-roles-permissions.js first.');
            return;
        }

        const roleId = salespersonRole[0].id;
        console.log(`Found salesperson role with ID: ${roleId}`);

        // 创建示例业务员账户
        const salespersonAccounts = [
            {
                username: 'sales001',
                password: 'sales123456',
                nickname: '张三',
                phone: '13800138001',
                email: 'zhangsan@example.com',
                remark: '负责华东地区业务'
            },
            {
                username: 'sales002',
                password: 'sales123456',
                nickname: '李四',
                phone: '13800138002',
                email: 'lisi@example.com',
                remark: '负责华南地区业务'
            },
            {
                username: 'sales003',
                password: 'sales123456',
                nickname: '王五',
                phone: '13800138003',
                email: 'wangwu@example.com',
                remark: '负责华北地区业务'
            },
            {
                username: 'sales004',
                password: 'sales123456',
                nickname: '赵六',
                phone: '13800138004',
                email: 'zhaoliu@example.com',
                remark: '负责西南地区业务'
            },
            {
                username: 'sales005',
                password: 'sales123456',
                nickname: '钱七',
                phone: '13800138005',
                email: 'qianqi@example.com',
                remark: '负责西北地区业务'
            }
        ];

        console.log('Creating sample salesperson accounts...');
        for (const account of salespersonAccounts) {
            // 检查用户名是否已存在
            const [existing] = await connection.execute(
                'SELECT id FROM `admin` WHERE username = ?',
                [account.username]
            );

            if (existing.length === 0) {
                // 密码加密
                const hashedPassword = await bcrypt.hash(account.password, 10);

                // 创建业务员账户
                await connection.execute(`
                    INSERT INTO \`admin\` (
                        \`username\`, \`password\`, \`nickname\`, \`position\`, 
                        \`phone\`, \`email\`, \`role_id\`, \`remark\`, \`status\`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                `, [
                    account.username,
                    hashedPassword,
                    account.nickname,
                    '业务员',
                    account.phone,
                    account.email,
                    roleId,
                    account.remark
                ]);

                console.log(`Created salesperson account: ${account.username} (${account.nickname})`);
            } else {
                console.log(`Salesperson account already exists: ${account.username}`);
            }
        }

        // 创建财务经理账户
        const [financeRole] = await connection.execute(
            'SELECT id FROM `roles` WHERE code = ?',
            ['FINANCE_MANAGER']
        );

        if (financeRole.length > 0) {
            const financeRoleId = financeRole[0].id;
            
            const [existingFinance] = await connection.execute(
                'SELECT id FROM `admin` WHERE username = ?',
                ['finance001']
            );

            if (existingFinance.length === 0) {
                const hashedPassword = await bcrypt.hash('finance123456', 10);
                
                await connection.execute(`
                    INSERT INTO \`admin\` (
                        \`username\`, \`password\`, \`nickname\`, \`position\`, 
                        \`phone\`, \`email\`, \`role_id\`, \`remark\`, \`status\`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                `, [
                    'finance001',
                    hashedPassword,
                    '财务经理',
                    '财务经理',
                    '13800138006',
                    'finance@example.com',
                    financeRoleId,
                    '负责财务管理'
                ]);

                console.log('Created finance manager account: finance001');
            } else {
                console.log('Finance manager account already exists: finance001');
            }
        }

        console.log('Sample accounts creation completed successfully!');
        console.log('\nCreated accounts:');
        console.log('业务员账户:');
        salespersonAccounts.forEach(account => {
            console.log(`  用户名: ${account.username}, 密码: ${account.password}, 姓名: ${account.nickname}`);
        });
        console.log('财务经理账户:');
        console.log('  用户名: finance001, 密码: finance123456, 姓名: 财务经理');

    } catch (error) {
        console.error('Account creation failed:', error);
    } finally {
        if (connection) {
            await connection.end();
        }
    }
}

createSampleSalespersonAccounts();
