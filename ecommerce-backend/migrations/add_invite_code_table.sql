-- 创建邀请码表
CREATE TABLE IF NOT EXISTS `invite_code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `invite_code` varchar(20) NOT NULL COMMENT '邀请码',
  `salesperson_name` varchar(100) NOT NULL COMMENT '业务员姓名',
  `salesperson_phone` varchar(20) DEFAULT NULL COMMENT '业务员电话',
  `salesperson_id` varchar(50) DEFAULT NULL COMMENT '业务员ID',
  `used_count` int(11) NOT NULL DEFAULT '0' COMMENT '已使用次数',
  `max_usage` int(11) NOT NULL DEFAULT '0' COMMENT '最大使用次数，0表示无限制',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `expire_time` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_invite_code` (`invite_code`),
  KEY `idx_salesperson_name` (`salesperson_name`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邀请码表';

-- 为商家表添加邀请码相关字段
ALTER TABLE `merchant` 
ADD COLUMN `invite_code` varchar(20) DEFAULT NULL COMMENT '注册时使用的邀请码' AFTER `total_withdraw`,
ADD COLUMN `salesperson_name` varchar(100) DEFAULT NULL COMMENT '业务员姓名' AFTER `invite_code`,
ADD COLUMN `salesperson_phone` varchar(20) DEFAULT NULL COMMENT '业务员电话' AFTER `salesperson_name`,
ADD COLUMN `salesperson_id` varchar(50) DEFAULT NULL COMMENT '业务员ID' AFTER `salesperson_phone`;

-- 添加索引
ALTER TABLE `merchant` 
ADD KEY `idx_invite_code` (`invite_code`),
ADD KEY `idx_salesperson_name` (`salesperson_name`);

-- 插入一些示例邀请码
INSERT INTO `invite_code` (`invite_code`, `salesperson_name`, `salesperson_phone`, `salesperson_id`, `max_usage`, `status`, `remark`) VALUES
('WELCOME2024', '张三', '012-3456789', 'SALES001', 100, 1, '欢迎新商家入驻'),
('VIP2024', '李四', '012-9876543', 'SALES002', 50, 1, 'VIP客户专用邀请码'),
('PREMIUM2024', '王五', '012-1111111', 'SALES003', 0, 1, '高级客户无限制邀请码');
