/**
 * 格式化工具函数
 */

/**
 * 格式化时间
 * @param date 日期字符串或Date对象
 * @param format 格式化模式，默认为 'YYYY-MM-DD HH:mm:ss'
 * @returns 格式化后的时间字符串
 */
export function formatTime(date: string | Date, format: string = 'YYYY-MM-DD HH:mm:ss'): string {
  if (!date) return '';
  
  const d = new Date(date);
  if (isNaN(d.getTime())) return '';
  
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const hours = String(d.getHours()).padStart(2, '0');
  const minutes = String(d.getMinutes()).padStart(2, '0');
  const seconds = String(d.getSeconds()).padStart(2, '0');
  
  return format
    .replace('YYYY', String(year))
    .replace('MM', month)
    .replace('DD', day)
    .replace('HH', hours)
    .replace('mm', minutes)
    .replace('ss', seconds);
}

/**
 * 格式化日期（只显示日期部分）
 * @param date 日期字符串或Date对象
 * @returns 格式化后的日期字符串
 */
export function formatDate(date: string | Date): string {
  return formatTime(date, 'YYYY-MM-DD');
}

/**
 * 格式化金额
 * @param amount 金额
 * @param currency 货币符号，默认为 'RM'
 * @returns 格式化后的金额字符串
 */
export function formatAmount(amount: number | string, currency: string = 'RM'): string {
  if (amount === null || amount === undefined || amount === '') return '';
  
  const num = typeof amount === 'string' ? parseFloat(amount) : amount;
  if (isNaN(num)) return '';
  
  return `${currency} ${num.toFixed(2)}`;
}

/**
 * 格式化数字（添加千分位分隔符）
 * @param num 数字
 * @returns 格式化后的数字字符串
 */
export function formatNumber(num: number | string): string {
  if (num === null || num === undefined || num === '') return '';
  
  const n = typeof num === 'string' ? parseFloat(num) : num;
  if (isNaN(n)) return '';
  
  return n.toLocaleString();
}

/**
 * 格式化文件大小
 * @param bytes 字节数
 * @returns 格式化后的文件大小字符串
 */
export function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * 格式化手机号码
 * @param phone 手机号码
 * @returns 格式化后的手机号码
 */
export function formatPhone(phone: string): string {
  if (!phone) return '';
  
  // 移除所有非数字字符
  const cleaned = phone.replace(/\D/g, '');
  
  // 马来西亚手机号码格式
  if (cleaned.length === 10 && cleaned.startsWith('01')) {
    return cleaned.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
  }
  
  return phone;
}

/**
 * 截断文本
 * @param text 文本
 * @param length 最大长度
 * @param suffix 后缀，默认为 '...'
 * @returns 截断后的文本
 */
export function truncateText(text: string, length: number, suffix: string = '...'): string {
  if (!text || text.length <= length) return text;
  return text.substring(0, length) + suffix;
}
