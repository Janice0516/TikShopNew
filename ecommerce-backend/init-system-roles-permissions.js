const mysql = require('mysql2/promise');

async function initSystemRolesAndPermissions() {
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

        // 插入系统权限
        console.log('Inserting system permissions...');
        const systemPermissions = [
            // 用户管理
            { code: 'USER_VIEW', name: '查看用户', group: '用户管理', description: '查看用户列表和详情' },
            { code: 'USER_CREATE', name: '创建用户', group: '用户管理', description: '创建新用户' },
            { code: 'USER_UPDATE', name: '编辑用户', group: '用户管理', description: '编辑用户信息' },
            { code: 'USER_DELETE', name: '删除用户', group: '用户管理', description: '删除用户' },
            
            // 商家管理
            { code: 'MERCHANT_VIEW', name: '查看商家', group: '商家管理', description: '查看商家列表和详情' },
            { code: 'MERCHANT_CREATE', name: '创建商家', group: '商家管理', description: '创建新商家' },
            { code: 'MERCHANT_UPDATE', name: '编辑商家', group: '商家管理', description: '编辑商家信息' },
            { code: 'MERCHANT_DELETE', name: '删除商家', group: '商家管理', description: '删除商家' },
            { code: 'MERCHANT_AUDIT', name: '审核商家', group: '商家管理', description: '审核商家申请' },
            
            // 商品管理
            { code: 'PRODUCT_VIEW', name: '查看商品', group: '商品管理', description: '查看商品列表和详情' },
            { code: 'PRODUCT_CREATE', name: '创建商品', group: '商品管理', description: '创建新商品' },
            { code: 'PRODUCT_UPDATE', name: '编辑商品', group: '商品管理', description: '编辑商品信息' },
            { code: 'PRODUCT_DELETE', name: '删除商品', group: '商品管理', description: '删除商品' },
            
            // 订单管理
            { code: 'ORDER_VIEW', name: '查看订单', group: '订单管理', description: '查看订单列表和详情' },
            { code: 'ORDER_UPDATE', name: '编辑订单', group: '订单管理', description: '编辑订单状态' },
            
            // 财务管理
            { code: 'FINANCE_VIEW', name: '查看财务', group: '财务管理', description: '查看财务数据' },
            { code: 'FINANCE_WITHDRAWAL', name: '提现管理', group: '财务管理', description: '管理商家提现' },
            { code: 'FINANCE_RECHARGE', name: '充值管理', group: '财务管理', description: '管理商家充值' },
            
            // 系统管理
            { code: 'SYSTEM_ROLE', name: '角色管理', group: '系统管理', description: '管理系统角色' },
            { code: 'SYSTEM_PERMISSION', name: '权限管理', group: '系统管理', description: '管理系统权限' },
            { code: 'SYSTEM_ADMIN', name: '管理员管理', group: '系统管理', description: '管理系统管理员' },
            { code: 'SYSTEM_SETTINGS', name: '系统设置', group: '系统管理', description: '系统参数设置' },
            
            // 邀请码管理
            { code: 'INVITE_CODE_VIEW', name: '查看邀请码', group: '邀请码管理', description: '查看邀请码列表' },
            { code: 'INVITE_CODE_CREATE', name: '创建邀请码', group: '邀请码管理', description: '创建新邀请码' },
            { code: 'INVITE_CODE_UPDATE', name: '编辑邀请码', group: '邀请码管理', description: '编辑邀请码信息' },
            { code: 'INVITE_CODE_DELETE', name: '删除邀请码', group: '邀请码管理', description: '删除邀请码' },
        ];

        for (const permData of systemPermissions) {
            const [existing] = await connection.execute(
                'SELECT id FROM `permissions` WHERE code = ?',
                [permData.code]
            );
            
            if (existing.length === 0) {
                await connection.execute(`
                    INSERT INTO \`permissions\` (\`code\`, \`name\`, \`description\`, \`group\`, \`status\`, \`is_system\`)
                    VALUES (?, ?, ?, ?, 1, 1)
                `, [permData.code, permData.name, permData.description, permData.group]);
                console.log(`Inserted permission: ${permData.code}`);
            } else {
                console.log(`Permission already exists: ${permData.code}`);
            }
        }

        // 插入系统角色
        console.log('Inserting system roles...');
        const systemRoles = [
            {
                code: 'SUPER_ADMIN',
                name: '超级管理员',
                description: '拥有所有权限的超级管理员',
                permissions: systemPermissions.map(p => p.code)
            },
            {
                code: 'SALES_MANAGER',
                name: '销售经理',
                description: '负责销售团队管理',
                permissions: ['USER_VIEW', 'MERCHANT_VIEW', 'MERCHANT_AUDIT', 'INVITE_CODE_VIEW', 'INVITE_CODE_CREATE']
            },
            {
                code: 'FINANCE_MANAGER',
                name: '财务经理',
                description: '负责财务管理',
                permissions: ['FINANCE_VIEW', 'FINANCE_WITHDRAWAL', 'FINANCE_RECHARGE', 'ORDER_VIEW']
            },
            {
                code: 'SALES_PERSON',
                name: '业务员',
                description: '负责客户开发',
                permissions: ['MERCHANT_VIEW', 'INVITE_CODE_VIEW', 'INVITE_CODE_CREATE']
            }
        ];

        for (const roleData of systemRoles) {
            const [existing] = await connection.execute(
                'SELECT id FROM `roles` WHERE code = ?',
                [roleData.code]
            );
            
            if (existing.length === 0) {
                // 插入角色
                const [result] = await connection.execute(`
                    INSERT INTO \`roles\` (\`code\`, \`name\`, \`description\`, \`status\`, \`is_system\`)
                    VALUES (?, ?, ?, 1, 1)
                `, [roleData.code, roleData.name, roleData.description]);
                
                const roleId = result.insertId;
                console.log(`Inserted role: ${roleData.code} with ID: ${roleId}`);

                // 插入角色权限关联
                for (const permCode of roleData.permissions) {
                    const [permResult] = await connection.execute(
                        'SELECT id FROM `permissions` WHERE code = ?',
                        [permCode]
                    );
                    
                    if (permResult.length > 0) {
                        const permissionId = permResult[0].id;
                        await connection.execute(`
                            INSERT INTO \`role_permissions\` (\`role_id\`, \`permission_id\`)
                            VALUES (?, ?)
                        `, [roleId, permissionId]);
                    }
                }
                console.log(`Associated permissions for role: ${roleData.code}`);
            } else {
                console.log(`Role already exists: ${roleData.code}`);
            }
        }

        // 更新现有管理员为超级管理员角色
        console.log('Updating existing admin to super admin role...');
        const [superAdminRole] = await connection.execute(
            'SELECT id FROM `roles` WHERE code = ?',
            ['SUPER_ADMIN']
        );
        
        if (superAdminRole.length > 0) {
            await connection.execute(
                'UPDATE `admin` SET role_id = ? WHERE username = ?',
                [superAdminRole[0].id, 'admin']
            );
            console.log('Updated admin user to super admin role');
        }

        console.log('System roles and permissions initialization completed successfully!');

    } catch (error) {
        console.error('Initialization failed:', error);
    } finally {
        if (connection) {
            await connection.end();
        }
    }
}

initSystemRolesAndPermissions();
