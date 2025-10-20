-- 创建商户充值表
CREATE TABLE IF NOT EXISTS merchant_recharge (
  id bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  merchant_id bigint(20) NOT NULL COMMENT '商户ID',
  amount decimal(10,2) NOT NULL COMMENT '充值金额',
  payment_method varchar(50) NOT NULL COMMENT '支付方式',
  payment_reference varchar(200) DEFAULT NULL COMMENT '支付凭证号',
  remark text DEFAULT NULL COMMENT '备注',
  status tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 0待审核 1审核通过 2审核拒绝',
  admin_id bigint(20) DEFAULT NULL COMMENT '审核管理员ID',
  admin_name varchar(100) DEFAULT NULL COMMENT '审核管理员姓名',
  audit_reason text DEFAULT NULL COMMENT '审核原因',
  audit_time datetime DEFAULT NULL COMMENT '审核时间',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (id),
  KEY idx_merchant_id (merchant_id),
  KEY idx_status (status),
  KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商户充值记录表';

-- 插入测试数据
INSERT IGNORE INTO merchant_recharge (merchant_id, amount, payment_method, payment_reference, remark, status, created_at) VALUES
(93, 1000.00, '银行转账', 'REF001', '测试充值1', 0, NOW()),
(94, 2000.00, '支付宝', 'REF002', '测试充值2', 1, NOW()),
(95, 1500.00, '微信支付', 'REF003', '测试充值3', 2, NOW()),
(96, 3000.00, '银行转账', 'REF004', '测试充值4', 0, NOW()),
(97, 2500.00, '支付宝', 'REF005', '测试充值5', 1, NOW()),
(98, 1800.00, '银行转账', 'REF006', '测试充值6', 0, NOW()),
(99, 2200.00, '微信支付', 'REF007', '测试充值7', 1, NOW()),
(100, 1200.00, '支付宝', 'REF008', '测试充值8', 2, NOW());
