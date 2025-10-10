-- Insert test data for fund operations
INSERT INTO `fund_operation` (`merchant_id`, `operation_type`, `amount`, `balance_before`, `balance_after`, `frozen_before`, `frozen_after`, `admin_id`, `admin_name`, `reason`, `remark`, `create_time`, `update_time`) VALUES
(1, 1, 5000.00, 0.00, 5000.00, 0.00, 0.00, 1, 'admin', '初始充值', '商户注册后初始资金', NOW(), NOW()),
(2, 1, 3000.00, 0.00, 3000.00, 0.00, 0.00, 1, 'admin', '初始充值', '商户注册后初始资金', NOW(), NOW()),
(1, 3, 1000.00, 5000.00, 4000.00, 0.00, 1000.00, 1, 'admin', '违规处理', '因违规行为冻结资金', NOW(), NOW()),
(2, 5, 500.00, 3000.00, 2500.00, 0.00, 0.00, 1, 'admin', '违规罚款', '因违规行为扣除保证金', NOW(), NOW());
