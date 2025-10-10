import { createApp } from 'vue'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import { createPinia } from 'pinia'
import router from './router'
import i18n from './i18n'
import App from './App.vue'
import './styles/index.css'

const app = createApp(App)

// 注册所有Element Plus图标
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component)
}

// 配置Element Plus国际化
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import en from 'element-plus/es/locale/lang/en'
// Element Plus没有马来文，使用英文作为后备
const elementLocales: Record<string, any> = {
  zh: zhCn,
  en: en,
  ms: en // 马来文使用英文作为Element Plus的后备语言
}

app.use(ElementPlus, {
  locale: elementLocales[i18n.global.locale.value]
})

app.use(createPinia())
app.use(i18n)
app.use(router)

app.mount('#app')

// 监听语言切换，更新Element Plus语言
import { watch } from 'vue'
watch(() => i18n.global.locale.value, (newLocale) => {
  // 动态更新Element Plus语言
  ElementPlus.locale(elementLocales[newLocale])
})
