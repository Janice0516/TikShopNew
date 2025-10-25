/**
 * 统一翻译配置
 * 为各模块提供统一的翻译配置
 */

// 导出共享翻译库
export * from '../shared-translations';

// 为各模块提供便捷的翻译函数
import { t, getSupportedLocales, getDefaultLocale, getBrowserLocale } from '../shared-translations';

// 默认语言设置
export const DEFAULT_LOCALE = 'zh-CN';

// 支持的语言列表
export const SUPPORTED_LOCALES = ['zh-CN', 'en-US', 'ms-MY'];

// 语言配置
export const LOCALE_CONFIG = {
  'zh-CN': {
    name: '简体中文',
    flag: '🇨🇳',
    direction: 'ltr'
  },
  'en-US': {
    name: 'English',
    flag: '🇺🇸',
    direction: 'ltr'
  },
  'ms-MY': {
    name: 'Bahasa Melayu',
    flag: '🇲🇾',
    direction: 'ltr'
  }
};

// 便捷的翻译函数
export const translate = t;

// 获取当前语言的翻译
export function getCurrentTranslation(locale: string = DEFAULT_LOCALE) {
  return t('', locale);
}

// 批量翻译函数
export function translateBatch(keys: string[], locale: string = DEFAULT_LOCALE) {
  return keys.reduce((acc, key) => {
    acc[key] = t(key, locale);
    return acc;
  }, {} as Record<string, string>);
}

// 翻译状态
export function translateStatus(status: number | string, locale: string = DEFAULT_LOCALE) {
  const statusMap: Record<string, string> = {
    '1': 'common.status.active',
    '0': 'common.status.inactive',
    '2': 'common.status.pending',
    '3': 'common.status.shipped',
    '4': 'common.status.completed',
    '5': 'common.status.cancelled'
  };
  
  const key = statusMap[String(status)] || 'common.status.pending';
  return t(key, locale);
}

// 翻译错误消息
export function translateError(errorKey: string, locale: string = DEFAULT_LOCALE) {
  return t(`errors.${errorKey}`, locale);
}

// 翻译成功消息
export function translateSuccess(successKey: string, locale: string = DEFAULT_LOCALE) {
  return t(`messages.success.${successKey}`, locale);
}


