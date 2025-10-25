import { useI18n } from 'vue-i18n'

export function useProductTranslations() {
  const { t } = useI18n()
  
  const getTranslatedProduct = (product: any) => {
    if (!product) return {}
    
    return {
      ...product,
      // 直接使用原始名称，不依赖翻译
      name: product.name || t('common.noName'),
      description: product.description || t('common.noDescription'),
      // 确保价格字段正确
      price: product.costPrice || product.price || 0,
      salePrice: product.suggestPrice || product.salePrice || product.price || 0,
      originalPrice: product.suggestPrice || product.originalPrice || product.price || 0,
      // 确保图片字段正确
      mainImage: product.mainImage || product.image || '',
      images: product.images || []
    }
  }
  
  return {
    getTranslatedProduct
  }
}
