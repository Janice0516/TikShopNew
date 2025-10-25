import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

export interface TranslatedProduct {
  name: string
  description: string
}

export function useProductTranslations() {
  const { locale } = useI18n()

  const getTranslatedProduct = (product: any): TranslatedProduct => {
    if (!product) {
      return { name: '', description: '' }
    }

    // 获取当前语言
    const currentLocale = locale.value || 'zh-CN'
    
    // 尝试从翻译文件中获取翻译
    try {
      // 这里可以根据产品ID或名称从翻译文件中获取翻译
      // 暂时返回原始数据
      return {
        name: product.name || '',
        description: product.description || ''
      }
    } catch (error) {
      console.warn('Failed to get product translation:', error)
      return {
        name: product.name || '',
        description: product.description || ''
      }
    }
  }

  return {
    getTranslatedProduct
  }
}
