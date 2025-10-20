import { createI18n } from 'vue-i18n'
import zhCN from './locales/zh-CN.json'
import enUS from './locales/en-US.json'

// 获取浏览器语言设置
const getDefaultLocale = () => {
  const savedLocale = localStorage.getItem('admin-locale')
  if (savedLocale) {
    return savedLocale
  }
  
  const browserLocale = navigator.language
  if (browserLocale.startsWith('zh')) {
    return 'zh-CN'
  } else if (browserLocale.startsWith('en')) {
    return 'en-US'
  }
  
  return 'zh-CN' // 默认中文
}

const i18n = createI18n({
  legacy: false,
  locale: getDefaultLocale(),
  fallbackLocale: 'zh-CN',
  messages: {
    'zh-CN': zhCN,
    'en-US': enUS
  }
})

// 切换语言
export const setLocale = (locale: 'zh-CN' | 'en-US') => {
  i18n.global.locale.value = locale
  localStorage.setItem('admin-locale', locale)
}

export default i18n
