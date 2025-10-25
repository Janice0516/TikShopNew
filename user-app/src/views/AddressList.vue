<template>
  <div class="address-management">
    <!-- 头部 -->
    <div class="header">
      <button class="back-btn" @click="goBack">
        <i class="el-icon-arrow-left"></i>
      </button>
      <h1>{{ $t('address.title') }}</h1>
      <button class="add-btn" @click="addAddress">
        <i class="el-icon-plus"></i>
      </button>
    </div>

    <!-- 地址列表 -->
    <div class="address-list">
      <div v-if="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>{{ $t('common.loading') }}</p>
      </div>

      <div v-else-if="addresses.length === 0" class="empty-state">
        <div class="empty-icon">📍</div>
        <h3>{{ $t('address.noAddress') }}</h3>
        <p>{{ $t('address.noAddressDesc') }}</p>
        <button class="add-first-btn" @click="addAddress">
          {{ $t('address.addFirst') }}
        </button>
      </div>

      <div v-else class="address-items">
        <div 
          v-for="address in addresses" 
          :key="address.id"
          class="address-item"
          :class="{ 'default': address.isDefault }"
        >
          <div class="address-content">
            <div class="receiver-info">
              <span class="name">{{ address.receiverName }}</span>
              <span class="phone">{{ address.phone }}</span>
              <span v-if="address.isDefault" class="default-tag">{{ $t('address.default') }}</span>
            </div>
            <div class="address-detail">
              {{ address.province }} {{ address.city }} {{ address.district }} {{ address.detailAddress }}
            </div>
          </div>
          
          <div class="address-actions">
            <button 
              v-if="!address.isDefault" 
              class="set-default-btn"
              @click="setDefaultAddress(address.id)"
            >
              {{ $t('address.setDefault') }}
            </button>
            <button class="edit-btn" @click="editAddress(address)">
              {{ $t('common.edit') }}
            </button>
            <button class="delete-btn" @click="deleteAddress(address)">
              {{ $t('common.delete') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { addressApi } from '@/api'

const router = useRouter()
const { t } = useI18n()

// 状态管理
const loading = ref(false)
const addresses = ref<any[]>([])

// 方法
const goBack = () => {
  router.go(-1)
}

const addAddress = () => {
  router.push('/address/add')
}

const editAddress = (address: any) => {
  router.push(`/address/edit/${address.id}`)
}

const setDefaultAddress = async (id: string) => {
  try {
    await addressApi.setDefaultAddress(id)
    ElMessage.success(t('address.setDefaultSuccess'))
    await loadAddresses()
  } catch (error: any) {
    ElMessage.error(error.message || t('address.setDefaultFailed'))
  }
}

const deleteAddress = async (address: any) => {
  try {
    await ElMessageBox.confirm(
      t('address.deleteConfirm'),
      t('common.confirm'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning',
      }
    )
    
    await addressApi.deleteAddress(address.id)
    ElMessage.success(t('address.deleteSuccess'))
    await loadAddresses()
  } catch (error: any) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || t('address.deleteFailed'))
    }
  }
}

const loadAddresses = async () => {
  try {
    loading.value = true
    const response = await addressApi.getUserAddresses()
    addresses.value = response.data || []
  } catch (error: any) {
    console.error('加载地址失败:', error)
    ElMessage.error(error.message || t('address.loadFailed'))
  } finally {
    loading.value = false
  }
}

// 生命周期
onMounted(() => {
  loadAddresses()
})
</script>

<style scoped lang="scss">
.address-management {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #fff;
  border-bottom: 1px solid #eee;

  .back-btn, .add-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #f0f0f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #666;

    &:hover {
      background: #e0e0e0;
    }
  }

  h1 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #333;
  }
}

.address-list {
  padding: 20px;
}

.loading {
  text-align: center;
  padding: 40px 0;

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #409eff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
  }

  p {
    color: #666;
    margin: 0;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 60px 20px;

  .empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
  }

  h3 {
    font-size: 18px;
    color: #333;
    margin: 0 0 8px;
  }

  p {
    color: #666;
    margin: 0 0 24px;
  }

  .add-first-btn {
    background: #409eff;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;

    &:hover {
      background: #337ecc;
    }
  }
}

.address-items {
  .address-item {
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

    &.default {
      border: 2px solid #409eff;
    }

    .address-content {
      margin-bottom: 12px;

      .receiver-info {
        display: flex;
        align-items: center;
        margin-bottom: 8px;

        .name {
          font-weight: 600;
          color: #333;
          margin-right: 12px;
        }

        .phone {
          color: #666;
          margin-right: 12px;
        }

        .default-tag {
          background: #409eff;
          color: white;
          font-size: 12px;
          padding: 2px 8px;
          border-radius: 12px;
        }
      }

      .address-detail {
        color: #666;
        line-height: 1.5;
      }
    }

    .address-actions {
      display: flex;
      gap: 8px;

      button {
        padding: 6px 12px;
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;

        &.set-default-btn {
          color: #409eff;
          border-color: #409eff;

          &:hover {
            background: #409eff;
            color: white;
          }
        }

        &.edit-btn {
          color: #67c23a;
          border-color: #67c23a;

          &:hover {
            background: #67c23a;
            color: white;
          }
        }

        &.delete-btn {
          color: #f56c6c;
          border-color: #f56c6c;

          &:hover {
            background: #f56c6c;
            color: white;
          }
        }
      }
    }
  }
}
</style>

