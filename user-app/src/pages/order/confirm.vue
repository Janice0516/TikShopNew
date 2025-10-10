<template>
  <view class="order-confirm">
    <!-- 收货地址 -->
    <view class="address-section">
      <view class="section-header" @click="goToAddress">
        <view class="address-info" v-if="selectedAddress">
          <view class="address-top">
            <text class="recipient-name">{{ selectedAddress.name }}</text>
            <text class="recipient-phone">{{ selectedAddress.phone }}</text>
          </view>
          <text class="address-detail">{{ selectedAddress.fullAddress }}</text>
        </view>
        <view class="no-address" v-else>
          <text class="no-address-text">{{ $t('checkout.selectAddress') }}</text>
        </view>
        <uni-icons type="right" size="16" color="#999"></uni-icons>
      </view>
    </view>

    <!-- 商品列表 -->
    <view class="products-section">
      <view class="section-title">{{ $t('checkout.products') }}</view>
      <view class="product-list">
        <view 
          v-for="item in orderItems" 
          :key="item.id"
          class="product-item"
        >
          <image :src="item.image" class="product-image" />
          <view class="product-info">
            <text class="product-name">{{ item.name }}</text>
            <view class="product-specs" v-if="item.specs && Object.keys(item.specs).length > 0">
              <text 
                v-for="(value, key) in item.specs" 
                :key="key"
                class="spec-text"
              >
                {{ key }}: {{ value }}
              </text>
            </view>
            <view class="product-bottom">
              <text class="product-price">${{ item.price }}</text>
              <text class="product-quantity">x{{ item.quantity }}</text>
            </view>
          </view>
        </view>
      </view>
    </view>

    <!-- 配送方式 -->
    <view class="shipping-section">
      <view class="section-title">{{ $t('checkout.shippingMethod') }}</view>
      <view class="shipping-options">
        <view 
          v-for="option in shippingOptions" 
          :key="option.id"
          class="shipping-option"
          :class="{ active: selectedShipping === option.id }"
          @click="selectShipping(option.id)"
        >
          <view class="option-info">
            <text class="option-name">{{ option.name }}</text>
            <text class="option-desc">{{ option.description }}</text>
          </view>
          <view class="option-price">${{ option.price }}</view>
          <view class="option-radio">
            <uni-icons 
              :type="selectedShipping === option.id ? 'checkbox-filled' : 'checkbox'" 
              :color="selectedShipping === option.id ? '#409EFF' : '#999'"
              size="18"
            ></uni-icons>
          </view>
        </view>
      </view>
    </view>

    <!-- 支付方式 -->
    <view class="payment-section">
      <view class="section-title">{{ $t('checkout.paymentMethod') }}</view>
      <view class="payment-options">
        <view 
          v-for="option in paymentOptions" 
          :key="option.id"
          class="payment-option"
          :class="{ active: selectedPayment === option.id }"
          @click="selectPayment(option.id)"
        >
          <image :src="option.icon" class="payment-icon" />
          <text class="payment-name">{{ option.name }}</text>
          <view class="payment-radio">
            <uni-icons 
              :type="selectedPayment === option.id ? 'checkbox-filled' : 'checkbox'" 
              :color="selectedPayment === option.id ? '#409EFF' : '#999'"
              size="18"
            ></uni-icons>
          </view>
        </view>
      </view>
    </view>

    <!-- 订单备注 -->
    <view class="remark-section">
      <view class="section-title">{{ $t('checkout.remark') }}</view>
      <textarea 
        v-model="orderRemark"
        class="remark-input"
        :placeholder="$t('checkout.remarkPlaceholder')"
        maxlength="200"
      ></textarea>
    </view>

    <!-- 费用明细 -->
    <view class="cost-breakdown">
      <view class="cost-item">
        <text class="cost-label">{{ $t('checkout.subtotal') }}</text>
        <text class="cost-value">${{ subtotal }}</text>
      </view>
      <view class="cost-item">
        <text class="cost-label">{{ $t('checkout.shipping') }}</text>
        <text class="cost-value">${{ shippingFee }}</text>
      </view>
      <view class="cost-item total">
        <text class="cost-label">{{ $t('checkout.total') }}</text>
        <text class="cost-value">${{ totalAmount }}</text>
      </view>
    </view>

    <!-- 底部提交栏 -->
    <view class="submit-bar">
      <view class="total-info">
        <text class="total-label">{{ $t('checkout.total') }}:</text>
        <text class="total-price">${{ totalAmount }}</text>
      </view>
      <view class="submit-btn" @click="submitOrder" :class="{ disabled: !canSubmit }">
        {{ $t('checkout.placeOrder') }}
      </view>
    </view>

    <!-- 地址选择弹窗 -->
    <uni-popup ref="addressPopup" type="bottom">
      <view class="address-modal">
        <view class="modal-header">
          <text class="modal-title">{{ $t('address.title') }}</text>
          <uni-icons type="close" size="20" @click="closeAddressModal"></uni-icons>
        </view>
        
        <view class="address-list">
          <view 
            v-for="address in addressList" 
            :key="address.id"
            class="address-item"
            :class="{ active: selectedAddress?.id === address.id }"
            @click="selectAddress(address)"
          >
            <view class="address-content">
              <view class="address-top">
                <text class="recipient-name">{{ address.name }}</text>
                <text class="recipient-phone">{{ address.phone }}</text>
                <text class="default-tag" v-if="address.isDefault">{{ $t('address.default') }}</text>
              </view>
              <text class="address-detail">{{ address.fullAddress }}</text>
            </view>
            <view class="address-actions">
              <view class="action-btn" @click.stop="editAddress(address)">
                <uni-icons type="compose" size="16" color="#409EFF"></uni-icons>
              </view>
              <view class="action-btn" @click.stop="deleteAddress(address)">
                <uni-icons type="trash" size="16" color="#f56c6c"></uni-icons>
              </view>
            </view>
          </view>
        </view>

        <view class="modal-footer">
          <view class="add-address-btn" @click="addNewAddress">
            <uni-icons type="plus" size="16" color="#409EFF"></uni-icons>
            <text>{{ $t('address.add') }}</text>
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { onLoad } from '@dcloudio/uni-app'

const { t } = useI18n()

const orderItems = ref<any[]>([])
const selectedAddress = ref<any>(null)
const addressList = ref<any[]>([])
const selectedShipping = ref(1)
const selectedPayment = ref(1)
const orderRemark = ref('')

// 配送方式
const shippingOptions = ref([
  {
    id: 1,
    name: 'Standard Shipping',
    description: '5-7 business days',
    price: '9.99'
  },
  {
    id: 2,
    name: 'Express Shipping',
    description: '2-3 business days',
    price: '19.99'
  },
  {
    id: 3,
    name: 'Next Day Delivery',
    description: 'Next business day',
    price: '29.99'
  }
])

// 支付方式
const paymentOptions = ref([
  {
    id: 1,
    name: 'Credit Card',
    icon: '/static/payment/credit-card.png'
  },
  {
    id: 2,
    name: 'PayPal',
    icon: '/static/payment/paypal.png'
  },
  {
    id: 3,
    name: 'Apple Pay',
    icon: '/static/payment/apple-pay.png'
  },
  {
    id: 4,
    name: 'Google Pay',
    icon: '/static/payment/google-pay.png'
  }
])

// 模拟地址数据
const mockAddresses = [
  {
    id: 1,
    name: 'John Smith',
    phone: '+1 234-567-8900',
    fullAddress: '123 Main Street, New York, NY 10001, United States',
    isDefault: true
  },
  {
    id: 2,
    name: 'Jane Doe',
    phone: '+1 234-567-8901',
    fullAddress: '456 Oak Avenue, Los Angeles, CA 90210, United States',
    isDefault: false
  }
]

// 计算属性
const subtotal = computed(() => {
  return orderItems.value
    .reduce((total, item) => total + (item.price * item.quantity), 0)
    .toFixed(2)
})

const shippingFee = computed(() => {
  const option = shippingOptions.value.find(opt => opt.id === selectedShipping.value)
  return option ? option.price : '0.00'
})

const totalAmount = computed(() => {
  const subtotalNum = parseFloat(subtotal.value)
  const shippingNum = parseFloat(shippingFee.value)
  return (subtotalNum + shippingNum).toFixed(2)
})

const canSubmit = computed(() => {
  return selectedAddress.value && orderItems.value.length > 0
})

// 页面加载
onLoad((options: any) => {
  if (options.items) {
    try {
      orderItems.value = JSON.parse(decodeURIComponent(options.items))
    } catch (error) {
      console.error('Failed to parse order items:', error)
    }
  }
  loadAddresses()
})

// 加载地址列表
const loadAddresses = async () => {
  try {
    // 模拟数据
    addressList.value = mockAddresses
    selectedAddress.value = mockAddresses.find(addr => addr.isDefault) || mockAddresses[0]

    // 实际API调用
    // const res = await getAddressList()
    // addressList.value = res.list
    // selectedAddress.value = res.defaultAddress
    
  } catch (error) {
    console.error('Failed to load addresses:', error)
  }
}

// 选择配送方式
const selectShipping = (id: number) => {
  selectedShipping.value = id
}

// 选择支付方式
const selectPayment = (id: number) => {
  selectedPayment.value = id
}

// 跳转到地址管理
const goToAddress = () => {
  uni.$refs.addressPopup?.open()
}

// 关闭地址弹窗
const closeAddressModal = () => {
  uni.$refs.addressPopup?.close()
}

// 选择地址
const selectAddress = (address: any) => {
  selectedAddress.value = address
  closeAddressModal()
}

// 编辑地址
const editAddress = (address: any) => {
  uni.navigateTo({
    url: `/pages/address/edit?id=${address.id}`
  })
}

// 删除地址
const deleteAddress = (address: any) => {
  uni.showModal({
    title: t('common.warning'),
    content: t('message.confirmDelete'),
    success: (res) => {
      if (res.confirm) {
        addressList.value = addressList.value.filter(addr => addr.id !== address.id)
        if (selectedAddress.value?.id === address.id) {
          selectedAddress.value = addressList.value[0] || null
        }
        
        uni.showToast({
          title: t('message.deleteSuccess'),
          icon: 'success'
        })
      }
    }
  })
}

// 添加新地址
const addNewAddress = () => {
  uni.navigateTo({
    url: '/pages/address/add'
  })
}

// 提交订单
const submitOrder = async () => {
  if (!canSubmit.value) {
    uni.showToast({
      title: t('checkout.selectAddressFirst'),
      icon: 'none'
    })
    return
  }

  try {
    const orderData = {
      items: orderItems.value,
      address: selectedAddress.value,
      shipping: shippingOptions.value.find(opt => opt.id === selectedShipping.value),
      payment: paymentOptions.value.find(opt => opt.id === selectedPayment.value),
      remark: orderRemark.value,
      subtotal: subtotal.value,
      shippingFee: shippingFee.value,
      totalAmount: totalAmount.value
    }

    // 实际API调用
    // const res = await createOrder(orderData)
    
    uni.showToast({
      title: t('message.orderSuccess'),
      icon: 'success'
    })

    // 跳转到订单详情
    setTimeout(() => {
      uni.redirectTo({
        url: `/pages/order/detail?id=ORD${Date.now()}`
      })
    }, 1500)

  } catch (error) {
    console.error('Failed to submit order:', error)
    uni.showToast({
      title: t('message.operationFailed'),
      icon: 'error'
    })
  }
}

onMounted(() => {
  // 初始化默认选择
  if (shippingOptions.value.length > 0) {
    selectedShipping.value = shippingOptions.value[0].id
  }
  if (paymentOptions.value.length > 0) {
    selectedPayment.value = paymentOptions.value[0].id
  }
})
</script>

<style scoped>
.order-confirm {
  background-color: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 100px;
}

/* 地址部分 */
.address-section {
  background-color: #fff;
  margin-bottom: 10px;
}

.section-header {
  display: flex;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #f0f0f0;
}

.address-info {
  flex: 1;
}

.address-top {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.recipient-name {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-right: 15px;
}

.recipient-phone {
  font-size: 14px;
  color: #666;
}

.address-detail {
  font-size: 14px;
  color: #666;
  line-height: 1.4;
}

.no-address {
  flex: 1;
}

.no-address-text {
  font-size: 16px;
  color: #999;
}

/* 商品列表 */
.products-section {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 20px;
}

.section-title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 15px;
}

.product-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.product-item {
  display: flex;
  align-items: center;
  gap: 15px;
}

.product-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
}

.product-info {
  flex: 1;
}

.product-name {
  font-size: 16px;
  color: #333;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-specs {
  margin-bottom: 8px;
}

.spec-text {
  font-size: 12px;
  color: #666;
  background-color: #f5f5f5;
  padding: 2px 6px;
  border-radius: 4px;
  margin-right: 6px;
}

.product-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.product-price {
  font-size: 16px;
  font-weight: bold;
  color: #f56c6c;
}

.product-quantity {
  font-size: 14px;
  color: #666;
}

/* 配送方式 */
.shipping-section {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 20px;
}

.shipping-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.shipping-option {
  display: flex;
  align-items: center;
  padding: 15px;
  border: 1px solid #eee;
  border-radius: 8px;
  background-color: #fafafa;
}

.shipping-option.active {
  border-color: #409EFF;
  background-color: #e6f7ff;
}

.option-info {
  flex: 1;
}

.option-name {
  font-size: 16px;
  color: #333;
  margin-bottom: 4px;
}

.option-desc {
  font-size: 12px;
  color: #666;
}

.option-price {
  font-size: 16px;
  font-weight: bold;
  color: #f56c6c;
  margin-right: 15px;
}

/* 支付方式 */
.payment-section {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 20px;
}

.payment-options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.payment-option {
  display: flex;
  align-items: center;
  padding: 15px;
  border: 1px solid #eee;
  border-radius: 8px;
  background-color: #fafafa;
}

.payment-option.active {
  border-color: #409EFF;
  background-color: #e6f7ff;
}

.payment-icon {
  width: 24px;
  height: 24px;
  margin-right: 10px;
}

.payment-name {
  flex: 1;
  font-size: 14px;
  color: #333;
}

/* 订单备注 */
.remark-section {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 20px;
}

.remark-input {
  width: 100%;
  height: 80px;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 10px;
  font-size: 14px;
  color: #333;
  background-color: #fafafa;
}

/* 费用明细 */
.cost-breakdown {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 20px;
}

.cost-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.cost-item.total {
  border-top: 1px solid #eee;
  padding-top: 10px;
  font-weight: bold;
}

.cost-label {
  font-size: 14px;
  color: #666;
}

.cost-value {
  font-size: 14px;
  color: #333;
}

.cost-item.total .cost-value {
  font-size: 18px;
  color: #f56c6c;
}

/* 底部提交栏 */
.submit-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  border-top: 1px solid #eee;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.total-label {
  font-size: 16px;
  color: #333;
}

.total-price {
  font-size: 20px;
  font-weight: bold;
  color: #f56c6c;
}

.submit-btn {
  background-color: #409EFF;
  color: #fff;
  padding: 12px 24px;
  border-radius: 6px;
  font-size: 16px;
  font-weight: bold;
}

.submit-btn.disabled {
  background-color: #ccc;
}

/* 地址选择弹窗 */
.address-modal {
  background-color: #fff;
  border-radius: 12px 12px 0 0;
  max-height: 80vh;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-title {
  font-size: 18px;
  font-weight: bold;
  color: #333;
}

.address-list {
  max-height: 400px;
  overflow-y: auto;
}

.address-item {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #f0f0f0;
}

.address-item.active {
  background-color: #e6f7ff;
}

.address-content {
  flex: 1;
}

.default-tag {
  background-color: #409EFF;
  color: #fff;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 4px;
  margin-left: 10px;
}

.address-actions {
  display: flex;
  gap: 10px;
}

.action-btn {
  padding: 8px;
}

.modal-footer {
  padding: 20px;
  border-top: 1px solid #eee;
}

.add-address-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  border: 1px dashed #409EFF;
  border-radius: 8px;
  color: #409EFF;
  font-size: 16px;
}
</style>
