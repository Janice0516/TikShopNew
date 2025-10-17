
-- 创建merchant_product表
CREATE TABLE IF NOT EXISTS merchant_product (
  id BIGSERIAL PRIMARY KEY,
  merchant_id BIGINT NOT NULL,
  product_id BIGINT NOT NULL,
  sale_price DECIMAL(10,2) NOT NULL,
  profit_margin DECIMAL(5,2) DEFAULT 0,
  status SMALLINT DEFAULT 1,
  sales INTEGER DEFAULT 0,
  create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(merchant_id, product_id)
);

-- 插入商家产品数据
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 8, 347.05, 19.32, 1, 44) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 7, 8185.56, -3.84, 1, 91) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 6, 1272.53, 5.7, 1, 85) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 5, 391.59, 10.62, 1, 75) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 4, 4703.55, 14.96, 1, 37) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 3, 965.77, 17.16, 1, 11) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 2, 7641.63, 1.85, 1, 14) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (1, 1, 5086.74, 11.53, 1, 58) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 8, 291.11, 3.82, 1, 62) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 7, 8131.75, -4.53, 1, 1) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 6, 1289.23, 6.92, 1, 89) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 5, 393.68, 11.1, 1, 40) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 4, 3962.22, -0.95, 1, 9) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 3, 879.22, 9.01, 1, 88) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 2, 7748.92, 3.21, 1, 17) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (2, 1, 4967.52, 9.41, 1, 93) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 8, 328.18, 14.68, 1, 2) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 7, 9754.49, 12.86, 1, 22) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 6, 1177.09, -1.95, 1, 12) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 5, 406.51, 13.9, 1, 16) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 4, 4085.59, 2.09, 1, 47) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 3, 887.01, 9.81, 1, 78) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 2, 8594.84, 12.74, 1, 61) ON CONFLICT (merchant_id, product_id) DO NOTHING;
INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (3, 1, 5402.24, 16.7, 1, 83) ON CONFLICT (merchant_id, product_id) DO NOTHING;
