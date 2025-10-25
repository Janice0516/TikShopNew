/**
 * 统一翻译工具函数
 * 用于前端和后端的翻译处理
 */

import zhCN from '../locales/zh-CN.json';
import enUS from '../locales/en-US.json';
import msMY from '../locales/ms-MY.json';

export type Locale = 'zh-CN' | 'en-US' | 'ms-MY';

export interface TranslationMessages {
  [key: string]: any;
}

export const translations: Record<Locale, TranslationMessages> = {
  'zh-CN': zhCN,
  'en-US': enUS,
  'ms-MY': msMY
};

/**
 * 获取翻译文本
 * @param key 翻译键，支持嵌套路径，如 'common.status.active'
 * @param locale 语言代码
 * @param params 参数对象，用于替换占位符
 * @returns 翻译后的文本
 */
export function t(key: string, locale: Locale = 'zh-CN', params?: Record<string, any>): string {
  const messages = translations[locale];
  if (!messages) {
    console.warn(`Translation for locale ${locale} not found`);
    return key;
  }

  // 支持嵌套路径，如 'common.status.active'
  const keys = key.split('.');
  let value: any = messages;
  
  for (const k of keys) {
    if (value && typeof value === 'object' && k in value) {
      value = value[k];
    } else {
      console.warn(`Translation key ${key} not found in locale ${locale}`);
      return key;
    }
  }

  if (typeof value !== 'string') {
    console.warn(`Translation value for key ${key} is not a string`);
    return key;
  }

  // 替换参数占位符，如 {min}, {max}
  if (params) {
    return value.replace(/\{(\w+)\}/g, (match, paramKey) => {
      return params[paramKey] !== undefined ? String(params[paramKey]) : match;
    });
  }

  return value;
}

/**
 * 获取所有支持的语言
 */
export function getSupportedLocales(): Locale[] {
  return Object.keys(translations) as Locale[];
}

/**
 * 检查语言是否支持
 */
export function isLocaleSupported(locale: string): locale is Locale {
  return locale in translations;
}

/**
 * 获取默认语言
 */
export function getDefaultLocale(): Locale {
  return 'zh-CN';
}

/**
 * 从浏览器语言设置获取最佳匹配语言
 */
export function getBrowserLocale(): Locale {
  if (typeof window === 'undefined') {
    return getDefaultLocale();
  }

  const browserLang = navigator.language || navigator.languages?.[0] || 'zh-CN';
  
  // 精确匹配
  if (isLocaleSupported(browserLang)) {
    return browserLang;
  }

  // 语言代码匹配（如 zh-CN 匹配 zh）
  const langCode = browserLang.split('-')[0];
  for (const locale of getSupportedLocales()) {
    if (locale.startsWith(langCode)) {
      return locale;
    }
  }

  return getDefaultLocale();
}

/**
 * 批量获取翻译
 * @param keys 翻译键数组
 * @param locale 语言代码
 * @returns 翻译对象
 */
export function tBatch(keys: string[], locale: Locale = 'zh-CN'): Record<string, string> {
  const result: Record<string, string> = {};
  for (const key of keys) {
    result[key] = t(key, locale);
  }
  return result;
}

/**
 * 获取嵌套翻译对象
 * @param prefix 前缀，如 'common.status'
 * @param locale 语言代码
 * @returns 翻译对象
 */
export function tNested(prefix: string, locale: Locale = 'zh-CN'): Record<string, string> {
  const messages = translations[locale];
  if (!messages) {
    return {};
  }

  const keys = prefix.split('.');
  let value: any = messages;
  
  for (const k of keys) {
    if (value && typeof value === 'object' && k in value) {
      value = value[k];
    } else {
      return {};
    }
  }

  if (typeof value !== 'object') {
    return {};
  }

  // 递归获取所有字符串值
  const result: Record<string, string> = {};
  function extractStrings(obj: any, currentKey = '') {
    for (const [key, val] of Object.entries(obj)) {
      const fullKey = currentKey ? `${currentKey}.${key}` : key;
      if (typeof val === 'string') {
        result[fullKey] = val;
      } else if (typeof val === 'object' && val !== null) {
        extractStrings(val, fullKey);
      }
    }
  }

  extractStrings(value);
  return result;
}


