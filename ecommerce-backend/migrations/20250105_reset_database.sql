-- 重置数据库脚本
-- 删除所有表数据（保留表结构）

-- 删除外键约束相关的表
SET FOREIGN_KEY_CHECKS = 0;

-- 清空所有表
TRUNCATE TABLE merchant_recharge;
TRUNCATE TABLE fund_operation;
TRUNCATE TABLE merchant_credit_rating;
TRUNCATE TABLE merchant_withdrawal;
TRUNCATE TABLE merchant_withdrawal_info;
TRUNCATE TABLE merchant;
TRUNCATE TABLE admin;
TRUNCATE TABLE user;
TRUNCATE TABLE category;

-- 重置自增ID
ALTER TABLE merchant_recharge AUTO_INCREMENT = 1;
ALTER TABLE fund_operation AUTO_INCREMENT = 1;
ALTER TABLE merchant_credit_rating AUTO_INCREMENT = 1;
ALTER TABLE merchant_withdrawal AUTO_INCREMENT = 1;
ALTER TABLE merchant_withdrawal_info AUTO_INCREMENT = 1;
ALTER TABLE merchant AUTO_INCREMENT = 1;
ALTER TABLE admin AUTO_INCREMENT = 1;
ALTER TABLE user AUTO_INCREMENT = 1;
ALTER TABLE category AUTO_INCREMENT = 1;

-- 恢复外键约束
SET FOREIGN_KEY_CHECKS = 1;
