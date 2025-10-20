-- 为商家商品表添加推荐控制字段
ALTER TABLE `merchant_product` 
ADD COLUMN `is_popular` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐为热门商品 0否 1是',
ADD COLUMN `is_top_deal` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐为Top Deals 0否 1是',
ADD COLUMN `recommend_reason` varchar(255) DEFAULT NULL COMMENT '推荐理由',
ADD COLUMN `recommend_priority` int NOT NULL DEFAULT 0 COMMENT '推荐优先级 数字越大优先级越高',
ADD COLUMN `recommend_start_time` datetime DEFAULT NULL COMMENT '推荐开始时间',
ADD COLUMN `recommend_end_time` datetime DEFAULT NULL COMMENT '推荐结束时间',
ADD INDEX `idx_is_popular` (`is_popular`),
ADD INDEX `idx_is_top_deal` (`is_top_deal`),
ADD INDEX `idx_recommend_priority` (`recommend_priority`),
ADD INDEX `idx_recommend_time` (`recommend_start_time`, `recommend_end_time`);
