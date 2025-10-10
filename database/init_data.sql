-- =============================================
-- 初始化数据脚本
-- =============================================

USE `ecommerce`;

-- =============================================
-- 1. 插入测试管理员账号
-- =============================================
-- 密码: admin123 (BCrypt加密后)
INSERT INTO `admin` (`username`, `password`, `nickname`, `role`, `status`) 
VALUES ('admin', '$2b$10$N8qzH5Y1WZ8F.K0vB5LQ2OJzxHJ7WQ8VJ9wK3FQ5sX7zJ8Y3Q9F5S', '超级管理员', 'admin', 1);

-- =============================================
-- 2. 插入商品分类（2级分类）
-- =============================================

-- 一级分类
INSERT INTO `category` (`id`, `parent_id`, `name`, `level`, `sort`, `status`) VALUES
(1, 0, '服装鞋包', 1, 1, 1),
(2, 0, '数码家电', 1, 2, 1),
(3, 0, '食品生鲜', 1, 3, 1),
(4, 0, '美妆个护', 1, 4, 1),
(5, 0, '家居生活', 1, 5, 1);

-- 二级分类
INSERT INTO `category` (`parent_id`, `name`, `level`, `sort`, `status`) VALUES
-- 服装鞋包
(1, '男装', 2, 1, 1),
(1, '女装', 2, 2, 1),
(1, '运动鞋', 2, 3, 1),
(1, '箱包', 2, 4, 1),
-- 数码家电
(2, '手机', 2, 1, 1),
(2, '电脑', 2, 2, 1),
(2, '家用电器', 2, 3, 1),
-- 食品生鲜
(3, '零食', 2, 1, 1),
(3, '水果', 2, 2, 1),
(3, '饮料', 2, 3, 1),
-- 美妆个护
(4, '护肤品', 2, 1, 1),
(4, '化妆品', 2, 2, 1),
-- 家居生活
(5, '家纺', 2, 1, 1),
(5, '厨具', 2, 2, 1);

-- =============================================
-- 3. 插入示例平台商品（10个测试商品）
-- =============================================

INSERT INTO `platform_product` (`product_no`, `name`, `category_id`, `brand`, `main_image`, `images`, `cost_price`, `suggest_price`, `stock`, `description`, `status`, `sort`) VALUES
('P202510040001', '纯棉T恤 经典款', 6, '优衣库', '/images/products/tshirt.jpg', '["
/images/products/tshirt1.jpg","/images/products/tshirt2.jpg"]', 39.00, 79.00, 1000, '100%纯棉，舒适透气', 1, 1),
('P202510040002', '运动跑鞋 透气款', 8, '耐克', '/images/products/shoes.jpg', '["
/images/products/shoes1.jpg"]', 180.00, 399.00, 500, '轻便透气，适合跑步', 1, 2),
('P202510040003', '无线蓝牙耳机', 10, '小米', '/images/products/earphone.jpg', '[]', 89.00, 199.00, 800, '主动降噪，续航24小时', 1, 3),
('P202510040004', '智能手环', 10, '华为', '/images/products/band.jpg', '[]', 129.00, 299.00, 600, '心率监测，睡眠监测', 1, 4),
('P202510040005', '坚果礼盒', 13, '三只松鼠', '/images/products/nuts.jpg', '[]', 49.00, 99.00, 2000, '每日坚果，健康零食', 1, 5),
('P202510040006', '保温杯 316不锈钢', 19, '象印', '/images/products/cup.jpg', '[]', 79.00, 189.00, 1500, '24小时保温保冷', 1, 6),
('P202510040007', '面膜套装', 16, '一叶子', '/images/products/mask.jpg', '[]', 39.00, 89.00, 3000, '补水保湿，10片装', 1, 7),
('P202510040008', '四件套 纯棉', 18, '水星家纺', '/images/products/bedding.jpg', '[]', 199.00, 399.00, 500, '60支纯棉，亲肤柔软', 1, 8),
('P202510040009', '电动牙刷', 4, '飞利浦', '/images/products/toothbrush.jpg', '[]', 149.00, 329.00, 800, '声波震动，5种模式', 1, 9),
('P202510040010', '保鲜盒套装', 19, '乐扣', '/images/products/box.jpg', '[]', 29.00, 69.00, 1000, '密封防漏，5件套', 1, 10);

-- =============================================
-- 4. 插入系统配置
-- =============================================

INSERT INTO `system_config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('system_name', '供货型电商平台', 'string', '系统名称'),
('freight_free_amount', '99', 'number', '包邮金额（元）'),
('default_freight', '6', 'number', '默认运费（元）'),
('withdraw_min_amount', '100', 'number', '最低提现金额（元）'),
('withdraw_fee_rate', '0', 'number', '提现手续费率（%）'),
('order_auto_cancel_time', '30', 'number', '订单自动取消时间（分钟）'),
('order_auto_finish_time', '7', 'number', '订单自动完成时间（天）'),
('sms_sign_name', '电商平台', 'string', '短信签名'),
('contact_phone', '400-000-0000', 'string', '客服电话'),
('contact_email', 'service@example.com', 'string', '客服邮箱');

-- =============================================
-- 5. 插入测试用户（可选）
-- =============================================
-- 密码: 123456
INSERT INTO `user` (`phone`, `password`, `nickname`, `status`) VALUES
('13800138000', '$2b$10$N8qzH5Y1WZ8F.K0vB5LQ2OJzxHJ7WQ8VJ9wK3FQ5sX7zJ8Y3Q9F5S', '测试用户', 1);

-- =============================================
-- 6. 插入测试商家（可选）
-- =============================================
-- 密码: 123456
INSERT INTO `merchant` (`username`, `password`, `merchant_name`, `contact_name`, `contact_phone`, `status`, `shop_name`, `shop_description`) VALUES
('merchant001', '$2b$10$N8qzH5Y1WZ8F.K0vB5LQ2OJzxHJ7WQ8VJ9wK3FQ5sX7zJ8Y3Q9F5S', '测试商家', '张三', '13900139000', 1, '优品小店', '品质生活，优选好物');

-- =============================================
-- 初始化数据插入完成
-- =============================================

-- 查询统计
SELECT '分类数量' as '统计项', COUNT(*) as '数量' FROM category
UNION ALL
SELECT '商品数量', COUNT(*) FROM platform_product
UNION ALL
SELECT '管理员数量', COUNT(*) FROM admin
UNION ALL
SELECT '测试用户', COUNT(*) FROM user
UNION ALL
SELECT '测试商家', COUNT(*) FROM merchant;

