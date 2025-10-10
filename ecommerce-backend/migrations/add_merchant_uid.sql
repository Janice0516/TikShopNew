-- 为商家表添加merchant_uid字段
-- 执行前请备份数据库

-- 添加merchant_uid字段
ALTER TABLE merchant ADD COLUMN merchant_uid VARCHAR(20) UNIQUE COMMENT '商家唯一标识符';

-- 为现有商家生成UID
UPDATE merchant SET merchant_uid = CONCAT('M', DATE_FORMAT(create_time, '%Y%m%d'), LPAD(id, 6, '0')) WHERE merchant_uid IS NULL;

-- 确保字段不为空
ALTER TABLE merchant MODIFY COLUMN merchant_uid VARCHAR(20) NOT NULL UNIQUE COMMENT '商家唯一标识符';

-- 添加索引以提高查询性能
CREATE INDEX idx_merchant_uid ON merchant(merchant_uid);
