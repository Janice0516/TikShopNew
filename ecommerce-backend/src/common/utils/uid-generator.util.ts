/**
 * UID生成工具
 * 为商家生成唯一的标识符
 */
export class UidGenerator {
  /**
   * 生成商家UID
   * 格式: M + 年月日 + 6位随机数
   * 例如: M20251008001
   */
  static generateMerchantUid(): string {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    
    // 生成6位随机数
    const randomNum = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
    
    return `M${year}${month}${day}${randomNum}`;
  }

  /**
   * 生成用户UID
   * 格式: U + 年月日 + 6位随机数
   */
  static generateUserUid(): string {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    
    const randomNum = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
    
    return `U${year}${month}${day}${randomNum}`;
  }

  /**
   * 生成订单UID
   * 格式: O + 年月日 + 6位随机数
   */
  static generateOrderUid(): string {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    
    const randomNum = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
    
    return `O${year}${month}${day}${randomNum}`;
  }

  /**
   * 验证UID格式
   */
  static validateUid(uid: string, type: 'merchant' | 'user' | 'order'): boolean {
    const prefix = type === 'merchant' ? 'M' : type === 'user' ? 'U' : 'O';
    const pattern = new RegExp(`^${prefix}\\d{8}\\d{6}$`);
    return pattern.test(uid);
  }
}
