<template>
  <div class="help-center">
    <!-- 头部 -->
    <div class="header">
      <button class="back-btn" @click="goBack">
        <i class="el-icon-arrow-left"></i>
      </button>
      <h1>{{ $t('help.title') }}</h1>
      <div class="placeholder"></div>
    </div>

    <!-- 搜索框 -->
    <div class="search-section">
      <div class="search-box">
        <i class="el-icon-search"></i>
        <input 
          v-model="searchQuery" 
          :placeholder="$t('help.searchPlaceholder')"
          @input="handleSearch"
        />
      </div>
    </div>

    <!-- 帮助分类 -->
    <div class="help-categories">
      <div 
        v-for="category in filteredCategories" 
        :key="category.id"
        class="category-item"
        @click="toggleCategory(category.id)"
      >
        <div class="category-header">
          <div class="category-icon">
            <i :class="category.icon"></i>
          </div>
          <div class="category-info">
            <h3>{{ category.name }}</h3>
            <p>{{ category.description }}</p>
          </div>
          <div class="category-arrow">
            <i :class="expandedCategories.includes(category.id) ? 'el-icon-arrow-up' : 'el-icon-arrow-down'"></i>
          </div>
        </div>
        
        <!-- 展开的FAQ列表 -->
        <div v-if="expandedCategories.includes(category.id)" class="faq-list">
          <div 
            v-for="faq in category.faqs" 
            :key="faq.id"
            class="faq-item"
            @click="toggleFaq(faq.id)"
          >
            <div class="faq-question">
              <h4>{{ faq.question }}</h4>
              <i :class="expandedFaqs.includes(faq.id) ? 'el-icon-arrow-up' : 'el-icon-arrow-down'"></i>
            </div>
            <div v-if="expandedFaqs.includes(faq.id)" class="faq-answer">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 联系客服 -->
    <div class="contact-section">
      <h3>{{ $t('help.contactSupport') }}</h3>
      <p>{{ $t('help.contactDesc') }}</p>
      
      <div class="contact-methods">
        <button class="contact-btn" @click="openLiveChat">
          <i class="el-icon-chat-line-round"></i>
          {{ $t('help.liveChat') }}
        </button>
        <button class="contact-btn" @click="sendEmail">
          <i class="el-icon-message"></i>
          {{ $t('help.email') }}
        </button>
        <button class="contact-btn" @click="callPhone">
          <i class="el-icon-phone"></i>
          {{ $t('help.phone') }}
        </button>
      </div>
    </div>

    <!-- 反馈建议 -->
    <div class="feedback-section">
      <h3>{{ $t('help.feedback') }}</h3>
      <p>{{ $t('help.feedbackDesc') }}</p>
      
      <div class="feedback-form">
        <textarea 
          v-model="feedbackText"
          :placeholder="$t('help.feedbackPlaceholder')"
          rows="4"
        ></textarea>
        <button class="submit-feedback-btn" @click="submitFeedback">
          {{ $t('help.submitFeedback') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

const router = useRouter()
const { t } = useI18n()

// 状态管理
const searchQuery = ref('')
const expandedCategories = ref<number[]>([])
const expandedFaqs = ref<number[]>([])
const feedbackText = ref('')

// 帮助分类数据
const helpCategories = ref([
  {
    id: 1,
    name: '账户与登录',
    description: '账户注册、登录、密码相关问题',
    icon: 'el-icon-user',
    faqs: [
      {
        id: 1,
        question: '如何注册账户？',
        answer: '点击首页的"注册"按钮，输入手机号和验证码，设置密码即可完成注册。'
      },
      {
        id: 2,
        question: '忘记密码怎么办？',
        answer: '在登录页面点击"忘记密码"，输入手机号获取验证码，即可重置密码。'
      },
      {
        id: 3,
        question: '如何修改个人信息？',
        answer: '进入个人中心，点击"编辑资料"即可修改姓名、邮箱、生日等信息。'
      }
    ]
  },
  {
    id: 2,
    name: '购物与支付',
    description: '商品购买、支付方式、订单相关问题',
    icon: 'el-icon-shopping-cart-2',
    faqs: [
      {
        id: 4,
        question: '支持哪些支付方式？',
        answer: '目前支持微信支付、支付宝、银行卡等多种支付方式。'
      },
      {
        id: 5,
        question: '如何查看订单状态？',
        answer: '进入"我的订单"页面，可以查看所有订单的详细状态和物流信息。'
      },
      {
        id: 6,
        question: '可以取消订单吗？',
        answer: '在订单未发货前，可以在订单详情页面申请取消订单。'
      }
    ]
  },
  {
    id: 3,
    name: '配送与售后',
    description: '物流配送、退换货、售后服务',
    icon: 'el-icon-truck',
    faqs: [
      {
        id: 7,
        question: '配送时间需要多久？',
        answer: '一般情况下，商品会在1-3个工作日内发货，3-7天内送达。'
      },
      {
        id: 8,
        question: '如何申请退换货？',
        answer: '在订单详情页面点击"申请售后"，选择退换货原因并提交申请。'
      },
      {
        id: 9,
        question: '退换货政策是什么？',
        answer: '商品支持7天无理由退换货，特殊商品除外。具体政策请查看商品详情。'
      }
    ]
  },
  {
    id: 4,
    name: '技术问题',
    description: 'APP使用、页面显示、功能问题',
    icon: 'el-icon-setting',
    faqs: [
      {
        id: 10,
        question: 'APP无法正常使用怎么办？',
        answer: '请尝试重启APP或更新到最新版本，如问题持续请联系客服。'
      },
      {
        id: 11,
        question: '页面显示异常怎么办？',
        answer: '请清除浏览器缓存或尝试使用其他浏览器访问。'
      },
      {
        id: 12,
        question: '如何反馈问题？',
        answer: '可以通过帮助中心的"反馈建议"功能提交问题，我们会及时处理。'
      }
    ]
  }
])

// 计算属性
const filteredCategories = computed(() => {
  if (!searchQuery.value) {
    return helpCategories.value
  }
  
  return helpCategories.value.map(category => ({
    ...category,
    faqs: category.faqs.filter(faq => 
      faq.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      faq.answer.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })).filter(category => category.faqs.length > 0)
})

// 方法
const goBack = () => {
  router.go(-1)
}

const handleSearch = () => {
  // 搜索时自动展开所有分类
  if (searchQuery.value) {
    expandedCategories.value = filteredCategories.value.map(cat => cat.id)
  } else {
    expandedCategories.value = []
  }
}

const toggleCategory = (categoryId: number) => {
  const index = expandedCategories.value.indexOf(categoryId)
  if (index > -1) {
    expandedCategories.value.splice(index, 1)
  } else {
    expandedCategories.value.push(categoryId)
  }
}

const toggleFaq = (faqId: number) => {
  const index = expandedFaqs.value.indexOf(faqId)
  if (index > -1) {
    expandedFaqs.value.splice(index, 1)
  } else {
    expandedFaqs.value.push(faqId)
  }
}

const openLiveChat = () => {
  window.open('https://direct.lc.chat/19346006/', '_blank')
}

const sendEmail = () => {
  window.location.href = 'mailto:support@tiktokbusines.store'
}

const callPhone = () => {
  window.location.href = 'tel:+60123456789'
}

const submitFeedback = () => {
  if (!feedbackText.value.trim()) {
    ElMessage.warning(t('help.feedbackRequired'))
    return
  }
  
  // 这里应该调用API提交反馈
  ElMessage.success(t('help.feedbackSubmitted'))
  feedbackText.value = ''
}

// 生命周期
onMounted(() => {
  // 可以在这里加载帮助数据
})
</script>

<style scoped lang="scss">
.help-center {
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

  .back-btn {
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

  .placeholder {
    width: 40px;
  }
}

.search-section {
  padding: 20px;
  background: #fff;
  margin-bottom: 12px;

  .search-box {
    position: relative;
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 8px;
    padding: 12px 16px;

    i {
      color: #999;
      margin-right: 8px;
    }

    input {
      flex: 1;
      border: none;
      background: transparent;
      outline: none;
      font-size: 16px;
      color: #333;

      &::placeholder {
        color: #999;
      }
    }
  }
}

.help-categories {
  background: #fff;
  margin-bottom: 12px;

  .category-item {
    border-bottom: 1px solid #f0f0f0;

    &:last-child {
      border-bottom: none;
    }

    .category-header {
      display: flex;
      align-items: center;
      padding: 20px;
      cursor: pointer;

      .category-icon {
        width: 40px;
        height: 40px;
        background: #409eff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;

        i {
          color: white;
          font-size: 18px;
        }
      }

      .category-info {
        flex: 1;

        h3 {
          font-size: 16px;
          font-weight: 600;
          color: #333;
          margin: 0 0 4px;
        }

        p {
          font-size: 14px;
          color: #666;
          margin: 0;
        }
      }

      .category-arrow {
        i {
          color: #999;
          font-size: 14px;
        }
      }
    }

    .faq-list {
      background: #f8f9fa;
      padding: 0 20px 20px;

      .faq-item {
        border-bottom: 1px solid #e9ecef;

        &:last-child {
          border-bottom: none;
        }

        .faq-question {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 16px 0;
          cursor: pointer;

          h4 {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin: 0;
            flex: 1;
            margin-right: 12px;
          }

          i {
            color: #999;
            font-size: 12px;
          }
        }

        .faq-answer {
          padding: 0 0 16px;

          p {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
            margin: 0;
          }
        }
      }
    }
  }
}

.contact-section {
  background: #fff;
  padding: 20px;
  margin-bottom: 12px;

  h3 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px;
  }

  p {
    font-size: 14px;
    color: #666;
    margin: 0 0 16px;
  }

  .contact-methods {
    display: flex;
    flex-direction: column;
    gap: 8px;

    .contact-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      font-size: 14px;
      color: #333;
      cursor: pointer;
      transition: all 0.2s;

      &:hover {
        background: #e9ecef;
      }

      i {
        font-size: 16px;
        color: #409eff;
      }
    }
  }
}

.feedback-section {
  background: #fff;
  padding: 20px;

  h3 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px;
  }

  p {
    font-size: 14px;
    color: #666;
    margin: 0 0 16px;
  }

  .feedback-form {
    textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      color: #333;
      resize: vertical;
      margin-bottom: 12px;

      &:focus {
        outline: none;
        border-color: #409eff;
      }

      &::placeholder {
        color: #999;
      }
    }

    .submit-feedback-btn {
      width: 100%;
      background: #409eff;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;

      &:hover {
        background: #337ecc;
      }
    }
  }
}
</style>
