/**
 * 统一翻译管理系统
 * 提供前端和后端共享的翻译功能
 */

// 导出翻译工具
export * from './utils/translations';

// 导出常量
export * from './constants/messages';

// 导出类型
export type { Locale, TranslationMessages } from './utils/translations';

// 默认导出翻译工具
export { t, getSupportedLocales, isLocaleSupported, getDefaultLocale, getBrowserLocale } from './utils/translations';


