-- PostgreSQL数据库迁移脚本
-- 从MySQL迁移到PostgreSQL

-- 创建数据库
-- CREATE DATABASE tiktokshop;

-- 用户表
CREATE TABLE IF NOT EXISTS "user" (
    id SERIAL PRIMARY KEY,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nickname VARCHAR(50),
    avatar VARCHAR(500),
    status INTEGER DEFAULT 1,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 管理员表
CREATE TABLE IF NOT EXISTS admin (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    status INTEGER DEFAULT 1,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商家表
CREATE TABLE IF NOT EXISTS merchant (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    shop_name VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    business_license VARCHAR(100),
    status INTEGER DEFAULT 1,
    balance DECIMAL(10,2) DEFAULT 0.00,
    frozen_amount DECIMAL(10,2) DEFAULT 0.00,
    uid VARCHAR(50) UNIQUE,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 分类表
CREATE TABLE IF NOT EXISTS category (
    id SERIAL PRIMARY KEY,
    parent_id INTEGER DEFAULT 0,
    name VARCHAR(100) NOT NULL,
    level INTEGER DEFAULT 1,
    sort INTEGER DEFAULT 0,
    icon VARCHAR(200),
    status INTEGER DEFAULT 1,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商品表
CREATE TABLE IF NOT EXISTS product (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    stock INTEGER DEFAULT 0,
    images TEXT,
    category_id INTEGER,
    merchant_id INTEGER,
    status INTEGER DEFAULT 1,
    sales_count INTEGER DEFAULT 0,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商家商品表
CREATE TABLE IF NOT EXISTS merchant_product (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    status INTEGER DEFAULT 1,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(merchant_id, product_id)
);

-- 购物车表
CREATE TABLE IF NOT EXISTS cart (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER DEFAULT 1,
    selected BOOLEAN DEFAULT true,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, product_id)
);

-- 订单表
CREATE TABLE IF NOT EXISTS "order" (
    id SERIAL PRIMARY KEY,
    order_no VARCHAR(50) UNIQUE NOT NULL,
    user_id INTEGER NOT NULL,
    merchant_id INTEGER NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    shipping_address TEXT,
    payment_method VARCHAR(50),
    payment_time TIMESTAMP,
    shipping_time TIMESTAMP,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 订单项表
CREATE TABLE IF NOT EXISTS order_item (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INTEGER NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商家充值表
CREATE TABLE IF NOT EXISTS merchant_recharge (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商家提现信息表
CREATE TABLE IF NOT EXISTS merchant_withdrawal_info (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL,
    bank_name VARCHAR(100),
    bank_account VARCHAR(50),
    account_holder VARCHAR(100),
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商家提现记录表
CREATE TABLE IF NOT EXISTS merchant_withdrawal (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 资金操作记录表
CREATE TABLE IF NOT EXISTS fund_operation (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL,
    operation_type VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    balance_before DECIMAL(10,2) NOT NULL,
    balance_after DECIMAL(10,2) NOT NULL,
    description TEXT,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商家信用评级表
CREATE TABLE IF NOT EXISTS merchant_credit_rating (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL,
    score INTEGER NOT NULL,
    level VARCHAR(10) NOT NULL,
    evaluation_reason TEXT,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 创建索引
CREATE INDEX IF NOT EXISTS idx_user_phone ON "user"(phone);
CREATE INDEX IF NOT EXISTS idx_merchant_username ON merchant(username);
CREATE INDEX IF NOT EXISTS idx_product_category ON product(category_id);
CREATE INDEX IF NOT EXISTS idx_product_merchant ON product(merchant_id);
CREATE INDEX IF NOT EXISTS idx_cart_user ON cart(user_id);
CREATE INDEX IF NOT EXISTS idx_order_user ON "order"(user_id);
CREATE INDEX IF NOT EXISTS idx_order_merchant ON "order"(merchant_id);
CREATE INDEX IF NOT EXISTS idx_order_status ON "order"(status);
CREATE INDEX IF NOT EXISTS idx_order_item_order ON order_item(order_id);
CREATE INDEX IF NOT EXISTS idx_fund_operation_merchant ON fund_operation(merchant_id);
CREATE INDEX IF NOT EXISTS idx_credit_rating_merchant ON merchant_credit_rating(merchant_id);

-- 插入初始数据
INSERT INTO admin (username, password, email) VALUES 
('admin', '$2b$10$rQZ8K9vL2nM3pQ4rS5tU6uV7wX8yZ9aA0bB1cC2dD3eE4fF5gG6hH7iI8jJ9kK0lL1mM2nN3oO4pP5qQ6rR7sS8tT9uU0vV1wW2xX3yY4zZ5', 'admin@tiktokshop.com')
ON CONFLICT (username) DO NOTHING;

INSERT INTO merchant (username, password, shop_name, contact_phone, contact_email, status, balance) VALUES 
('merchant001', '$2b$10$rQZ8K9vL2nM3pQ4rS5tU6uV7wX8yZ9aA0bB1cC2dD3eE4fF5gG6hH7iI8jJ9kK0lL1mM2nN3oO4pP5qQ6rR7sS8tT9uU0vV1wW2xX3yY4zZ5', '测试商家店铺', '13800138001', 'merchant001@test.com', 1, 10000.00)
ON CONFLICT (username) DO NOTHING;

INSERT INTO "user" (phone, password, nickname, status) VALUES 
('13800138000', '$2b$10$rQZ8K9vL2nM3pQ4rS5tU6uV7wX8yZ9aA0bB1cC2dD3eE4fF5gG6hH7iI8jJ9kK0lL1mM2nN3oO4pP5qQ6rR7sS8tT9uU0vV1wW2xX3yY4zZ5', '测试用户', 1)
ON CONFLICT (phone) DO NOTHING;

-- 插入分类数据
INSERT INTO category (parent_id, name, level, sort, status) VALUES 
(0, '电子产品', 1, 1, 1),
(0, '服装鞋帽', 1, 2, 1),
(0, '家居生活', 1, 3, 1),
(0, '美妆护肤', 1, 4, 1),
(0, '食品饮料', 1, 5, 1),
(1, '手机数码', 2, 1, 1),
(1, '电脑办公', 2, 2, 1),
(2, '男装', 2, 1, 1),
(2, '女装', 2, 2, 1),
(3, '家具', 2, 1, 1),
(3, '家电', 2, 2, 1)
ON CONFLICT DO NOTHING;

-- 插入商品数据
INSERT INTO product (name, description, price, original_price, stock, category_id, merchant_id, status, sales_count) VALUES 
('iPhone 15 Pro', '最新款iPhone，性能强劲', 7999.00, 8999.00, 50, 6, 1, 1, 0),
('MacBook Pro 16', '专业级笔记本电脑', 15999.00, 17999.00, 20, 7, 1, 1, 0),
('Nike Air Max', '经典运动鞋', 899.00, 1099.00, 100, 8, 1, 1, 0),
('Adidas T恤', '舒适运动T恤', 299.00, 399.00, 200, 9, 1, 1, 0),
('小米电视', '4K智能电视', 2999.00, 3999.00, 30, 11, 1, 1, 0),
('SK-II神仙水', '高端护肤精华', 1299.00, 1599.00, 50, 4, 1, 1, 0),
('星巴克咖啡', '精选咖啡豆', 89.00, 129.00, 100, 5, 1, 1, 0)
ON CONFLICT DO NOTHING;

-- 插入商家商品关联
INSERT INTO merchant_product (merchant_id, product_id, status) VALUES 
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1)
ON CONFLICT DO NOTHING;
