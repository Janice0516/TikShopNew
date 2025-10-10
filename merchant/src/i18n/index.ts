import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import zh from './locales/zh.json'
import ms from './locales/ms.json'

// 从localStorage获取保存的语言设置，默认英文
const savedLocale = localStorage.getItem('merchant-locale') || 'en'

const i18n = createI18n({
  legacy: false, // 使用 Composition API 模式
  locale: savedLocale, // 默认语言
  fallbackLocale: 'en', // 回退语言
  messages: {
    en,
    zh,
    ms
  },
  globalInjection: true // 全局注入 $t 函数
})

export default i18n

// 导出语言列表
export const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'zh', name: '中文', flag: '🇨🇳' },
  { code: 'ms', name: 'Bahasa Melayu', flag: '🇲🇾' }
]

// 切换语言函数
export function setLanguage(locale: string) {
  i18n.global.locale.value = locale as any
  localStorage.setItem('merchant-locale', locale)
  // 更新HTML lang属性
  document.documentElement.lang = locale
}

