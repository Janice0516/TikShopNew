<template>
  <el-dropdown @command="handleLanguageChange" trigger="click">
    <div class="language-selector">
      <span class="flag">{{ currentLanguage.flag }}</span>
      <span>{{ currentLanguage.name }}</span>
      <el-icon><ArrowDown /></el-icon>
    </div>
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item
          v-for="lang in languages"
          :key="lang.code"
          :command="lang.code"
          :disabled="lang.code === currentLanguage.code"
        >
          <span class="flag">{{ lang.flag }}</span>
          {{ lang.name }}
        </el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { languages, setLanguage } from '@/i18n'
import { ElMessage } from 'element-plus'

const { locale, t } = useI18n()

const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === locale.value) || languages[0]
})

const handleLanguageChange = (langCode: string) => {
  setLanguage(langCode)
  ElMessage.success(t('message.operationSuccess'))
}
</script>

<style scoped>
.language-selector {
  padding: 5px 10px;
  border-radius: 4px;
  transition: all 0.3s;
}

.language-selector:hover {
  background-color: rgba(64, 158, 255, 0.1);
}

.flag {
  font-size: 18px;
  margin-right: 5px;
}

.el-dropdown-menu__item .flag {
  margin-right: 8px;
}
</style>

