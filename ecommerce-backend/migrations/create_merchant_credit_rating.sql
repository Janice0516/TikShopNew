-- Create merchant_credit_rating table
CREATE TABLE `merchant_credit_rating` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `merchant_id` bigint NOT NULL COMMENT '商户ID',
  `rating` tinyint NOT NULL COMMENT '信用评级 1-5星',
  `score` decimal(5,2) NOT NULL COMMENT '信用分数 0-100',
  `level` varchar(20) NOT NULL COMMENT '信用等级 AAA/AA/A/BBB/BB/B/C',
  `evaluation_date` date NOT NULL COMMENT '评级日期',
  `valid_until` date NOT NULL COMMENT '有效期至',
  `evaluator_id` bigint NOT NULL COMMENT '评估人ID',
  `evaluation_reason` text COMMENT '评级原因',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '状态 0无效 1有效',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_level` (`level`),
  KEY `idx_status` (`status`),
  KEY `idx_evaluation_date` (`evaluation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='商户信用评级表';
