import { createI18n } from 'vue-i18n'
import en from './en.json'
import zh from './zh.json'
import ms from './ms.json'

// 从本地存储获取语言设置，默认英文
const getDefaultLocale = () => {
  try {
    return uni.getStorageSync('user-locale') || 'en'
  } catch {
    return 'en'
  }
}

const i18n = createI18n({
  legacy: false,
  locale: getDefaultLocale(),
  fallbackLocale: 'en',
  messages: {
    en,
    zh,
    ms
  }
})

export default i18n

// 语言列表
export const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'zh', name: '中文', flag: '🇨🇳' },
  { code: 'ms', name: 'Bahasa Melayu', flag: '🇲🇾' }
]

// 切换语言
export function setLanguage(locale: string) {
  i18n.global.locale.value = locale as any
  try {
    uni.setStorageSync('user-locale', locale)
  } catch (e) {
    console.error('Failed to save language preference', e)
  }
}

