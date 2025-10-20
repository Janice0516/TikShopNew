const mysql = require('mysql2/promise');

async function addOrderRemarkFields() {
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

        // 检查并添加订单备注相关字段
        console.log('Adding order remark fields...');
        const [columns] = await connection.execute("SHOW COLUMNS FROM `order_main`");
        const existingColumns = columns.map(row => row.Field);

        let alterSql = "ALTER TABLE `order_main`";
        let changesMade = false;

        if (!existingColumns.includes('admin_remark')) {
            alterSql += " ADD COLUMN `admin_remark` text DEFAULT NULL COMMENT '管理员备注' AFTER `remark`";
            changesMade = true;
        }
        if (!existingColumns.includes('logistics_status')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `logistics_status` varchar(100) DEFAULT NULL COMMENT '物流状态' AFTER `admin_remark`";
            changesMade = true;
        }
        if (!existingColumns.includes('tracking_number')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `tracking_number` varchar(50) DEFAULT NULL COMMENT '快递单号' AFTER `logistics_status`";
            changesMade = true;
        }
        if (!existingColumns.includes('logistics_company')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `logistics_company` varchar(50) DEFAULT NULL COMMENT '物流公司' AFTER `tracking_number`";
            changesMade = true;
        }
        if (!existingColumns.includes('logistics_update_time')) {
            alterSql += (changesMade ? "," : "") + " ADD COLUMN `logistics_update_time` timestamp NULL DEFAULT NULL COMMENT '物流更新时间' AFTER `logistics_company`";
            changesMade = true;
        }

        if (changesMade) {
            console.log('SQL:', alterSql);
            await connection.execute(alterSql);
            console.log('Order remark fields added successfully!');
        } else {
            console.log('Order remark fields already exist, no changes needed.');
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

addOrderRemarkFields();
