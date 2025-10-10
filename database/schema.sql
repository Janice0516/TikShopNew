-- =============================================
-- International E-commerce Platform Database Schema
-- Database Version: MySQL 8.0+
-- Charset: utf8mb4
-- Currency: USD (US Dollar)
-- Created: 2025-10-04
-- =============================================

-- 创建数据库
CREATE DATABASE IF NOT EXISTS `ecommerce` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `ecommerce`;

-- =============================================
-- 1. 用户模块
-- =============================================

-- 用户表
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `phone` VARCHAR(11) NOT NULL COMMENT '手机号',
  `password` VARCHAR(100) NOT NULL COMMENT '密码（BCrypt加密）',
  `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
  `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像URL',
  `gender` TINYINT DEFAULT 0 COMMENT '性别 0未知 1男 2女',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1正常 0禁用',
  `last_login_time` DATETIME DEFAULT NULL COMMENT '最后登录时间',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_phone` (`phone`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- 用户地址表
DROP TABLE IF EXISTS `user_address`;
CREATE TABLE `user_address` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `receiver_name` VARCHAR(50) NOT NULL COMMENT '收货人姓名',
  `phone` VARCHAR(11) NOT NULL COMMENT '收货电话',
  `province` VARCHAR(50) NOT NULL COMMENT '省份',
  `city` VARCHAR(50) NOT NULL COMMENT '城市',
  `district` VARCHAR(50) NOT NULL COMMENT '区/县',
  `detail_address` VARCHAR(255) NOT NULL COMMENT '详细地址',
  `is_default` TINYINT DEFAULT 0 COMMENT '是否默认 1是 0否',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户地址表';

-- =============================================
-- 2. 商家模块
-- =============================================

-- 商家表
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '商家ID',
  `username` VARCHAR(50) NOT NULL COMMENT '登录账号',
  `password` VARCHAR(100) NOT NULL COMMENT '密码（BCrypt加密）',
  `merchant_name` VARCHAR(100) NOT NULL COMMENT '商家名称',
  `contact_name` VARCHAR(50) DEFAULT NULL COMMENT '联系人',
  `contact_phone` VARCHAR(11) DEFAULT NULL COMMENT '联系电话',
  `business_license` VARCHAR(255) DEFAULT NULL COMMENT '营业执照图片URL',
  `id_card_front` VARCHAR(255) DEFAULT NULL COMMENT '身份证正面',
  `id_card_back` VARCHAR(255) DEFAULT NULL COMMENT '身份证反面',
  `status` TINYINT DEFAULT 0 COMMENT '状态 0待审核 1已通过 2已拒绝 3已禁用',
  `reject_reason` VARCHAR(255) DEFAULT NULL COMMENT '拒绝原因',
  `shop_name` VARCHAR(100) DEFAULT NULL COMMENT '店铺名称',
  `shop_logo` VARCHAR(255) DEFAULT NULL COMMENT '店铺Logo',
  `shop_banner` TEXT DEFAULT NULL COMMENT '店铺Banner（JSON数组）',
  `shop_description` VARCHAR(500) DEFAULT NULL COMMENT '店铺简介',
  `balance` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Account Balance (USD)',
  `frozen_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Frozen Amount (USD)',
  `total_income` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total Income (USD)',
  `total_withdraw` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total Withdraw (USD)',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家表';

-- =============================================
-- 3. 商品模块
-- =============================================

-- 商品分类表
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `parent_id` BIGINT DEFAULT 0 COMMENT '父级ID，0为顶级分类',
  `name` VARCHAR(50) NOT NULL COMMENT '分类名称',
  `level` TINYINT DEFAULT 1 COMMENT '层级 1一级 2二级 3三级',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `icon` VARCHAR(255) DEFAULT NULL COMMENT '分类图标',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_status_sort` (`status`, `sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';

-- 平台商品库表
DROP TABLE IF EXISTS `platform_product`;
CREATE TABLE `platform_product` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `product_no` VARCHAR(50) DEFAULT NULL COMMENT '商品编号',
  `name` VARCHAR(200) NOT NULL COMMENT '商品名称',
  `category_id` BIGINT NOT NULL COMMENT '分类ID',
  `brand` VARCHAR(100) DEFAULT NULL COMMENT '品牌',
  `main_image` VARCHAR(255) NOT NULL COMMENT '主图',
  `images` TEXT DEFAULT NULL COMMENT '轮播图（JSON数组）',
  `video` VARCHAR(255) DEFAULT NULL COMMENT '商品视频',
  `cost_price` DECIMAL(10,2) NOT NULL COMMENT 'Platform Cost Price (USD)',
  `suggest_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Suggested Price (USD)',
  `stock` INT DEFAULT 0 COMMENT '总库存',
  `sales` INT DEFAULT 0 COMMENT '总销量',
  `description` TEXT DEFAULT NULL COMMENT '商品详情（富文本）',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1上架 0下架',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_no` (`product_no`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_status_sort` (`status`, `sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='平台商品库表';

-- 商品SKU表
DROP TABLE IF EXISTS `product_sku`;
CREATE TABLE `product_sku` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'SKU ID',
  `product_id` BIGINT NOT NULL COMMENT '商品ID',
  `sku_no` VARCHAR(50) DEFAULT NULL COMMENT 'SKU编号',
  `sku_name` VARCHAR(100) DEFAULT NULL COMMENT '规格名称（如：红色-XL）',
  `specs` VARCHAR(255) DEFAULT NULL COMMENT '规格属性（JSON：{"颜色":"红色","尺寸":"XL"}）',
  `cost_price` DECIMAL(10,2) NOT NULL COMMENT 'Cost Price (USD)',
  `stock` INT DEFAULT 0 COMMENT '库存',
  `sku_image` VARCHAR(255) DEFAULT NULL COMMENT 'SKU图片',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sku_no` (`sku_no`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品SKU表';

-- 商家商品表
DROP TABLE IF EXISTS `merchant_product`;
CREATE TABLE `merchant_product` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `platform_product_id` BIGINT NOT NULL COMMENT '平台商品ID',
  `sale_price` DECIMAL(10,2) NOT NULL COMMENT 'Sale Price (USD)',
  `profit_margin` DECIMAL(10,2) NOT NULL COMMENT 'Profit Margin (USD)',
  `profit_rate` DECIMAL(5,2) DEFAULT NULL COMMENT 'Profit Rate (%)',
  `stock` INT DEFAULT 0 COMMENT '可售库存',
  `sales` INT DEFAULT 0 COMMENT '销量',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1上架 0下架',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_merchant_product` (`merchant_id`, `platform_product_id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_platform_product_id` (`platform_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家商品表';

-- =============================================
-- 4. 购物车模块
-- =============================================

-- 购物车表
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '购物车ID',
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `product_id` BIGINT NOT NULL COMMENT '商品ID（平台商品）',
  `sku_id` BIGINT DEFAULT NULL COMMENT 'SKU ID',
  `quantity` INT DEFAULT 1 COMMENT '数量',
  `selected` TINYINT DEFAULT 1 COMMENT '是否选中 1是 0否',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_product_sku` (`user_id`, `product_id`, `sku_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车表';

-- =============================================
-- 5. 订单模块
-- =============================================

-- 订单主表
DROP TABLE IF EXISTS `order_main`;
CREATE TABLE `order_main` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_no` VARCHAR(32) NOT NULL COMMENT '订单号',
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `total_amount` DECIMAL(10,2) NOT NULL COMMENT '订单总额',
  `cost_amount` DECIMAL(10,2) NOT NULL COMMENT 'Cost Amount (USD)',
  `merchant_profit` DECIMAL(10,2) NOT NULL COMMENT 'Merchant Profit (USD)',
  `platform_profit` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Platform Profit (USD)',
  `freight` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Shipping Fee (USD)',
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Discount Amount (USD)',
  `pay_amount` DECIMAL(10,2) NOT NULL COMMENT 'Total Paid Amount (USD)',
  `receiver_name` VARCHAR(50) NOT NULL COMMENT '收货人',
  `receiver_phone` VARCHAR(11) NOT NULL COMMENT '收货电话',
  `receiver_province` VARCHAR(50) NOT NULL COMMENT '省份',
  `receiver_city` VARCHAR(50) NOT NULL COMMENT '城市',
  `receiver_district` VARCHAR(50) NOT NULL COMMENT '区/县',
  `receiver_address` VARCHAR(255) NOT NULL COMMENT '详细地址',
  `order_status` TINYINT DEFAULT 1 COMMENT '订单状态 1待付款 2待发货 3待收货 4已完成 5已取消 6售后中',
  `pay_status` TINYINT DEFAULT 0 COMMENT '支付状态 0未支付 1已支付 2已退款',
  `pay_type` TINYINT DEFAULT NULL COMMENT '支付方式 1微信 2支付宝',
  `pay_time` DATETIME DEFAULT NULL COMMENT '支付时间',
  `transaction_id` VARCHAR(100) DEFAULT NULL COMMENT '第三方支付交易号',
  `ship_time` DATETIME DEFAULT NULL COMMENT '发货时间',
  `receive_time` DATETIME DEFAULT NULL COMMENT '收货时间',
  `finish_time` DATETIME DEFAULT NULL COMMENT '完成时间',
  `cancel_time` DATETIME DEFAULT NULL COMMENT '取消时间',
  `cancel_reason` VARCHAR(255) DEFAULT NULL COMMENT '取消原因',
  `buyer_message` VARCHAR(255) DEFAULT NULL COMMENT '买家留言',
  `remark` VARCHAR(500) DEFAULT NULL COMMENT '备注',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_order_status` (`order_status`),
  KEY `idx_pay_status` (`pay_status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单主表';

-- 订单明细表
DROP TABLE IF EXISTS `order_item`;
CREATE TABLE `order_item` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` BIGINT NOT NULL COMMENT '订单ID',
  `product_id` BIGINT NOT NULL COMMENT '商品ID',
  `sku_id` BIGINT DEFAULT NULL COMMENT 'SKU ID',
  `product_name` VARCHAR(200) NOT NULL COMMENT '商品名称',
  `product_image` VARCHAR(255) NOT NULL COMMENT '商品图片',
  `sku_name` VARCHAR(100) DEFAULT NULL COMMENT '规格名称',
  `quantity` INT NOT NULL COMMENT '数量',
  `cost_price` DECIMAL(10,2) NOT NULL COMMENT 'Cost Price (USD)',
  `sale_price` DECIMAL(10,2) NOT NULL COMMENT 'Sale Price (USD)',
  `total_price` DECIMAL(10,2) NOT NULL COMMENT 'Subtotal (USD)',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单明细表';

-- 订单物流表
DROP TABLE IF EXISTS `order_logistics`;
CREATE TABLE `order_logistics` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` BIGINT NOT NULL COMMENT '订单ID',
  `logistics_company` VARCHAR(50) DEFAULT NULL COMMENT '物流公司',
  `logistics_code` VARCHAR(50) DEFAULT NULL COMMENT '物流公司编码',
  `tracking_number` VARCHAR(50) DEFAULT NULL COMMENT '快递单号',
  `ship_time` DATETIME DEFAULT NULL COMMENT '发货时间',
  `receive_time` DATETIME DEFAULT NULL COMMENT '收货时间',
  `logistics_status` TINYINT DEFAULT 0 COMMENT '物流状态 0未发货 1运输中 2派送中 3已签收',
  `logistics_info` TEXT DEFAULT NULL COMMENT '物流信息（JSON）',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_id` (`order_id`),
  KEY `idx_tracking_number` (`tracking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单物流表';

-- =============================================
-- 6. 售后模块
-- =============================================

-- 售后申请表
DROP TABLE IF EXISTS `after_sale`;
CREATE TABLE `after_sale` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `after_sale_no` VARCHAR(32) DEFAULT NULL COMMENT '售后单号',
  `order_id` BIGINT NOT NULL COMMENT '订单ID',
  `order_no` VARCHAR(32) NOT NULL COMMENT '订单号',
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `type` TINYINT NOT NULL COMMENT '类型 1仅退款 2退货退款 3换货',
  `reason` VARCHAR(255) NOT NULL COMMENT '退款原因',
  `description` VARCHAR(500) DEFAULT NULL COMMENT '问题描述',
  `images` TEXT DEFAULT NULL COMMENT '凭证图片（JSON数组）',
  `refund_amount` DECIMAL(10,2) NOT NULL COMMENT 'Refund Amount (USD)',
  `status` TINYINT DEFAULT 0 COMMENT '状态 0待审核 1已同意 2已拒绝 3退款中 4已完成',
  `reject_reason` VARCHAR(255) DEFAULT NULL COMMENT '拒绝原因',
  `return_logistics_company` VARCHAR(50) DEFAULT NULL COMMENT '退货物流公司',
  `return_tracking_number` VARCHAR(50) DEFAULT NULL COMMENT '退货快递单号',
  `refund_time` DATETIME DEFAULT NULL COMMENT '退款时间',
  `remark` VARCHAR(500) DEFAULT NULL COMMENT '备注',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_after_sale_no` (`after_sale_no`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后申请表';

-- =============================================
-- 7. 财务模块
-- =============================================

-- 资金流水表
DROP TABLE IF EXISTS `fund_flow`;
CREATE TABLE `fund_flow` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `flow_no` VARCHAR(32) DEFAULT NULL COMMENT '流水号',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `order_id` BIGINT DEFAULT NULL COMMENT '关联订单ID',
  `type` TINYINT NOT NULL COMMENT '类型 1订单收入 2提现支出 3退款支出 4冻结 5解冻',
  `amount` DECIMAL(10,2) NOT NULL COMMENT 'Amount (USD)',
  `balance_before` DECIMAL(10,2) NOT NULL COMMENT 'Balance Before (USD)',
  `balance_after` DECIMAL(10,2) NOT NULL COMMENT 'Balance After (USD)',
  `description` VARCHAR(255) DEFAULT NULL COMMENT '说明',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_flow_no` (`flow_no`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资金流水表';

-- 提现申请表
DROP TABLE IF EXISTS `withdraw`;
CREATE TABLE `withdraw` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `withdraw_no` VARCHAR(32) DEFAULT NULL COMMENT '提现单号',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `amount` DECIMAL(10,2) NOT NULL COMMENT 'Withdraw Amount (USD)',
  `bank_name` VARCHAR(50) NOT NULL COMMENT '银行名称',
  `bank_account` VARCHAR(50) NOT NULL COMMENT '银行账号',
  `account_name` VARCHAR(50) NOT NULL COMMENT '开户姓名',
  `status` TINYINT DEFAULT 0 COMMENT '状态 0待审核 1已通过 2已拒绝 3已打款',
  `reject_reason` VARCHAR(255) DEFAULT NULL COMMENT '拒绝原因',
  `fee` DECIMAL(10,2) DEFAULT 0.00 COMMENT '手续费',
  `actual_amount` DECIMAL(10,2) DEFAULT NULL COMMENT 'Actual Amount Received (USD)',
  `apply_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `audit_time` DATETIME DEFAULT NULL COMMENT '审核时间',
  `transfer_time` DATETIME DEFAULT NULL COMMENT '打款时间',
  `remark` VARCHAR(500) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_withdraw_no` (`withdraw_no`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现申请表';

-- =============================================
-- 8. 系统配置
-- =============================================

-- 系统配置表
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `config_key` VARCHAR(50) NOT NULL COMMENT '配置键',
  `config_value` TEXT NOT NULL COMMENT '配置值',
  `config_type` VARCHAR(20) DEFAULT 'string' COMMENT '类型 string/number/json/boolean',
  `description` VARCHAR(255) DEFAULT NULL COMMENT '说明',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- 管理员表（平台管理员）
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` VARCHAR(50) NOT NULL COMMENT '用户名',
  `password` VARCHAR(100) NOT NULL COMMENT '密码（BCrypt加密）',
  `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
  `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像',
  `role` VARCHAR(20) DEFAULT 'admin' COMMENT '角色',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1正常 0禁用',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- =============================================
-- 数据库脚本创建完成
-- =============================================

