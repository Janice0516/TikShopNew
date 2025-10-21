/**
 * 货币工具函数
 * Currency: RM (Malaysian Ringgit)
 */

/**
 * 格式化价格显示
 * @param price 价格数值
 * @param showSymbol 是否显示货币符号
 * @returns 格式化后的价格字符串
 */
export function formatPrice(price: number | string, showSymbol: boolean = true): string {
  const numPrice = typeof price === 'string' ? parseFloat(price) : price
  
  if (isNaN(numPrice)) {
    return showSymbol ? 'RM0.00' : '0.00'
  }
  
  const formatted = numPrice.toFixed(2)
  return showSymbol ? `RM${formatted}` : formatted
}

/**
 * 格式化价格（带千位分隔符）
 * @param price 价格数值
 * @param showSymbol 是否显示货币符号
 * @returns 格式化后的价格字符串
 */
export function formatPriceWithComma(price: number | string, showSymbol: boolean = true): string {
  const numPrice = typeof price === 'string' ? parseFloat(price) : price
  
  if (isNaN(numPrice)) {
    return showSymbol ? 'RM0.00' : '0.00'
  }
  
  const formatted = numPrice.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
  
  return showSymbol ? `RM${formatted}` : formatted
}

/**
 * 解析价格字符串为数值
 * @param priceStr 价格字符串
 * @returns 价格数值
 */
export function parsePrice(priceStr: string): number {
  const cleaned = priceStr.replace(/[$,\s]/g, '')
  const num = parseFloat(cleaned)
  return isNaN(num) ? 0 : num
}

/**
 * 货币符号
 */
export const CURRENCY_SYMBOL = 'RM'

/**
 * 货币代码
 */
export const CURRENCY_CODE = 'RM'

/**
 * 货币名称
 */
export const CURRENCY_NAME = 'Malaysian Ringgit'

