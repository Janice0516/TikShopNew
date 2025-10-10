-- 创建商户提现表
CREATE TABLE IF NOT EXISTS `merchant_withdrawal` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `merchant_id` bigint(20) NOT NULL COMMENT '商户ID',
  `withdrawal_amount` decimal(10,2) NOT NULL COMMENT '提现金额',
  `bank_name` varchar(100) NOT NULL COMMENT '银行名称',
  `bank_account` varchar(50) NOT NULL COMMENT '银行账号',
  `account_holder` varchar(50) NOT NULL COMMENT '账户持有人',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 0待审核 1已通过 2已拒绝 3已打款',
  `remark` varchar(500) DEFAULT NULL COMMENT '备注',
  `admin_remark` varchar(500) DEFAULT NULL COMMENT '管理员备注',
  `processed_by` bigint(20) DEFAULT NULL COMMENT '处理人ID',
  `processed_at` datetime DEFAULT NULL COMMENT '处理时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户提现表';
