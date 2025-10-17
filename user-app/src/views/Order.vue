<template>
  <div class="order-page">
    <div class="container">
      <div class="order-header">
        <h1>确认订单</h1>
      </div>
      
      <div class="order-content" v-if="orderItems.length > 0">
        <!-- 收货地址 -->
        <div class="address-section">
          <div class="section-header">
            <h2>收货地址</h2>
            <el-button type="primary" size="small" @click="showAddressDialog = true">
              选择地址
            </el-button>
          </div>
          
          <div class="address-info" v-if="selectedAddress">
            <div class="address-card">
              <div class="address-details">
                <div class="receiver">
                  <span class="name">{{ selectedAddress.name }}</span>
                  <span class="phone">{{ selectedAddress.phone }}</span>
                </div>
                <div class="address">{{ selectedAddress.address }}</div>
              </div>
              <div class="address-actions">
                <el-button type="text" @click="showAddressDialog = true">修改</el-button>
              </div>
            </div>
          </div>
          
          <div class="no-address" v-else>
            <el-button type="primary" @click="showAddressDialog = true">
              添加收货地址
            </el-button>
          </div>
        </div>
        
        <!-- 商品信息 -->
        <div class="products-section">
          <div class="section-header">
            <h2>商品信息</h2>
          </div>
          
          <div class="products-list">
            <div class="product-item" v-for="item in orderItems" :key="item.id">
              <div class="product-image">
                <img :src="item.product.image" :alt="item.product.name" />
              </div>
              <div class="product-info">
                <h3 class="product-name">{{ item.product.name }}</h3>
                <p class="product-description">{{ item.product.description }}</p>
                <div class="product-price">RM{{ item.product.price }}</div>
              </div>
              <div class="product-quantity">
                <span>数量: {{ item.quantity }}</span>
              </div>
              <div class="product-total">
                <span>小计: RM{{ (item.product.price * item.quantity).toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 订单汇总 -->
        <div class="order-summary">
          <div class="summary-item">
            <span>商品总价:</span>
            <span>RM{{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="summary-item">
            <span>运费:</span>
            <span>RM{{ shippingFee.toFixed(2) }}</span>
          </div>
          <div class="summary-item total">
            <span>应付总额:</span>
            <span>RM{{ totalAmount.toFixed(2) }}</span>
          </div>
        </div>
        
        <!-- 支付方式 -->
        <div class="payment-section">
          <div class="section-header">
            <h2>支付方式</h2>
          </div>
          
          <div class="payment-methods">
            <div 
              class="payment-method"
              v-for="method in paymentMethods"
              :key="method.id"
              :class="{ active: selectedPayment === method.id }"
              @click="selectedPayment = method.id"
            >
              <div class="method-icon">
                <el-icon><component :is="method.icon" /></el-icon>
              </div>
              <div class="method-info">
                <div class="method-name">{{ method.name }}</div>
                <div class="method-desc">{{ method.description }}</div>
              </div>
              <div class="method-radio">
                <el-radio :value="method.id" v-model="selectedPayment" />
              </div>
            </div>
          </div>
        </div>
        
        <!-- 提交订单 -->
        <div class="submit-section">
          <div class="submit-info">
            <p>提交订单即表示您同意我们的服务条款</p>
          </div>
          <div class="submit-actions">
            <el-button size="large" @click="$router.back()">返回购物车</el-button>
            <el-button 
              type="primary" 
              size="large"
              @click="submitOrder"
              :loading="submitting"
              :disabled="!selectedAddress || !selectedPayment"
            >
              提交订单
            </el-button>
          </div>
        </div>
      </div>
      
      <!-- 无商品 -->
      <div class="no-items" v-else>
        <div class="no-items-content">
          <el-icon size="64" color="#ccc"><ShoppingCart /></el-icon>
          <h3>没有商品需要结算</h3>
          <p>请先选择要购买的商品</p>
          <el-button type="primary" @click="$router.push('/')">去购物</el-button>
        </div>
      </div>
    </div>
    
    <!-- 地址选择对话框 -->
    <el-dialog v-model="showAddressDialog" title="选择收货地址" width="600px">
      <div class="address-list">
        <div 
          class="address-item"
          v-for="address in addresses"
          :key="address.id"
          :class="{ active: selectedAddress?.id === address.id }"
          @click="selectAddress(address)"
        >
          <div class="address-details">
            <div class="receiver">
              <span class="name">{{ address.name }}</span>
              <span class="phone">{{ address.phone }}</span>
            </div>
            <div class="address">{{ address.address }}</div>
          </div>
          <div class="address-actions">
            <el-button type="text" size="small">编辑</el-button>
            <el-button type="text" size="small" @click.stop="deleteAddress(address)">删除</el-button>
          </div>
        </div>
        
        <div class="add-address">
          <el-button type="primary" @click="addAddress">添加新地址</el-button>
        </div>
      </div>
      
      <template #footer>
        <el-button @click="showAddressDialog = false">取消</el-button>
        <el-button type="primary" @click="confirmAddress">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { orderApi } from '@/api'
import { ShoppingCart, CreditCard, Wallet, Phone } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'

const router = useRouter()

const orderItems = ref<any[]>([])
const selectedAddress = ref<any>(null)
const selectedPayment = ref('alipay')
const submitting = ref(false)
const showAddressDialog = ref(false)
const addresses = ref<any[]>([])

const paymentMethods = [
  {
    id: 'alipay',
    name: '支付宝',
    description: '安全便捷的在线支付',
    icon: 'CreditCard'
  },
  {
    id: 'wechat',
    name: '微信支付',
    description: '微信扫码支付',
    icon: 'Phone'
  },
  {
    id: 'bank',
    name: '银行卡',
    description: '支持各大银行',
    icon: 'Wallet'
  }
]

// 计算属性
const subtotal = computed(() => {
  return orderItems.value.reduce((total, item) => {
    return total + (item.product.price * item.quantity)
  }, 0)
})

const shippingFee = computed(() => {
  return subtotal.value >= 100 ? 0 : 10 // 满100免运费
})

const totalAmount = computed(() => {
  return subtotal.value + shippingFee.value
})

// 加载订单商品
const loadOrderItems = () => {
  const items = sessionStorage.getItem('checkoutItems')
  if (items) {
    orderItems.value = JSON.parse(items)
  } else {
    // 使用默认数据
    orderItems.value = [
      {
        id: '1',
        product: {
          id: '1',
          name: '商品名称',
          description: '商品描述',
          price: 99.99,
          image: 'https://via.placeholder.com/100x100/409EFF/ffffff?text=商品'
        },
        quantity: 1
      }
    ]
  }
}

// 加载地址列表
const loadAddresses = () => {
  // 使用默认地址数据
  addresses.value = [
    {
      id: '1',
      name: '张三',
      phone: '13800138000',
      address: '北京市朝阳区某某街道某某小区1号楼1单元101室'
    },
    {
      id: '2',
      name: '李四',
      phone: '13900139000',
      address: '上海市浦东新区某某路某某大厦2楼'
    }
  ]
}

// 选择地址
const selectAddress = (address: any) => {
  selectedAddress.value = address
}

// 确认地址
const confirmAddress = () => {
  showAddressDialog.value = false
}

// 添加地址
const addAddress = () => {
  ElMessage.info('添加地址功能待开发')
}

// 删除地址
const deleteAddress = (address: any) => {
  const index = addresses.value.findIndex(addr => addr.id === address.id)
  if (index > -1) {
    addresses.value.splice(index, 1)
    ElMessage.success('地址已删除')
  }
}

// 提交订单
const submitOrder = async () => {
  if (!selectedAddress.value) {
    ElMessage.warning('请选择收货地址')
    return
  }
  
  if (!selectedPayment.value) {
    ElMessage.warning('请选择支付方式')
    return
  }
  
  try {
    submitting.value = true
    
    const orderData = {
      items: orderItems.value,
      address: selectedAddress.value,
      paymentMethod: selectedPayment.value,
      subtotal: subtotal.value,
      shippingFee: shippingFee.value,
      totalAmount: totalAmount.value
    }
    
    const response = await orderApi.createOrder(orderData)
    ElMessage.success('订单提交成功')
    
    // 清空购物车中的已购买商品
    sessionStorage.removeItem('checkoutItems')
    
    // 跳转到订单详情页
    router.push(`/orders/${response.orderId}`)
  } catch (error) {
    console.error('提交订单失败:', error)
    ElMessage.error('提交订单失败，请重试')
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadOrderItems()
  loadAddresses()
})
</script>

<style scoped lang="scss">
.order-page {
  padding: 20px 0;
  background: $background-base;
  min-height: 100vh;
}

.order-header {
  background: #fff;
  padding: 20px 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  
  h1 {
    font-size: 24px;
    font-weight: bold;
    color: $text-primary;
    margin: 0;
  }
}

.order-content {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 20px;
}

.address-section,
.products-section,
.payment-section {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  
  h2 {
    font-size: 18px;
    font-weight: bold;
    color: $text-primary;
    margin: 0;
  }
}

.address-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border: 1px solid $border-base;
  border-radius: 8px;
  
  .address-details {
    .receiver {
      margin-bottom: 5px;
      
      .name {
        font-weight: 500;
        margin-right: 10px;
      }
      
      .phone {
        color: $text-secondary;
      }
    }
    
    .address {
      color: $text-regular;
    }
  }
}

.no-address {
  text-align: center;
  padding: 40px;
  color: $text-secondary;
}

.products-list {
  .product-item {
    display: grid;
    grid-template-columns: 80px 1fr 100px 120px;
    gap: 15px;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid $border-lighter;
    
    &:last-child {
      border-bottom: none;
    }
  }
  
  .product-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  
  .product-info {
    .product-name {
      font-size: 16px;
      font-weight: 500;
      color: $text-primary;
      margin: 0 0 5px 0;
    }
    
    .product-description {
      font-size: 14px;
      color: $text-secondary;
      margin: 0 0 5px 0;
    }
    
    .product-price {
      font-size: 16px;
      font-weight: bold;
      color: $danger-color;
    }
  }
  
  .product-quantity,
  .product-total {
    text-align: center;
    color: $text-regular;
  }
}

.order-summary {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  position: sticky;
  top: 20px;
  height: fit-content;
  
  .summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid $border-lighter;
    
    &:last-child {
      border-bottom: none;
    }
    
    &.total {
      font-size: 18px;
      font-weight: bold;
      color: $danger-color;
    }
  }
}

.payment-methods {
  .payment-method {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 1px solid $border-base;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:hover {
      border-color: $primary-color;
    }
    
    &.active {
      border-color: $primary-color;
      background: rgba(64, 158, 255, 0.1);
    }
    
    .method-icon {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: $background-base;
      border-radius: 50%;
      margin-right: 15px;
    }
    
    .method-info {
      flex: 1;
      
      .method-name {
        font-weight: 500;
        margin-bottom: 5px;
      }
      
      .method-desc {
        font-size: 12px;
        color: $text-secondary;
      }
    }
  }
}

.submit-section {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  
  .submit-info {
    p {
      color: $text-secondary;
      margin: 0;
    }
  }
  
  .submit-actions {
    display: flex;
    gap: 15px;
  }
}

.no-items {
  background: #fff;
  padding: 60px 20px;
  border-radius: 8px;
  text-align: center;
  
  .no-items-content {
    h3 {
      font-size: 20px;
      color: $text-primary;
      margin: 20px 0 10px;
    }
    
    p {
      color: $text-secondary;
      margin-bottom: 20px;
    }
  }
}

.address-list {
  .address-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 1px solid $border-base;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:hover {
      border-color: $primary-color;
    }
    
    &.active {
      border-color: $primary-color;
      background: rgba(64, 158, 255, 0.1);
    }
    
    .address-details {
      .receiver {
        margin-bottom: 5px;
        
        .name {
          font-weight: 500;
          margin-right: 10px;
        }
        
        .phone {
          color: $text-secondary;
        }
      }
      
      .address {
        color: $text-regular;
      }
    }
    
    .address-actions {
      display: flex;
      gap: 10px;
    }
  }
  
  .add-address {
    text-align: center;
    padding: 20px;
    border: 2px dashed $border-base;
    border-radius: 8px;
  }
}

@media (max-width: 768px) {
  .order-content {
    grid-template-columns: 1fr;
  }
  
  .order-summary {
    position: static;
  }
  
  .product-item {
    grid-template-columns: 1fr;
    gap: 10px;
    text-align: center;
  }
  
  .submit-section {
    flex-direction: column;
    gap: 15px;
    
    .submit-actions {
      width: 100%;
      
      .el-button {
        flex: 1;
      }
    }
  }
}
</style>
