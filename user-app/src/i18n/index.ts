import { createI18n } from 'vue-i18n'
import zhCN from './locales/zh-CN.json'
import enUS from './locales/en-US.json'
import msMY from './locales/ms-MY.json'

// 获取浏览器语言设置
const getDefaultLocale = () => {
  const savedLocale = localStorage.getItem('locale')
  if (savedLocale) {
    return savedLocale
  }
  
  const browserLocale = navigator.language || navigator.languages[0]
  if (browserLocale.startsWith('zh')) {
    return 'zh-CN'
  } else if (browserLocale.startsWith('ms')) {
    return 'ms-MY'
  } else {
    return 'en-US'
  }
}

const i18n = createI18n({
  legacy: false, // 使用 Composition API 模式
  locale: getDefaultLocale(),
  fallbackLocale: 'en-US',
  messages: {
    'zh-CN': zhCN,
    'en-US': enUS,
    'ms-MY': msMY
  }
})

export default i18n
