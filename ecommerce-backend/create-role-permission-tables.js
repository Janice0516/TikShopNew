const mysql = require('mysql2/promise');

async function createRolePermissionTables() {
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

        // 创建权限表
        console.log('Creating permissions table...');
        await connection.execute(`
            CREATE TABLE IF NOT EXISTS \`permissions\` (
                \`id\` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                \`code\` varchar(50) NOT NULL UNIQUE COMMENT '权限代码',
                \`name\` varchar(100) NOT NULL COMMENT '权限名称',
                \`description\` varchar(255) DEFAULT NULL COMMENT '权限描述',
                \`group\` varchar(100) NOT NULL COMMENT '权限分组',
                \`status\` smallint(6) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
                \`is_system\` smallint(6) NOT NULL DEFAULT '0' COMMENT '是否系统权限 0否 1是',
                \`create_time\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                \`update_time\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                PRIMARY KEY (\`id\`),
                UNIQUE KEY \`uk_code\` (\`code\`),
                KEY \`idx_group\` (\`group\`),
                KEY \`idx_status\` (\`status\`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限表';
        `);
        console.log('Permissions table created successfully!');

        // 创建角色表
        console.log('Creating roles table...');
        await connection.execute(`
            CREATE TABLE IF NOT EXISTS \`roles\` (
                \`id\` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                \`code\` varchar(50) NOT NULL UNIQUE COMMENT '角色代码',
                \`name\` varchar(100) NOT NULL COMMENT '角色名称',
                \`description\` varchar(255) DEFAULT NULL COMMENT '角色描述',
                \`status\` smallint(6) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
                \`is_system\` smallint(6) NOT NULL DEFAULT '0' COMMENT '是否系统角色 0否 1是',
                \`create_time\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                \`update_time\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                PRIMARY KEY (\`id\`),
                UNIQUE KEY \`uk_code\` (\`code\`),
                KEY \`idx_status\` (\`status\`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色表';
        `);
        console.log('Roles table created successfully!');

        // 创建角色权限关联表
        console.log('Creating role_permissions table...');
        await connection.execute(`
            CREATE TABLE IF NOT EXISTS \`role_permissions\` (
                \`role_id\` bigint(20) NOT NULL COMMENT '角色ID',
                \`permission_id\` bigint(20) NOT NULL COMMENT '权限ID',
                PRIMARY KEY (\`role_id\`, \`permission_id\`),
                KEY \`idx_role_id\` (\`role_id\`),
                KEY \`idx_permission_id\` (\`permission_id\`),
                CONSTRAINT \`fk_role_permissions_role\` FOREIGN KEY (\`role_id\`) REFERENCES \`roles\` (\`id\`) ON DELETE CASCADE,
                CONSTRAINT \`fk_role_permissions_permission\` FOREIGN KEY (\`permission_id\`) REFERENCES \`permissions\` (\`id\`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色权限关联表';
        `);
        console.log('Role permissions table created successfully!');

        // 更新admin表，添加角色相关字段
        console.log('Updating admin table...');
        const [columns] = await connection.execute("SHOW COLUMNS FROM `admin`");
        const existingColumns = columns.map(row => row.Field);

        let alterSql = "ALTER TABLE `admin`";
        let changesMade = false;

        if (!existingColumns.includes('role_id')) {
            alterSql += " ADD COLUMN `role_id` varchar(50) DEFAULT NULL COMMENT '角色ID' AFTER `role`";
            changesMade = true;
        }
        if (!existingColumns.includes('position')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `position` varchar(50) DEFAULT NULL COMMENT '职务' AFTER `role_id`";
            changesMade = true;
        }
        if (!existingColumns.includes('phone')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `phone` varchar(20) DEFAULT NULL COMMENT '手机号' AFTER `position`";
            changesMade = true;
        }
        if (!existingColumns.includes('email')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `email` varchar(100) DEFAULT NULL COMMENT '邮箱' AFTER `phone`";
            changesMade = true;
        }
        if (!existingColumns.includes('remark')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `remark` varchar(255) DEFAULT NULL COMMENT '备注' AFTER `email`";
            changesMade = true;
        }

        if (changesMade) {
            console.log('SQL:', alterSql);
            await connection.execute(alterSql);
            console.log('Admin table updated successfully!');
        } else {
            console.log('Admin table already has all required columns.');
        }

        console.log('Database migration completed successfully!');

    } catch (error) {
        console.error('Database migration failed:', error);
    } finally {
        if (connection) {
            await connection.end();
        }
    }
}

createRolePermissionTables();
