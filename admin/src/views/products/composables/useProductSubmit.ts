import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { createProduct, updateProduct } from '@/api/product'
import type { ProductForm } from '../types/product'

export function useProductSubmit() {
  const router = useRouter()
  const { t } = useI18n()
  
  const loading = ref(false)

  // 统一清洗函数：去除反引号、中文引号与省略号，并移除多余空格
  const sanitizeString = (s: string): string => {
    if (!s) return ''
    return s
      .replace(/[`\u201C\u201D\u2018\u2019\u2026]/g, '')
      .replace(/\s+/g, '')
      .trim()
  }

  const submitProduct = async (form: ProductForm, isEdit: boolean, productId?: number) => {
    loading.value = true
    
    try {
      // 处理 images：若是 JSON 字符串则逐项清洗
      let cleanedImages = ''
      if (typeof form.images === 'string' && form.images.length > 0) {
        try {
          const imgs = JSON.parse(form.images)
          if (Array.isArray(imgs)) {
            cleanedImages = JSON.stringify(imgs.map(u => sanitizeString(String(u))))
          } else {
            cleanedImages = sanitizeString(form.images)
          }
        } catch {
          cleanedImages = sanitizeString(form.images)
        }
      }
      
      // 仅构造后端允许的字段，避免触发 forbidNonWhitelisted
      const baseData: any = {
        name: form.name,
        categoryId: Number(form.categoryId),
        brand: form.brand || '',
        mainImage: sanitizeString(String(form.mainImage)),
        images: cleanedImages || '',
        video: sanitizeString(String(form.video)),
        costPrice: Number(form.costPrice) || 0,
        suggestPrice: Number(form.suggestPrice) || 0,
        stock: Number(form.stock) || 0,
        description: form.description || ''
      }

      console.log('提交数据:', baseData)

      if (isEdit && productId) {
        // 更新接口允许 status，可按需附加
        const updateData = { ...baseData }
        if (form.status !== undefined && form.status !== null && form.status !== 0) {
          updateData.status = Number(form.status)
        }
        await updateProduct(productId, updateData)
        ElMessage.success(t('products.updateSuccess'))
      } else {
        await createProduct(baseData)
        ElMessage.success(t('products.createSuccess'))
      }
      
      router.push('/products')
    } catch (error: any) {
      console.error('提交产品失败:', error)
      const msg = error?.response?.data?.message || t('products.operationFailed')
      ElMessage.error(String(msg))
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    submitProduct
  }
}
