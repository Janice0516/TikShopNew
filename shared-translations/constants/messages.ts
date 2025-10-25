export const ERROR_MESSAGES = {
  // 通用错误
  INTERNAL_SERVER_ERROR: 'Internal server error',
  UNAUTHORIZED: 'Unauthorized',
  FORBIDDEN: 'Forbidden',
  NOT_FOUND: 'Not found',
  BAD_REQUEST: 'Bad request',
  
  // 用户相关
  USER_NOT_FOUND: 'User not found',
  USER_ALREADY_EXISTS: 'User already exists',
  INVALID_CREDENTIALS: 'Invalid credentials',
  
  // 商品相关
  PRODUCT_NOT_FOUND: 'Product not found',
  PRODUCT_OUT_OF_STOCK: 'Product out of stock',
  INVALID_PRODUCT_DATA: 'Invalid product data',
  
  // 订单相关
  ORDER_NOT_FOUND: 'Order not found',
  ORDER_CANNOT_BE_CANCELLED: 'Order cannot be cancelled',
  INVALID_ORDER_DATA: 'Invalid order data',
  
  // 购物车相关
  CART_NOT_FOUND: 'Cart not found',
  CART_ITEM_NOT_FOUND: 'Cart item not found',
  INVALID_CART_DATA: 'Invalid cart data',
  
  // 商家相关
  MERCHANT_NOT_FOUND: 'Merchant not found',
  MERCHANT_ALREADY_EXISTS: 'Merchant already exists',
  INVALID_MERCHANT_DATA: 'Invalid merchant data',
  
  // 支付相关
  PAYMENT_FAILED: 'Payment failed',
  INVALID_PAYMENT_DATA: 'Invalid payment data',
  
  // 库存相关
  INSUFFICIENT_STOCK: 'Insufficient stock',
  STOCK_UPDATE_FAILED: 'Stock update failed'
  STOCK_INSUFFICIENT: "库存不足",
};

export const SUCCESS_MESSAGES = {
  // 通用成功
  OPERATION_SUCCESS: 'Operation successful',
  DATA_RETRIEVED: 'Data retrieved successfully',
  DATA_UPDATED: 'Data updated successfully',
  DATA_DELETED: 'Data deleted successfully',
  
  // 用户相关
  USER_CREATED: 'User created successfully',
  USER_UPDATED: 'User updated successfully',
  USER_DELETED: 'User deleted successfully',
  LOGIN_SUCCESS: 'Login successful',
  LOGOUT_SUCCESS: 'Logout successful',
  
  // 商品相关
  PRODUCT_CREATED: 'Product created successfully',
  PRODUCT_UPDATED: 'Product updated successfully',
  PRODUCT_DELETED: 'Product deleted successfully',
  
  // 订单相关
  ORDER_CREATED: 'Order created successfully',
  ORDER_UPDATED: 'Order updated successfully',
  ORDER_CANCELLED: 'Order cancelled successfully',
  
  // 购物车相关
  CART_ITEM_ADDED: 'Item added to cart successfully',
  CART_ITEM_UPDATED: 'Cart item updated successfully',
  CART_ITEM_REMOVED: 'Cart item removed successfully',
  CART_CLEARED: 'Cart cleared successfully',
  
  // 商家相关
  MERCHANT_CREATED: 'Merchant created successfully',
  MERCHANT_UPDATED: 'Merchant updated successfully',
  MERCHANT_DELETED: 'Merchant deleted successfully',
  
  // 支付相关
  PAYMENT_SUCCESS: 'Payment successful',
  
  // 库存相关
  STOCK_UPDATED: 'Stock updated successfully'
};
