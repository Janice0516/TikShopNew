import { defineStore } from 'pinia'
import { ref } from 'vue'
import { cartApi } from '@/api'

export const useCartStore = defineStore('cart', () => {
  // 状态
  const cartItems = ref<any[]>([])
  const cartTotal = ref(0)
  const cartCount = ref(0)

  // 计算总价和数量
  const calculateTotal = () => {
    cartTotal.value = cartItems.value.reduce((total, item) => {
      return total + (item.price * item.quantity)
    }, 0)
    cartCount.value = cartItems.value.reduce((count, item) => {
      return count + item.quantity
    }, 0)
  }

  // 获取购物车
  const fetchCart = async () => {
    try {
      const response = await cartApi.getCart()
      cartItems.value = response.data?.items || []
      calculateTotal()
      return response
    } catch (error) {
      throw error
    }
  }

  // 添加商品到购物车
  const addToCart = async (productId: string, quantity: number = 1) => {
    try {
      await cartApi.addToCart({ productId, quantity })
      await fetchCart()
    } catch (error) {
      throw error
    }
  }

  // 更新商品数量
  const updateQuantity = async (itemId: string, quantity: number) => {
    try {
      await cartApi.updateCartItem(itemId, quantity)
      await fetchCart()
    } catch (error) {
      throw error
    }
  }

  // 删除商品
  const removeItem = async (itemId: string) => {
    try {
      await cartApi.removeCartItem(itemId)
      await fetchCart()
    } catch (error) {
      throw error
    }
  }

  // 清空购物车
  const clearCart = async () => {
    try {
      await cartApi.clearCart()
      cartItems.value = []
      cartTotal.value = 0
      cartCount.value = 0
    } catch (error) {
      throw error
    }
  }

  return {
    cartItems,
    cartTotal,
    cartCount,
    fetchCart,
    addToCart,
    updateQuantity,
    removeItem,
    clearCart
  }
})
