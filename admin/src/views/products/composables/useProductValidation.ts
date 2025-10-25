import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { FormRules } from 'element-plus'

export function useProductValidation() {
  const { t } = useI18n()

  const rules = computed<FormRules>(() => ({
    name: [{ required: true, message: t('products.enterProductName'), trigger: 'blur' }],
    categoryId: [{ required: true, message: t('products.selectCategory'), trigger: 'change' }],
    costPrice: [{ required: true, message: t('products.enterCostPrice'), trigger: 'blur' }],
    suggestPrice: [{ required: true, message: t('products.enterSuggestedPrice'), trigger: 'blur' }],
    stock: [{ required: true, message: t('products.enterStock'), trigger: 'blur' }]
  }))

  const validateForm = async (formRef: any): Promise<boolean> => {
    if (!formRef) return false
    
    try {
      await formRef.validate()
      return true
    } catch (error) {
      return false
    }
  }

  return {
    rules,
    validateForm
  }
}
