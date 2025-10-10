-- 创建商家商品表
CREATE TABLE IF NOT EXISTS `merchant_product` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `merchant_id` bigint NOT NULL COMMENT '商家ID',
  `product_id` bigint NOT NULL COMMENT '商品ID',
  `sale_price` decimal(10,2) NOT NULL COMMENT '销售价格',
  `profit_margin` decimal(10,2) DEFAULT NULL COMMENT '利润率',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '状态 1上架 0下架',
  `sales` int NOT NULL DEFAULT '0' COMMENT '销量',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_merchant_product` (`merchant_id`, `product_id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家商品表';
